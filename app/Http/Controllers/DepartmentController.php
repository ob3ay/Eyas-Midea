<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignEmployeeRequest;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Models\AuditLog;
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
            ->when($scope !== 'organization', fn ($query) => $query->where('department_id', $user->employee?->department_id ?? 0))
            ->with(['department', 'head.user'])
            ->withCount(['employees', 'users'])
            ->orderBy('name')
            ->get();

        $availableEmployees = Employee::query()
            ->active()
            ->when($scope !== 'organization', fn ($query) => $query->where('department_id', $user->employee?->department_id ?? 0))
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
            ->when($scope !== 'organization', fn ($q) => $q->whereKey($user->employee?->department_id ?? 0))
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
            'projects' => function ($query) use ($user): void {
                $query->whereIn(
                    'projects.id',
                    $user->projectsWithinScope('projects.view')->select('projects.id'),
                )->with('owner.user');
            },
        ]);
        // Get available employees for assignment (those not already assigned to this department)
        $availableEmployees = $user->highestScopeFor('departments.update') === 'organization'
            ? Employee::query()
            ->active()
            ->where(function ($query) use ($department) {
                $query->whereNull('department_id')
                    ->orWhere('department_id', '!=', $department->id);
            })
            ->with('user')
            ->orderBy('full_name')
            ->get()
            : collect();

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
            $department = Department::create($data);

            if ($managerId) {
                $manager = Employee::query()->lockForUpdate()->findOrFail($managerId);
                $manager->update([
                    'department_id' => $department->id,
                    'section_id' => null,
                ]);
                $department->update(['manager_employee_id' => $manager->id]);
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
            AuditLog::record('Create', 'Department', $department->id);
        });

        return redirect()->route('departments.index')->with('success', 'تمت إضافة الدائرة وأقسامها بنجاح.');
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $department): void {
            if (! empty($data['manager_employee_id'])) {
                $manager = Employee::query()->lockForUpdate()->findOrFail((int) $data['manager_employee_id']);
                if ($manager->department_id !== $department->id) {
                    $manager->update([
                        'department_id' => $department->id,
                        'section_id' => null,
                    ]);
                }
            }

            $department->update($data);
            AuditLog::record('Update', 'Department', $department->id);
        });

        return redirect()->route('departments.index')->with('success', 'تم تحديث بيانات الدائرة بنجاح.');
    }

    public function destroy(Request $request, Department $department): RedirectResponse
    {
        Gate::authorize('delete', $department);

        $reasons = DB::transaction(function () use ($department): array {
            $lockedDepartment = Department::query()->lockForUpdate()->findOrFail($department->id);
            $reasons = [];
            $employees = $lockedDepartment->employees()->lockForUpdate();
            if ($employees->exists()) {
                $empCount = (clone $employees)->count();
                $empNames = (clone $employees)->limit(3)->pluck('full_name')->implode('، ');
                if ($empCount > 3) {
                    $empNames .= ' وآخرين';
                }
                $reasons[] = "وجود ({$empCount}) موظف مسجلين بها [{$empNames}]";
            }

            $sections = $lockedDepartment->sections()->lockForUpdate();
            if ($sections->exists()) {
                $reasons[] = 'وجود ('.$sections->count().') قسم تخصصي تابع لها';
            }

            $projects = $lockedDepartment->projects()->lockForUpdate();
            if ($projects->exists()) {
                $reasons[] = 'وجود ('.$projects->count().') مشروع مرتبط بهذه الدائرة';
            }

            if ($reasons !== []) {
                return $reasons;
            }

            $lockedDepartment->sections()->withTrashed()->forceDelete();
            $lockedDepartment->forceDelete();
            AuditLog::record('Delete', 'Department', $lockedDepartment->id);

            return [];
        });

        if ($reasons !== []) {
            return back()->with('error', 'لا يمكن حذف هذه الدائرة بسبب: '.implode('، و', $reasons).'. يرجى نقل الموظفين إلى دائرة أخرى أولاً عبر زر "نقل / تعيين موظف" أعلى الصفحة.');
        }

        return back()->with('success', 'تم حذف الدائرة بنجاح.');
    }

    public function storeSection(StoreSectionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->assertDepartmentScope($request->user(), $data['department_id'], 'departments.create');

        DB::transaction(function () use ($data): void {
            $section = Section::create($data);
            AuditLog::record('Create', 'Section', $section->id);
        });

        return back()->with('success', 'تمت إضافة القسم التخصصي بنجاح');
    }

    public function updateSection(UpdateSectionRequest $request, Section $section): RedirectResponse
    {
        $data = $request->validated();
        $this->assertDepartmentScope($request->user(), $data['department_id'], 'departments.update');

        if (
            (int) $section->department_id !== (int) $data['department_id']
            && $section->employees()->exists()
        ) {
            return back()->withInput()->withErrors([
                'department_id' => 'انقل الموظفين المرتبطين بالقسم قبل نقله إلى دائرة أخرى.',
            ]);
        }

        DB::transaction(function () use ($section, $data): void {
            $section->update($data);
            AuditLog::record('Update', 'Section', $section->id);
        });

        return back()->with('success', 'تم تحديث بيانات القسم بنجاح');
    }

    public function destroySection(Section $section): RedirectResponse
    {
        Gate::authorize('delete', $section);

        $deleted = DB::transaction(function () use ($section): bool {
            $lockedSection = Section::query()->lockForUpdate()->findOrFail($section->id);
            if ($lockedSection->employees()->lockForUpdate()->exists()) {
                return false;
            }

            $lockedSection->delete();
            AuditLog::record('Delete', 'Section', $lockedSection->id);

            return true;
        });

        if (! $deleted) {
            return back()->with('error', 'لا يمكن حذف هذا القسم نظراً لوجود موظفين مسجلين به.');
        }

        return back()->with('success', 'تم حذف القسم بنجاح');
    }

    public function assignEmployee(AssignEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $targetDepartment = Department::findOrFail($data['department_id']);
        Gate::authorize('update', $targetDepartment);

        $scope = $user->highestScopeFor('departments.update');
        $ownEmployee = $user->employee;

        if ($scope !== 'organization') {
            abort_unless($ownEmployee !== null, 403, 'حسابك غير مرتبط بسجل موظف.');

            if ($scope === 'self' && $employee->id !== $ownEmployee->id) {
                abort(403, 'لا يمكنك نقل موظف خارج حسابك.');
            }

            if ($scope === 'department') {
                abort_unless(
                    (int) $employee->department_id === (int) $ownEmployee->department_id,
                    403,
                    'لا يمكنك نقل موظف خارج دائرتك.'
                );
            }
        }

        DB::transaction(function () use ($data, $employee): void {
            $employee = Employee::query()->lockForUpdate()->findOrFail($employee->id);
            $employee->update([
                'department_id' => $data['department_id'],
                'section_id' => $data['section_id'] ?? null,
            ]);
            AuditLog::record('AssignEmployee', 'Employee', $employee->id);
        });

        return back()->with('success', 'تم نقل الموظف وتحديث تبعيته التنظيمية');
    }

    public function showSection(Request $request, Section $section)
    {
        $user = $request->user();
        Gate::authorize('view', $section);
        $section->load([
            'department.manager.user',
            'head.user',
            'employees.user',
            'department.projects' => function ($query) use ($user): void {
                $query->whereIn(
                    'projects.id',
                    $user->projectsWithinScope('projects.view')->select('projects.id'),
                )->with('owner.user');
            },
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
        $code = 'DEPT-'.str_pad($candidateNum, 2, '0', STR_PAD_LEFT);

        while (Department::withTrashed()->where('code', $code)->exists()) {
            $candidateNum++;
            $code = 'DEPT-'.str_pad($candidateNum, 2, '0', STR_PAD_LEFT);
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
