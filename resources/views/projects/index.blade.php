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
                <button type="button" class="ems-btn ems-btn-primary" onclick="openModal('modal-add-project')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>

                    <span>مشروع جديد</span>
                </button>
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
                                <th>القسم</th>
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
                                        'in_progress' => 'قيد التنفيذ',
                                        'in_review' => 'قيد المراجعة',
                                        'completed' => 'مكتمل',
                                        'on_hold' => 'متوقف',
                                    ];

                                    $statusClasses = [
                                        'in_progress' => 'badge-warning',
                                        'in_review' => 'badge-purple',
                                        'completed' => 'badge-success',
                                        'on_hold' => 'badge-danger',
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

                                            {{-- Tasks --}}
                                            <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
                                                class="ems-btn ems-btn-icon" title="المهام">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="m9 11 3 3L22 4"></path>
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11">
                                                    </path>
                                                </svg>
                                            </a>


                                            {{-- Edit --}}
                                            <button type="button" class="ems-btn ems-btn-icon" title="تعديل"
                                                onclick="openEditProjectModal({{ $project->id }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                                                </svg>
                                            </button>


                                            {{-- Delete --}}
                                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
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

        </div>

    </div>


    {{-- Add Project Modal --}}
    <div class="ems-modal-overlay" id="modal-add-project">
        <div class="ems-modal">
            <div class="ems-modal-header">
                <div class="ems-modal-title">إضافة مشروع جديد</div>
                <button type="button" onclick="closeModal('modal-add-project')" class="ems-modal-close">✕</button>
            </div>

            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="ems-modal-body">
                    <div class="ems-form-group">
                        <label class="ems-label"><span class="required">*</span> اسم المشروع</label>
                        <input type="text" name="title" class="ems-input" required placeholder="أدخل اسم المشروع">
                    </div>

                    <div class="ems-form-group">
                        <label class="ems-label">الوصف</label>
                        <textarea name="description" class="ems-input" rows="3" placeholder="تفاصيل وأهداف المشروع"></textarea>
                    </div>

                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label class="ems-label">القسم / الدائرة</label>
                            <select name="department_id" class="ems-input">
                                <option value="">اختر القسم</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label">المسؤول عن المشروع</label>
                            <select name="owner_employee_id" class="ems-input">
                                <option value="">اختر المسؤول</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label class="ems-label"><span class="required">*</span> تاريخ البداية</label>
                            <input type="date" name="start_date" class="ems-input" value="{{ date('Y-m-d') }}" required>
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
                                <option value="in_progress">قيد التنفيذ</option>
                                <option value="in_review">قيد المراجعة</option>
                                <option value="completed">مكتمل</option>
                                <option value="on_hold">متوقف</option>
                            </select>
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label">نسبة الإنجاز (%)</label>
                            <input type="number" name="completion_percentage" class="ems-input" min="0" max="100" value="0">
                        </div>
                    </div>
                </div>

                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-add-project')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ المشروع</button>
                </div>
            </form>
        </div>
    </div>


    {{-- Edit Project Modal --}}
    <div class="ems-modal-overlay" id="modal-edit-project">
        <div class="ems-modal">
            <div class="ems-modal-header">
                <div class="ems-modal-title">تعديل بيانات المشروع</div>
                <button type="button" onclick="closeModal('modal-edit-project')" class="ems-modal-close">✕</button>
            </div>

            <form id="editProjectForm" method="POST">
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
                            <label class="ems-label">القسم / الدائرة</label>
                            <select id="edit_department_id" name="department_id" class="ems-input">
                                <option value="">اختر القسم</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label">المسؤول عن المشروع</label>
                            <select id="edit_owner_employee_id" name="owner_employee_id" class="ems-input">
                                <option value="">اختر المسؤول</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
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
                            <input type="date" id="edit_actual_end_date" name="actual_end_date" class="ems-input">
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label"><span class="required">*</span> الحالة</label>
                            <select id="edit_status" name="status" class="ems-input" required>
                                <option value="in_progress">قيد التنفيذ</option>
                                <option value="in_review">قيد المراجعة</option>
                                <option value="completed">مكتمل</option>
                                <option value="on_hold">متوقف</option>
                            </select>
                        </div>
                    </div>

                    <div class="ems-form-group">
                        <label class="ems-label">نسبة الإنجاز (%)</label>
                        <input type="number" id="edit_completion_percentage" name="completion_percentage"
                            class="ems-input" min="0" max="100" step="0.01" required>
                    </div>
                </div>

                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-edit-project')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>


    {{-- JavaScript --}}
    <script>
        const projectsData = @js(
    $projects
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
            ];
        })
        ->values()
        ->all(),
);

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

            openModal('modal-edit-project');
        }
    </script>

@endsection
