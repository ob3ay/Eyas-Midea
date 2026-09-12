<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with([
            'project',
            'assignee',
            'creator',
            'parentTask',
            'subtasks',
        ]);

        // فلترة حسب المشروع
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // فلترة حسب الموظف
        if ($request->filled('assignee_employee_id')) {
            $query->where(
                'assignee_employee_id',
                $request->assignee_employee_id
            );
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query
            ->orderByRaw("
                CASE priority
                    WHEN 'Urgent' THEN 1
                    WHEN 'High' THEN 2
                    WHEN 'Medium' THEN 3
                    WHEN 'Low' THEN 4
                    ELSE 5
                END
            ")
            ->latest()
            ->get();

        $projects = Project::orderBy('title')->get();

        $employees = Employee::orderBy('full_name')->get();

        $stats = [
            'total' => $tasks->count(),

            'draft' => $tasks
                ->where('status', 'Draft')
                ->count(),

            'todo' => $tasks
                ->where('status', 'To-Do')
                ->count(),

            'in_progress' => $tasks
                ->where('status', 'In Progress')
                ->count(),

            'under_review' => $tasks
                ->where('status', 'Under Review')
                ->count(),

            'completed' => $tasks
                ->where('status', 'Completed')
                ->count(),

            'rejected' => $tasks
                ->where('status', 'Rejected')
                ->count(),

            'overdue' => $tasks
                ->filter(function ($task) {
                    return $task->due_datetime
                        && $task->due_datetime->isPast()
                        && $task->status !== 'Completed';
                })
                ->count(),
        ];

        $averageProgress = $tasks->avg('completion_percentage') ?? 0;

        return view('tasks.index', compact(
            'tasks',
            'projects',
            'employees',
            'stats',
            'averageProgress'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:200',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'project_id' => [
                'nullable',
                'exists:projects,id',
            ],

            'parent_task_id' => [
                'nullable',
                'exists:tasks,id',
            ],

            'assignee_employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'priority' => [
                'required',
                Rule::in([
                    'Low',
                    'Medium',
                    'High',
                    'Urgent',
                ]),
            ],

            'due_datetime' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Draft',
                    'To-Do',
                    'In Progress',
                    'Under Review',
                    'Completed',
                    'Rejected',
                ]),
            ],

            'completion_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | منشئ المهمة
        |--------------------------------------------------------------------------
        */

        $creatorEmployeeId = auth()->user()?->employee?->id;

        if (!$creatorEmployeeId) {
            return back()
                ->withInput()
                ->withErrors([
                    'created_by' => 'لا يمكن تحديد الموظف الذي أنشأ المهمة.',
                ]);
        }

        $validated['created_by'] = $creatorEmployeeId;

        /*
        |--------------------------------------------------------------------------
        | نسبة الإنجاز
        |--------------------------------------------------------------------------
        */

        $validated['completion_percentage']
            = $validated['completion_percentage'] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | حالة Completed
        |--------------------------------------------------------------------------
        */

        if ($validated['status'] === 'Completed') {
            $validated['completion_percentage'] = 100;
            $validated['completed_at'] = now();
        }

        Task::create($validated);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'تم إنشاء المهمة بنجاح');
    }

    public function update(
        Request $request,
        Task $task
    ): RedirectResponse {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:200',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'project_id' => [
                'nullable',
                'exists:projects,id',
            ],

            'parent_task_id' => [
                'nullable',
                'exists:tasks,id',
            ],

            'assignee_employee_id' => [
                'required',
                'exists:employees,id',
            ],

            'priority' => [
                'required',
                Rule::in([
                    'Low',
                    'Medium',
                    'High',
                    'Urgent',
                ]),
            ],

            'due_datetime' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Draft',
                    'To-Do',
                    'In Progress',
                    'Under Review',
                    'Completed',
                    'Rejected',
                ]),
            ],

            'completion_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | منع المهمة من أن تكون أباً لنفسها
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['parent_task_id']) &&
            (int) $validated['parent_task_id'] === (int) $task->id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'parent_task_id' => 'لا يمكن أن تكون المهمة الرئيسية هي نفس المهمة.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Completed
        |--------------------------------------------------------------------------
        */

        if ($validated['status'] === 'Completed') {
            $validated['completion_percentage'] = 100;

            if (!$task->completed_at) {
                $validated['completed_at'] = now();
            }
        } else {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function updateState(
        Request $request,
        Task $task
    ): JsonResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'Draft',
                    'To-Do',
                    'In Progress',
                    'Under Review',
                    'Completed',
                    'Rejected',
                ]),
            ],

            'completion_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        $status = $validated['status'];

        $progress = $validated['completion_percentage']
            ?? $task->completion_percentage;

        if ($status === 'Completed') {
            $progress = 100;
        }

        $completedAt = $status === 'Completed'
            ? ($task->completed_at ?? now())
            : null;

        $task->update([
            'status' => $status,
            'completion_percentage' => $progress,
            'completed_at' => $completedAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة المهمة بنجاح',

            'task' => [
                'id' => $task->id,
                'status' => $task->status,
                'completion_percentage' =>
                (float) $task->completion_percentage,
                'completed_at' =>
                $task->completed_at?->toISOString(),
            ],
        ]);
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'تم حذف المهمة بنجاح');
    }
}
