<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\EmployeeRequest;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load('employee', 'roles');
        $employeeScope = $user->highestScopeFor('employees.view');
        $departmentScope = $user->highestScopeFor('departments.view');
        $projectScope = $user->highestScopeFor('projects.view');
        $taskScope = $user->highestScopeFor('tasks.view');
        $attendanceScope = $user->highestScopeFor('attendance.view');
        $performanceScope = $user->highestScopeFor('performance.view');
        $requestApprovalScope = $user->highestScopeFor('requests.approve');

        $welcomeName = $user->employee->full_name ?? $user->username;
        $primaryRoleName = $user->roles->first()?->display_name;
        $isEmployee = $employeeScope === 'self';

        $canViewEmployees = $employeeScope !== null;
        $canViewDepartments = $departmentScope !== null;
        $canViewProjects = $projectScope !== null;
        $canViewTasks = $taskScope !== null;
        $canViewAttendance = $attendanceScope !== null;
        $canViewPerformance = $performanceScope !== null;
        $canCreateProject = $user->hasPermission('projects.create') && $canViewProjects;

        $employeeQuery = $user->employeesWithinScope('employees.view', $employeeScope);
        $employeeCount = $canViewEmployees ? (clone $employeeQuery)->count() : 0;
        $departmentCount = $canViewDepartments
            ? $this->scopedDepartmentsQuery($user, $departmentScope)->count()
            : 0;

        $dashboardProjectScope = $isEmployee && $canViewProjects ? 'self' : $projectScope;
        $projectsQuery = $this->scopedProjectsQuery($user, $dashboardProjectScope);
        $projectCount = $canViewProjects ? (clone $projectsQuery)->count() : 0;
        $activeProjectsCount = $canViewProjects
            ? (clone $projectsQuery)->whereIn('status', ['in_progress', 'in_review'])->count()
            : 0;
        $activeProjects = $canViewProjects
            ? (clone $projectsQuery)
                ->with('department')
                ->whereIn('status', ['in_progress', 'in_review'])
                ->orderByDesc('completion_percentage')
                ->limit(3)
                ->get()
            : collect();

        $taskStages = [
            'Draft' => 'مسودة',
            'To-Do' => 'انتظار',
            'In Progress' => 'تنفيذ',
            'Under Review' => 'مراجعة',
            'Completed' => 'مكتمل',
        ];
        $dashboardTaskScope = $isEmployee && $canViewTasks ? 'self' : $taskScope;
        $tasksQuery = $this->scopedTasksQuery($user, $dashboardTaskScope);
        $taskCount = $canViewTasks ? (clone $tasksQuery)->count() : 0;
        $underReviewCount = $canViewTasks
            ? (clone $tasksQuery)->where('status', 'Under Review')->count()
            : 0;
        $taskStatusCounts = $canViewTasks
            ? (clone $tasksQuery)
                ->selectRaw('status, count(*) as total')
                ->whereIn('status', array_keys($taskStages))
                ->groupBy('status')
                ->pluck('total', 'status')
            : collect();

        $attendanceEmployeesQuery = $user->employeesWithinScope('attendance.view', $attendanceScope)
            ->where('employment_status', 'active');
        $presentTodayCount = $canViewAttendance
            ? $this->scopedAttendanceQuery($user, $attendanceScope)
                ->whereDate('date', today())
                ->where('status', 'Present')
                ->count()
            : 0;
        $activeAttendanceEmployeesCount = $canViewAttendance
            ? $attendanceEmployeesQuery->count()
            : 0;
        $attendanceRate = $activeAttendanceEmployeesCount > 0
            ? round(($presentTodayCount / $activeAttendanceEmployeesCount) * 100, 1)
            : 0;

        $recentActivities = AuditLog::with('user.employee')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->limit(4)
            ->get();

        $pendingRequestsCount = $this->pendingRequestsCount($user, $requestApprovalScope);
        $latestEvaluation = null;
        $todayAttendance = null;
        $presentThisMonthCount = 0;
        $lateThisMonthCount = 0;
        $myActiveTasksCount = 0;

        if ($isEmployee && $user->employee_id !== null) {
            $employeeId = $user->employee_id;

            if ($canViewTasks) {
                $myActiveTasksCount = (clone $tasksQuery)
                    ->whereIn('status', ['To-Do', 'In Progress', 'Under Review'])
                    ->count();
            }

            if ($canViewAttendance) {
                $attendanceThisMonth = AttendanceRecord::query()
                    ->where('employee_id', $employeeId)
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->get();
                $presentThisMonthCount = $attendanceThisMonth->whereIn('status', ['Present', 'Late'])->count();
                $lateThisMonthCount = $attendanceThisMonth->where('status', 'Late')->count();
                $todayAttendance = AttendanceRecord::query()
                    ->where('employee_id', $employeeId)
                    ->whereDate('date', today())
                    ->first();
            }

            if ($canViewPerformance) {
                $latestEvaluation = PerformanceEvaluation::query()
                    ->where('employee_id', $employeeId)
                    ->latest('evaluation_date')
                    ->first();
            }
        }

        return view('dashboard', compact(
            'welcomeName',
            'primaryRoleName',
            'employeeCount',
            'departmentCount',
            'projectCount',
            'activeProjectsCount',
            'taskCount',
            'underReviewCount',
            'attendanceRate',
            'presentTodayCount',
            'activeProjects',
            'taskStages',
            'taskStatusCounts',
            'recentActivities',
            'isEmployee',
            'latestEvaluation',
            'todayAttendance',
            'presentThisMonthCount',
            'lateThisMonthCount',
            'myActiveTasksCount',
            'pendingRequestsCount',
            'canViewEmployees',
            'canViewDepartments',
            'canViewProjects',
            'canViewTasks',
            'canViewAttendance',
            'canViewPerformance',
            'canCreateProject',
        ));
    }

    private function scopedDepartmentsQuery(User $user, ?string $scope): Builder
    {
        $query = Department::query();

        if ($scope === 'organization') {
            return $query;
        }

        if ($scope === null || $user->employee === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereKey($user->employee->department_id);
    }

    private function scopedProjectsQuery(User $user, ?string $scope): Builder
    {
        return $user->projectsWithinScope('projects.view', $scope);
    }

    private function scopedTasksQuery(User $user, ?string $scope): Builder
    {
        return $user->tasksWithinScope('tasks.view', $scope);
    }

    private function scopedAttendanceQuery(User $user, ?string $scope): Builder
    {
        return AttendanceRecord::query()->whereIn(
            'employee_id',
            $user->employeesWithinScope('attendance.view', $scope)->select('employees.id'),
        );
    }

    private function pendingRequestsCount(User $user, ?string $scope): int
    {
        if ($scope === null || $scope === 'self') {
            return 0;
        }

        $query = EmployeeRequest::query()->where('status', 'pending');

        if ($scope === 'organization') {
            return $query->count();
        }

        return $query->whereIn('employee_id', $user->employeesWithinScope('requests.approve')->select('employees.id'))->count();
    }
}
