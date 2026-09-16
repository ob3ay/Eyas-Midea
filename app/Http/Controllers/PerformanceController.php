<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PerformanceEvaluation;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('performance.view');

        $employees = Employee::active()
            ->when($scope === 'self', fn($q) => $q->where('id', $user->employee?->id ?? 0))
            ->when($scope === 'department', fn($q) => $user->hasRole('SH')
                ? $q->where('section_id', $user->employee?->section_id)
                : $q->where('department_id', $user->employee?->department_id))
            ->with(['department', 'section'])
            ->orderBy('full_name')
            ->get();

        $rows = $employees->map(function (Employee $employee) {
            $completedTasks = Task::where('assignee_employee_id', $employee->id)
                ->where('status', 'Completed')
                ->count();

            $totalTasksWithDueDate = Task::where('assignee_employee_id', $employee->id)
                ->whereNotNull('due_datetime')
                ->where('status', 'Completed')
                ->count();

            $onTimeTasks = Task::where('assignee_employee_id', $employee->id)
                ->whereNotNull('due_datetime')
                ->where('status', 'Completed')
                ->whereColumn('completed_at', '<=', 'due_datetime')
                ->count();

            $onTimeRate = $totalTasksWithDueDate > 0 ? round(($onTimeTasks / $totalTasksWithDueDate) * 100, 1) : null;

            $latestEvaluation = PerformanceEvaluation::where('employee_id', $employee->id)
                ->with('evaluator')
                ->latest('evaluation_date')
                ->first();

            return (object) [
                'employee' => $employee,
                'completed_tasks' => $completedTasks,
                'on_time_rate' => $onTimeRate,
                'evaluation' => $latestEvaluation,
            ];
        });

        $finalizedScores = PerformanceEvaluation::whereIn('employee_id', $employees->pluck('id'))
            ->where('is_finalized', true)
            ->pluck('score');

        $overallScore = $finalizedScores->isNotEmpty() ? round($finalizedScores->avg(), 1) : null;

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
        $this->assertWithinScope($user, $scope, $employee);

        $evaluation = PerformanceEvaluation::create([
            'employee_id' => $employee->id,
            'evaluator_user_id' => $user->id,
            'evaluation_date' => $data['evaluation_date'],
            'period' => $data['period'],
            'score' => $data['score'],
            'notes' => $data['notes'] ?? null,
            'is_finalized' => true,
        ]);
        $notifications->createForEmployee(
            $evaluation->employee_id,
            'performance_evaluated',
            'تم تسجيل تقييم أداء',
            'تم تسجيل تقييم أداء جديد لك.',
            $evaluation,
        );

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

    private function assertWithinScope(User $user, string $scope, Employee $employee): void
    {
        if ($scope === 'organization') {
            return;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            abort(403, 'حسابك غير مرتبط بسجل موظف.');
        }

        if ($scope === 'self' && $employee->id !== $ownEmployee->id) {
            abort(403, 'غير مصرح لك بإجراء تقييم خارج نطاق الصلاحيات الممنوحة لك.');
        }

        if ($scope === 'department') {
            $boundaryField = $user->hasRole('SH') ? 'section_id' : 'department_id';
            if ($employee->{$boundaryField} !== $ownEmployee->{$boundaryField}) {
                abort(403, 'لا تملك صلاحية تقييم سوى الموظفين التابعين لنطاقك الإداري.');
            }
        }
    }
}
