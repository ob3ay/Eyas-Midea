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
            'nextDeptCode' => 'DEPT-' . str_pad((Department::max('id') ?? 0) + 1, 2, '0', STR_PAD_LEFT),
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

        return view('departments.structure', compact(
            'departments',
            'totalSections',
            'totalEmployees',
            'assignedManagers',
            'ratio',
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

        if (! empty($data['manager_employee_id'])) {
            abort(422, 'أضف الدائرة أولاً ثم عيّن مديراً تابعاً لها.');
        }

        Department::create($data);

        return back()->with('success', 'تمت إضافة الدائرة بنجاح');
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $data = $request->validated();
        $this->assertManagerBelongsToDepartment($data['manager_employee_id'] ?? null, $department->id);

        $department->update($data);

        return back()->with('success', 'تم تحديث الدائرة');
    }

    public function destroy(Request $request, Department $department): RedirectResponse
    {
        Gate::authorize('delete', $department);

        if ($department->employees()->exists() || $department->sections()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذه الدائرة لوجود أقسام أو موظفين مرتبطين بها.');
        }

        $department->delete();

        return back()->with('success', 'تم حذف الدائرة بنجاح');
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

    private function assertManagerBelongsToDepartment(?int $employeeId, ?int $departmentId): void
    {
        if ($employeeId === null) {
            return;
        }

        $employee = Employee::findOrFail($employeeId);
        $expectedDepartmentId = $departmentId ?? request()->integer('department_id');

        if ($departmentId !== null && $employee->department_id !== $expectedDepartmentId) {
            abort(422, 'يجب أن يكون مدير الدائرة موظفاً تابعاً للدائرة نفسها.');
        }
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
