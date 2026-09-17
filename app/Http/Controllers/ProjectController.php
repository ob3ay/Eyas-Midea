<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->authorizeScope('projects.view');

        $projects = $this->scopedProjectsQuery($user, $scope)
            ->with(['department', 'owner', 'members', 'tasks.assignee', 'attachments.uploader'])
            ->latest()
            ->get();

        $departments = $scope === 'organization'
            ? Department::with('manager')->orderBy('name')->get()
            : Department::with('manager')->whereKey($user->employee?->department_id ?? 0)->get();

        $employees = Employee::active()
            ->when($scope !== 'organization', fn($q) => $q->where('department_id', $user->employee?->department_id ?? 0))
            ->orderBy('full_name')
            ->get();

        $stats = [
            'total' => $projects->count(),
            'in_progress' => $projects->where('status', 'in_progress')->count(),
            'in_review' => $projects->where('status', 'in_review')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'on_hold' => $projects->where('status', 'on_hold')->count(),
        ];

        $averageProgress = $projects->avg('completion_percentage') ?? 0;

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
            'department_id' => ['required', 'exists:departments,id'],
            'owner_employee_id' => ['nullable', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:in_progress,in_review,completed,on_hold'],
            'completion_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:20480'],
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

        $project = Project::create($validated);

        if (! empty($attachments)) {
            $uploaderId = $user->employee?->id;
            foreach ($attachments as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $sizeKb = max(1, (int) round($file->getSize() / 1024));
                    $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
                    $path = $file->store('projects/attachments', 'public');

                    $project->attachments()->create([
                        'file_name' => $originalName,
                        'file_path' => $path,
                        'file_size_kb' => $sizeKb,
                        'mime_type' => $mimeType,
                        'uploaded_by' => $uploaderId,
                    ]);
                }
            }
        }

        if ($project->owner_employee_id !== null) {
            $notifications->createForEmployee(
                $project->owner_employee_id,
                'project_assigned',
                'تم إسناد مشروع جديد',
                "تم إسناد المشروع «{$project->title}» إليك.",
                $project,
            );
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
            'department_id' => ['required', 'exists:departments,id'],
            'owner_employee_id' => ['nullable', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'actual_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'in:in_progress,in_review,completed,on_hold'],
            'completion_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'max:20480'],
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

        $project->update($validated);

        if (! empty($attachments)) {
            $uploaderId = $user->employee?->id;
            foreach ($attachments as $file) {
                if ($file && $file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $sizeKb = max(1, (int) round($file->getSize() / 1024));
                    $mimeType = $file->getClientMimeType() ?: $file->getMimeType();
                    $path = $file->store('projects/attachments', 'public');

                    $project->attachments()->create([
                        'file_name' => $originalName,
                        'file_path' => $path,
                        'file_size_kb' => $sizeKb,
                        'mime_type' => $mimeType,
                        'uploaded_by' => $uploaderId,
                    ]);
                }
            }
        }

        return redirect()->route('projects.index')->with('success', 'تم تعديل بيانات المشروع بنجاح');
    }

    public function destroy(Request $request, Project $project): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('projects.delete', $project, $user);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح');
    }

    public function downloadAttachment(ProjectAttachment $attachment)
    {
        if (! Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'الملف غير موجود في الخادم.');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function destroyAttachment(Request $request, ProjectAttachment $attachment): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeScope('projects.update', $attachment->project, $user);

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->forceDelete();

        return back()->with('success', 'تم حذف المرفق بنجاح.');
    }

    private function authorizeScope(string $permissionKey, ?Project $project = null, ?User $user = null): string
    {
        $user ??= auth()->user();
        $scope = $user->highestScopeFor($permissionKey);

        if ($scope === null) {
            abort(403, 'ليس لديك صلاحية لتنفيذ هذه العملية.');
        }

        if ($project !== null && $scope !== 'organization') {
            $ownEmployee = $user->employee;

            if (! $ownEmployee) {
                abort(403, 'حسابك غير مرتبط بسجل موظف.');
            }

            if ($scope === 'self' && $project->owner_employee_id !== $ownEmployee->id) {
                abort(403, 'لا تملك صلاحية إدارة سوى المشاريع المسندة إليك.');
            }

            if ($scope === 'department' && $project->department_id !== $ownEmployee->department_id) {
                abort(403, 'لا تملك صلاحية إدارة سوى مشاريع دائرتك.');
            }
        }

        return $scope;
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
        $query = Project::query();

        if ($scope === 'organization') {
            return $query;
        }

        $ownEmployee = $user->employee;
        if (! $ownEmployee) {
            return $query->whereRaw('1 = 0');
        }

        if ($scope === 'self') {
            return $query->where(function ($q) use ($ownEmployee) {
                $q->where('owner_employee_id', $ownEmployee->id)
                    ->orWhereHas('members', function ($memberQuery) use ($ownEmployee) {
                        $memberQuery->where('employees.id', $ownEmployee->id);
                    });
            });
        }

        return $query->where('department_id', $ownEmployee->department_id);
    }
}
