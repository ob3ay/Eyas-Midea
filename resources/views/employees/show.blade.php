```blade
@extends('layouts.portal')

@section('title', 'ملف الموظف - ' . $employee->full_name)

@section('content')

    @php
        $statusLabels = [
            'active' => [
                'text' => 'على رأس عمله',
                'class' => 'badge-completed',
            ],
            'on_leave' => [
                'text' => 'في إجازة',
                'class' => 'badge-gold',
            ],
            'inactive' => [
                'text' => 'غير نشط',
                'class' => '',
            ],
        ];

        $status = $statusLabels[$employee->employment_status] ?? [
            'text' => $employee->employment_status ?? '-',
            'class' => '',
        ];

        $taskStatusLabels = [
            'Draft' => 'مسودة',
            'To-Do' => 'مطلوبة',
            'In Progress' => 'قيد التنفيذ',
            'Under Review' => 'قيد المراجعة',
            'Completed' => 'مكتملة',
            'Rejected' => 'مرفوضة',
        ];

        $projectStatusLabels = [
            'Draft' => 'مسودة',
            'Active' => 'نشط',
            'On Hold' => 'متوقف مؤقتاً',
            'Completed' => 'مكتمل',
            'Cancelled' => 'ملغي',
        ];

        $attendanceStatusLabels = [
            'Present' => 'حاضر',
            'Absent' => 'غائب',
            'Late' => 'متأخر',
            'Leave' => 'إجازة',
        ];

        $initial = mb_substr($employee->full_name ?? '?', 0, 1);

        $attendancePercentage = $attendanceTotal > 0 ? round(($attendancePresent / $attendanceTotal) * 100) : 0;
    @endphp


    {{-- =========================================================
    Header
========================================================= --}}

    <div class="ems-page-header">

        <div class="ems-page-title-wrap">

            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>

                <span class="ems-breadcrumb-separator">/</span>

                <a href="{{ route('employees.index') }}">
                    الموارد البشرية
                </a>

                <span class="ems-breadcrumb-separator">/</span>

                <span style="color: var(--ems-text-main); font-weight: 700;">
                    ملف الموظف
                </span>
            </div>

            <h1 class="ems-page-title">
                ملف الموظف
            </h1>

            <p class="ems-page-subtitle">
                الملف الإداري والوظيفي الكامل للموظف
            </p>

        </div>

        <div class="ems-page-actions">

            <a href="{{ route('employees.index') }}" class="ems-btn ems-btn-secondary">
                ← العودة للموظفين
            </a>

            @if ($employee->user)
                <span class="ems-badge {{ $employee->user->is_active ? 'badge-completed' : '' }}">
                    {{ $employee->user->is_active ? 'حساب الدخول فعال' : 'حساب الدخول غير فعال' }}
                </span>
            @endif

        </div>

    </div>


    {{-- =========================================================
    Employee Main Card
========================================================= --}}

    <div class="ems-card" style="margin-bottom: 20px;">

        <div class="ems-card-body">

            <div
                style="
            display:flex;
            align-items:center;
            gap:20px;
            flex-wrap:wrap;
        ">

                <div class="ems-avatar"
                    style="
                    width:80px;
                    height:80px;
                    font-size:30px;
                    background:#c5a059;
                 ">
                    {{ $initial }}
                </div>

                <div style="flex:1; min-width:250px;">

                    <div
                        style="
                    display:flex;
                    align-items:center;
                    gap:10px;
                    flex-wrap:wrap;
                ">

                        <h2
                            style="
                        margin:0;
                        font-size:24px;
                        color:var(--ems-text-main);
                    ">
                            {{ $employee->full_name }}
                        </h2>

                        <span class="ems-badge {{ $status['class'] }}">
                            {{ $status['text'] }}
                        </span>

                    </div>

                    <div
                        style="
                    margin-top:8px;
                    color:var(--ems-text-muted);
                ">
                        {{ $employee->position ?? 'بدون مسمى وظيفي' }}
                    </div>

                    <div
                        style="
                    margin-top:6px;
                    font-family:var(--font-mono);
                    color:var(--ems-gold-hover);
                    font-weight:700;
                ">
                        {{ $employee->employee_code ?? 'EMP-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
                    </div>

                </div>

                <div style="min-width:220px;">

                    <div
                        style="
                    color:var(--ems-text-muted);
                    font-size:12px;
                ">
                        تاريخ التعيين
                    </div>

                    <div style="
                    font-weight:700;
                    margin-top:5px;
                ">
                        {{ $employee->hire_date?->translatedFormat('d F Y') ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    General Statistics
========================================================= --}}

    <div class="ems-stats-grid">

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي المهام</span>
                <span class="ems-stat-value">
                    {{ $taskTotal }}
                </span>
                <span class="ems-stat-subtext">
                    جميع المهام المسندة
                </span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">المهام المكتملة</span>
                <span class="ems-stat-value" style="color:#10b981;">
                    {{ $taskCompleted }}
                </span>
                <span class="ems-stat-subtext">
                    تم إنجازها
                </span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">المشاريع</span>
                <span class="ems-stat-value">
                    {{ $projects->count() }}
                </span>
                <span class="ems-stat-subtext">
                    عضو فيها
                </span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">متوسط التقييم</span>
                <span class="ems-stat-value" style="color:#c5a059;">
                    {{ $averageScore !== null ? number_format($averageScore, 2) : '-' }}
                </span>
                <span class="ems-stat-subtext">
                    من تقييمات الأداء
                </span>
            </div>
        </div>

    </div>


    {{-- =========================================================
    Employee Information
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">
            <div class="ems-card-title">
                البيانات الوظيفية والشخصية
            </div>
        </div>

        <div class="ems-card-body">

            <div class="ems-form-row">

                <div>
                    <div class="ems-stat-label">الاسم الكامل</div>
                    <strong>{{ $employee->full_name }}</strong>
                </div>

                <div>
                    <div class="ems-stat-label">رقم الهوية / الإقامة</div>
                    <strong>{{ $employee->national_id ?? '-' }}</strong>
                </div>

            </div>

            <div class="ems-form-row" style="margin-top:20px;">

                <div>
                    <div class="ems-stat-label">رقم الهاتف</div>
                    <strong>{{ $employee->phone ?? '-' }}</strong>
                </div>

                <div>
                    <div class="ems-stat-label">المسمى الوظيفي</div>
                    <strong>{{ $employee->position ?? '-' }}</strong>
                </div>

            </div>

            <div class="ems-form-row" style="margin-top:20px;">

                <div>
                    <div class="ems-stat-label">الدائرة</div>
                    <strong>
                        {{ $employee->department->name ?? '-' }}
                    </strong>
                </div>

                <div>
                    <div class="ems-stat-label">القسم</div>
                    <strong>
                        {{ $employee->section->name ?? '-' }}
                    </strong>
                </div>

            </div>

            <div class="ems-form-row" style="margin-top:20px;">

                <div>
                    <div class="ems-stat-label">المدير المباشر</div>

                    @if ($employee->manager)
                        <a href="{{ route('employees.show', $employee->manager) }}"
                            style="
                            font-weight:700;
                            color:var(--ems-gold-hover);
                       ">
                            {{ $employee->manager->full_name }}
                        </a>
                    @else
                        <strong>-</strong>
                    @endif

                </div>

                <div>
                    <div class="ems-stat-label">نهاية العقد</div>
                    <strong>
                        {{ $employee->contract_end_date?->translatedFormat('d F Y') ?? '-' }}
                    </strong>
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Login Account
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">
            <div class="ems-card-title">
                حساب الدخول والصلاحيات
            </div>
        </div>

        <div class="ems-card-body">

            @if ($employee->user)

                <div class="ems-form-row">

                    <div>
                        <div class="ems-stat-label">اسم المستخدم</div>
                        <strong>
                            {{ $employee->user->username }}
                        </strong>
                    </div>

                    <div>
                        <div class="ems-stat-label">البريد الإلكتروني</div>
                        <strong>
                            {{ $employee->user->email }}
                        </strong>
                    </div>

                </div>

                <div class="ems-form-row" style="margin-top:20px;">

                    <div>
                        <div class="ems-stat-label">حالة الحساب</div>

                        <span class="ems-badge {{ $employee->user->is_active ? 'badge-completed' : '' }}">
                            {{ $employee->user->is_active ? 'فعال' : 'غير فعال' }}
                        </span>
                    </div>

                    <div>
                        <div class="ems-stat-label">آخر دخول</div>

                        <strong>
                            {{ $employee->user->last_login_at?->format('Y-m-d H:i') ?? 'لم يسجل دخول بعد' }}
                        </strong>
                    </div>

                </div>

                <div style="margin-top:20px;">

                    <div class="ems-stat-label" style="margin-bottom:8px;">
                        الأدوار والصلاحيات
                    </div>

                    @forelse($employee->user->roles as $role)
                        <span class="ems-badge badge-gold" style="margin-left:6px;">
                            {{ $role->display_name ?? $role->name }}
                        </span>

                    @empty

                        <span style="color:var(--ems-text-muted);">
                            لا توجد أدوار مرتبطة بالحساب.
                        </span>
                    @endforelse

                </div>
            @else
                <div style="color:var(--ems-text-muted);">
                    لا يوجد حساب دخول مرتبط بهذا الموظف.
                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
    Subordinates
========================================================= --}}

    @if ($employee->subordinates->count())

        <div class="ems-card" style="margin-top:20px;">

            <div class="ems-card-header">
                <div class="ems-card-title">
                    الموظفون التابعون
                </div>

                <span class="ems-badge badge-gold">
                    {{ $employee->subordinates->count() }}
                </span>
            </div>

            <div class="ems-card-body">

                <div class="ems-table-responsive">

                    <table class="ems-table">

                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>المسمى</th>
                                <th>القسم</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($employee->subordinates as $subordinate)
                                @php
                                    $subStatus = $statusLabels[$subordinate->employment_status] ?? [
                                        'text' => $subordinate->employment_status ?? '-',
                                        'class' => '',
                                    ];
                                @endphp

                                <tr>

                                    <td>
                                        <a href="{{ route('employees.show', $subordinate) }}"
                                            style="
                                        font-weight:700;
                                        color:var(--ems-text-main);
                                   ">
                                            {{ $subordinate->full_name }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $subordinate->position ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $subordinate->department->name ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="ems-badge {{ $subStatus['class'] }}">
                                            {{ $subStatus['text'] }}
                                        </span>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
    Projects
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">

            <div class="ems-card-title">
                المشاريع التي يشارك فيها
            </div>

            <span class="ems-badge badge-gold">
                {{ $projects->count() }}
            </span>

        </div>

        <div class="ems-card-body">

            <div class="ems-table-responsive">

                <table class="ems-table">

                    <thead>
                        <tr>
                            <th>المشروع</th>
                            <th>الدائرة</th>
                            <th>المسؤول</th>
                            <th>الدور</th>
                            <th>الحالة</th>
                            <th>الإنجاز</th>
                            <th>المهام</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($projects as $project)
                            <tr>

                                <td>
                                    <strong>
                                        {{ $project->title }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $project->department->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $project->owner->full_name ?? '-' }}
                                </td>

                                <td>
                                    {{ $project->pivot->role_in_project ?? '-' }}
                                </td>

                                <td>
                                    <span class="ems-badge">
                                        {{ $projectStatusLabels[$project->status] ?? $project->status }}
                                    </span>
                                </td>

                                <td>
                                    {{ $project->completion_percentage ?? 0 }}%
                                </td>

                                <td>
                                    {{ $project->tasks_count }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7"
                                    style="
                                    text-align:center;
                                    color:var(--ems-text-muted);
                                ">
                                    لا يوجد مشاريع مرتبطة بهذا الموظف.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Managed Projects
========================================================= --}}

    @if ($managedProjects->count())

        <div class="ems-card" style="margin-top:20px;">

            <div class="ems-card-header">

                <div class="ems-card-title">
                    المشاريع التي يديرها الموظف
                </div>

                <span class="ems-badge badge-gold">
                    {{ $managedProjects->count() }}
                </span>

            </div>

            <div class="ems-card-body">

                <div class="ems-table-responsive">

                    <table class="ems-table">

                        <thead>
                            <tr>
                                <th>المشروع</th>
                                <th>الدائرة</th>
                                <th>الحالة</th>
                                <th>نسبة الإنجاز</th>
                                <th>عدد المهام</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($managedProjects as $project)
                                <tr>

                                    <td>
                                        <strong>{{ $project->title }}</strong>
                                    </td>

                                    <td>
                                        {{ $project->department->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $projectStatusLabels[$project->status] ?? $project->status }}
                                    </td>

                                    <td>
                                        {{ $project->completion_percentage ?? 0 }}%
                                    </td>

                                    <td>
                                        {{ $project->tasks_count }}
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
    Tasks
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">

            <div class="ems-card-title">
                المهام المسندة للموظف
            </div>

            <span class="ems-badge badge-gold">
                {{ $taskTotal }} مهمة
            </span>

        </div>

        <div class="ems-card-body">

            <div
                style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(140px,1fr));
            gap:10px;
            margin-bottom:20px;
        ">

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">مكتملة</span>
                        <span class="ems-stat-value">{{ $taskCompleted }}</span>
                    </div>
                </div>

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">قيد التنفيذ</span>
                        <span class="ems-stat-value">{{ $taskInProgress }}</span>
                    </div>
                </div>

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">قيد المراجعة</span>
                        <span class="ems-stat-value">{{ $taskUnderReview }}</span>
                    </div>
                </div>

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">مطلوبة</span>
                        <span class="ems-stat-value">{{ $taskTodo }}</span>
                    </div>
                </div>

            </div>

            <div class="ems-table-responsive">

                <table class="ems-table">

                    <thead>
                        <tr>
                            <th>المهمة</th>
                            <th>المشروع</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                            <th>الإنجاز</th>
                            <th>الاستحقاق</th>
                            <th>التفاعل</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($tasks as $task)
                            <tr>

                                <td>
                                    <strong>
                                        {{ $task->title }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $task->project->title ?? 'بدون مشروع' }}
                                </td>

                                <td>
                                    {{ $task->priority ?? '-' }}
                                </td>

                                <td>
                                    <span class="ems-badge">
                                        {{ $taskStatusLabels[$task->status] ?? $task->status }}
                                    </span>
                                </td>

                                <td>
                                    {{ $task->completion_percentage ?? 0 }}%
                                </td>

                                <td>
                                    {{ $task->due_datetime?->format('Y-m-d H:i') ?? '-' }}
                                </td>

                                <td>
                                    تعليقات: {{ $task->comments_count }}
                                    <br>
                                    مرفقات: {{ $task->attachments_count }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7"
                                    style="
                                    text-align:center;
                                    color:var(--ems-text-muted);
                                ">
                                    لا توجد مهام مسندة لهذا الموظف.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Attendance
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">

            <div class="ems-card-title">
                الحضور والانصراف
            </div>

            <span class="ems-badge badge-gold">
                آخر 30 سجل
            </span>

        </div>

        <div class="ems-card-body">

            <div
                style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(140px,1fr));
            gap:10px;
            margin-bottom:20px;
        ">

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">إجمالي السجلات</span>
                        <span class="ems-stat-value">{{ $attendanceTotal }}</span>
                    </div>
                </div>

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">حضور</span>
                        <span class="ems-stat-value" style="color:#10b981;">
                            {{ $attendancePresent }}
                        </span>
                    </div>
                </div>

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">غياب</span>
                        <span class="ems-stat-value">
                            {{ $attendanceAbsent }}
                        </span>
                    </div>
                </div>

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">تأخير</span>
                        <span class="ems-stat-value" style="color:#d97706;">
                            {{ $attendanceLate }}
                        </span>
                    </div>
                </div>

                <div class="ems-stat-card">
                    <div class="ems-stat-info">
                        <span class="ems-stat-label">إجازة</span>
                        <span class="ems-stat-value">
                            {{ $attendanceLeave }}
                        </span>
                    </div>
                </div>

            </div>


            <div style="margin-bottom:20px;">

                <div
                    style="
                display:flex;
                justify-content:space-between;
                margin-bottom:6px;
            ">
                    <span>نسبة الحضور</span>
                    <strong>{{ $attendancePercentage }}%</strong>
                </div>

                <div
                    style="
                height:8px;
                background:var(--ems-surface-alt);
                border-radius:10px;
                overflow:hidden;
            ">
                    <div
                        style="
                    height:100%;
                    width:{{ $attendancePercentage }}%;
                    background:#10b981;
                ">
                    </div>
                </div>

            </div>


            <div class="ems-table-responsive">

                <table class="ems-table">

                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>دخول</th>
                            <th>خروج</th>
                            <th>الدقائق</th>
                            <th>التأخير</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($attendanceRecords as $record)
                            <tr>

                                <td>
                                    {{ $record->date?->translatedFormat('d F Y') ?? '-' }}
                                </td>

                                <td>
                                    <span class="ems-badge">
                                        {{ $attendanceStatusLabels[$record->status] ?? $record->status }}
                                    </span>
                                </td>

                                <td>
                                    {{ $record->check_in_time?->format('H:i') ?? '-' }}
                                </td>

                                <td>
                                    {{ $record->check_out_time?->format('H:i') ?? '-' }}
                                </td>

                                <td>
                                    {{ $record->work_minutes ?? 0 }}
                                </td>

                                <td>
                                    {{ $record->late_minutes ?? 0 }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6"
                                    style="
                                    text-align:center;
                                    color:var(--ems-text-muted);
                                ">
                                    لا توجد سجلات حضور لهذا الموظف.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Attendance Correction Requests
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">

            <div class="ems-card-title">
                طلبات تصحيح الحضور
            </div>

            <span class="ems-badge badge-gold">
                {{ $correctionRequests->count() }}
            </span>

        </div>

        <div class="ems-card-body">

            <div class="ems-table-responsive">

                <table class="ems-table">

                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>السبب</th>
                            <th>الحالة المطلوبة</th>
                            <th>حالة الطلب</th>
                            <th>تاريخ المراجعة</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($correctionRequests as $request)
                            <tr>

                                <td>
                                    {{ $request->attendanceRecord?->date?->format('Y-m-d') ?? '-' }}
                                </td>

                                <td>
                                    {{ $request->reason ?? '-' }}
                                </td>

                                <td>
                                    {{ $attendanceStatusLabels[$request->requested_status] ?? ($request->requested_status ?? '-') }}
                                </td>

                                <td>
                                    {{ $request->status ?? '-' }}
                                </td>

                                <td>
                                    {{ $request->reviewed_at?->format('Y-m-d H:i') ?? '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5"
                                    style="
                                    text-align:center;
                                    color:var(--ems-text-muted);
                                ">
                                    لا توجد طلبات تصحيح حضور.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Performance Evaluations
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">

            <div class="ems-card-title">
                تقييمات الأداء
            </div>

            <span class="ems-badge badge-gold">
                {{ $evaluations->count() }}
            </span>

        </div>

        <div class="ems-card-body">

            <div class="ems-table-responsive">

                <table class="ems-table">

                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الفترة</th>
                            <th>المقيّم</th>
                            <th>النتيجة</th>
                            <th>نهائي</th>
                            <th>الملاحظات</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($evaluations as $evaluation)
                            <tr>

                                <td>
                                    {{ $evaluation->evaluation_date?->translatedFormat('d F Y') ?? '-' }}
                                </td>

                                <td>
                                    {{ $evaluation->period ?? '-' }}
                                </td>

                                <td>
                                    {{ $evaluation->evaluator->username ?? '-' }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $evaluation->score ?? '-' }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $evaluation->is_finalized ? 'نعم' : 'لا' }}
                                </td>

                                <td>
                                    {{ $evaluation->notes ?? '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6"
                                    style="
                                    text-align:center;
                                    color:var(--ems-text-muted);
                                ">
                                    لا توجد تقييمات أداء لهذا الموظف.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Notifications
========================================================= --}}

    <div class="ems-card" style="margin-top:20px;">

        <div class="ems-card-header">

            <div class="ems-card-title">
                آخر الإشعارات
            </div>

            <span class="ems-badge badge-gold">
                {{ $notifications->count() }}
            </span>

        </div>

        <div class="ems-card-body">

            @if ($notifications->count())

                <div
                    style="
                display:flex;
                flex-direction:column;
                gap:10px;
            ">

                    @foreach ($notifications as $notification)
                        <div
                            style="
                        padding:12px;
                        border:1px solid var(--ems-border);
                        border-radius:10px;
                    ">

                            <strong>
                                {{ $notification->title ?? ($notification->type ?? 'إشعار') }}
                            </strong>

                            @if (isset($notification->message))
                                <div
                                    style="
                                margin-top:5px;
                                color:var(--ems-text-muted);
                            ">
                                    {{ $notification->message }}
                                </div>
                            @endif

                            <div
                                style="
                            margin-top:5px;
                            font-size:12px;
                            color:var(--ems-text-muted);
                        ">
                                {{ $notification->created_at?->format('Y-m-d H:i') }}
                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <div style="color:var(--ems-text-muted);">
                    لا توجد إشعارات لهذا الموظف.
                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
    Audit Log
========================================================= --}}

    <div class="ems-card" style="margin-top:20px; margin-bottom:30px;">

        <div class="ems-card-header">

            <div class="ems-card-title">
                سجل العمليات والتدقيق
            </div>

            <span class="ems-badge badge-gold">
                آخر {{ $auditLogs->count() }} عملية
            </span>

        </div>

        <div class="ems-card-body">

            <div class="ems-table-responsive">

                <table class="ems-table">

                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>المستخدم</th>
                            <th>العملية</th>
                            <th>البيانات السابقة</th>
                            <th>البيانات الجديدة</th>
                            <th>IP</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($auditLogs as $log)
                            <tr>

                                <td>
                                    {{ $log->created_at?->format('Y-m-d H:i') ?? '-' }}
                                </td>

                                <td>
                                    {{ $log->user->username ?? 'النظام' }}
                                </td>

                                <td>
                                    {{ $log->action_type }}
                                </td>

                                <td style="max-width:250px;">

                                    @if ($log->old_value)
                                        <pre
                                            style="
                                        white-space:pre-wrap;
                                        font-size:11px;
                                        margin:0;
                                    ">{{ json_encode($log->old_value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        -
                                    @endif

                                </td>

                                <td style="max-width:250px;">

                                    @if ($log->new_value)
                                        <pre
                                            style="
                                        white-space:pre-wrap;
                                        font-size:11px;
                                        margin:0;
                                    ">{{ json_encode($log->new_value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        -
                                    @endif

                                </td>

                                <td>
                                    {{ $log->ip_address ?? '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    style="
                                    text-align:center;
                                    color:var(--ems-text-muted);
                                ">
                                    لا يوجد سجل عمليات لهذا الموظف حتى الآن.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
```
