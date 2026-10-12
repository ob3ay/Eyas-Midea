@extends('layouts.portal')

@section('title', 'لوحة التحكم التنفيذية')

@section('content')
    <!-- بانر الترحيب التنفيذي -->
    <div class="ems-welcome-banner">
        <div>
            <span class="ems-badge badge-gold" style="margin-bottom: 8px;">بوابة الإدارة الرقمية الموحدة (EMS)</span>
            <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 6px;">
                مرحباً بك{{ $primaryRoleName ? '، ' . $primaryRoleName : '' }}: {{ $welcomeName }}
            </h1>
            <p style="font-size: 13.5px; color: #94a3b8; max-width: 600px;">
                @if ($isEmployee ?? false)
                    متابعة بياناتك ومهامك ومشاريعك وسجل دوامك بحسب الصلاحيات المتاحة لحسابك.
                @else
                    نظرة على سير العمليات والإنتاج وشؤون الكوادر ضمن نطاق صلاحياتك في مجموعة إياس الإعلامية.
                @endif
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            @if (! ($isEmployee ?? false) && isset($pendingRequestsCount) && $pendingRequestsCount > 0)
                <a href="{{ route('requests.index') }}" class="ems-btn ems-btn-secondary"
                    style="background: rgba(245, 158, 11, 0.2); border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    <span>طلبات بانتظار المراجعة ({{ $pendingRequestsCount }})</span>
                </a>
            @endif
            @if ($canCreateProject)
                <a href="{{ route('projects.index') }}" class="ems-btn ems-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>مشروع جديد</span>
                </a>
            @endif
            @if ($canViewTasks)
                <a href="{{ route('tasks.index') }}" class="ems-btn ems-btn-secondary"
                    style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: #fff;">
                    <span>{{ ($isEmployee ?? false) ? 'متابعة مهامي' : 'متابعة المهام' }} ({{ $taskCount }})</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 4 بطاقات إحصائية رئيسية -->
    <div class="ems-stats-grid">
        @if ($isEmployee ?? false)
            <!-- كروت مخصصة للموظف -->
            @if ($canViewTasks)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">مهامي النشطة</span>
                    <span class="ems-stat-value">{{ $myActiveTasksCount }}</span>
                    <span class="ems-stat-subtext" style="color: #6d28d9;">{{ $underReviewCount }} مهام قيد المراجعة</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 11 3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                </div>
            </div>
            @endif

            @if ($canViewProjects)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">مشاريعي الحالية</span>
                    <span class="ems-stat-value">{{ $projectCount }}</span>
                    <span class="ems-stat-subtext" style="color: #d97706;">{{ $activeProjectsCount }} مشاريع قيد التطوير</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
                    </svg>
                </div>
            </div>
            @endif

            @if ($canViewAttendance)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">سجل دوامي هذا الشهر</span>
                    <span class="ems-stat-value" style="color: #10b981;">{{ $presentThisMonthCount }} يوم</span>
                    <span class="ems-stat-subtext">{{ $todayAttendance ? 'حاضر اليوم' : 'بانتظار تسجيل اليوم' }}</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
            </div>
            @endif

            @if ($canViewPerformance)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">تقييم أدائي الأخير</span>
                    <span class="ems-stat-value" style="color: #c5a059;">{{ $latestEvaluation ? $latestEvaluation->score . '%' : 'قيد التقييم' }}</span>
                    <span class="ems-stat-subtext">{{ $latestEvaluation ? 'الفترة: ' . $latestEvaluation->period : 'تقييم رسمي معتمد' }}</span>
                </div>
                <div class="ems-stat-icon" style="background-color: rgba(197, 160, 89, 0.15); border-color: rgba(197, 160, 89, 0.3); color: #c5a059;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20V10" />
                        <path d="M18 20V4" />
                        <path d="M6 20v-4" />
                    </svg>
                </div>
            </div>
            @endif
        @else
            <!-- كروت الإدارة العامة -->
            @if ($canViewEmployees)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">إجمالي الكادر والقيادة</span>
                    <span class="ems-stat-value">{{ $employeeCount }}</span>
                    @if ($canViewDepartments)
                        <span class="ems-stat-subtext" style="color: #10b981;">{{ $departmentCount }} دوائر تخصصية رئيسية</span>
                    @endif
                </div>
                <div class="ems-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
            </div>
            @endif

            @if ($canViewProjects)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">المشاريع البرمجية والإعلامية</span>
                    <span class="ems-stat-value">{{ $projectCount }}</span>
                    <span class="ems-stat-subtext" style="color: #d97706;">{{ $activeProjectsCount }} مشاريع قيد التطوير</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
                    </svg>
                </div>
            </div>
            @endif

            @if ($canViewTasks)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">المهام في دورة العمل</span>
                    <span class="ems-stat-value">{{ $taskCount }}</span>
                    <span class="ems-stat-subtext" style="color: #6d28d9;">{{ $underReviewCount }} مهام قيد المراجعة</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m9 11 3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                </div>
            </div>
            @endif

            @if ($canViewAttendance)
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">نسبة الالتزام بالدوام اليوم</span>
                    <span class="ems-stat-value" style="color: #10b981;">{{ $attendanceRate }}%</span>
                    <span class="ems-stat-subtext">{{ $presentTodayCount }} حاضراً في الميدان والمقر</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
            </div>
            @endif
        @endif
    </div>

    <div class="ems-two-columns">
        <div>
            <!-- بطاقة المشاريع الحيوية -->
            @if ($canViewProjects)
            <div class="ems-card">
                <div class="ems-card-header">
                    <div class="ems-card-title">مشاريع الإنتاج والتطوير النشطة</div>
                    <a href="{{ route('projects.index') }}"
                        style="font-size: 12.5px; font-weight: 700; color: var(--ems-gold-hover);">عرض الكل
                        ({{ $projectCount }}) ←</a>
                </div>
                <div class="ems-card-body">
                    <div style="display: flex; flex-direction: column; gap: 18px;">
                        @forelse ($activeProjects as $project)
                            <div>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <div>
                                        <strong
                                            style="font-size: 14px; color: var(--ems-text-main);">{{ $project->title }}</strong>
                                        <span class="ems-badge badge-gold"
                                            style="font-size: 10px; margin-right: 6px;">{{ $project->department->name ?? 'بدون دائرة' }}</span>
                                    </div>
                                    <span
                                        style="font-family: var(--font-mono); font-weight: 700; font-size: 13px; color: var(--ems-gold-hover);">{{ (int) $project->completion_percentage }}%</span>
                                </div>
                                <div class="ems-progress">
                                    <div class="ems-progress-bar"
                                        style="width: {{ (int) $project->completion_percentage }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p style="font-size: 13px; color: var(--ems-text-muted);">لا توجد مشاريع نشطة حالياً.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            <!-- بطاقة حالات المهام الخمس -->
            @if ($canViewTasks)
            <div class="ems-card">
                <div class="ems-card-header">
                    <div class="ems-card-title">حالات المهام الخمس المعتمدة (Task Lifecycle)</div>
                    <a href="{{ route('tasks.index') }}"
                        style="font-size: 12.5px; font-weight: 700; color: var(--ems-gold-hover);">لوحة كانبان ←</a>
                </div>
                <div class="ems-card-body">
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; text-align: center;">
                        <div
                            style="background: var(--state-draft-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-draft-border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--state-draft-text);">1. مسودة</div>
                            <div
                                style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">
                                {{ $taskStatusCounts->get('Draft', 0) }}</div>
                        </div>
                        <div
                            style="background: var(--state-todo-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-todo-border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--state-todo-text);">2. انتظار</div>
                            <div
                                style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">
                                {{ $taskStatusCounts->get('To-Do', 0) }}</div>
                        </div>
                        <div
                            style="background: var(--state-progress-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-progress-border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--state-progress-text);">3. تنفيذ
                            </div>
                            <div
                                style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">
                                {{ $taskStatusCounts->get('In Progress', 0) }}</div>
                        </div>
                        <div
                            style="background: var(--state-review-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-review-border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--state-review-text);">4. مراجعة
                            </div>
                            <div
                                style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">
                                {{ $taskStatusCounts->get('Under Review', 0) }}</div>
                        </div>
                        <div
                            style="background: var(--state-completed-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-completed-border);">
                            <div style="font-size: 11px; font-weight: 700; color: var(--state-completed-text);">5. مكتمل
                            </div>
                            <div
                                style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">
                                {{ $taskStatusCounts->get('Completed', 0) }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div>
            <!-- سجل الأنشطة المباشرة -->
            <div class="ems-card">
                <div class="ems-card-header">
                    <div class="ems-card-title">سجل أنشطتك الأخيرة</div>
                    <span class="ems-status-dot"></span>
                </div>
                <div class="ems-card-body">
                    @forelse ($recentActivities as $activity)
                        <div class="ems-feed-item">
                            @php
                                $actorName =
                                    $activity->user->employee->full_name ?? ($activity->user->username ?? 'النظام');
                                $initial = mb_substr($actorName, 0, 1);
                                $actionLabels = [
                                    'Login' => 'سجّل دخولاً إلى المنظومة',
                                    'Logout' => 'سجّل خروجاً من المنظومة',
                                    'AccountLocked' => 'تم قفل حسابه مؤقتاً',
                                    'UpdateRolePermission' => 'قام بتحديث صلاحيات دور',
                                    'CreateProject' => 'أنشأ مشروعاً جديداً',
                                    'UpdateProject' => 'عدّل بيانات مشروع',
                                    'CreateTask' => 'أنشأ مهمة جديدة',
                                    'UpdateTask' => 'حدّث بيانات مهمة',
                                ];
                                $actionText =
                                    $actionLabels[$activity->action_type] ??
                                    'قام بعملية على ' . $activity->entity_type;
                            @endphp
                            <div class="ems-avatar"
                                style="width: 32px; height: 32px; font-size: 12px; background:#c5a059;">
                                {{ $initial }}</div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 700; color: var(--ems-text-main);">
                                    {{ $actorName }} {{ $actionText }}</div>
                                <div style="font-size: 11.5px; color: var(--ems-text-muted);">
                                    {{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="font-size: 13px; color: var(--ems-text-muted);">لا توجد أنشطة مسجلة بعد.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
