<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('reports.view');

        $from = $request->filled('from') ? $request->date('from') : now()->subDays(30)->startOfDay();
        $to = $request->filled('to') ? $request->date('to') : now()->endOfDay();

        $employeeScope = $this->scopedEmployeeIds($user, $scope);

        $employeesQuery = Employee::query()->when($employeeScope !== null, fn($q) => $q->whereIn('id', $employeeScope));

        $employeesByDepartment = (clone $employeesQuery)
            ->join('departments', 'departments.id', '=', 'employees.department_id')
            ->selectRaw('departments.name as department_name, count(*) as total')
            ->groupBy('departments.name')
            ->orderByDesc('total')
            ->get();

        $employeesByStatus = (clone $employeesQuery)
            ->selectRaw('employment_status, count(*) as total')
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status');

        $projectsQuery = Project::query();
        if ($scope === 'department') {
            $projectsQuery->where('department_id', $user->employee?->department_id);
        } elseif ($scope === 'self') {
            $projectsQuery->where('owner_employee_id', $user->employee?->id);
        }

        $projectsByStatus = (clone $projectsQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $averageProjectCompletion = round((clone $projectsQuery)->avg('completion_percentage') ?? 0, 1);

        $tasksQuery = Task::query();
        if ($scope === 'department') {
            $tasksQuery->whereHas('assignee', fn($q) => $q->where('department_id', $user->employee?->department_id));
        } elseif ($scope === 'self') {
            $tasksQuery->where('assignee_employee_id', $user->employee?->id);
        }

        $tasksByStatus = (clone $tasksQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdueTasks = (clone $tasksQuery)
            ->whereNotNull('due_datetime')
            ->where('due_datetime', '<', now())
            ->where('status', '!=', 'Completed')
            ->count();

        $attendanceQuery = AttendanceRecord::query()->whereBetween('date', [$from, $to]);
        if ($employeeScope !== null) {
            $attendanceQuery->whereIn('employee_id', $employeeScope);
        }

        $attendanceByStatus = (clone $attendanceQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $workingDaysInRange = max(1, $from->diffInWeekdays($to));
        $expectedAttendanceRecords = ($employeeScope !== null ? count($employeeScope) : Employee::active()->count()) * $workingDaysInRange;
        $attendanceRate = $expectedAttendanceRecords > 0
            ? round((($attendanceByStatus->get('Present', 0) + $attendanceByStatus->get('Late', 0)) / $expectedAttendanceRecords) * 100, 1)
            : 0;

        $topLateEmployees = collect();
        if ($scope !== 'self') {
            $topLateEmployees = (clone $attendanceQuery)
                ->where('status', 'Late')
                ->selectRaw('employee_id, count(*) as late_count, sum(late_minutes) as total_late_minutes')
                ->groupBy('employee_id')
                ->orderByDesc('late_count')
                ->with('employee')
                ->limit(5)
                ->get();
        }

        return view('reports.index', compact(
            'scope',
            'from',
            'to',
            'employeesByDepartment',
            'employeesByStatus',
            'projectsByStatus',
            'averageProjectCompletion',
            'tasksByStatus',
            'overdueTasks',
            'attendanceByStatus',
            'attendanceRate',
            'topLateEmployees',
        ));
    }

    private function authorizeScope(string $permissionKey): string
    {
        $user = auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ما عندك صلاحية الاطلاع على التقارير.');
        }

        return $scope;
    }

    private function scopedEmployeeIds(User $user, string $scope): ?array
    {
        if ($scope === 'organization') {
            return null;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            return [];
        }

        if ($scope === 'self') {
            return [$ownEmployee->id];
        }

        if ($user->hasRole('SH')) {
            return Employee::where('section_id', $ownEmployee->section_id)->pluck('id')->all();
        }

        return Employee::where('department_id', $ownEmployee->department_id)->pluck('id')->all();
    }
}
