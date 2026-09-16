<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    private const STATUSES = ['Draft', 'To-Do', 'In Progress', 'Under Review', 'Completed', 'Rejected'];

    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('tasks.view');

        $query = $this->scopedTasksQuery($user, $scope)
            ->with(['project', 'assignee', 'creator', 'parentTask', 'subtasks']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('assignee_employee_id')) {
            $query->where('assignee_employee_id', $request->assignee_employee_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query
            ->orderByRaw("CASE priority WHEN 'Urgent' THEN 1 WHEN 'High' THEN 2 WHEN 'Medium' THEN 3 WHEN 'Low' THEN 4 ELSE 5 END")
            ->latest()
            ->get();

        $projects = $scope === 'organization'
            ? Project::orderBy('title')->get()
            : Project::where('department_id', $user->employee?->department_id ?? 0)->orderBy('title')->get();

        $employees = Employee::active()
            ->when($scope !== 'organization', fn($q) => $q->where('department_id', $user->employee?->department_id ?? 0))
            ->orderBy('full_name')
            ->get();

        $stats = [
            'total' => $tasks->count(),
            'draft' => $tasks->where('status', 'Draft')->count(),
            'todo' => $tasks->where('status', 'To-Do')->count(),
            'in_progress' => $tasks->where('status', 'In Progress')->count(),
            'under_review' => $tasks->where('status', 'Under Review')->count(),
            'completed' => $tasks->where('status', 'Completed')->count(),
            'rejected' => $tasks->where('status', 'Rejected')->count(),
            'overdue' => $tasks->filter(fn($t) => $t->due_datetime && $t->due_datetime->isPast() && $t->status !== 'Completed')->count(),
        ];

        $averageProgress = $tasks->avg('completion_percentage') ?? 0;

        $canCreate = $user->hasPermission('tasks.create');
        $canUpdate = $user->hasPermission('tasks.update');
        $canDelete = $user->hasPermission('tasks.delete');

        return view('tasks.index', compact(
            'tasks',
            'projects',
            'employees',
            'stats',
            'averageProgress',
            'canCreate',
            'canUpdate',
            'canDelete',
        ));
    }

    public function store(Request $request, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('tasks.create');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'parent_task_id' => ['nullable', 'exists:tasks,id'],
            'assignee_employee_id' => ['required', 'exists:employees,id'],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Urgent'])],
            'due_datetime' => ['nullable', 'date'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $assignee = Employee::findOrFail($validated['assignee_employee_id']);
        $this->assertWithinScope($user, $scope, $assignee);

        $creatorEmployeeId = $user->employee?->id;
        if (! $creatorEmployeeId) {
            return back()->withInput()->withErrors(['created_by' => 'لا يمكن تحديد الموظف الذي أنشأ المهمة.']);
        }
        $validated['created_by'] = $creatorEmployeeId;

        $validated['completion_percentage'] = $validated['completion_percentage'] ?? 0;

        if ($validated['status'] === 'Completed') {
            $validated['completion_percentage'] = 100;
            $validated['completed_at'] = now();
        }

        $task = Task::create($validated);
        $notifications->createForEmployee(
            $task->assignee_employee_id,
            'task_assigned',
            'تم إسناد مهمة جديدة',
            "تم إسناد المهمة «{$task->title}» إليك.",
            $task,
        );

        return redirect()->route('tasks.index')->with('success', 'تم إنشاء المهمة بنجاح');
    }

    public function update(Request $request, Task $task, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('tasks.update', $task, $user);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'parent_task_id' => ['nullable', 'exists:tasks,id'],
            'assignee_employee_id' => ['required', 'exists:employees,id'],
            'priority' => ['required', Rule::in(['Low', 'Medium', 'High', 'Urgent'])],
            'due_datetime' => ['nullable', 'date'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'completion_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $assignee = Employee::findOrFail($validated['assignee_employee_id']);
        $this->assertWithinScope($user, $scope, $assignee);

        if (! empty($validated['parent_task_id']) && (int) $validated['parent_task_id'] === (int) $task->id) {
            return back()->withInput()->withErrors(['parent_task_id' => 'لا يمكن أن تكون المهمة الرئيسية هي نفس المهمة.']);
        }

        if ($validated['status'] === 'Completed') {
            $validated['completion_percentage'] = 100;
            if (! $task->completed_at) {
                $validated['completed_at'] = now();
            }
        } else {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        $notifications->createForEmployee(
            $task->assignee_employee_id,
            'task_updated',
            'تم تحديث مهمة',
            "تم تحديث المهمة «{$task->title}».",
            $task,
        );

        return redirect()->route('tasks.index')->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function updateState(Request $request, Task $task, NotificationService $notifications): JsonResponse
    {
        $user = $request->user();
        $this->authorizeScope('tasks.update', $task, $user);

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $status = $validated['status'];
        $progress = $validated['completion_percentage'] ?? $task->completion_percentage;

        if ($status === 'Completed') {
            $progress = 100;
        }

        $completedAt = $status === 'Completed' ? ($task->completed_at ?? now()) : null;

        $task->update([
            'status' => $status,
            'completion_percentage' => $progress,
            'completed_at' => $completedAt,
        ]);
        $notifications->createForEmployee(
            $task->assignee_employee_id,
            'task_status_changed',
            'تغيرت حالة مهمة',
            "تغيرت حالة المهمة «{$task->title}» إلى {$task->status}.",
            $task,
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المهمة بنجاح',
            'task' => [
                'id' => $task->id,
                'status' => $task->status,
                'completion_percentage' => (float) $task->completion_percentage,
                'completed_at' => $task->completed_at?->toISOString(),
            ],
        ]);
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('tasks.delete', $task, $user);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }

    private function authorizeScope(string $permissionKey, ?Task $task = null, ?User $user = null): string
    {
        $user ??= auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        if ($task !== null && $scope !== 'organization') {
            $ownEmployee = $user->employee;
            if (! $ownEmployee) {
                abort(403, 'حسابك غير مرتبط بسجل موظف.');
            }

            if ($scope === 'self' && $task->assignee_employee_id !== $ownEmployee->id) {
                abort(403, 'لا تملك صلاحية إدارة سوى المهام المسندة إليك.');
            }

            if ($scope === 'department') {
                $assigneeDept = $task->assignee?->department_id;
                if ($assigneeDept !== $ownEmployee->department_id) {
                    abort(403, 'لا تملك صلاحية إدارة سوى مهام موظفي دائرتك.');
                }
            }
        }

        return $scope;
    }

    private function assertWithinScope(User $user, string $scope, Employee $assignee): void
    {
        if ($scope === 'organization') {
            return;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            abort(403, 'حسابك غير مرتبط بسجل موظف.');
        }

        if ($scope === 'self' && $assignee->id !== $ownEmployee->id) {
            abort(403, 'لا تملك صلاحية إنشاء أو تعديل مهام إلا لنفسك.');
        }

        if ($scope === 'department' && $assignee->department_id !== $ownEmployee->department_id) {
            abort(403, 'لا تملك صلاحية إسناد مهام إلا لموظفي دائرتك.');
        }
    }

    private function scopedTasksQuery(User $user, string $scope)
    {
        $query = Task::query();

        if ($scope === 'organization') {
            return $query;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            return $query->whereRaw('1 = 0');
        }

        if ($scope === 'self') {
            return $query->where('assignee_employee_id', $ownEmployee->id);
        }

        return $query->whereHas('assignee', fn($q) => $q->where('department_id', $ownEmployee->department_id));
    }
}
