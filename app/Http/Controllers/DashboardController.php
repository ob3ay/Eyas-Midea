<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeRequest;
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
        $activeProjectsCount = Project::whereIn('status', ['in_progress', 'in_review'])->count();
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
            ->whereIn('status', ['in_progress', 'in_review'])
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

        // 8) إجمالي طلبات الموظفين المعلقة بانتظار البت
        $pendingRequestsCount = EmployeeRequest::where('status', 'pending')->count();

        $isEmployee = $user->hasRole('EMP') || $user->highestScopeFor('employees.view') === 'self';
        $latestEvaluation = null;
        $todayAttendance = null;
        $presentThisMonthCount = 0;
        $lateThisMonthCount = 0;
        $myActiveTasksCount = 0;

        if ($isEmployee && $user->employee_id) {
            $empId = $user->employee_id;

            // مهام الموظف
            $myTasks = Task::where('assignee_employee_id', $empId)->get();
            $taskCount = $myTasks->count();
            $myActiveTasksCount = $myTasks->whereIn('status', ['To-Do', 'In Progress', 'Under Review'])->count();
            $underReviewCount = $myTasks->where('status', 'Under Review')->count();

            // مشاريع الموظف
            $myProjectsQuery = Project::whereHas('members', fn($q) => $q->where('employees.id', $empId));
            $projectCount = $myProjectsQuery->count();
            $activeProjectsCount = (clone $myProjectsQuery)->whereIn('status', ['in_progress', 'in_review'])->count();

            // سجل حضور الموظف هذا الشهر
            $attendanceThisMonth = \App\Models\AttendanceRecord::where('employee_id', $empId)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->get();
            $presentThisMonthCount = $attendanceThisMonth->whereIn('status', ['Present', 'Late'])->count();
            $lateThisMonthCount = $attendanceThisMonth->where('status', 'Late')->count();

            // حالة تسجيل الحضور اليوم
            $todayAttendance = \App\Models\AttendanceRecord::where('employee_id', $empId)
                ->whereDate('date', today())
                ->first();

            // آخر تقييم أداء
            $latestEvaluation = \App\Models\PerformanceEvaluation::where('employee_id', $empId)
                ->latest('evaluation_date')
                ->first();

            // أبرز مشاريع الموظف
            $activeProjects = (clone $myProjectsQuery)
                ->with('department')
                ->whereIn('status', ['in_progress', 'in_review'])
                ->orderByDesc('completion_percentage')
                ->limit(3)
                ->get();

            // توزيع مهام الموظف
            $taskStatusCounts = Task::where('assignee_employee_id', $empId)
                ->selectRaw('status, count(*) as total')
                ->whereIn('status', array_keys($taskStages))
                ->groupBy('status')
                ->pluck('total', 'status');

            // أنشطة خاصة بالموظف
            $recentActivities = AuditLog::with('user.employee')
                ->where(function ($q) use ($user, $empId) {
                    $q->where('user_id', $user->id)
                      ->orWhere(function ($sub) use ($empId) {
                          $sub->where('entity_type', 'Task')
                              ->whereIn('entity_id', Task::where('assignee_employee_id', $empId)->pluck('id'));
                      });
                })
                ->latest('created_at')
                ->limit(4)
                ->get();

            if ($recentActivities->isEmpty()) {
                $recentActivities = AuditLog::with('user.employee')->latest('created_at')->limit(4)->get();
            }
        }

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
            'isEmployee',
            'latestEvaluation',
            'todayAttendance',
            'presentThisMonthCount',
            'lateThisMonthCount',
            'myActiveTasksCount',
            'pendingRequestsCount',
        ));
    }
}
