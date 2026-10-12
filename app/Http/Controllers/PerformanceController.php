<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('performance.view');

        $employeesQuery = $user->employeesWithinScope('performance.view')->active();

        $employees = $employeesQuery
            ->with(['department', 'section', 'latestEvaluation.evaluator'])
            ->withCount([
                'assignedTasks as completed_tasks_count' => fn (Builder $query) => $query->where('status', 'Completed'),
                'assignedTasks as completed_tasks_with_due_date_count' => fn (Builder $query) => $query
                    ->where('status', 'Completed')
                    ->whereNotNull('due_datetime'),
                'assignedTasks as completed_on_time_count' => fn (Builder $query) => $query
                    ->where('status', 'Completed')
                    ->whereNotNull('due_datetime')
                    ->whereColumn('completed_at', '<=', 'due_datetime'),
            ])
            ->orderBy('full_name')
            ->get();

        $rows = $employees->map(function (Employee $employee) {
            $totalTasksWithDueDate = $employee->completed_tasks_with_due_date_count;
            $onTimeRate = $totalTasksWithDueDate > 0
                ? round(($employee->completed_on_time_count / $totalTasksWithDueDate) * 100, 1)
                : null;

            return (object) [
                'employee' => $employee,
                'completed_tasks' => $employee->completed_tasks_count,
                'on_time_rate' => $onTimeRate,
                'evaluation' => $employee->latestEvaluation,
            ];
        });

        $overallScore = PerformanceEvaluation::whereIn('employee_id', $employees->modelKeys())
            ->where('is_finalized', true)
            ->avg('score');
        $overallScore = $overallScore === null ? null : round((float) $overallScore, 1);

        $canCreate = $user->hasPermission('performance.create');

        return view('performance.index', compact('rows', 'employees', 'overallScore', 'canCreate'));
    }

    public function evaluate(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('performance.create');

        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'evaluation_date' => ['required', 'date'],
            'period' => ['required', 'string', 'max:50'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $employee = Employee::findOrFail($data['employee_id']);
        $this->assertWithinScope($user, 'performance.create', $employee);

        DB::transaction(function () use ($employee, $user, $data, $notifications): void {
            $evaluation = PerformanceEvaluation::create([
                'employee_id' => $employee->id,
                'evaluator_user_id' => $user->id,
                'evaluation_date' => $data['evaluation_date'],
                'period' => $data['period'],
                'score' => $data['score'],
                'notes' => $data['notes'] ?? null,
                'is_finalized' => true,
            ]);
            AuditLog::record('Create', 'PerformanceEvaluation', $evaluation->id);
            $notifications->createForEmployee($evaluation->employee_id, 'performance_evaluated', 'تم تسجيل تقييم أداء',
                'تم تسجيل تقييم أداء جديد لك.', $evaluation);
        });

        return back()->with('success', "تم اعتماد تقييم أداء {$employee->full_name} بنجاح");
    }

    private function authorizeScope(string $permissionKey): string
    {
        $user = auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        return $scope;
    }

    private function assertWithinScope(User $user, string $permissionKey, Employee $employee): void
    {
        if (! $user->allowsEmployeeWithinScope($permissionKey, $employee)) {
            abort(403, 'لا تملك صلاحية تقييم هذا الموظف خارج نطاقك الإداري.');
        }
    }
}
