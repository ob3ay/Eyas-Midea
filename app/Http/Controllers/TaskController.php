<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    private const STATUSES = ['Draft', 'To-Do', 'In Progress', 'Under Review', 'Completed', 'Rejected'];

    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('tasks.view');
        $projectScope = $user->highestScopeFor('projects.view');
        $query = $this->scopedTasksQuery($user, $scope)
            ->with([
                'project' => fn ($query) => $projectScope === null
                    ? $query->whereRaw('1 = 0')
                    : $this->constrainProjectQuery($query, $user, $projectScope)->select(['projects.id', 'projects.title']),
                'assignee:id,full_name',
            ])
            ->select([
                'id', 'title', 'description', 'project_id', 'parent_task_id',
                'assignee_employee_id', 'priority', 'due_datetime', 'status', 'completion_percentage', 'created_at',
            ]);

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

        $projects = $projectScope !== null
            ? $this->scopedProjectsQuery($user, $projectScope)->orderBy('title')->get(['projects.id', 'projects.title'])
            : collect();

        $assignmentPermission = $this->widestPermission($user, ['tasks.create', 'tasks.update']);
        $employees = $assignmentPermission !== null
            ? $user->employeesWithinScope($assignmentPermission)->active()->orderBy('full_name')->get(['id', 'full_name', 'employee_code', 'department_id'])
            : collect();

        $stats = [
            'total' => $tasks->count(),
            'draft' => $tasks->where('status', 'Draft')->count(),
            'todo' => $tasks->where('status', 'To-Do')->count(),
            'in_progress' => $tasks->where('status', 'In Progress')->count(),
            'under_review' => $tasks->where('status', 'Under Review')->count(),
            'completed' => $tasks->where('status', 'Completed')->count(),
            'rejected' => $tasks->where('status', 'Rejected')->count(),
            'overdue' => $tasks->filter(fn ($task) => $task->due_datetime
                && $task->due_datetime->isPast()
                && $task->status !== 'Completed')->count(),
        ];

        $averageProgress = $tasks->avg('completion_percentage') ?? 0;

        $canCreate = $user->hasPermission('tasks.create') && ! $user->hasRole('SA');
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

        if ($user->hasRole('SA')) {
            abort(403, 'مهام المشاريع تُضاف وتُدار بواسطة مدير الدائرة المسند إليه المشروع.');
        }

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
        $this->assertWithinScope($user, 'tasks.create', $assignee);
        $this->assertTaskRelationships($validated, $assignee);

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

        $this->assertTaskStatusAllowed($user, $validated['status']);

        DB::transaction(function () use ($validated, $notifications, $creatorEmployeeId): void {
            $task = Task::create($validated);
            TaskStatusHistory::create([
                'task_id' => $task->id,
                'old_status' => $task->status,
                'new_status' => $task->status,
                'notes' => 'Initial status',
                'changed_by' => $creatorEmployeeId,
            ]);
            AuditLog::record('Create', 'Task', $task->id);
            $notifications->createForEmployee($task->assignee_employee_id, 'task_assigned', 'تم إسناد مهمة جديدة',
                "تم إسناد المهمة «{$task->title}» إليك.", $task);
        });

        return redirect()->route('tasks.index')->with('success', 'تم إنشاء المهمة بنجاح');
    }

    public function update(Request $request, Task $task, NotificationService $notifications): RedirectResponse
    {
        $user = $request->user();
        $this->assertTaskManagementAllowed($user);
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
        $this->assertWithinScope($user, 'tasks.update', $assignee);
        $this->assertTaskRelationships($validated, $assignee, $task);

        if ($validated['status'] === 'Completed') {
            $validated['completion_percentage'] = 100;
            if (! $task->completed_at) {
                $validated['completed_at'] = now();
            }
        } else {
            $validated['completed_at'] = null;
        }

        $this->assertTaskStatusAllowed($user, $validated['status']);

        DB::transaction(function () use ($task, $validated, $notifications, $user): void {
            $lockedTask = Task::query()->lockForUpdate()->with('assignee')->findOrFail($task->id);
            $this->authorizeScope('tasks.update', $lockedTask, $user);
            $this->assertTaskStatusAllowed($user, $validated['status']);
            $oldStatus = $lockedTask->status;
            $lockedTask->update($validated);
            if ($oldStatus !== $lockedTask->status && $user->employee_id !== null) {
                TaskStatusHistory::create([
                    'task_id' => $lockedTask->id,
                    'old_status' => $oldStatus,
                    'new_status' => $lockedTask->status,
                    'changed_by' => $user->employee_id,
                ]);
            }
            AuditLog::record('Update', 'Task', $lockedTask->id, ['status' => $oldStatus], ['status' => $lockedTask->status]);
            $notifications->createForEmployee($lockedTask->assignee_employee_id, 'task_updated', 'تم تحديث مهمة',
                "تم تحديث المهمة «{$lockedTask->title}».", $lockedTask);
        });

        return redirect()->route('tasks.index')->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function updateState(Request $request, Task $task, NotificationService $notifications): JsonResponse
    {
        $user = $request->user();

        $this->assertTaskManagementAllowed($user);

        $this->authorizeScope('tasks.update', $task, $user);

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $status = $validated['status'];
        $this->assertTaskStatusAllowed($user, $status);
        $task = DB::transaction(function () use ($task, $status, $validated, $user, $notifications): Task {
            $lockedTask = Task::query()->lockForUpdate()->with('assignee')->findOrFail($task->id);
            $this->authorizeScope('tasks.update', $lockedTask, $user);
            $progress = $status === 'Completed'
                ? 100
                : ($validated['completion_percentage'] ?? $lockedTask->completion_percentage);
            $completedAt = $status === 'Completed' ? ($lockedTask->completed_at ?? now()) : null;
            $oldStatus = $lockedTask->status;
            $lockedTask->update([
                'status' => $status,
                'completion_percentage' => $progress,
                'completed_at' => $completedAt,
            ]);
            if ($oldStatus !== $lockedTask->status && $user->employee_id !== null) {
                TaskStatusHistory::create([
                    'task_id' => $lockedTask->id,
                    'old_status' => $oldStatus,
                    'new_status' => $lockedTask->status,
                    'changed_by' => $user->employee_id,
                ]);
            }
            AuditLog::record('UpdateState', 'Task', $lockedTask->id, ['status' => $oldStatus], ['status' => $lockedTask->status]);
            $notifications->createForEmployee($lockedTask->assignee_employee_id, 'task_status_changed', 'تغيرت حالة مهمة',
                "تغيرت حالة المهمة «{$lockedTask->title}» إلى {$lockedTask->status}.", $lockedTask);

            return $lockedTask;
        });

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
        $this->assertTaskManagementAllowed($user);
        $this->authorizeScope('tasks.delete', $task, $user);

        DB::transaction(function () use ($task, $user): void {
            $lockedTask = Task::query()->lockForUpdate()->with('assignee')->findOrFail($task->id);
            $this->authorizeScope('tasks.delete', $lockedTask, $user);
            $lockedTask->delete();
            AuditLog::record('Delete', 'Task', $lockedTask->id);
        });

        return redirect()->route('tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }

    private function authorizeScope(string $permissionKey, ?Task $task = null, ?User $user = null): string
    {
        $user ??= auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        if ($task !== null && ! $user->allowsTaskWithinScope($permissionKey, $task)) {
            abort(403, 'لا تملك صلاحية إدارة هذه المهمة خارج نطاقك.');
        }

        return $scope;
    }

    private function assertTaskManagementAllowed(User $user): void
    {
        abort_if($user->hasRole('SA'), 403,
            'دور مدير النظام الأعلى مخصص لمتابعة المهام والرقابة، وإدارتها من مسؤول الدائرة أو القسم.');
    }

    private function assertTaskStatusAllowed(User $user, string $status): void
    {
        if ($user->highestScopeFor('tasks.update') === 'self' && in_array($status, ['Completed', 'Rejected'], true)) {
            abort(403, 'يمكنك إرسال المهمة للمراجعة، ويعتمد المسؤول إكمالها أو رفضها.');
        }
    }

    private function assertWithinScope(User $user, string $permissionKey, Employee $assignee): void
    {
        if (! $user->allowsEmployeeWithinScope($permissionKey, $assignee)) {
            abort(403, 'لا تملك صلاحية إسناد المهمة إلى موظف خارج نطاقك.');
        }
    }

    private function scopedTasksQuery(User $user, string $scope)
    {
        return $user->tasksWithinScope('tasks.view', $scope);
    }

    private function scopedProjectsQuery(User $user, string $scope)
    {
        return $user->projectsWithinScope('projects.view', $scope);
    }

    private function constrainProjectQuery($query, User $user, string $scope)
    {
        return $query->whereIn(
            'projects.id',
            $user->projectsWithinScope('projects.view', $scope)->select('projects.id'),
        );
    }

    private function widestPermission(User $user, array $permissions): ?string
    {
        $priority = ['self' => 1, 'section' => 2, 'department' => 3, 'organization' => 4];
        $selected = null;
        $selectedPriority = 0;

        foreach ($permissions as $permission) {
            $scope = $user->highestScopeFor($permission);
            if ($scope !== null && ($priority[$scope] ?? 0) > $selectedPriority) {
                $selected = $permission;
                $selectedPriority = $priority[$scope];
            }
        }

        return $selected;
    }

    private function assertTaskRelationships(
        array $data,
        Employee $assignee,
        ?Task $currentTask = null,
    ): void {
        $project = null;

        if (! empty($data['project_id'])) {
            $project = Project::query()
                ->whereKey($data['project_id'])
                ->where('department_id', $assignee->department_id)
                ->first();

            if ($project === null) {
                throw ValidationException::withMessages([
                    'project_id' => 'المشروع المحدد لا يتبع دائرة الموظف المكلّف.',
                ]);
            }
        }

        if (empty($data['parent_task_id'])) {
            return;
        }

        $parentTask = Task::query()->find($data['parent_task_id']);

        if ($parentTask === null || $parentTask->project_id !== ($project?->id)) {
            throw ValidationException::withMessages([
                'parent_task_id' => 'يجب أن تتبع المهمة الأم المشروع نفسه.',
            ]);
        }

        $visitedTaskIds = [];
        while ($parentTask !== null) {
            if ($currentTask !== null && $parentTask->id === $currentTask->id) {
                throw ValidationException::withMessages([
                    'parent_task_id' => 'لا يمكن إنشاء دورة في تسلسل المهام.',
                ]);
            }

            if (isset($visitedTaskIds[$parentTask->id])) {
                throw ValidationException::withMessages([
                    'parent_task_id' => 'تسلسل المهام الحالي يحتوي دورة غير صالحة.',
                ]);
            }

            $visitedTaskIds[$parentTask->id] = true;
            $parentTask = $parentTask->parentTask;
        }
    }
}
