<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    private const STATUSES = [
        'Backlog',
        'Todo',
        'In Progress',
        'Review',
        'Completed',
        'Cancelled',
    ];

    private const PRIORITIES = [
        'Low',
        'Medium',
        'High',
        'Critical',
    ];

    public function index(Request $request)
    {
        $projectId = $request->query('project_id');

        $query = Task::with([
            'project',
            'assignee',
        ])->latest();

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $tasks = $query->paginate(50)->appends($request->query());

        $employees = Employee::where('employment_status', 'active')
            ->orderBy('full_name')
            ->get();

        $selectedProject = $projectId ? Project::find($projectId) : null;

        return view(
            'tasks.index',
            compact('tasks', 'employees', 'selectedProject')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $this->normalizeAssignee($data);

        $task = DB::transaction(function () use (
            $request,
            $data
        ) {
            $task = Task::create([
                'task_code' => $data['task_code'] ?? null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'project_id' => $data['project_id'] ?? null,
                'parent_task_id' => $data['parent_task_id'] ?? null,
                'assignee_employee_id' => $data['assignee_employee_id'],
                'priority' => $data['priority'] ?? 'Medium',
                'due_datetime' => $data['due_datetime'] ?? null,
                'status' => $data['status'] ?? 'Todo',
                'completion_percentage' => $data['completion_percentage'] ?? 0,
            ]);

            TaskStatusHistory::create([
                'task_id' => $task->id,
                'old_status' => null,
                'new_status' => $task->status,
                'notes' => 'Task created',
                'changed_by' => $request->user()->employee?->id,
            ]);

            $this->notifyEmployee(
                $task->assignee_employee_id,
                'task_assigned',
                'مهمة جديدة',
                "تم إسناد المهمة: {$task->title}",
                $task
            );

            $this->audit(
                'Create',
                'Task',
                $task->id,
                null,
                $task->toArray(),
                $request
            );

            return $task;
        });

        return back()->with(
            'success',
            "تم إنشاء المهمة #{$task->id} وإسنادها بنجاح."
        );
    }

    public function updateState(
        Request $request,
        Task $task
    ): JsonResponse {
        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(self::STATUSES),
            ],
            'completion_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'الحساب الحالي غير مرتبط بموظف.',
            ], 422);
        }

        DB::transaction(function () use (
            $task,
            $data,
            $employee,
            $request
        ) {
            $oldStatus = $task->status;
            $newStatus = $data['status'];

            $percentage = array_key_exists('completion_percentage', $data)
                ? $data['completion_percentage']
                : ($newStatus === 'Completed' ? 100 : $task->completion_percentage);

            $task->update([
                'status' => $newStatus,
                'completion_percentage' => $percentage,
                'completed_at' => $newStatus === 'Completed' ? now() : null,
            ]);

            if ($oldStatus !== $newStatus) {
                TaskStatusHistory::create([
                    'task_id' => $task->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'notes' => $data['notes'] ?? null,
                    'changed_by' => $employee->id,
                ]);
            }

            $this->audit(
                'Update',
                'Task',
                $task->id,
                ['status' => $oldStatus],
                $task->fresh()->toArray(),
                $request
            );
        });

        return response()->json([
            'message' => 'تم تحديث حالة المهمة.',
            'task' => $task->fresh()->only([
                'id',
                'status',
                'completion_percentage',
                'completed_at',
            ]),
        ]);
    }

    public function update(
        Request $request,
        Task $task
    ): RedirectResponse {
        $data = $this->validated($request, true);

        $this->normalizeAssignee($data);

        $old = $task->toArray();
        $status = $data['status'] ?? $task->status;

        $task->update([
            'task_code' => $data['task_code'] ?? $task->task_code,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'parent_task_id' => $data['parent_task_id'] ?? null,
            'assignee_employee_id' => $data['assignee_employee_id'],
            'priority' => $data['priority'] ?? 'Medium',
            'due_datetime' => $data['due_datetime'] ?? null,
            'status' => $status,
            'completion_percentage' => $data['completion_percentage'] ?? $task->completion_percentage,
            'completed_at' => $status === 'Completed' ? ($task->completed_at ?? now()) : null,
        ]);

        if (array_key_exists('status', $data) && $old['status'] !== $status) {
            TaskStatusHistory::create([
                'task_id' => $task->id,
                'old_status' => $old['status'],
                'new_status' => $status,
                'changed_by' => $request->user()->employee?->id,
            ]);
        }

        $this->audit(
            'Update',
            'Task',
            $task->id,
            $old,
            $task->fresh()->toArray(),
            $request
        );

        return back()->with(
            'success',
            'تم تحديث المهمة.'
        );
    }

    public function destroy(
        Request $request,
        Task $task
    ): RedirectResponse {
        $old = $task->toArray();

        $task->delete();

        $this->audit(
            'Delete',
            'Task',
            $task->id,
            $old,
            null,
            $request
        );

        return back()->with(
            'success',
            'تم حذف المهمة.'
        );
    }

    private function validated(
        Request $request,
        bool $updating = false
    ): array {
        $data = $request->validate([
            'task_code' => ['nullable', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')],
            'parent_task_id' => ['nullable', 'integer', Rule::exists('tasks', 'id')],
            'assignee_employee_id' => ['nullable', 'integer', Rule::exists('employees', 'id')],
            'assigned_to' => ['nullable', 'integer', Rule::exists('employees', 'id')],
            'priority' => ['nullable', Rule::in(self::PRIORITIES)],
            'due_datetime' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(self::STATUSES)],
            'stage' => ['nullable', Rule::in(self::STATUSES)],
            'completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        if (empty($data['assignee_employee_id']) && empty($data['assigned_to'])) {
            throw ValidationException::withMessages([
                'assigned_to' => 'يجب تحديد الموظف المسند إليه.',
            ]);
        }

        return $data;
    }

    private function normalizeAssignee(array &$data): void
    {
        if (empty($data['assignee_employee_id']) && !empty($data['assigned_to'])) {
            $data['assignee_employee_id'] = $data['assigned_to'];
        }

        if (empty($data['status']) && !empty($data['stage'])) {
            $data['status'] = $data['stage'];
        }
    }

    private function notifyEmployee(
        int $employeeId,
        string $type,
        string $title,
        string $message,
        Task $task
    ): void {
        $userId = Employee::find($employeeId)?->user?->id;

        if (!$userId) {
            return;
        }

        Notification::create([
            'recipient_user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_entity_type' => Task::class,
            'related_entity_id' => $task->id,
        ]);
    }
}
