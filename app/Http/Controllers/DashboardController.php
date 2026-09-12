<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('employee', 'roles');

        // بطاقة الترحيب: اسم الموظف + أول دور (Role) إله
        $welcomeName = $user->employee->full_name ?? $user->username;
        $primaryRoleName = $user->roles->first()->display_name ?? null;

        // 1) بطاقة الكادر
        $employeeCount = Employee::count();
        $departmentCount = Department::count();

        // 2) بطاقة المشاريع
        $projectCount = Project::count();
        $activeProjectsCount = Project::where('status', 'Active')->count();

        // 3) بطاقة المهام
        $taskCount = Task::count();
        $underReviewCount = Task::where('status', 'Under Review')->count();

        // 4) بطاقة نسبة الحضور اليوم
        $presentTodayCount = \App\Models\AttendanceRecord::whereDate('date', today())
            ->where('status', 'Present')
            ->count();
        $attendanceRate = $employeeCount > 0
            ? round(($presentTodayCount / $employeeCount) * 100, 1)
            : 0;

        // 5) أبرز 3 مشاريع نشطة (لعرضها بشريط التقدم)
        $activeProjects = Project::with('department')
            ->where('status', 'Active')
            ->orderByDesc('completion_percentage')
            ->limit(3)
            ->get();

        // 6) توزيع المهام على المراحل الخمس المعتمدة
        $taskStages = [
            'Draft' => 'مسودة',
            'To-Do' => 'انتظار',
            'In Progress' => 'تنفيذ',
            'Under Review' => 'مراجعة',
            'Completed' => 'مكتمل',
        ];
        $taskStatusCounts = Task::selectRaw('status, count(*) as total')
            ->whereIn('status', array_keys($taskStages))
            ->groupBy('status')
            ->pluck('total', 'status');

        // 7) آخر 4 أنشطة من سجل التدقيق (audit_logs)
        $recentActivities = AuditLog::with('user.employee')
            ->latest('created_at')
            ->limit(4)
            ->get();

        return view('dashboard', compact(
            'welcomeName',
            'primaryRoleName',
            'employeeCount',
            'departmentCount',
            'projectCount',
            'activeProjectsCount',
            'taskCount',
            'underReviewCount',
            'attendanceRate',
            'presentTodayCount',
            'activeProjects',
            'taskStages',
            'taskStatusCounts',
            'recentActivities',
        ));
    }
}
