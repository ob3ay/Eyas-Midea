<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignEmployeeRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        Gate::authorize('viewAny', Department::class);
        $scope = $user->highestScopeFor('departments.view');
        $departmentQuery = Department::query();

        if ($scope !== 'organization') {
            $departmentQuery->whereKey($user->employee?->department_id ?? 0);
        }

        $departments = $departmentQuery
            ->withCount(['sections', 'employees', 'users'])
            ->with(['manager.user', 'sections'])
            ->orderBy('name')
            ->get();

        $sections = Section::query()
            ->when($scope !== 'organization', fn($query) => $query->where('department_id', $user->employee?->department_id ?? 0))
            ->with(['department', 'head.user'])
            ->withCount(['employees', 'users'])
            ->orderBy('name')
            ->get();

        $availableEmployees = Employee::query()
            ->active()
            ->when($scope !== 'organization', fn($query) => $query->where('department_id', $user->employee?->department_id ?? 0))
            ->with('user')
            ->orderBy('full_name')
            ->get();

        return view('departments.index', [
            'departments' => $departments,
            'sections' => $sections,
            'availableEmployees' => $availableEmployees,
            'nextDeptCode' => $this->getNextDepartmentCode(),
        ]);
    }
    public function structure(Request $request)
    {
        $user = $request->user();
        Gate::authorize('viewAny', Department::class);
        $scope = $user->highestScopeFor('departments.view');

        $departments = Department::query()
            ->when($scope !== 'organization', fn($q) => $q->whereKey($user->employee?->department_id ?? 0))
            ->with(['manager', 'sections.head', 'sections.employees', 'employees'])
            ->withCount(['sections', 'employees'])
            ->orderBy('name')
            ->get();

        $totalSections = $departments->sum('sections_count');
        $totalEmployees = $departments->sum('employees_count');
        $assignedManagers = $departments->whereNotNull('manager_employee_id')->count();
        $ratio = $departments->count() > 0 ? round(($assignedManagers / $departments->count()) * 100) : 100;
        $selectedDeptId = $request->query('department_id', '');

        return view('departments.structure', compact(
            'departments',
            'totalSections',
            'totalEmployees',
            'assignedManagers',
            'ratio',
            'selectedDeptId',
        ));
    }

    public function show(Request $request, Department $department)
    {
        $user = $request->user();
        Gate::authorize('view', $department);
        $scope = $user->highestScopeFor('departments.view');

        if ($scope !== 'organization' && (int) $user->employee?->department_id !== $department->id) {
            abort(403, 'لا يمكنك عرض تفاصيل دائرة خارج نطاق دائرتك.');
        }

        $department->load([
            'manager.user',
            'sections.head.user',
            'sections.employees.user',
            'employees.user',
            'projects.owner.user',
        ]);
        // Get available employees for assignment (those not already assigned to this department)
        $availableEmployees = Employee::query()
            ->active()
            ->where(function ($query) use ($department) {
                $query->whereNull('department_id')
                    ->orWhere('department_id', '!=', $department->id);
            })
            ->with('user')
            ->orderBy('full_name')
            ->get();


        return view('departments.show', compact('department', 'availableEmployees'))->with('sections', $department->sections);

    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $sections = $data['sections'] ?? [];
        unset($data['sections']);

        $managerId = ! empty($data['manager_employee_id']) ? (int) $data['manager_employee_id'] : null;
        unset($data['manager_employee_id']);

        if (empty($data['code'])) {
            $data['code'] = $this->getNextDepartmentCode();
        }

        DB::transaction(function () use ($data, $managerId, $sections) {
            // Force delete any trashed department with the same code or name so MySQL unique constraints do not conflict
            Department::onlyTrashed()
                ->where(function ($q) use ($data) {
                    $q->where('name', $data['name']);
                    if (! empty($data['code'])) {
                        $q->orWhere('code', $data['code']);
                    }
                })
                ->forceDelete();

            $department = Department::create($data);

            if ($managerId) {
                $manager = Employee::find($managerId);
                if ($manager) {
                    $manager->update([
                        'department_id' => $department->id,
                        'section_id' => null,
                    ]);
                    $department->update(['manager_employee_id' => $manager->id]);
                }
            }

            if (! empty($sections)) {
                foreach (array_unique(array_filter($sections)) as $sectionName) {
                    $trimmed = trim($sectionName);
                    if ($trimmed !== '') {
                        Section::create([
                            'department_id' => $department->id,
                            'name' => $trimmed,
                            'is_active' => true,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('departments.index')->with('success', 'تمت إضافة الدائرة وأقسامها بنجاح.');
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['manager_employee_id'])) {
            $manager = Employee::find((int) $data['manager_employee_id']);
            if ($manager && $manager->department_id !== $department->id) {
                $manager->update([
                    'department_id' => $department->id,
                    'section_id' => null,
                ]);
            }
        }

        $department->update($data);

        return redirect()->route('departments.index')->with('success', 'تم تحديث بيانات الدائرة بنجاح.');
    }

    public function destroy(Request $request, Department $department): RedirectResponse
    {
        Gate::authorize('delete', $department);

        $reasons = [];
        if ($department->employees()->exists()) {
            $empCount = $department->employees()->count();
            $empNames = $department->employees()->pluck('full_name')->take(3)->implode('، ');
            if ($empCount > 3) {
                $empNames .= ' وآخرين';
            }
            $reasons[] = "وجود ({$empCount}) موظف مسجلين بها [{$empNames}]";
        }

        if ($department->sections()->exists()) {
            $secCount = $department->sections()->count();
            $reasons[] = "وجود ({$secCount}) قسم تخصصي تابع لها";
        }

        if ($department->projects()->exists()) {
            $projCount = $department->projects()->count();
            $reasons[] = "وجود ({$projCount}) مشروع مرتبط بهذه الدائرة";
        }

        if (! empty($reasons)) {
            return back()->with('error', 'لا يمكن حذف هذه الدائرة بسبب: ' . implode('، و', $reasons) . '. يرجى نقل الموظفين إلى دائرة أخرى أولاً عبر زر "نقل / تعيين موظف" أعلى الصفحة.');
        }

        // Clean up any trashed sections belonging to this department so FK doesn't fail
        $department->sections()->withTrashed()->forceDelete();

        $department->forceDelete();

        return back()->with('success', 'تم حذف الدائرة بنجاح.');
    }

    public function storeSection(StoreSectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->assertDepartmentScope($request->user(), $data['department_id'], 'departments.create');

        Section::create($data);

        return back()->with('success', 'تمت إضافة القسم التخصصي بنجاح');
    }

    public function updateSection(UpdateSectionRequest $request, Section $section): RedirectResponse
    {
        $data = $request->validated();
        $this->assertDepartmentScope($request->user(), $data['department_id'], 'departments.update');
        $section->update($data);

        return back()->with('success', 'تم تحديث بيانات القسم بنجاح');
    }

    public function destroySection(Section $section): RedirectResponse
    {
        Gate::authorize('delete', $section);

        if ($section->employees()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا القسم نظراً لوجود موظفين مسجلين به.');
        }

        $section->delete();

        return back()->with('success', 'تم حذف القسم بنجاح');
    }

    public function assignEmployee(AssignEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();
        Gate::authorize('update', Department::findOrFail($data['department_id']));

        $employee->update([
            'department_id' => $data['department_id'],
            'section_id' => $data['section_id'] ?? null,
        ]);

        return back()->with('success', 'تم نقل الموظف وتحديث تبعيته التنظيمية');
    }

    public function showSection(Request $request, Section $section)
    {
        $user = $request->user();
        Gate::authorize('view', $section);
        $scope = $user->highestScopeFor('departments.view');

        if ($scope !== 'organization' && (int) $user->employee?->department_id !== $section->department_id && (int) $user->employee?->section_id !== $section->id) {
            abort(403, 'لا يمكنك عرض تفاصيل هذا القسم خارج نطاقك.');
        }

        $section->load([
            'department.manager.user',
            'head.user',
            'employees.user',
            'department.projects.owner.user',
        ]);

        return view('departments.section', compact('section'));
    }

    private function getNextDepartmentCode(): string
    {
        $existingCodes = Department::withTrashed()->pluck('code')->filter()->toArray();
        $maxNum = 0;
        foreach ($existingCodes as $code) {
            if (preg_match('/^DEPT-(\d+)$/i', $code, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNum) {
                    $maxNum = $num;
                }
            }
        }

        $candidateNum = $maxNum + 1;
        $code = 'DEPT-' . str_pad($candidateNum, 2, '0', STR_PAD_LEFT);

        while (Department::withTrashed()->where('code', $code)->exists()) {
            $candidateNum++;
            $code = 'DEPT-' . str_pad($candidateNum, 2, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    private function assertDepartmentScope($user, int $departmentId, string $permission): void
    {
        $scope = $user->highestScopeFor($permission);

        if ($scope === 'organization') {
            return;
        }

        if ($scope !== 'department' || (int) $user->employee?->department_id !== $departmentId) {
            abort(403, 'لا يمكنك إدارة عناصر خارج نطاق دائرتك.');
        }
    }
}
