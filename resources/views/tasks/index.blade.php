@extends('layouts.portal')

@section('title', 'المهام وتتبع الحالات')

@section('content')

    <style>
        .sortable-ghost {
            opacity: 0.4;
            background-color: var(--ems-surface-alt, #f3f4f6);
            border: 2px dashed #3b82f6 !important;
        }

        .kanban-dropzone {
            min-height: 150px;
            padding-bottom: 20px;
        }

        .ems-task-card {
            cursor: grab;
            user-select: none;
        }

        .ems-task-card:active {
            cursor: grabbing;
        }
    </style>

    @php
        $statusLabels = [
            'Draft' => 'مسودة',
            'To-Do' => 'قيد الانتظار',
            'In Progress' => 'قيد التنفيذ',
            'Under Review' => 'قيد المراجعة',
            'Completed' => 'مكتملة',
            'Rejected' => 'مرفوضة',
        ];

        $statusClasses = [
            'Draft' => 'badge-draft',
            'To-Do' => 'badge-danger',
            'In Progress' => 'badge-progress',
            'Under Review' => 'badge-review',
            'Completed' => 'badge-completed',
            'Rejected' => 'badge-danger',
        ];

        $priorityLabels = [
            'Low' => 'منخفضة',
            'Medium' => 'متوسطة',
            'High' => 'عالية',
            'Urgent' => 'عاجلة',
        ];

        $priorityClasses = [
            'Low' => 'badge-success',
            'Medium' => 'badge-warning',
            'High' => 'badge-danger',
            'Urgent' => 'badge-danger',
        ];

        $statuses = ['Draft', 'To-Do', 'In Progress', 'Under Review', 'Completed', 'Rejected'];

        $groupedTasks = [];

        foreach ($statuses as $status) {
            $groupedTasks[$status] = $tasks->where('status', $status);
        }
    @endphp

    {{-- Header --}}
    <div class="ems-page-header">

        <div class="ems-page-title-wrap">

            <div class="ems-breadcrumb">

                <a href="{{ route('dashboard') }}">
                    الرئيسية
                </a>

                <span class="ems-breadcrumb-separator">/</span>

                <span>
                    سير العمل والإنتاج
                </span>

                <span class="ems-breadcrumb-separator">/</span>

                <span style="color: var(--ems-text-main); font-weight: 700;">
                    المهام وتتبع الحالات
                </span>

            </div>

            <h1 class="ems-page-title">
                إدارة المهام ودورة حياة الإنتاج
            </h1>

            <p class="ems-page-subtitle">
                مسودة ➔ قيد الانتظار ➔ قيد التنفيذ ➔ قيد المراجعة ➔ مكتملة
                مع إمكانية رفض المهمة وإعادتها للمعالجة.
            </p>

        </div>

        <div class="ems-page-actions">

            @if ($canCreate)
                <button type="button" class="ems-btn ems-btn-primary" onclick="openModal('modal-add-task')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20"
                        height="20">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>

                    <span>
                        إضافة مهمة جديدة
                    </span>
                </button>
            @endif

        </div>

    </div>

    {{-- رسالة النجاح --}}
    @if (session('success'))
        <div class="ems-alert ems-alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- أخطاء التحقق --}}
    @if ($errors->any())

        <div class="ems-alert ems-alert-danger" style="margin-bottom: 20px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif

    {{-- بطاقات الإحصائيات (شبكة متوازنة 3x2) --}}
    <div class="ems-stats-grid" style="grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        {{-- Total --}}
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي المهام</span>
                <span class="ems-stat-value">{{ $stats['total'] }}</span>
                <span class="ems-stat-subtext" style="color: var(--ems-gold-hover);">جميع المهام المسجلة في المنظومة</span>
            </div>
            <div class="ems-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </div>
        </div>

        {{-- To Do --}}
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">قيد الانتظار (To-Do)</span>
                <span class="ems-stat-value" style="color: #0284c7;">{{ $stats['todo'] }}</span>
                <span class="ems-stat-subtext" style="color: #0369a1;">مهام بانتظار البدء في التنفيذ</span>
            </div>
            <div class="ems-stat-icon" style="background: #e0f2fe; border-color: #bae6fd; color: #0284c7;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </div>
        </div>

        {{-- In Progress --}}
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">قيد التنفيذ (In Progress)</span>
                <span class="ems-stat-value" style="color: #d97706;">{{ $stats['in_progress'] }}</span>
                <span class="ems-stat-subtext" style="color: #b45309;">مهام جاري العمل عليها حالياً</span>
            </div>
            <div class="ems-stat-icon" style="background: #fef3c7; border-color: #fde68a; color: #d97706;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20V10" />
                    <path d="M18 20V4" />
                    <path d="M6 20v-4" />
                </svg>
            </div>
        </div>

        {{-- Under Review --}}
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">قيد المراجعة والتدقيق</span>
                <span class="ems-stat-value" style="color: #7c3aed;">{{ $stats['under_review'] }}</span>
                <span class="ems-stat-subtext" style="color: #6d28d9;">مهام بانتظار الاعتماد النهائي</span>
            </div>
            <div class="ems-stat-icon" style="background: #ede9fe; border-color: #ddd6fe; color: #7c3aed;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
            </div>
        </div>

        {{-- Completed --}}
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">مكتملة ومعتمدة</span>
                <span class="ems-stat-value" style="color: #16a34a;">{{ $stats['completed'] }}</span>
                <span class="ems-stat-subtext" style="color: #15803d;">تم إنجازها بنجاح تام</span>
            </div>
            <div class="ems-stat-icon" style="background: #dcfce7; border-color: #bbf7d0; color: #16a34a;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
            </div>
        </div>

        {{-- Overdue --}}
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">مهام متأخرة</span>
                <span class="ems-stat-value" style="color: #dc2626;">{{ $stats['overdue'] }}</span>
                <span class="ems-stat-subtext" style="color: #b91c1c;">تجاوزت التاريخ المحدد للإنجاز</span>
            </div>
            <div class="ems-stat-icon" style="background: #fee2e2; border-color: #fecaca; color: #dc2626;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                    <line x1="12" y1="9" x2="12" y2="13" />
                    <line x1="12" y1="17" x2="12.01" y2="17" />
                </svg>
            </div>
        </div>
    </div>

    {{-- لوحة Kanban --}}
    <div class="ems-kanban-board" style="
            overflow-x: auto;
            padding-bottom: 15px;
        ">

        @foreach ($statuses as $index => $status)
            <div class="ems-kanban-column">

                <div class="ems-kanban-header">

                    <div class="ems-kanban-title">
                        <span>{{ $index + 1 }}. {{ $statusLabels[$status] }}</span>
                        <bdi
                            style="opacity: .65; font-size: 11px; font-family: var(--font-sans); direction: ltr;">({{ $status }})</bdi>
                    </div>

                    <span class="ems-kanban-count">
                        {{ $groupedTasks[$status]->count() }}
                    </span>

                </div>

                <div class="ems-kanban-cards kanban-dropzone" data-status="{{ $status }}">

                    @forelse ($groupedTasks[$status] as $task)
                        <div class="ems-task-card" data-task-id="{{ $task->id }}"
                            data-current-status="{{ $task->status }}">

                            {{-- رأس البطاقة --}}
                            <div class="ems-task-header">

                                <span class="ems-task-id">
                                    TSK-{{ str_pad($task->id, 4, '0', STR_PAD_LEFT) }}
                                </span>

                                <span class="ems-badge {{ $priorityClasses[$task->priority] ?? 'badge-warning' }}">
                                    {{ $priorityLabels[$task->priority] ?? $task->priority }}
                                </span>

                            </div>

                            {{-- العنوان --}}
                            <h4 class="ems-task-title">
                                {{ $task->title }}
                            </h4>

                            {{-- الوصف --}}
                            @if ($task->description)
                                <p
                                    style="
                                        font-size: 12px;
                                        color: var(--ems-text-muted);
                                        margin: 8px 0;
                                        line-height: 1.6;
                                    ">
                                    {{ \Illuminate\Support\Str::limit($task->description, 90) }}
                                </p>
                            @endif

                            {{-- المشروع --}}
                            @if ($task->project)
                                <div
                                    style="
                                        font-size: 12px;
                                        margin-bottom: 7px;
                                    ">
                                    📁
                                    {{ $task->project->title }}
                                </div>
                            @endif

                            {{-- الموظف --}}
                            <div class="ems-task-footer">

                                <span>
                                    👤
                                    {{ $task->assignee?->full_name ?? 'غير مسند' }}
                                </span>

                                @if ($task->due_datetime)
                                    <span
                                        style="
                                            font-size: 11px;
                                            {{ $task->due_datetime->isPast() && $task->status !== 'Completed' ? 'color:#dc2626;font-weight:700;' : '' }}
                                        ">
                                        📅
                                        {{ $task->due_datetime->format('d/m/Y H:i') }}
                                    </span>
                                @endif

                            </div>

                            {{-- نسبة الإنجاز --}}
                            <div style="margin-top: 10px;">

                                <div
                                    style="
                                        display:flex;
                                        justify-content:space-between;
                                        font-size:11px;
                                        margin-bottom:4px;
                                    ">
                                    <span>
                                        الإنجاز
                                    </span>

                                    <strong>
                                        {{ number_format((float) $task->completion_percentage, 0) }}%
                                    </strong>
                                </div>

                                <div
                                    style="
                                        width:100%;
                                        height:6px;
                                        background:#e5e7eb;
                                        border-radius:999px;
                                        overflow:hidden;
                                    ">
                                    <div
                                        style="
                                            width: {{ min(100, max(0, (float) $task->completion_percentage)) }}%;
                                            height:100%;
                                            background:#3b82f6;
                                            border-radius:999px;
                                        ">
                                    </div>
                                </div>

                            </div>

                        </div>

                    @empty

                        <div
                            style="
                                text-align:center;
                                padding:30px 10px;
                                color:var(--ems-text-muted);
                                font-size:13px;
                            ">
                            لا توجد مهام هنا
                        </div>
                    @endforelse

                </div>

            </div>
        @endforeach

    </div>

    {{-- Modal إضافة مهمة --}}
    @if ($canCreate)
        <div class="ems-modal-overlay" id="modal-add-task">

            <div class="ems-modal">

                <div class="ems-modal-header">

                    <div class="ems-modal-title">
                        إضافة مهمة جديدة
                    </div>

                    <button type="button" class="ems-modal-close" onclick="closeModal('modal-add-task')">
                        ✕
                    </button>

                </div>

                <form action="{{ route('tasks.store') }}" method="POST">

                    @csrf

                    <div class="ems-modal-body">

                        {{-- العنوان --}}
                        <div class="ems-form-group">

                            <label for="title" class="ems-label">
                                <span class="required">*</span>
                                عنوان المهمة
                            </label>

                            <input type="text" id="title" name="title" class="ems-input"
                                value="{{ old('title') }}" maxlength="200" required>

                        </div>

                        {{-- الوصف --}}
                        <div class="ems-form-group">

                            <label for="description" class="ems-label">
                                وصف المهمة
                            </label>

                            <textarea id="description" name="description" class="ems-input" rows="4">{{ old('description') }}</textarea>

                        </div>

                        {{-- المشروع + المهمة الرئيسية --}}
                        <div class="ems-form-row">

                            <div class="ems-form-group">

                                <label for="project_id" class="ems-label">
                                    المشروع التابع
                                </label>

                                <select id="project_id" name="project_id" class="ems-select">

                                    <option value="">
                                        -- اختياري --
                                    </option>

                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>
                                            {{ $project->title }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="ems-form-group">

                                <label for="parent_task_id" class="ems-label">
                                    المهمة الرئيسية
                                </label>

                                <select id="parent_task_id" name="parent_task_id" class="ems-select">

                                    <option value="">
                                        -- مهمة رئيسية --
                                    </option>

                                    @foreach ($tasks->whereNull('parent_task_id') as $parentTask)
                                        <option value="{{ $parentTask->id }}" @selected(old('parent_task_id') == $parentTask->id)>
                                            TSK-{{ str_pad($parentTask->id, 4, '0', STR_PAD_LEFT) }}
                                            —
                                            {{ $parentTask->title }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                        </div>

                        {{-- الموظف + الأولوية --}}
                        <div class="ems-form-row">

                            <div class="ems-form-group">

                                <label for="assignee_employee_id" class="ems-label">
                                    <span class="required">*</span>
                                    الموظف المكلف
                                </label>

                                <select id="assignee_employee_id" name="assignee_employee_id" class="ems-select"
                                    required>

                                    <option value="">
                                        -- اختر موظف --
                                    </option>

                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('assignee_employee_id') == $employee->id)>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>

                            <div class="ems-form-group">

                                <label for="priority" class="ems-label">
                                    <span class="required">*</span>
                                    الأولوية
                                </label>

                                <select id="priority" name="priority" class="ems-select" required>

                                    <option value="Low">
                                        منخفضة
                                    </option>

                                    <option value="Medium" selected>
                                        متوسطة
                                    </option>

                                    <option value="High">
                                        عالية
                                    </option>

                                    <option value="Urgent">
                                        عاجلة
                                    </option>

                                </select>

                            </div>

                        </div>

                        {{-- الموعد + الحالة --}}
                        <div class="ems-form-row">

                            <div class="ems-form-group">

                                <label for="due_datetime" class="ems-label">
                                    الموعد النهائي
                                </label>

                                <input type="datetime-local" id="due_datetime" name="due_datetime" class="ems-input"
                                    value="{{ old('due_datetime') }}">

                            </div>

                            <div class="ems-form-group">

                                <label for="status" class="ems-label">
                                    <span class="required">*</span>
                                    حالة المهمة
                                </label>

                                <select id="status" name="status" class="ems-select" required>

                                    <option value="Draft">
                                        مسودة
                                    </option>

                                    <option value="To-Do" selected>
                                        قيد الانتظار
                                    </option>

                                    <option value="In Progress">
                                        قيد التنفيذ
                                    </option>

                                    <option value="Under Review">
                                        قيد المراجعة
                                    </option>

                                    <option value="Completed">
                                        مكتملة
                                    </option>

                                    <option value="Rejected">
                                        مرفوضة
                                    </option>

                                </select>

                            </div>

                        </div>

                        {{-- نسبة الإنجاز --}}
                        <div class="ems-form-group">

                            <label for="completion_percentage" class="ems-label">
                                نسبة الإنجاز
                            </label>

                            <input type="number" id="completion_percentage" name="completion_percentage"
                                class="ems-input" min="0" max="100" step="0.01"
                                value="{{ old('completion_percentage', 0) }}">

                        </div>

                    </div>

                    <div class="ems-modal-footer">

                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-add-task')">
                            إلغاء
                        </button>

                        <button type="submit" class="ems-btn ems-btn-primary">
                            حفظ المهمة
                        </button>

                    </div>

                </form>

            </div>

        </div>
    @endif

    {{-- SortableJS --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const dropzones = document.querySelectorAll('.kanban-dropzone');

            dropzones.forEach(function(dropzone) {

                new Sortable(dropzone, {

                    group: 'kanban',

                    animation: 150,

                    ghostClass: 'sortable-ghost',

                    onEnd: function(evt) {

                        const itemEl = evt.item;

                        const taskId =
                            itemEl.getAttribute('data-task-id');

                        const newStatus =
                            evt.to.getAttribute('data-status');

                        const oldStatus =
                            evt.from.getAttribute('data-status');

                        // النقل داخل نفس العمود لا يحتاج تحديث للسيرفر
                        if (oldStatus === newStatus) {
                            return;
                        }

                        fetch(
                                '{{ url('/tasks') }}/' +
                                taskId +
                                '/state', {
                                    method: 'POST',

                                    headers: {
                                        'Content-Type': 'application/json',

                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',

                                        'Accept': 'application/json'
                                    },

                                    body: JSON.stringify({
                                        status: newStatus
                                    })
                                }
                            )
                            .then(function(response) {

                                if (!response.ok) {
                                    throw new Error(
                                        'فشل تحديث الحالة'
                                    );
                                }

                                return response.json();
                            })
                            .then(function(data) {

                                if (!data.success) {
                                    throw new Error(
                                        'لم يتم تحديث المهمة'
                                    );
                                }

                                itemEl.setAttribute(
                                    'data-current-status',
                                    newStatus
                                );

                                updateCounts(
                                    evt.from,
                                    evt.to
                                );

                            })
                            .catch(function() {

                                alert(
                                    'تعذر حفظ الحالة الجديدة، سيتم إعادة تحميل الصفحة.'
                                );

                                window.location.reload();

                            });

                    }

                });

            });


            function updateCounts(fromEl, toEl) {

                const fromCount =
                    fromEl
                    .closest('.ems-kanban-column')
                    .querySelector('.ems-kanban-count');

                const toCount =
                    toEl
                    .closest('.ems-kanban-column')
                    .querySelector('.ems-kanban-count');

                if (!fromCount || !toCount) {
                    return;
                }

                fromCount.textContent =
                    Math.max(
                        0,
                        parseInt(fromCount.textContent) - 1
                    );

                toCount.textContent =
                    parseInt(toCount.textContent) + 1;
            }

        });
    </script>

@endsection
