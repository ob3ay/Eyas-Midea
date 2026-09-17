@extends('layouts.portal')

@section('title', (auth()->user()?->employee_id === $employee->id ? 'ملفي الشخصي' : 'ملف الموظف') . ' - ' . $employee->full_name)

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

        $initial = mb_substr($employee->full_name ?? '?', 0, 1);
        $isMyProfile = auth()->user()?->employee_id === $employee->id;
        $canViewOtherEmployees = auth()->user()?->hasPermission('employees.view') && auth()->user()?->highestScopeFor('employees.view') !== 'self';
    @endphp


    {{-- =========================================================
    Header
========================================================= --}}

    <div class="ems-page-header">

        <div class="ems-page-title-wrap">

            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>

                @if(!$isMyProfile && $canViewOtherEmployees)
                    <span class="ems-breadcrumb-separator">/</span>
                    <a href="{{ route('employees.index') }}">الموارد البشرية</a>
                @endif

                <span class="ems-breadcrumb-separator">/</span>

                <span style="color: var(--ems-text-main); font-weight: 700;">
                    {{ $isMyProfile ? 'ملفي الشخصي' : 'ملف الموظف' }}
                </span>
            </div>

            <h1 class="ems-page-title">
                {{ $isMyProfile ? 'ملفي الشخصي' : 'ملف الموظف' }}
            </h1>

            <p class="ems-page-subtitle">
                {{ $isMyProfile ? 'البيانات الشخصية والوظيفية وتفاصيل الحساب المسجلة بالنظام' : 'الملف الإداري والوظيفي للموظف' }}
            </p>

        </div>

        <div class="ems-page-actions">

            @if(!$isMyProfile && $canViewOtherEmployees)
                <a href="{{ route('employees.index') }}" class="ems-btn ems-btn-secondary">
                    ← العودة للموظفين
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="ems-btn ems-btn-secondary">
                    ← العودة للرئيسية
                </a>
            @endif

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
    Profile Summary Stats Grid
========================================================= --}}

    <div class="ems-stats-grid">

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الدائرة</span>
                <span class="ems-stat-value" style="font-size: 1.15rem; color: var(--ems-text-main);">
                    {{ $employee->department->name ?? '-' }}
                </span>
                <span class="ems-stat-subtext">
                    {{ $employee->section->name ?? 'القسم العام' }}
                </span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الحالة الوظيفية</span>
                <span class="ems-stat-value" style="font-size: 1.15rem; color: #10b981;">
                    {{ $status['text'] }}
                </span>
                <span class="ems-stat-subtext">
                    {{ $employee->employment_type ?? 'دوام كامل' }}
                </span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">المدير المباشر</span>
                <span class="ems-stat-value" style="font-size: 1.15rem; color: var(--ems-gold-hover);">
                    {{ $employee->manager?->full_name ?? 'الإدارة العليا' }}
                </span>
                <span class="ems-stat-subtext">
                    المسؤول المباشر
                </span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">تقييم الأداء</span>
                <span class="ems-stat-value" style="color:#c5a059;">
                    {{ $averageScore !== null ? number_format($averageScore, 1) . '%' : 'قيد التقييم' }}
                </span>
                <span class="ems-stat-subtext">
                    التقييم العام المعتمد
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
                    <div class="ems-stat-label">الرمز الوظيفي</div>
                    <strong style="font-family:var(--font-mono); color:var(--ems-gold-hover);">
                        {{ $employee->employee_code ?? 'EMP-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
                    </strong>
                </div>

            </div>

            <div class="ems-form-row" style="margin-top:20px;">

                <div>
                    <div class="ems-stat-label">رقم الهوية / الإقامة</div>
                    <strong>{{ $employee->national_id ?? '-' }}</strong>
                </div>

                <div>
                    <div class="ems-stat-label">رقم الهاتف</div>
                    <strong>{{ $employee->phone ?? '-' }}</strong>
                </div>

            </div>

            <div class="ems-form-row" style="margin-top:20px;">

                <div>
                    <div class="ems-stat-label">المسمى الوظيفي</div>
                    <strong>{{ $employee->position ?? '-' }}</strong>
                </div>

                <div>
                    <div class="ems-stat-label">نوع التوظيف</div>
                    <strong>{{ $employee->employment_type ?? 'دوام كامل' }}</strong>
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
                        <span style="font-weight:700; color:var(--ems-gold-hover);">
                            {{ $employee->manager->full_name }}
                        </span>
                    @else
                        <strong>-</strong>
                    @endif

                </div>

                <div>
                    <div class="ems-stat-label">تاريخ التعيين</div>
                    <strong>
                        {{ $employee->hire_date?->translatedFormat('d F Y') ?? '-' }}
                    </strong>
                </div>

            </div>

            <div class="ems-form-row" style="margin-top:20px;">

                <div>
                    <div class="ems-stat-label">تاريخ نهاية العقد</div>
                    <strong>
                        {{ $employee->contract_end_date?->translatedFormat('d F Y') ?? 'عقد غير محدد المدة' }}
                    </strong>
                </div>

                <div>
                    <div class="ems-stat-label">البريد الإلكتروني للعمل</div>
                    <strong>{{ $employee->email ?? ($employee->user?->email ?? '-') }}</strong>
                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    Login Account
========================================================= --}}

    <div class="ems-card" style="margin-top:20px; margin-bottom: 20px;">

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
                        <div class="ems-stat-label">آخر تسجيل دخول</div>

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
    Subordinates (Only if the employee is a manager/supervisor)
