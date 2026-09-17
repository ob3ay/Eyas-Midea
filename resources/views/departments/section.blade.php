@extends('layouts.portal')
@section('title', 'تفاصيل القسم | مجموعة إياس الإعلامية')

@section('content')
    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <a href="{{ route('departments.index') }}">الدوائر والأقسام</a>
                <span class="ems-breadcrumb-separator">/</span>
                <a href="{{ route('departments.show', ['department' => $section->department_id]) }}">{{ $section->department?->name ?? 'الدائرة' }}</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">{{ $section->name }}</span>
            </div>
            <h1 class="ems-page-title">{{ $section->name }}</h1>
            <p class="ems-page-subtitle">تفاصيل القسم، الموظفين، والفروع المرتبطة به</p>
        </div>
    </div>

    <section class="ems-welcome-banner">
        <div>
            <h1 class="department-name">مرحبا بك في {{ $section->name }}</h1>
            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.88); max-width: 760px;">
                القسم التابع لـ {{ $section->department?->name ?? 'الدائرة' }}، ويضم الموظفين والمشاريع ذات العلاقة المباشرة.
            </p>
        </div>

        <div class="department-content">
            <span class="manager">رئيس القسم</span>
            <h2 class="manager-name">{{ $section->head?->full_name ?? 'غير محدد' }}</h2>
            <span class="manager-status">{{ $section->is_active ? 'نشط' : 'غير نشط' }}</span>
        </div>
    </section>

    <div class="ems-stats-grid">
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الدائرة التابعة</span>
                <span class="ems-stat-value" style="color:#0284c7;">{{ $section->department?->name ?? '-' }}</span>
                <span class="ems-stat-subtext" style="color:#0284c7;">{{ $section->department?->manager?->full_name ?? 'مدير غير محدد' }}</span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">رئيس القسم</span>
                <span class="ems-stat-value" style="color:#10b981;">{{ $section->head?->full_name ?? 'غير محدد' }}</span>
                <span class="ems-stat-subtext" style="color:#10b981;">{{ $section->is_active ? 'نشط' : 'غير نشط' }}</span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي الموظفين</span>
                <span class="ems-stat-value" style="color:#8b5cf6;">{{ $section->employees->count() }}</span>
                <span class="ems-stat-subtext" style="color:#8b5cf6;">موظفاً مسجلاً بالقسم</span>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">المشاريع</span>
                <span class="ems-stat-value" style="color:#f97316;">{{ $section->department?->projects->count() ?? 0 }}</span>
                <span class="ems-stat-subtext" style="color:#f97316;">مشاريع مرتبطة بالدائرة</span>
            </div>
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">موظفو {{ $section->name }}</div>
            <div style="display:flex; gap:8px; align-items:center;">
                <a href="{{ route('departments.show', ['department' => $section->department_id]) }}" class="ems-btn ems-btn-secondary">رجوع إلى الدائرة</a>
                @can('update', $section)
                    <button type="button" class="ems-btn ems-btn-primary" onclick="openEditSectionModal({{ json_encode(['id' => $section->id, 'name' => $section->name, 'department_id' => $section->department_id, 'section_head_employee_id' => $section->section_head_employee_id]) }})">تعديل القسم</button>
                @endcan
                @can('delete', $section)
                    <form action="{{ route('sections.destroy', $section) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف القسم {{ $section->name }}؟');" style="margin:0; display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ems-btn ems-btn-danger">حذف القسم</button>
                    </form>
                @endcan
            </div>
        </div>
        <div class="ems-card-body">
            <div class="ems-table-responsive">
                <table class="ems-table" style="text-align:center;">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>الوظيفة</th>
                            <th>تاريخ الانضمام</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($section->employees as $emp)
                            <tr>
                                <td>{{ $emp->full_name }}</td>
                                <td>{{ $emp->position ?? '-' }}</td>
                                <td>{{ optional($emp->hire_date)->toDateString() ?? '-' }}</td>
                                <td>
                                    <span class="ems-badge {{ $emp->employment_status === 'active' ? 'badge-completed' : '' }}">
                                        {{ $emp->employment_status === 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('employees.show', $emp) }}" class="ems-btn ems-btn-icon bg-blue-500 text-white" title="عرض">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="color:var(--ems-text-muted);">لا يوجد موظفين مسجلين في هذا القسم.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">مشاريع الدائرة</div>
        </div>
        <div class="ems-card-body">
            <div class="ems-table-responsive">
                <table class="ems-table" style="text-align:center;">
                    <thead>
                        <tr>
                            <th>اسم المشروع</th>
                            <th>مالك المشروع</th>
                            <th>نسبة الإنجاز</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($section->department?->projects ?? [] as $project)
                            <tr>
                                <td>{{ $project->title }}</td>
                                <td>{{ $project->owner?->full_name ?? '-' }}</td>
                                <td>{{ number_format((float) ($project->completion_percentage ?? 0), 0) }}%</td>
                                <td>{{ optional($project->start_date)->toDateString() ?? '-' }}</td>
                                <td>{{ optional($project->end_date)->toDateString() ?? '-' }}</td>
                                <td>{{ $project->status ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="color:var(--ems-text-muted);">لا توجد مشاريع مرتبطة بهذه الدائرة بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ems-modal-overlay" id="modal-edit-section" style="display:none;">
        <div class="ems-modal">
            <div class="ems-modal-header">
                <div class="ems-modal-title">تعديل بيانات القسم</div>
                <button class="ems-modal-close" onclick="closeModal('modal-edit-section')">✕</button>
            </div>
            <form action="{{ route('sections.update', $section) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="ems-modal-body">
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="edit_sec_name" class="ems-label"><span class="required">*</span> اسم القسم</label>
                            <input type="text" id="edit_sec_name" name="name" class="ems-input" value="{{ $section->name }}" required>
                        </div>
                        <div class="ems-form-group">
                            <label for="edit_sec_department_id" class="ems-label"><span class="required">*</span> الدائرة التابع لها</label>
                            <select id="edit_sec_department_id" name="department_id" class="ems-select" required>
                                @foreach (App\Models\Department::orderBy('name')->get() as $dept)
                                    <option value="{{ $dept->id }}" {{ $section->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="edit_sec_head_employee_id" class="ems-label">المسؤول المباشر / رئيس القسم</label>
                            <select id="edit_sec_head_employee_id" name="section_head_employee_id" class="ems-select">
                                <option value="">-- غير محدد --</option>
                                @foreach (App\Models\Employee::where('department_id', $section->department_id)->orderBy('full_name')->get() as $emp)
                                    <option value="{{ $emp->id }}" {{ $section->section_head_employee_id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}{{ $emp->position ? ' — (' . $emp->position . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label class="ems-label">الحالة</label>
                            <select name="is_active" class="ems-select">
                                <option value="1" {{ $section->is_active ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ ! $section->is_active ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-edit-section')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .ems-welcome-banner { justify-content: space-around; }
        .ems-welcome-banner .department-name { font-size: 24px; font-weight: 700; color: #fff; }
        .ems-welcome-banner .manager { font-size: 12px; font-weight: 500; color: #fff; }
        .ems-welcome-banner .manager-name { font-size: 20px; font-weight: 700; color: #fff; }
        .ems-welcome-banner .manager-status { font-size: 10px; font-weight: 200; color: #fff; }
        .ems-stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:12px; margin-bottom:16px; }
    </style>

    <script>
        function openEditSectionModal(section) {
            const form = document.getElementById('editSectionForm');
            form.action = '{{ url('sections') }}/' + section.id;
            document.getElementById('edit_sec_name').value = section.name || '';
            document.getElementById('edit_sec_department_id').value = section.department_id || '';
            document.getElementById('edit_sec_head_employee_id').value = section.section_head_employee_id || '';
            openModal('modal-edit-section');
        }

        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'block';
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        }
    </script>
@endsection
