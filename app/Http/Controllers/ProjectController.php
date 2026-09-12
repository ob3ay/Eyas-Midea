<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * عرض قائمة المشاريع والإنتاج مع الإحصائيات
     */
    public function index()
    {
        // جلب المشاريع مع العلاقات مرتبة من الأحدث
        $projects = Project::with(['department', 'manager'])
            ->latest()
            ->get();

        // الإحصائيات المطابقة لكروت Dashboard بالأعلى
        $stats = [
            'total'          => $projects->count(),
            'software_count' => $projects->where('type', 'software')->count(),
            'media_count'    => $projects->where('type', 'media')->count(),
            'in_progress'    => $projects->where('status', 'in_progress')->count(),
            'in_review'      => $projects->where('status', 'in_review')->count(),
            'completed'      => $projects->where('status', 'completed')->count(),
        ];

        // نسبة الإنجاز الكلية
        $averageProgress = $projects->avg('progress') ?? 0;

        return view('projects.index', compact('projects', 'stats', 'averageProgress'));
    }

    /**
     * حفظ مشروع جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'delivery_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id'    => 'nullable|exists:users,id',
            'type'          => 'nullable|in:software,media',
        ]);

        // توليد كود تلقائي برمز إياس الإعلامية
        $validated['code'] = 'EYAS-PRJ-' . strtoupper(Str::random(4));
        $validated['progress'] = 0;
        $validated['status'] = 'in_progress';

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'تم إضافة المشروع بنجاح');
    }

    /**
     * تحديث بيانات مشروع قائم (مطابق للمسار projects.update)
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'delivery_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'manager_id'    => 'nullable|exists:users,id',
            'progress'      => 'nullable|integer|min:0|max:100',
            'status'        => 'required|in:in_progress,in_review,completed,on_hold',
            'type'          => 'required|in:software,media',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'تم تعديل بيانات المشروع بنجاح');
    }

    /**
     * حذف مشروع (مطابق للمسار projects.destroy)
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح');
    }
}
