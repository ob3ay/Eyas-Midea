<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('reports.view');

        $dates = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);
        $from = isset($dates['from']) ? Carbon::parse($dates['from'])->startOfDay() : now()->subDays(30)->startOfDay();
        $to = isset($dates['to']) ? Carbon::parse($dates['to'])->endOfDay() : now()->endOfDay();
        if ($from->greaterThan($to)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'to' => 'يجب أن يكون تاريخ النهاية مساوياً لتاريخ البداية أو بعده.',
            ]);
        }
        if ($from->diffInDays($to) > 365) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'to' => 'الفترة القصوى للتقرير سنة واحدة.',
            ]);
        }

        $employeesQuery = $user->employeesWithinScope('reports.view');
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

        $projectsQuery = $user->projectsWithinScope('reports.view');

        $projectsByStatus = (clone $projectsQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $averageProjectCompletion = round((clone $projectsQuery)->avg('completion_percentage') ?? 0, 1);

        $tasksQuery = $user->tasksWithinScope('reports.view');

        $tasksByStatus = (clone $tasksQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdueTasks = (clone $tasksQuery)
            ->whereNotNull('due_datetime')
            ->where('due_datetime', '<', now())
            ->where('status', '!=', 'Completed')
            ->count();

        $attendanceQuery = AttendanceRecord::query()
            ->whereBetween('date', [$from, $to])
            ->whereIn('employee_id', $user->employeesWithinScope('reports.view')->select('employees.id'));

        $attendanceByStatus = (clone $attendanceQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $workingDaysInRange = 0;
        for ($day = $from->copy()->startOfDay(); $day->lte($to); $day->addDay()) {
            if ($day->isWeekday()) {
                $workingDaysInRange++;
            }
        }
        $expectedAttendanceRecords = (clone $employeesQuery)->active()->count() * $workingDaysInRange;
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

}
