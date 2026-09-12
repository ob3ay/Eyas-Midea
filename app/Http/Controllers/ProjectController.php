<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * عرض قائمة المشاريع مع الإحصائيات
     */
    public function index()
    {
        $projects = Project::with([
            'department',
            'owner',
            'members',
            'tasks',
        ])
            ->latest()
            ->get();

        $departments = Department::orderBy('name')->get();

        $employees = Employee::orderBy('full_name')->get();

        $stats = [
            'total' => $projects->count(),

            'in_progress' => $projects
                ->where('status', 'in_progress')
                ->count(),

            'in_review' => $projects
                ->where('status', 'in_review')
                ->count(),

            'completed' => $projects
                ->where('status', 'completed')
                ->count(),

            'on_hold' => $projects
                ->where('status', 'on_hold')
                ->count(),
        ];

        $averageProgress = $projects->avg('completion_percentage') ?? 0;

        return view('projects.index', compact(
            'projects',
            'departments',
            'employees',
            'stats',
            'averageProgress'
        ));
    }

    /**
     * حفظ مشروع جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'department_id' => [
                'nullable',
                'exists:departments,id',
            ],

            'owner_employee_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'required',
                'in:in_progress,in_review,completed,on_hold',
            ],

            'completion_percentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        $validated['completion_percentage'] =
            $validated['completion_percentage'] ?? 0;

        if ($validated['status'] === 'completed') {
            $validated['completion_percentage'] = 100;
        }

        Project::create($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'تم إضافة المشروع بنجاح');
    }

    /**
     * تحديث مشروع قائم
     */
    public function update(
        Request $request,
        Project $project
    ): RedirectResponse {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'department_id' => [
                'nullable',
                'exists:departments,id',
            ],

            'owner_employee_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'actual_end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'required',
                'in:in_progress,in_review,completed,on_hold',
            ],

            'completion_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        if ($validated['status'] === 'completed') {
            $validated['completion_percentage'] = 100;

            if (empty($validated['actual_end_date'])) {
                $validated['actual_end_date'] = now()->toDateString();
            }
        }

        $project->update($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'تم تعديل بيانات المشروع بنجاح');
    }

    /**
     * حذف مشروع
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }
}
