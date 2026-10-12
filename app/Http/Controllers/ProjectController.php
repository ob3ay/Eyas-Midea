<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('projects.view');
        $taskEmployeeIds = $user->employeesWithinScope('tasks.view')->select('employees.id');

        $projectQuery = $this->scopedProjectsQuery($user, $scope);
        $statusCounts = (clone $projectQuery)
            ->select('status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $projects = $projectQuery
            ->with([
                'department', 'owner', 'members', 'attachments.uploader',
                'tasks' => fn ($tasks) => $tasks
                    ->whereIn('assignee_employee_id', $taskEmployeeIds)
                    ->with('assignee'),
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $createScope = $user->highestScopeFor('projects.create');
        if ($createScope === 'organization') {
            $departments = Department::with('manager')->orderBy('name')->get();
        } elseif (in_array($createScope, ['department', 'self'], true)) {
            $departments = Department::with('manager')->whereKey($user->employee?->department_id ?? 0)->get();
        } else {
            $departments = collect();
        }

        $employees = $createScope !== null
            ? $user->employeesWithinScope('projects.create')->active()->orderBy('full_name')->get()
            : collect();

        $stats = [
            'total' => (clone $projectQuery)->count(),
            'in_progress' => (int) $statusCounts->get('in_progress', 0),
            'in_review' => (int) $statusCounts->get('in_review', 0),
            'completed' => (int) $statusCounts->get('completed', 0),
            'on_hold' => (int) $statusCounts->get('on_hold', 0),
        ];

        $averageProgress = (clone $projectQuery)->avg('completion_percentage') ?? 0;

        $canCreate = $user->hasPermission('projects.create');
        $canUpdate = $user->hasPermission('projects.update');
        $canDelete = $user->hasPermission('projects.delete');

        return view('projects.index', compact(
            'projects',
            'departments',
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
        $scope = $this->authorizeScope('projects.create');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'owner_employee_id' => [
                'nullable',
                'integer',
                Rule::exists('employees', 'id')
                    ->where('department_id', $request->integer('department_id'))
                    ->whereNull('deleted_at'),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,in_progress,in_review,completed,on_hold,cancelled'],
            'completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,txt,zip',
                'extensions:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,txt,zip',
                'max:20480',
            ],
        ]);

        $attachments = $request->file('attachments', []);
        unset($validated['attachments']);

        if (empty($validated['owner_employee_id']) && ! empty($validated['department_id'])) {
            $dept = Department::find($validated['department_id']);
            if ($dept && $dept->manager_employee_id) {
                $validated['owner_employee_id'] = $dept->manager_employee_id;
            }
        }

        $this->assertWithinScope($user, $scope, $validated['department_id'] ?? null, $validated['owner_employee_id'] ?? null);

        $validated['completion_percentage'] = $validated['completion_percentage'] ?? 0;

        if ($validated['status'] === 'completed') {
            $validated['completion_percentage'] = 100;
        }

        $storedPaths = [];
        try {
            DB::transaction(function () use ($validated, $attachments, $user, $notifications, &$storedPaths): void {
                $project = Project::create($validated);
                AuditLog::record('Create', 'Project', $project->id);
                $this->storeAttachments($project, $attachments, $user->employee?->id, $storedPaths);
                if ($project->owner_employee_id !== null) {
                    $notifications->createForEmployee($project->owner_employee_id, 'project_assigned', 'تم إسناد مشروع جديد',
                        "تم إسناد المشروع «{$project->title}» إليك.", $project);
                }
            });
        } catch (\Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('local')->delete($storedPaths);
            }
            throw $exception;
        }

        return redirect()->route('projects.index')->with('success', 'تم إضافة المشروع بنجاح');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $user = $request->user();
        $scope = $this->authorizeScope('projects.update', $project, $user);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'owner_employee_id' => [
                'nullable',
                'integer',
                Rule::exists('employees', 'id')
                    ->where('department_id', $request->integer('department_id'))
                    ->whereNull('deleted_at'),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'actual_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:draft,in_progress,in_review,completed,on_hold,cancelled'],
            'completion_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,txt,zip',
                'extensions:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,txt,zip',
                'max:20480',
            ],
        ]);

        $attachments = $request->file('attachments', []);
        unset($validated['attachments']);

        if (empty($validated['owner_employee_id']) && ! empty($validated['department_id'])) {
            $dept = Department::find($validated['department_id']);
            if ($dept && $dept->manager_employee_id) {
                $validated['owner_employee_id'] = $dept->manager_employee_id;
            }
        }

        $this->assertWithinScope($user, $scope, $validated['department_id'] ?? null, $validated['owner_employee_id'] ?? null);

        if ($validated['status'] === 'completed') {
            $validated['completion_percentage'] = 100;

            if (empty($validated['actual_end_date'])) {
                $validated['actual_end_date'] = now()->toDateString();
            }
        }

        $storedPaths = [];
        try {
            DB::transaction(function () use ($project, $validated, $attachments, $user, &$storedPaths): void {
                $project->update($validated);
                AuditLog::record('Update', 'Project', $project->id);
                $this->storeAttachments($project, $attachments, $user->employee?->id, $storedPaths);
            });
        } catch (\Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('local')->delete($storedPaths);
            }
            throw $exception;
        }

        return redirect()->route('projects.index')->with('success', 'تم تعديل بيانات المشروع بنجاح');
    }

    public function destroy(Request $request, Project $project): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('projects.delete', $project, $user);

        DB::transaction(function () use ($project): void {
            $project->delete();
            AuditLog::record('Delete', 'Project', $project->id);
        });

        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح');
    }

    public function downloadAttachment(Request $request, ProjectAttachment $attachment)
    {
        abort_unless($this->canViewProject($request->user(), $attachment->project), 404);

        $disk = $attachment->disk ?: 'public';
        if (! Storage::disk($disk)->exists($attachment->file_path)) {
            abort(404, 'الملف غير موجود في الخادم.');
        }

        return Storage::disk($disk)->download(
            $attachment->file_path,
            basename($attachment->file_name),
            ['X-Content-Type-Options' => 'nosniff']
        );
    }

    public function destroyAttachment(Request $request, ProjectAttachment $attachment): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('projects.update', $attachment->project, $user);

        $path = $attachment->file_path;
        $disk = $attachment->disk ?: 'public';
        DB::transaction(function () use ($attachment): void {
            $attachment->forceDelete();
            AuditLog::record('DeleteAttachment', 'ProjectAttachment', $attachment->id);
        });
        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }

        return back()->with('success', 'تم حذف المرفق بنجاح.');
    }

    private function authorizeScope(string $permissionKey, ?Project $project = null, ?User $user = null): string
    {
        $user ??= auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        if ($scope === 'section') {
            abort(403, 'نطاق القسم غير مدعوم لصلاحيات المشاريع.');
        }

        if ($project !== null && ! $user->allowsProjectWithinScope($permissionKey, $project)) {
            abort(403, 'لا تملك صلاحية إدارة هذا المشروع خارج نطاقك.');
        }

        return $scope;
    }

    private function canViewProject(User $user, Project $project): bool
    {
        return $user->allowsProjectWithinScope('projects.view', $project);
    }

    private function assertWithinScope(User $user, string $scope, ?int $departmentId, ?int $ownerEmployeeId): void
    {
        if ($scope === 'organization') {
            return;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            abort(403, 'حسابك غير مرتبط بسجل موظف.');
        }

        if ($scope === 'self' && $ownerEmployeeId !== $ownEmployee->id) {
            abort(403, 'لا تملك صلاحية إنشاء مشاريع إلا إذا كنت المسؤول عنها.');
        }

        if ($scope === 'department' && $departmentId !== $ownEmployee->department_id) {
            abort(403, 'لا تملك صلاحية إنشاء أو تعديل مشاريع خارج نطاق دائرتك.');
        }
    }

    private function scopedProjectsQuery(User $user, string $scope)
    {
        return $user->projectsWithinScope('projects.view', $scope);
    }

    private function storeAttachments(Project $project, array $files, ?int $uploaderId, array &$storedPaths): void
    {
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('projects/attachments', 'local');
            if ($path === false) {
                throw new \RuntimeException('تعذر تخزين أحد مرفقات المشروع.');
            }
            $storedPaths[] = $path;

            $project->attachments()->create([
                'disk' => 'local',
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size_kb' => max(1, (int) round($file->getSize() / 1024)),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $uploaderId,
            ]);
        }
    }
}
