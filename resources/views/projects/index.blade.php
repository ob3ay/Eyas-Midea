@extends('layouts.portal')

@section('title', 'المشاريع والإنتاج')

@section('content')

    <div class="ems-content">

        {{-- Page Header --}}
        <div class="ems-page-header">
            <div class="ems-page-title-wrap">

                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span>إدارة الإنتاج</span>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span style="color: var(--ems-text-main); font-weight: 700;">
                        المشاريع والإنتاج
                    </span>
                </div>

                <h1 class="ems-page-title">
                    إدارة المشاريع والإنتاج
                </h1>

                <p class="ems-page-subtitle">
                    متابعة خطط التنفيذ ونسب الإنجاز ومواعيد تسليم مشاريع مجموعة إياس الإعلامية
                </p>

            </div>

            <div class="ems-page-actions">
                @if ($canCreate)
                    <button type="button" class="ems-btn ems-btn-primary" onclick="openAddProjectModal()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>

                        <span>مشروع جديد</span>
                    </button>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block; padding:10px 14px; margin-bottom:14px;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="ems-badge"
                style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c;">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="ems-badge"
                style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif


        {{-- Statistics --}}
        <div class="ems-stats-grid">

            {{-- Total --}}
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">إجمالي المشاريع</span>

                    <span class="ems-stat-value">
                        {{ $stats['total'] ?? 0 }}
                    </span>

                    <span class="ems-stat-subtext">
                        جميع المشاريع المسجلة
                    </span>
                </div>

                <div class="ems-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
                    </svg>
                </div>
            </div>


            {{-- In Progress --}}
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">
                        قيد التنفيذ
                    </span>

                    <span class="ems-stat-value" style="color: #d97706;">
                        {{ $stats['in_progress'] ?? 0 }}
                    </span>

                    <span class="ems-stat-subtext">
                        مشاريع قيد العمل
                    </span>
                </div>

                <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>


            {{-- Review --}}
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">
                        قيد المراجعة
                    </span>

                    <span class="ems-stat-value" style="color: #6d28d9;">
                        {{ $stats['in_review'] ?? 0 }}
                    </span>

                    <span class="ems-stat-subtext">
                        مشاريع بانتظار الاعتماد
                    </span>
                </div>

                <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
            </div>


            {{-- Completed --}}
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">
                        مكتملة
                    </span>

                    <span class="ems-stat-value" style="color: #10b981;">
                        {{ $stats['completed'] ?? 0 }}
                    </span>

                    <span class="ems-stat-subtext">
                        مشاريع تم إنجازها
                    </span>
                </div>

                <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5"></path>
                    </svg>
                </div>
            </div>

        </div>


        {{-- Projects Table --}}
        <div class="ems-card">

            <div class="ems-card-header">

                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path
                            d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
                    </svg>

                    <span>
                        سجل المشاريع
                    </span>
                </div>

                <span class="ems-badge badge-gold">
                    متوسط الإنجاز الكلي:
                    {{ number_format($averageProgress ?? 0, 0) }}%
                </span>

            </div>


            <div class="ems-card-body" style="padding: 0;">

                <div class="ems-table-responsive">

                    <table class="ems-table">

                        <thead>
                            <tr>
                                <th>المشروع</th>
                                <th>الدائرة</th>
                                <th>المسؤول</th>
                                <th style="min-width: 160px;">
                                    نسبة الإنجاز
                                </th>
                                <th>بداية المشروع</th>
                                <th>موعد الانتهاء</th>
                                <th>الحالة</th>
                                <th style="text-align: center;">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($projects as $project)
                                @php
                                    $progress = (float) ($project->completion_percentage ?? 0);

                                    $statusLabels = [
                                        'draft' => 'مسودة',
                                        'in_progress' => 'قيد التنفيذ',
                                        'in_review' => 'قيد المراجعة',
                                        'completed' => 'مكتمل',
                                        'on_hold' => 'متوقف',
                                        'cancelled' => 'ملغي',
                                    ];

                                    $statusClasses = [
                                        'draft' => 'badge-secondary',
                                        'in_progress' => 'badge-warning',
                                        'in_review' => 'badge-purple',
                                        'completed' => 'badge-success',
                                        'on_hold' => 'badge-danger',
                                        'cancelled' => 'badge-danger',
                                    ];

                                    $statusLabel = $statusLabels[$project->status] ?? $project->status;
                                    $statusClass = $statusClasses[$project->status] ?? '';
                                @endphp

                                <tr>

                                    {{-- Project --}}
                                    <td>

                                        <div style="font-weight: 800; color: var(--ems-text-main);">
                                            {{ $project->title }}
                                        </div>

                                        @if ($project->description)
                                            <div
                                                style="
                                                font-size: 11.5px;
                                                color: var(--ems-text-muted);
                                                margin-top: 4px;
                                            ">
                                                {{ Str::limit($project->description, 80) }}
                                            </div>
                                        @endif

                                        @if ($project->attachments->count() > 0)
                                            <div
                                                style="margin-top: 6px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                                <span class="ems-badge badge-gold"
                                                    style="font-size: 11px; padding: 2px 8px; display: inline-flex; align-items: center; gap: 4px;"
                                                    title="{{ $project->attachments->pluck('file_name')->implode('، ') }}">
                                                    <svg viewBox="0 0 24 24" width="12" height="12"
                                                        fill="none" stroke="currentColor" stroke-width="2">
                                                        <path
                                                            d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                                    </svg>
                                                    {{ $project->attachments->count() }} مرفق
                                                </span>
                                            </div>
                                        @endif

                                    </td>


                                    {{-- Department --}}
                                    <td>
                                        {{ $project->department?->name ?? 'غير محدد' }}
                                    </td>


                                    {{-- Owner --}}
                                    <td>
                                        {{ $project->owner?->full_name ?? 'غير معين' }}
                                    </td>


                                    {{-- Progress --}}
                                    <td>

                                        <div
                                            style="
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                        ">

                                            <div class="ems-progress"
                                                style="
                                                height: 6px;
                                                flex-grow: 1;
                                            ">
                                                <div class="ems-progress-bar {{ $progress >= 100 ? 'success' : '' }}"
                                                    style="width: {{ min(100, $progress) }}%;"></div>
                                            </div>

                                            <span
                                                style="
                                                font-size: 11px;
                                                font-weight: 700;
                                                font-family: var(--font-mono);
                                                {{ $progress >= 100 ? 'color: #10b981;' : '' }}
                                            ">
                                                {{ number_format($progress, 0) }}%
                                            </span>

                                        </div>

                                    </td>


                                    {{-- Start Date --}}
                                    <td>
                                        {{ $project->start_date?->format('d/m/Y') ?? 'غير محدد' }}
                                    </td>


                                    {{-- End Date --}}
                                    <td>
                                        {{ $project->end_date?->format('d/m/Y') ?? 'غير محدد' }}
                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        <span class="ems-badge {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td style="text-align: center;">

                                        <div
                                            style="
                                            display: flex;
                                            justify-content: center;
                                            gap: 4px;
                                        ">

                                            {{-- View Tasks Accordion --}}
                                            <button type="button" class="ems-btn ems-btn-icon"
                                                title="عرض مهام المشروع ({{ $project->tasks->count() }})"
                                                onclick="toggleProjectTasks({{ $project->id }})"
                                                id="btn-tasks-{{ $project->id }}" style="transition: all .2s ease;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>


                                            {{-- Edit --}}
                                            @if ($canUpdate)
                                                <button type="button" class="ems-btn ems-btn-icon" title="تعديل"
                                                    onclick="openEditProjectModal({{ $project->id }})">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <path d="M12 20h9"></path>
                                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                    </svg>
                                                </button>
                                            @endif


                                            {{-- Delete --}}
                                            @if ($canDelete)
                                                <form action="{{ route('projects.destroy', $project->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا المشروع؟');"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="ems-btn ems-btn-icon" title="حذف"
                                                        style="color: #ef4444;">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path
                                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                            </path>
                                                        </svg>
                                                    </button>

                                                </form>
                                            @endif

                                        </div>

                                    </td>

                                </tr>

                                {{-- Project Tasks Accordion Row --}}
                                <tr id="project-tasks-row-{{ $project->id }}" class="project-tasks-row"
                                    style="display: none; background: #fdfbf7;">
                                    <td colspan="8"
                                        style="padding: 16px 20px; border-bottom: 2px solid var(--ems-gold-light, #e2d2b4);">
                                        <div
                                            style="background: var(--ems-surface, #ffffff); border: 1px solid var(--ems-border, #e5e7eb); border-radius: 12px; padding: 18px 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                                            <div
                                                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div
                                                        style="width: 32px; height: 32px; border-radius: 8px; background: rgba(197, 160, 89, 0.15); display: flex; align-items: center; justify-content: center; color: var(--ems-gold-hover, #a8823b);">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" width="18" height="18">
                                                            <rect width="18" height="18" x="3" y="3"
                                                                rx="2"></rect>
                                                            <path d="m9 12 2 2 4-4"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            style="margin: 0; font-size: 15px; font-weight: 700; color: var(--ems-text-main);">
                                                            مهام مشروع: {{ $project->title }}
                                                        </h4>
                                                        <span style="font-size: 12px; color: var(--ems-text-muted);">
                                                            إجمالي المهام المسجلة:
                                                            <strong>{{ $project->tasks->count() }}</strong> مهمة
                                                        </span>
                                                    </div>
                                                </div>

                                                <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
                                                    class="ems-btn ems-btn-sm ems-btn-secondary"
                                                    style="font-size: 12px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;">
                                                    <span>فتح في لوحة المهام</span>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" width="14" height="14">
                                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6">
                                                        </path>
                                                        <polyline points="15 3 21 3 21 9"></polyline>
                                                        <line x1="10" y1="14" x2="21"
                                                            y2="3"></line>
                                                    </svg>
                                                </a>
                                            </div>

                                            @if ($project->tasks->count() > 0)
                                                <div class="ems-table-responsive" style="margin: 0;">
                                                    <table class="ems-table" style="font-size: 13px; margin: 0;">
                                                        <thead>
                                                            <tr style="background: var(--ems-surface-alt, #f9fafb);">
                                                                <th style="padding: 8px 12px;">المهمة</th>
                                                                <th style="padding: 8px 12px;">المسؤول</th>
                                                                <th style="padding: 8px 12px;">الأولوية</th>
                                                                <th style="padding: 8px 12px;">الحالة</th>
                                                                <th style="padding: 8px 12px;">الإنجاز</th>
                                                                <th style="padding: 8px 12px;">تاريخ الاستحقاق</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $taskStatusMap = [
                                                                    'Draft' => [
                                                                        'label' => 'مسودة',
                                                                        'class' => 'badge-draft',
                                                                    ],
                                                                    'To-Do' => [
                                                                        'label' => 'قيد الانتظار',
                                                                        'class' => 'badge-danger',
                                                                    ],
                                                                    'In Progress' => [
                                                                        'label' => 'قيد التنفيذ',
                                                                        'class' => 'badge-progress',
                                                                    ],
                                                                    'Under Review' => [
                                                                        'label' => 'قيد المراجعة',
                                                                        'class' => 'badge-review',
                                                                    ],
                                                                    'Completed' => [
                                                                        'label' => 'مكتملة',
                                                                        'class' => 'badge-completed',
                                                                    ],
                                                                    'Rejected' => [
                                                                        'label' => 'مرفوضة',
                                                                        'class' => 'badge-danger',
                                                                    ],
                                                                ];
                                                                $taskPriorityMap = [
                                                                    'Low' => [
                                                                        'label' => 'منخفضة',
                                                                        'class' => 'badge-completed',
                                                                    ],
                                                                    'Medium' => [
                                                                        'label' => 'متوسطة',
                                                                        'class' => 'badge-gold',
                                                                    ],
                                                                    'High' => [
                                                                        'label' => 'عالية',
                                                                        'class' => 'badge-danger',
                                                                    ],
                                                                    'Urgent' => [
                                                                        'label' => 'عاجلة',
                                                                        'class' => 'badge-danger',
                                                                    ],
                                                                ];
                                                            @endphp
                                                            @foreach ($project->tasks as $task)
                                                                @php
                                                                    $st = $taskStatusMap[$task->status] ?? [
                                                                        'label' => $task->status,
                                                                        'class' => '',
                                                                    ];
                                                                    $pr = $taskPriorityMap[$task->priority] ?? [
                                                                        'label' => $task->priority,
                                                                        'class' => '',
                                                                    ];
                                                                    $isMyTask =
                                                                        auth()->user()?->employee_id ===
                                                                        $task->assignee_employee_id;
                                                                @endphp
                                                                <tr
                                                                    style="{{ $isMyTask ? 'background: rgba(197, 160, 89, 0.05);' : '' }}">
                                                                    <td style="padding: 10px 12px;">
                                                                        <div
                                                                            style="font-weight: 700; color: var(--ems-text-main);">
                                                                            {{ $task->title }}
                                                                            @if ($isMyTask)
                                                                                <span class="ems-badge badge-gold"
                                                                                    style="font-size: 10px; padding: 2px 6px; margin-right: 6px;">مهمتي</span>
                                                                            @endif
                                                                        </div>
                                                                        @if ($task->description)
                                                                            <div
                                                                                style="font-size: 11px; color: var(--ems-text-muted); margin-top: 3px;">
                                                                                {{ Str::limit($task->description, 65) }}
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td
                                                                        style="padding: 10px 12px; color: var(--ems-text-main);">
                                                                        {{ $task->assignee?->full_name ?? 'غير مسند' }}
                                                                    </td>
                                                                    <td style="padding: 10px 12px;">
                                                                        <span class="ems-badge {{ $pr['class'] }}"
                                                                            style="font-size: 11px;">
                                                                            {{ $pr['label'] }}
                                                                        </span>
                                                                    </td>
                                                                    <td style="padding: 10px 12px;">
                                                                        <span class="ems-badge {{ $st['class'] }}"
                                                                            style="font-size: 11px;">
                                                                            {{ $st['label'] }}
                                                                        </span>
                                                                    </td>
                                                                    <td style="padding: 10px 12px;">
                                                                        <div
                                                                            style="display: flex; align-items: center; gap: 8px; min-width: 90px;">
                                                                            <div class="ems-progress"
                                                                                style="height: 5px; flex-grow: 1;">
                                                                                <div class="ems-progress-bar {{ $task->completion_percentage >= 100 ? 'success' : '' }}"
                                                                                    style="width: {{ min(100, $task->completion_percentage ?? 0) }}%;">
                                                                                </div>
                                                                            </div>
                                                                            <span
                                                                                style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">
                                                                                {{ round($task->completion_percentage ?? 0) }}%
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td
                                                                        style="padding: 10px 12px; font-size: 12px; color: var(--ems-text-muted);">
                                                                        {{ $task->due_datetime ? $task->due_datetime->format('Y-m-d') : '-' }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div
                                                    style="text-align: center; padding: 20px; color: var(--ems-text-muted); font-size: 13px;">
                                                    لا توجد مهام مسندة لهذا المشروع حالياً.
                                                </div>
                                            @endif

                                            {{-- Project Attachments Section --}}
                                            <div
                                                style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed var(--ems-border, #e5e7eb);">
                                                <div
                                                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                                    <span
                                                        style="font-size: 13.5px; font-weight: 700; color: var(--ems-text-main); display: inline-flex; align-items: center; gap: 6px;">
                                                        <svg viewBox="0 0 24 24" width="16" height="16"
                                                            fill="none" stroke="var(--ems-gold-hover, #a8823b)"
                                                            stroke-width="2">
                                                            <path
                                                                d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                                        </svg>
                                                        مرفقات المشروع ({{ $project->attachments->count() }})
                                                    </span>
                                                </div>

                                                @if ($project->attachments->count() > 0)
                                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                        @foreach ($project->attachments as $att)
                                                            <div
                                                                style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border, #e2e8f0); border-radius: 8px; padding: 8px 14px; display: inline-flex; align-items: center; gap: 10px;">
                                                                <svg viewBox="0 0 24 24" width="18" height="18"
                                                                    fill="none" stroke="var(--ems-gold-hover, #a8823b)"
                                                                    stroke-width="2">
                                                                    <path
                                                                        d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                                    </path>
                                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                                </svg>
                                                                <div>
                                                                    <div
                                                                        style="font-size: 12.5px; font-weight: 600; color: var(--ems-text-main);">
                                                                        {{ $att->file_name }}</div>
                                                                    <div
                                                                        style="font-size: 11px; color: var(--ems-text-muted);">
                                                                        {{ $att->formatted_size }} &bull;
                                                                        {{ $att->created_at->format('Y-m-d') }}</div>
                                                                </div>
                                                                <a href="{{ route('projects.attachments.download', $att) }}"
                                                                    class="ems-btn ems-btn-sm ems-btn-secondary"
                                                                    style="padding: 4px 10px; font-size: 11.5px; margin-right: 6px; text-decoration: none;"
                                                                    title="تحميل الملف">
                                                                    تحميل ⬇
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div style="font-size: 12px; color: var(--ems-text-muted);">
                                                        لا توجد ملفات مرفقة لهذا المشروع.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8"
                                        style="
                                        text-align: center;
                                        padding: 30px;
                                        color: var(--ems-text-muted);
                                    ">
                                        لا توجد مشاريع مسجلة حالياً.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="ems-card-body" style="padding-top: 0;">
                {{ $projects->links() }}
            </div>

        </div>

    </div>


    @if ($canCreate)
        {{-- Add Project Modal --}}
        <div class="ems-modal-overlay" id="modal-add-project">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">إضافة مشروع جديد</div>
                    <button type="button" onclick="closeModal('modal-add-project')" class="ems-modal-close">✕</button>
                </div>

                <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="ems-modal-body">
                        <div class="ems-form-group">
                            <label class="ems-label"><span class="required">*</span> اسم المشروع</label>
                            <input type="text" name="title" class="ems-input" required
                                placeholder="أدخل اسم المشروع">
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label">الوصف</label>
                            <textarea name="description" class="ems-input" rows="3" placeholder="تفاصيل وأهداف المشروع"></textarea>
                        </div>

                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الدائرة المسؤولة عن
                                    المشروع</label>
                                <select name="department_id" id="add_department_id" class="ems-input" required
                                    onchange="handleDepartmentChange(this.value, 'add')">
                                    <option value="">-- اختر الدائرة المسؤولة --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            data-manager-id="{{ $department->manager_employee_id ?? '' }}"
                                            data-manager-name="{{ $department->manager?->full_name ?? '' }}"
                                            {{ $departments->count() === 1 ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="ems-form-group">
                                <label class="ems-label">المسؤول عن المشروع</label>
                                <select name="owner_employee_id" id="add_owner_employee_id" class="ems-input">
                                    <option value="">اختر المسؤول</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            data-department-id="{{ $employee->department_id ?? '' }}">
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="add_manager_hint"
                                    style="font-size: 11.5px; color: #15803d; margin-top: 5px; display: none;"></div>
                            </div>
                        </div>

                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> تاريخ البداية</label>
                                <input type="date" name="start_date" class="ems-input" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>

                            <div class="ems-form-group">
                                <label class="ems-label">تاريخ الانتهاء المتوقع</label>
                                <input type="date" name="end_date" class="ems-input">
                            </div>
                        </div>

                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الحالة</label>
                                <select name="status" class="ems-input" required>
                                    <option value="draft">مسودة</option>
                                    <option value="in_progress">قيد التنفيذ</option>
                                    <option value="in_review">قيد المراجعة</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="on_hold">متوقف</option>
                                    <option value="cancelled">ملغي</option>
                                </select>
                            </div>

                            <div class="ems-form-group">
                                <label class="ems-label">نسبة الإنجاز (%)</label>
                                <input type="number" name="completion_percentage" class="ems-input" min="0"
                                    max="100" value="0">
                            </div>
                        </div>

                        {{-- مرفقات المشروع (اختياري) --}}
                        <div class="ems-form-group">
                            <label class="ems-label">
                                مرفقات المشروع
                                <span
                                    style="font-size: 11px; font-weight: normal; color: var(--ems-text-muted); margin-right: 6px;">(اختياري
                                    — ملفات، مستندات، عروض، صور — حتى 20MB لكل ملف)</span>
                            </label>
                            <div class="ems-dropzone"
                                style="border: 2px dashed var(--ems-border, #cbd5e1); border-radius: 10px; padding: 18px; text-align: center; cursor: pointer; transition: all 0.2s;"
                                onclick="document.getElementById('add_project_attachments').click()"
                                id="addProjectDropzone"
                                ondragover="event.preventDefault(); this.style.borderColor='var(--ems-gold, #c5a059)'; this.style.background='rgba(197, 160, 89, 0.05)';"
                                ondragleave="this.style.borderColor=''; this.style.background='';"
                                ondrop="handleProjectFilesDrop(event, 'add')">
                                <svg viewBox="0 0 24 24" width="30" height="30" fill="none"
                                    stroke="var(--ems-text-muted)" stroke-width="1.6"
                                    style="margin: 0 auto 6px; display:block;">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                <div style="font-size: 13px; font-weight: 600; color: var(--ems-text-main);">
                                    اضغط لرفع المرفقات أو اسحب الملفات هنا
                                </div>
                                <span
                                    style="font-size: 11.5px; color: var(--ems-text-muted); display: block; margin-top: 4px;">
                                    يمكن اختيار أكثر من ملف (PDF, Word, Excel, صور, ZIP)
                                </span>
                            </div>
                            <input type="file" id="add_project_attachments" name="attachments[]" multiple
                                style="display: none;" onchange="handleProjectFilesSelect(this, 'add')">
                            <div id="add_project_files_list"
                                style="margin-top: 10px; display: flex; flex-direction: column; gap: 6px;"></div>
                        </div>
                    </div>

                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary"
                            onclick="closeModal('modal-add-project')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ المشروع</button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    @if ($canUpdate)
        {{-- Edit Project Modal --}}
        <div class="ems-modal-overlay" id="modal-edit-project">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">تعديل بيانات المشروع</div>
                    <button type="button" onclick="closeModal('modal-edit-project')" class="ems-modal-close">✕</button>
                </div>

                <form id="editProjectForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="ems-modal-body">
                        <div class="ems-form-group">
                            <label class="ems-label"><span class="required">*</span> اسم المشروع</label>
                            <input type="text" id="edit_title" name="title" class="ems-input" required>
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label">الوصف</label>
                            <textarea id="edit_description" name="description" class="ems-input" rows="3"></textarea>
                        </div>

                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الدائرة المسؤولة عن
                                    المشروع</label>
                                <select id="edit_department_id" name="department_id" class="ems-input" required
                                    onchange="handleDepartmentChange(this.value, 'edit')">
                                    <option value="">-- اختر الدائرة المسؤولة --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            data-manager-id="{{ $department->manager_employee_id ?? '' }}"
                                            data-manager-name="{{ $department->manager?->full_name ?? '' }}">
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="ems-form-group">
                                <label class="ems-label">المسؤول عن المشروع</label>
                                <select id="edit_owner_employee_id" name="owner_employee_id" class="ems-input">
                                    <option value="">اختر المسؤول</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                            data-department-id="{{ $employee->department_id ?? '' }}">
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="edit_manager_hint"
                                    style="font-size: 11.5px; color: #15803d; margin-top: 5px; display: none;"></div>
                            </div>
                        </div>

                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> تاريخ البداية</label>
                                <input type="date" id="edit_start_date" name="start_date" class="ems-input" required>
                            </div>

                            <div class="ems-form-group">
                                <label class="ems-label">تاريخ الانتهاء</label>
                                <input type="date" id="edit_end_date" name="end_date" class="ems-input">
                            </div>
                        </div>

                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label">تاريخ الانتهاء الفعلي</label>
                                <input type="date" id="edit_actual_end_date" name="actual_end_date"
                                    class="ems-input">
                            </div>

                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الحالة</label>
                                <select id="edit_status" name="status" class="ems-input" required>
                                    <option value="draft">مسودة</option>
                                    <option value="in_progress">قيد التنفيذ</option>
                                    <option value="in_review">قيد المراجعة</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="on_hold">متوقف</option>
                                    <option value="cancelled">ملغي</option>
                                </select>
                            </div>
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label">نسبة الإنجاز (%)</label>
                            <input type="number" id="edit_completion_percentage" name="completion_percentage"
                                class="ems-input" min="0" max="100" step="0.01" required>
                        </div>

                        {{-- المرفقات الحالية للمشروع --}}
                        <div class="ems-form-group">
                            <label class="ems-label">المرفقات الحالية للمشروع</label>
                            <div id="edit_project_existing_attachments"
                                style="border: 1px solid var(--ems-border, #e5e7eb); border-radius: 8px; padding: 10px; min-height: 44px; background: var(--ems-surface-alt, #fafafa);">
                                <div
                                    style="font-size: 12px; color: var(--ems-text-muted); text-align: center; padding: 6px;">
                                    لا توجد مرفقات سابقة لهذا المشروع.</div>
                            </div>
                        </div>

                        {{-- إضافة مرفقات جديدة --}}
                        <div class="ems-form-group">
                            <label class="ems-label">
                                إضافة مرفقات جديدة
                                <span
                                    style="font-size: 11px; font-weight: normal; color: var(--ems-text-muted); margin-right: 6px;">(اختياري
                                    — حتى 20MB لكل ملف)</span>
                            </label>
                            <div class="ems-dropzone"
                                style="border: 2px dashed var(--ems-border, #cbd5e1); border-radius: 10px; padding: 16px; text-align: center; cursor: pointer; transition: all 0.2s;"
                                onclick="document.getElementById('edit_project_attachments').click()"
                                id="editProjectDropzone"
                                ondragover="event.preventDefault(); this.style.borderColor='var(--ems-gold, #c5a059)'; this.style.background='rgba(197, 160, 89, 0.05)';"
                                ondragleave="this.style.borderColor=''; this.style.background='';"
                                ondrop="handleProjectFilesDrop(event, 'edit')">
                                <svg viewBox="0 0 24 24" width="28" height="28" fill="none"
                                    stroke="var(--ems-text-muted)" stroke-width="1.6"
                                    style="margin: 0 auto 6px; display:block;">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <polyline points="17 8 12 3 7 8" />
                                    <line x1="12" y1="3" x2="12" y2="15" />
                                </svg>
                                <div style="font-size: 13px; font-weight: 600; color: var(--ems-text-main);">
                                    اضغط لاختيار مرفقات جديدة أو اسحبها هنا
                                </div>
                            </div>
                            <input type="file" id="edit_project_attachments" name="attachments[]" multiple
                                style="display: none;" onchange="handleProjectFilesSelect(this, 'edit')">
                            <div id="edit_project_files_list"
                                style="margin-top: 10px; display: flex; flex-direction: column; gap: 6px;"></div>
                        </div>
                    </div>

                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary"
                            onclick="closeModal('modal-edit-project')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Hidden Delete Attachment Form --}}
    <form id="deleteAttachmentForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>


    {{-- JavaScript --}}
    <script>
        const projectsData = @js(
    $projects
        ->getCollection()
        ->map(function ($project) {
            return [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'department_id' => $project->department_id,
                'owner_employee_id' => $project->owner_employee_id,
                'start_date' => $project->start_date?->format('Y-m-d'),
                'end_date' => $project->end_date?->format('Y-m-d'),
                'actual_end_date' => $project->actual_end_date?->format('Y-m-d'),
                'status' => $project->status,
                'completion_percentage' => (float) $project->completion_percentage,
                'attachments' => $project->attachments
                    ->map(
                        fn($att) => [
                            'id' => $att->id,
                            'file_name' => $att->file_name,
                            'file_size_formatted' => $att->formatted_size,
                            'download_url' => route('projects.attachments.download', $att),
                            'delete_url' => route('projects.attachments.destroy', $att),
                        ],
                    )
                    ->values()
                    ->all(),
            ];
        })
        ->values()
        ->all(),
);

        function openAddProjectModal() {
            openModal('modal-add-project');
            const deptSelect = document.getElementById('add_department_id');
            if (deptSelect && deptSelect.value) {
                handleDepartmentChange(deptSelect.value, 'add');
            }
        }

        function handleDepartmentChange(deptId, mode) {
            const deptSelect = document.getElementById(mode === 'add' ? 'add_department_id' : 'edit_department_id');
            const ownerSelect = document.getElementById(mode === 'add' ? 'add_owner_employee_id' :
            'edit_owner_employee_id');
            const hint = document.getElementById(mode === 'add' ? 'add_manager_hint' : 'edit_manager_hint');
            if (!deptSelect || !ownerSelect) return;

            if (!deptId) {
                if (mode === 'add') ownerSelect.value = '';
                if (hint) hint.style.display = 'none';
                return;
            }

            const selectedOption = deptSelect.options[deptSelect.selectedIndex];
            const managerId = selectedOption ? selectedOption.getAttribute('data-manager-id') : null;
            const managerName = selectedOption ? selectedOption.getAttribute('data-manager-name') : null;

            if (managerId && managerId !== '') {
                ownerSelect.value = managerId;
                if (hint) {
                    hint.style.display = 'block';
                    hint.style.color = '#15803d';
                    hint.replaceChildren();
                    hint.append(document.createTextNode('✓ تم تحديد مدير الدائرة تلقائياً: '));
                    const managerLabel = document.createElement('strong');
                    managerLabel.textContent = managerName || '';
                    hint.append(managerLabel);
                }
            } else {
                if (mode === 'add') {
                    ownerSelect.value = '';
                }
                if (hint) {
                    hint.style.display = 'block';
                    hint.style.color = '#b45309';
                    hint.textContent = 'تنبيه: لا يوجد مدير مسجل لهذه الدائرة حالياً، يمكنك اختيار المسؤول يدوياً.';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const deptSelect = document.getElementById('add_department_id');
            if (deptSelect && deptSelect.value) {
                handleDepartmentChange(deptSelect.value, 'add');
            }
        });

        @if ($canUpdate)
            function openEditProjectModal(projectId) {
                const project = projectsData.find(
                    item => Number(item.id) === Number(projectId)
                );

                if (!project) {
                    return;
                }

                const form = document.getElementById('editProjectForm');
                if (form) {
                    form.action = "{{ url('/projects') }}/" + project.id;
                }

                document.getElementById('edit_title').value = project.title ?? '';
                document.getElementById('edit_description').value = project.description ?? '';
                document.getElementById('edit_department_id').value = project.department_id ?? '';
                document.getElementById('edit_owner_employee_id').value = project.owner_employee_id ?? '';
                document.getElementById('edit_start_date').value = project.start_date ?? '';
                document.getElementById('edit_end_date').value = project.end_date ?? '';
                document.getElementById('edit_actual_end_date').value = project.actual_end_date ?? '';
                document.getElementById('edit_status').value = project.status ?? 'in_progress';
                document.getElementById('edit_completion_percentage').value = project.completion_percentage ?? 0;

                const editHint = document.getElementById('edit_manager_hint');
                if (!project.owner_employee_id && project.department_id) {
                    handleDepartmentChange(project.department_id, 'edit');
                } else if (editHint) {
                    editHint.style.display = 'none';
                }

                // Render existing attachments
                const existingContainer = document.getElementById('edit_project_existing_attachments');
                if (existingContainer) {
                    if (project.attachments && project.attachments.length > 0) {
                        existingContainer.replaceChildren(...project.attachments.map((att) => {
                            const row = document.createElement('div');
                            row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:6px 10px;background:#fff;border:1px solid var(--ems-border,#e5e7eb);border-radius:6px;margin-bottom:6px;';
                            const details = document.createElement('div');
                            details.style.cssText = 'display:flex;align-items:center;gap:8px;overflow:hidden;';
                            const icon = document.createElement('span');
                            icon.style.fontSize = '13px';
                            icon.textContent = '📎';
                            const name = document.createElement('span');
                            name.style.cssText = 'font-size:12.5px;font-weight:600;color:var(--ems-text-main);white-space:nowrap;text-overflow:ellipsis;overflow:hidden;max-width:250px;';
                            name.title = String(att.file_name || '');
                            name.textContent = String(att.file_name || '');
                            const size = document.createElement('span');
                            size.style.cssText = 'font-size:11px;color:var(--ems-text-muted);';
                            size.textContent = `(${String(att.file_size_formatted || '')})`;
                            details.append(icon, name, size);
                            const actions = document.createElement('div');
                            actions.style.cssText = 'display:flex;align-items:center;gap:6px;';
                            const download = document.createElement('a');
                            download.href = String(att.download_url || '#');
                            download.className = 'ems-btn ems-btn-sm ems-btn-secondary';
                            download.style.cssText = 'padding:3px 8px;font-size:11px;text-decoration:none;';
                            download.title = 'تحميل الملف';
                            download.textContent = '⬇';
                            const remove = document.createElement('button');
                            remove.type = 'button';
                            remove.className = 'ems-btn ems-btn-sm';
                            remove.style.cssText = 'padding:3px 8px;font-size:11px;color:#ef4444;border-color:#fecaca;background:#fef2f2;';
                            remove.title = 'حذف هذا المرفق';
                            remove.textContent = '✕';
                            remove.addEventListener('click', () => deleteProjectAttachment(String(att.delete_url || '')));
                            actions.append(download, remove);
                            row.append(details, actions);
                            return row;
                        }));
                    } else {
                        existingContainer.innerHTML =
                            '<div style="font-size: 12px; color: var(--ems-text-muted); text-align: center; padding: 6px;">لا توجد مرفقات سابقة لهذا المشروع.</div>';
                    }
                }

                // Reset new files input
                const editFileInput = document.getElementById('edit_project_attachments');
                if (editFileInput) editFileInput.value = '';
                const editFilesList = document.getElementById('edit_project_files_list');
                if (editFilesList) editFilesList.innerHTML = '';

                openModal('modal-edit-project');
            }

            function deleteProjectAttachment(url) {
                if (!confirm('هل أنت متأكد من رغبتك في حذف هذا المرفق نهائياً؟')) {
                    return;
                }
                const form = document.getElementById('deleteAttachmentForm');
                form.action = url;
                form.submit();
            }
        @endif

        function handleProjectFilesSelect(input, mode) {
            renderSelectedFiles(input.files, mode);
        }

        function handleProjectFilesDrop(event, mode) {
            event.preventDefault();
            const dropzone = document.getElementById(mode === 'add' ? 'addProjectDropzone' : 'editProjectDropzone');
            if (dropzone) {
                dropzone.style.borderColor = '';
                dropzone.style.background = '';
            }
            const input = document.getElementById(mode === 'add' ? 'add_project_attachments' : 'edit_project_attachments');
            if (event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length > 0) {
                input.files = event.dataTransfer.files;
                renderSelectedFiles(input.files, mode);
            }
        }

        function renderSelectedFiles(files, mode) {
            const list = document.getElementById(mode === 'add' ? 'add_project_files_list' : 'edit_project_files_list');
            if (!list) return;

            if (!files || files.length === 0) {
                list.innerHTML = '';
                return;
            }

            const items = [];
            for (let i = 0; i < files.length; i++) {
                const f = files[i];
                const kb = (f.size / 1024).toFixed(0);
                const sizeStr = kb >= 1024 ? (kb / 1024).toFixed(1) + ' MB' : kb + ' KB';
                const row = document.createElement('div');
                row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:6px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;font-size:12px;';
                const details = document.createElement('div');
                details.style.cssText = 'display:flex;align-items:center;gap:6px;overflow:hidden;';
                const check = document.createElement('span');
                check.style.cssText = 'color:#15803d;font-weight:bold;';
                check.textContent = '✓';
                const name = document.createElement('span');
                name.style.cssText = 'font-weight:600;color:#166534;white-space:nowrap;text-overflow:ellipsis;overflow:hidden;max-width:280px;';
                name.textContent = f.name;
                const size = document.createElement('span');
                size.style.cssText = 'color:#6b7280;font-size:11px;';
                size.textContent = `(${sizeStr})`;
                details.append(check, name, size);
                row.append(details);
                items.push(row);
            }
            list.replaceChildren(...items);
        }

        function toggleProjectTasks(projectId) {
            const row = document.getElementById('project-tasks-row-' + projectId);
            const btn = document.getElementById('btn-tasks-' + projectId);
            if (!row) return;

            if (row.style.display === 'none' || row.style.display === '') {
                row.style.display = 'table-row';
                if (btn) {
                    btn.style.background = 'var(--ems-gold-light, #e2d2b4)';
                    btn.style.color = 'var(--ems-gold-hover, #a8823b)';
                    btn.style.borderColor = 'var(--ems-gold, #c5a059)';
                }
            } else {
                row.style.display = 'none';
                if (btn) {
                    btn.style.background = '';
                    btn.style.color = '';
                    btn.style.borderColor = '';
                }
            }
        }
    </script>

@endsection
