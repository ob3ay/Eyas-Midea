<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\PerformanceEvaluation;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $scope = $this->authorizeScope('employees.view');

        $employeeQuery = $user->employeesWithinScope('employees.view', $scope);
        $search = $request->string('search')->trim()->toString();
        $departmentId = $request->integer('department_id');
        $employmentStatus = $request->string('employment_status')->toString();

        $filteredQuery = (clone $employeeQuery)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
                });
            })
            ->when($departmentId > 0, fn ($query) => $query->where('department_id', $departmentId))
            ->when(
                in_array($employmentStatus, ['active', 'inactive', 'on_leave'], true),
                fn ($query) => $query->where('employment_status', $employmentStatus)
            );

        $employees = $filteredQuery
            ->with(['department', 'section', 'user'])
            ->orderByDesc('hire_date')
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();

        $totalCount = (clone $employeeQuery)->count();
        $activeCount = (clone $employeeQuery)->where('employment_status', 'active')->count();
        $onLeaveCount = (clone $employeeQuery)->where('employment_status', 'on_leave')->count();
        $departmentCount = (clone $employeeQuery)->whereNotNull('department_id')->distinct('department_id')->count('department_id');

        $nextEmployeeCode = 'EMP-'.str_pad((Employee::withTrashed()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        $departments = $scope === 'organization'
            ? Department::with('sections')->orderBy('name')->get()
            : Department::with('sections')
                ->whereKey($user->employee?->department_id)
                ->orderBy('name')
                ->get();

        $canCreate = $user->hasPermission('employees.create');
        $canUpdate = $user->hasPermission('employees.update');
        $canDelete = $user->hasPermission('employees.delete');

        return view('employees.index', compact(
            'employees',
            'totalCount',
            'activeCount',
            'onLeaveCount',
            'departmentCount',
            'nextEmployeeCode',
            'departments',
            'canCreate',
            'canUpdate',
            'canDelete',
            'search',
            'departmentId',
            'employmentStatus',
        ));
    }

    public function show(Request $request, Employee $employee)
    {
        $viewer = $request->user();
        $this->authorizeScope('employees.view', $employee, $viewer);
        $canViewTasks = $viewer->allowsEmployeeWithinScope('tasks.view', $employee);
        $canViewProjects = $viewer->allowsEmployeeWithinScope('projects.view', $employee);
        $canViewAttendance = $viewer->allowsEmployeeWithinScope('attendance.view', $employee);
        $canViewPerformance = $viewer->allowsEmployeeWithinScope('performance.view', $employee);
        $canViewAuditLogs = $viewer->allowsEmployeeWithinScope('audit_logs.view', $employee);
        /*
    |--------------------------------------------------------------------------
    | بيانات الموظف الأساسية
    |--------------------------------------------------------------------------
    */
        $employee->load([
            'department',
            'section',
            'manager',
            'subordinates',
            'user.roles',
        ]);

        /*
    |--------------------------------------------------------------------------
    | المهام المسندة للموظف
    |--------------------------------------------------------------------------
    */
        $tasks = $canViewTasks
            ? Task::with(['project', 'assignee', 'comments', 'attachments', 'statusHistory'])
                ->withCount(['comments', 'attachments'])
                ->where('assignee_employee_id', $employee->id)
                ->latest()
                ->get()
            : collect();

        /*
    |--------------------------------------------------------------------------
    | إحصائيات المهام
    |--------------------------------------------------------------------------
    */
        $taskTotal = $tasks->count();

        $taskCompleted = $tasks
            ->where('status', 'Completed')
            ->count();

        $taskInProgress = $tasks
            ->where('status', 'In Progress')
            ->count();

        $taskUnderReview = $tasks
            ->where('status', 'Under Review')
            ->count();

        $taskTodo = $tasks
            ->where('status', 'To-Do')
            ->count();

        /*
    |--------------------------------------------------------------------------
    | المشاريع التي الموظف عضو فيها
    |--------------------------------------------------------------------------
    */
        $projects = $canViewProjects
            ? Project::with(['department', 'owner'])
                ->whereHas('members', fn ($query) => $query->where('employees.id', $employee->id))
                ->latest()
                ->get()
            : collect();

        /*
    |--------------------------------------------------------------------------
    | المشاريع التي الموظف مسؤول عنها
    |--------------------------------------------------------------------------
    */
        $managedProjects = $canViewProjects
            ? Project::with('department')
                ->where('owner_employee_id', $employee->id)
                ->latest()
                ->get()
            : collect();

        /*
    |--------------------------------------------------------------------------
    | الحضور والانصراف
    |--------------------------------------------------------------------------
    */
        $attendanceRecords = $canViewAttendance
            ? AttendanceRecord::where('employee_id', $employee->id)
                ->latest('date')->latest('id')->limit(30)->get()
            : collect();

        $attendanceTotal = $canViewAttendance ? AttendanceRecord::where('employee_id', $employee->id)->count() : 0;

        $attendancePresent = $canViewAttendance ? AttendanceRecord::where('employee_id', $employee->id)->where('status', 'Present')->count() : 0;

        $attendanceAbsent = $canViewAttendance ? AttendanceRecord::where('employee_id', $employee->id)->where('status', 'Absent')->count() : 0;

        $attendanceLate = $canViewAttendance ? AttendanceRecord::where('employee_id', $employee->id)->where('status', 'Late')->count() : 0;

        $attendanceLeave = $canViewAttendance ? AttendanceRecord::where('employee_id', $employee->id)->where('status', 'Leave')->count() : 0;

        /*
    |--------------------------------------------------------------------------
    | طلبات تصحيح الحضور
    |--------------------------------------------------------------------------
    */
        $correctionRequests = $canViewAttendance
            ? AttendanceCorrectionRequest::with('attendanceRecord')
                ->whereHas('attendanceRecord', fn ($query) => $query->where('employee_id', $employee->id))
                ->latest()->limit(20)->get()
            : collect();

        /*
    |--------------------------------------------------------------------------
    | تقييمات الأداء
    |--------------------------------------------------------------------------
    */
        $evaluations = $canViewPerformance
            ? PerformanceEvaluation::with('evaluator')->where('employee_id', $employee->id)
                ->latest('evaluation_date')->latest('id')->get()
            : collect();

        $averageScore = $evaluations->avg('score');

        /*
    |--------------------------------------------------------------------------
    | الإشعارات الخاصة بحساب الموظف
    |--------------------------------------------------------------------------
    */
        $notifications = collect();

        if ($employee->user && $viewer->is($employee->user)) {
            $notifications = $employee->user
                ->notifications()
                ->latest()
                ->limit(15)
                ->get();
        }

        /*
    |--------------------------------------------------------------------------
    | سجل العمليات Audit
    |--------------------------------------------------------------------------
    */
        $auditLogs = $canViewAuditLogs
            ? AuditLog::with('user')->where('entity_type', 'Employee')->where('entity_id', $employee->id)
                ->latest()->limit(30)->get()
            : collect();

        /*
    |--------------------------------------------------------------------------
    | إحصائيات عامة
    |--------------------------------------------------------------------------
    */
        $totalWorkMinutes = $attendanceRecords->sum(function ($record) {
            return (int) ($record->work_minutes ?? 0);
        });

        $totalLateMinutes = $attendanceRecords->sum(function ($record) {
            return (int) ($record->late_minutes ?? 0);
        });

        return view('employees.show', compact(
            'employee',

            'canViewTasks',
            'canViewProjects',
            'canViewAttendance',
            'canViewPerformance',
            'canViewAuditLogs',

            'tasks',
            'taskTotal',
            'taskCompleted',
            'taskInProgress',
            'taskUnderReview',
            'taskTodo',

            'projects',
            'managedProjects',

            'attendanceRecords',
            'attendanceTotal',
            'attendancePresent',
            'attendanceAbsent',
            'attendanceLate',
            'attendanceLeave',
            'totalWorkMinutes',
            'totalLateMinutes',

            'correctionRequests',

            'evaluations',
            'averageScore',

            'notifications',

            'auditLogs',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('employees.create');
        if (! in_array($scope, ['organization', 'department', 'section'], true)) {
            abort(403, 'نطاق صلاحية إنشاء الموظفين غير صالح لهذه العملية.');
        }

        $data = $request->validate([
            'emp_code' => ['nullable', 'string', 'max:30', 'unique:employees,employee_code'],
            'national_id' => ['required', 'string', 'max:20', 'unique:employees,national_id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:25'],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
            'section_id' => [
                'nullable',
                'integer',
                Rule::exists('sections', 'id')->where(
                    fn ($query) => $query->where('department_id', $request->integer('department_id'))
                ),
            ],
            'job_title' => ['required', 'string', 'max:100'],
            'hire_date' => ['required', 'date'],
        ]);

        if (in_array($scope, ['department', 'section'], true)
            && (int) $data['department_id'] !== (int) $user->employee?->department_id) {
            abort(403, 'لا تملك صلاحية إضافة موظفين إلا ضمن دائرتك.');
        }

        if ($scope === 'section') {
            $sectionId = $user->employee?->section_id;
            if ($sectionId === null || (int) ($data['section_id'] ?? 0) !== (int) $sectionId) {
                abort(403, 'لا تملك صلاحية إضافة موظفين إلا ضمن قسمك.');
            }
        }

        $username = $this->generateUniqueUsername($data['first_name'], $data['last_name']);
        $temporaryPassword = Str::random(40);

        $employee = DB::transaction(function () use ($data, $username, $temporaryPassword) {
            $employee = Employee::create([
                'employee_code' => $data['emp_code'] ?? null,
                'national_id' => $data['national_id'],
                'full_name' => trim($data['first_name'].' '.$data['last_name']),
                'phone' => $data['phone'],
                'department_id' => $data['department_id'],
                'section_id' => $data['section_id'] ?? null,
                'position' => $data['job_title'],
                'hire_date' => $data['hire_date'],
                'employment_status' => 'active',
            ]);

            User::create([
                'employee_id' => $employee->id,
                'username' => $username,
                'email' => $data['email'],
                'password' => Hash::make($temporaryPassword),
                'must_change_password' => true,
                'is_active' => true,
            ]);

            AuditLog::record('Create', 'Employee', $employee->id);

            return $employee;
        });

        return back()->with('success', "تم حفظ بيانات الموظف بنجاح. بيانات الدخول: {$username} / كلمة المرور: {$temporaryPassword}");
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('employees.update', $employee, $user);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:200'],
            'position' => ['nullable', 'string', 'max:100'],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
            'section_id' => [
                'nullable',
                'integer',
                Rule::exists('sections', 'id')->where(
                    fn ($query) => $query->where('department_id', $request->integer('department_id'))
                ),
            ],
            'phone' => ['nullable', 'string', 'max:25'],
            'employment_status' => ['required', Rule::in(['active', 'inactive', 'on_leave'])],
        ]);

        if ($scope === 'self') {
            // Self-service profile edits must never change the employee's authority anchor.
            $data = array_intersect_key($data, array_flip(['full_name', 'phone']));
        }

        if ($this->isDepartmentOutsideScope($user, $data['department_id'])) {
            abort(403, 'لا يمكنك إدارة موظفين خارج نطاق دائرتك.');
        }

        if ($user->highestScopeFor('employees.update') === 'section') {
            $sectionId = $user->employee?->section_id;
            if ($sectionId === null || (int) ($data['section_id'] ?? 0) !== (int) $sectionId) {
                abort(403, 'لا يمكنك نقل الموظفين خارج قسمك.');
            }
        }

        DB::transaction(function () use ($employee, $data): void {
            $lockedEmployee = Employee::query()->lockForUpdate()->findOrFail($employee->id);
            $lockedEmployee->update($data);
            AuditLog::record('Update', 'Employee', $lockedEmployee->id);
        });

        return back()->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('employees.delete', $employee, $user);

        $deleted = DB::transaction(function () use ($employee): bool {
            $lockedEmployee = Employee::query()->lockForUpdate()->findOrFail($employee->id);
            if ($lockedEmployee->user()->lockForUpdate()->exists()) {
                return false;
            }

            $lockedEmployee->delete();
            AuditLog::record('Delete', 'Employee', $lockedEmployee->id);

            return true;
        });

        if (! $deleted) {
            return back()->with('error', 'لا يمكن حذف هذا الموظف لوجود حساب مستخدم مرتبط به، يرجى تعطيل الحساب أولاً.');
        }

        return back()->with('success', 'تم حذف الموظف بنجاح');
    }

    private function authorizeScope(string $permissionKey, ?Employee $target = null, ?User $user = null): string
    {
        $user ??= auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        if ($target !== null && ! $user->allowsEmployeeWithinScope($permissionKey, $target)) {
            abort(403, 'لا تملك صلاحية استعراض أو تعديل هذا الموظف خارج نطاقك.');
        }

        return $scope;
    }

    private function isDepartmentOutsideScope(User $user, int $departmentId): bool
    {
        $scope = $user->highestScopeFor('employees.update');

        if ($scope === 'organization') {
            return false;
        }

        return in_array($scope, ['department', 'section'], true)
            && (int) $user->employee?->department_id !== $departmentId;
    }

    private function generateUniqueUsername(string $firstName, string $lastName): string
    {
        $base = Str::slug($firstName.'.'.$lastName, '.');
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$counter;
            $counter++;
        }

        return $username;
    }
}