========================================================= --}}

    @if ($employee->subordinates->count())

        <div class="ems-card" style="margin-top:20px; margin-bottom:30px;">

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
                                        @if(!$isMyProfile && $canViewOtherEmployees)
                                            <a href="{{ route('employees.show', $subordinate) }}"
                                                style="
                                            font-weight:700;
                                            color:var(--ems-text-main);
                                       ">
                                                {{ $subordinate->full_name }}
                                            </a>
                                        @else
                                            <strong>{{ $subordinate->full_name }}</strong>
                                        @endif
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
    Projects (المشاريع المرتبطة بالموظف)
    ========================================================= --}}
    @php
        $allEmployeeProjects = $managedProjects->merge($projects)->unique('id');
        $projStatusLabels = [
            'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'badge-gold'],
            'in_review' => ['label' => 'قيد المراجعة', 'class' => 'badge-review'],
            'completed' => ['label' => 'مكتمل', 'class' => 'badge-completed'],
            'on_hold' => ['label' => 'متوقف', 'class' => 'badge-danger'],
        ];
    @endphp

    <div class="ems-card" style="margin-top:20px; margin-bottom:20px;">
        <div class="ems-card-header">
            <div class="ems-card-title">
                المشاريع المرتبطة بالموظف
            </div>
            <span class="ems-badge badge-gold">
                {{ $allEmployeeProjects->count() }} مشروع/مشاريع
            </span>
        </div>

        <div class="ems-card-body" style="padding: 0;">
            @if ($allEmployeeProjects->count() > 0)
                <div class="ems-table-responsive">
                    <table class="ems-table">
                        <thead>
                            <tr>
                                <th>المشروع</th>
                                <th>دور الموظف</th>
                                <th>الدائرة</th>
                                <th style="min-width: 140px;">نسبة الإنجاز</th>
                                <th>الحالة</th>
                                <th>الفترة الزمنية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allEmployeeProjects as $project)
                                @php
                                    $isOwner = $project->owner_employee_id === $employee->id;
                                    $pjSt = $projStatusLabels[$project->status] ?? ['label' => $project->status, 'class' => ''];
                                    $progress = (float) ($project->completion_percentage ?? 0);
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--ems-text-main);">
                                            {{ $project->title }}
                                        </div>
                                        @if ($project->description)
                                            <div style="font-size: 11.5px; color: var(--ems-text-muted); margin-top: 3px;">
                                                {{ Str::limit($project->description, 60) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($isOwner)
                                            <span class="ems-badge badge-gold" style="font-size: 11px;">مسؤول المشروع</span>
                                        @else
                                            <span class="ems-badge badge-draft" style="font-size: 11px;">عضو فريق العمل</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $project->department->name ?? '-' }}
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="ems-progress" style="height: 6px; flex-grow: 1;">
                                                <div class="ems-progress-bar {{ $progress >= 100 ? 'success' : '' }}"
                                                    style="width: {{ min(100, $progress) }}%;"></div>
                                            </div>
                                            <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono); {{ $progress >= 100 ? 'color: #10b981;' : '' }}">
                                                {{ number_format($progress, 0) }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ems-badge {{ $pjSt['class'] }}">
                                            {{ $pjSt['label'] }}
                                        </span>
                                    </td>
                                    <td style="font-size: 12px; color: var(--ems-text-muted);">
                                        {{ $project->start_date?->format('d/m/Y') ?? '-' }}
                                        إلى
                                        {{ $project->end_date?->format('d/m/Y') ?? 'مفتوح' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 24px; color: var(--ems-text-muted); font-size: 13px;">
                    لا توجد مشاريع مسندة لهذا الموظف حالياً.
                </div>
            @endif
        </div>
    </div>

    {{-- =========================================================
    Tasks (المهام المسندة ومتابعة الإنجاز)
    ========================================================= --}}
    @php
        $taskStatusMap = [
            'Draft' => ['label' => 'مسودة', 'class' => 'badge-draft'],
            'To-Do' => ['label' => 'قيد الانتظار', 'class' => 'badge-danger'],
            'In Progress' => ['label' => 'قيد التنفيذ', 'class' => 'badge-progress'],
            'Under Review' => ['label' => 'قيد المراجعة', 'class' => 'badge-review'],
            'Completed' => ['label' => 'مكتملة', 'class' => 'badge-completed'],
            'Rejected' => ['label' => 'مرفوضة', 'class' => 'badge-danger'],
        ];
        $taskPriorityMap = [
            'Low' => ['label' => 'منخفضة', 'class' => 'badge-completed'],
            'Medium' => ['label' => 'متوسطة', 'class' => 'badge-gold'],
            'High' => ['label' => 'عالية', 'class' => 'badge-danger'],
            'Urgent' => ['label' => 'عاجلة', 'class' => 'badge-danger'],
        ];
    @endphp

    <div class="ems-card" style="margin-top:20px; margin-bottom:30px;">
        <div class="ems-card-header" style="flex-wrap: wrap; gap: 10px;">
            <div>
                <div class="ems-card-title">
                    المهام المسندة ومتابعة الإنجاز
                </div>
                <div style="font-size: 12px; color: var(--ems-text-muted); margin-top: 3px;">
                    إجمالي المهام المسجلة: <strong>{{ $taskTotal }}</strong> | مكتملة: <strong style="color: #10b981;">{{ $taskCompleted }}</strong> | قيد التنفيذ: <strong style="color: #d97706;">{{ $taskInProgress }}</strong> | مراجعة: <strong style="color: #6d28d9;">{{ $taskUnderReview }}</strong>
                </div>
            </div>
        </div>

        <div class="ems-card-body" style="padding: 0;">
            @if ($tasks->count() > 0)
                <div class="ems-table-responsive">
                    <table class="ems-table">
                        <thead>
                            <tr>
                                <th>المهمة</th>
                                <th>المشروع</th>
                                <th>المكلف بالتنفيذ</th>
                                <th>الأولوية</th>
                                <th>الحالة</th>
                                <th style="min-width: 120px;">نسبة الإنجاز</th>
                                <th>تاريخ الاستحقاق</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                @php
                                    $st = $taskStatusMap[$task->status] ?? ['label' => $task->status, 'class' => ''];
                                    $pr = $taskPriorityMap[$task->priority] ?? ['label' => $task->priority, 'class' => ''];
                                    $tProgress = (float) ($task->completion_percentage ?? 0);
                                    $isDirectAssignee = $task->assignee_employee_id === $employee->id;
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--ems-text-main);">
                                            {{ $task->title }}
                                        </div>
                                        @if ($task->description)
                                            <div style="font-size: 11.5px; color: var(--ems-text-muted); margin-top: 3px;">
                                                {{ Str::limit($task->description, 65) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($task->project)
                                            <span style="font-size: 12px; color: var(--ems-text-main); font-weight: 600;">
                                                {{ $task->project->title }}
                                            </span>
                                        @else
                                            <span style="font-size: 12px; color: var(--ems-text-muted);">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <span style="font-size: 12px; font-weight: 600; color: var(--ems-text-main);">
                                                {{ $task->assignee?->full_name ?? 'غير مسند' }}
                                            </span>
                                            @if ($isDirectAssignee)
                                                <span class="ems-badge badge-gold" style="font-size: 10px; padding: 2px 6px;">مهمته</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ems-badge {{ $pr['class'] }}" style="font-size: 11px;">
                                            {{ $pr['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="ems-badge {{ $st['class'] }}" style="font-size: 11px;">
                                            {{ $st['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="ems-progress" style="height: 5px; flex-grow: 1;">
                                                <div class="ems-progress-bar {{ $tProgress >= 100 ? 'success' : '' }}"
                                                    style="width: {{ min(100, $tProgress) }}%;"></div>
                                            </div>
                                            <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">
                                                {{ round($tProgress) }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td style="font-size: 12px; color: var(--ems-text-muted);">
                                        {{ $task->due_datetime ? $task->due_datetime->format('Y-m-d') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 24px; color: var(--ems-text-muted); font-size: 13px;">
                    لا توجد مهام مسندة لهذا الموظف حالياً.
                </div>
            @endif
        </div>
    </div>
@endsection
