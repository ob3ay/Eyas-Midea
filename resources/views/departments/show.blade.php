@extends('layouts.portal')
@section('title', $department->name . ' | تفاصيل الدائرة')

@section('content')
    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <a href="{{ route('departments.index') }}">الدوائر والأقسام</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">{{ $department->name }}</span>
            </div>
            <h1 class="ems-page-title">{{ $department->name }}</h1>
            <p class="ems-page-subtitle">تفاصيل الدائرة — الأقسام التابعة، كادر الموظفين، والمشاريع المرتبطة بها</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            @can('edit-department', $department)
                <button class="ems-btn ems-btn-secondary" onclick="openModal('modal-assign-employee')">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    <span>نقل / تعيين موظف</span>
                </button>
                <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-section')">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>إضافة قسم جديد</span>
                </button>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="ems-badge badge-completed" style="display:block; padding:10px 14px; margin-bottom:14px;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c; border-radius:8px; font-size:13px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- بانر الترحيب --}}
    <section class="ems-welcome-banner" style="margin-bottom: 20px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 700; color: #fff; margin: 0 0 6px;">{{ $department->name }}</h2>
            <p style="margin: 0; color: rgba(255,255,255,0.88); max-width: 760px; font-size: 13.5px;">
                {{ $department->description ?? 'لا يوجد وصف مضاف لهذه الدائرة حالياً.' }}
            </p>
            <div style="margin-top: 12px; display: flex; gap: 16px; flex-wrap: wrap;">
                <span style="background: rgba(255,255,255,0.15); color: #fff; font-size: 12px; padding: 4px 12px; border-radius: 20px; font-weight: 600;">
                    {{ $department->code ?? 'DEPT' }}
                </span>
                <span style="background: rgba(255,255,255,0.15); color: #fff; font-size: 12px; padding: 4px 12px; border-radius: 20px;">
                    {{ $department->is_active ? '🟢 نشطة' : '🔴 غير نشطة' }}
                </span>
            </div>
        </div>
        <div style="text-align: center;">
            <span style="font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.8); display:block; margin-bottom: 4px;">مدير الدائرة</span>
            <div style="font-size: 20px; font-weight: 700; color: #fff;">
                {{ $department->manager?->full_name ?? 'غير محدد' }}
            </div>
            @if ($department->manager?->user)
                <span style="font-size: 10.5px; font-weight: 400; color: rgba(255,255,255,0.7);">
                    {{ '@' . $department->manager->user->username }}
                </span>
            @endif
        </div>
    </section>

    {{-- إحصاءات الدائرة --}}
    <div class="ems-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الأقسام التابعة</span>
                <span class="ems-stat-value" style="color: #0284c7;">{{ $department->sections->count() }}</span>
                <span class="ems-stat-subtext" style="color: #0284c7;">قسم تخصصي</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #e0f2fe; border-color: #bae6fd; color: #0369a1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </div>
        </div>
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي الموظفين</span>
                <span class="ems-stat-value" style="color: #10b981;">{{ $department->employees->count() }}</span>
                <span class="ems-stat-subtext" style="color: #10b981;">موظف مسجل</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي المشاريع</span>
                <span class="ems-stat-value" style="color: #8b5cf6;">{{ $department->projects->count() }}</span>
                <span class="ems-stat-subtext" style="color: #8b5cf6;">مشروع مرتبط</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
        </div>
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">مشاريع نشطة</span>
                <span class="ems-stat-value" style="color: #f59e0b;">{{ $department->projects->whereIn('status', ['in_progress', 'in_review'])->count() }}</span>
                <span class="ems-stat-subtext" style="color: #f59e0b;">مشروع قيد التنفيذ أو المراجعة</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- جدول الأقسام التابعة --}}
    <div class="ems-card" style="margin-bottom: 20px;">
        <div class="ems-card-header">
            <div class="ems-card-title">الأقسام التخصصية التابعة لـ {{ $department->name }}</div>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th style="text-align: right;">اسم القسم</th>
                            <th>عدد الموظفين</th>
                            <th>المسؤول / رئيس القسم</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($department->sections as $section)
                            <tr>
                                <td style="text-align: right; font-weight: 600;">
                                    {{ $section->name }}
                                </td>
                                <td>
                                    <span class="ems-badge" style="background: #e0f2fe; color: #0369a1; font-weight: 700;">
                                        {{ $section->employees->count() }}
                                    </span>
                                </td>
                                <td>
                                    @if ($section->head)
                                        <span style="font-size: 13px; font-weight: 600; color: var(--ems-text-main);">
                                            {{ $section->head->full_name }}
                                        </span>
                                    @else
                                        <span style="color: var(--ems-text-muted); font-size: 12px;">شاغر</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="ems-badge {{ $section->is_active ? 'badge-completed' : '' }}"
                                        style="{{ ! $section->is_active ? 'background:#fee2e2; color:#b91c1c;' : '' }}">
                                        {{ $section->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; justify-content: center; gap: 6px;">
                                        <a href="{{ route('sections.show', $section) }}"
                                            class="ems-btn ems-btn-icon ems-btn-sm" title="عرض تفاصيل القسم"
                                            style="color: #0284c7;">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                        @can('update', $section)
                                            <button type="button" class="ems-btn ems-btn-icon ems-btn-sm" title="تعديل القسم"
                                                onclick="openEditSectionModal({{ json_encode([
                                                    'id' => $section->id,
                                                    'name' => $section->name,
                                                    'department_id' => $section->department_id,
                                                    'section_head_employee_id' => $section->section_head_employee_id,
                                                    'is_active' => $section->is_active ? 1 : 0,
                                                ]) }})">
                                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                        @endcan
                                        @can('delete', $section)
                                            <form action="{{ route('sections.destroy', $section) }}" method="POST" style="display:inline;"
                                                onsubmit="return confirm('هل أنت متأكد من حذف قسم {{ addslashes($section->name) }}؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ems-btn ems-btn-icon ems-btn-sm" title="حذف القسم"
                                                    style="color: var(--ems-danger, #ef4444);">
                                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="color: var(--ems-text-muted); padding: 28px; font-size: 13px; font-style: italic;">
                                    لا توجد أقسام تخصصية مسجلة لهذه الدائرة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- جدول المشاريع --}}
    <div class="ems-card" style="margin-bottom: 20px;">
        <div class="ems-card-header">
            <div class="ems-card-title">مشاريع {{ $department->name }}</div>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th style="text-align: right;">اسم المشروع</th>
                            <th>مالك المشروع</th>
                            <th>نسبة الإنجاز</th>
                            <th>تاريخ البدء</th>
                            <th>الموعد النهائي</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $statusMap = [
                                'in_progress' => ['label' => 'قيد التنفيذ', 'bg' => '#dcfce7', 'color' => '#15803d'],
                                'in_review'   => ['label' => 'قيد المراجعة','bg' => '#dbeafe', 'color' => '#1d4ed8'],
                                'completed'   => ['label' => 'مكتمل',        'bg' => '#f0fdf4', 'color' => '#166534'],
                                'on_hold'     => ['label' => 'موقوف',         'bg' => '#fef3c7', 'color' => '#b45309'],
                            ];
                        @endphp
                        @forelse ($department->projects as $project)
                            @php
                                $pct = (float) ($project->completion_percentage ?? 0);
                                $st = $statusMap[$project->status] ?? ['label' => $project->status, 'bg' => '#f3f4f6', 'color' => '#6b7280'];
                            @endphp
                            <tr>
                                <td style="text-align: right; font-weight: 600; max-width: 220px;">
                                    {{ $project->title }}
                                </td>
                                <td>{{ $project->owner?->full_name ?? '-' }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px; min-width: 120px;">
                                        <div class="ems-progress" style="height: 6px; flex-grow: 1;">
                                            <div class="ems-progress-bar {{ $pct >= 100 ? 'success' : '' }}"
                                                style="width: {{ min(100, $pct) }}%;"></div>
                                        </div>
                                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono); white-space: nowrap;">
                                            {{ number_format($pct, 0) }}%
                                        </span>
                                    </div>
                                </td>
                                <td style="font-size: 12.5px;">{{ $project->start_date?->toDateString() ?? '-' }}</td>
                                <td style="font-size: 12.5px;">{{ $project->end_date?->toDateString() ?? '-' }}</td>
                                <td>
                                    <span class="ems-badge"
                                        style="background: {{ $st['bg'] }}; color: {{ $st['color'] }}; font-weight: 700;">
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="color: var(--ems-text-muted); padding: 28px; font-size: 13px; font-style: italic;">
                                    لا توجد مشاريع مرتبطة بهذه الدائرة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- جدول الموظفين --}}
    <div class="ems-card" style="margin-bottom: 20px;">
        <div class="ems-card-header">
            <div class="ems-card-title">كادر موظفي {{ $department->name }}</div>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th style="text-align: right;">الموظف</th>
                            <th>المسمى الوظيفي</th>
                            <th>القسم</th>
                            <th>تاريخ التعيين</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($department->employees as $emp)
                            <tr>
                                <td style="text-align: right;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 34px; height: 34px; border-radius: 50%; background: var(--ems-surface-alt); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color: var(--ems-accent); flex-shrink:0; overflow:hidden;">
                                            @if ($emp->avatar)
                                                <img src="{{ asset('storage/' . $emp->avatar) }}" alt="{{ $emp->full_name }}" style="width:100%;height:100%;object-fit:cover;">
                                            @else
                                                {{ mb_substr($emp->full_name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 13px;">{{ $emp->full_name }}</div>
                                            @if ($emp->user)
                                                <div style="font-size: 11px; color: var(--ems-text-muted);">{{ '@' . $emp->user->username }}</div>
                                            @endif
                                            @if ($department->manager_employee_id === $emp->id)
                                                <span class="ems-badge badge-gold" style="font-size: 10px; padding: 1px 7px;">مدير الدائرة</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size: 12.5px;">{{ $emp->position ?? '-' }}</td>
                                <td>
                                    @if ($emp->section)
                                        <span class="ems-badge" style="background: #eff6ff; color: #1d4ed8; font-size: 11.5px;">
                                            {{ $emp->section->name }}
                                        </span>
                                    @else
                                        <span style="color: var(--ems-text-muted); font-size: 12px;">-</span>
                                    @endif
                                </td>
                                <td style="font-size: 12.5px;">{{ $emp->hire_date?->toDateString() ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusLabels = ['active' => 'نشط', 'inactive' => 'غير نشط', 'on_leave' => 'إجازة'];
                                        $statusStyles = [
                                            'active'   => 'badge-completed',
                                            'inactive' => '',
                                            'on_leave' => 'badge-gold',
                                        ];
                                    @endphp
                                    <span class="ems-badge {{ $statusStyles[$emp->employment_status] ?? '' }}"
                                        style="{{ $emp->employment_status === 'inactive' ? 'background:#fee2e2;color:#b91c1c;' : '' }}">
                                        {{ $statusLabels[$emp->employment_status] ?? $emp->employment_status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('employees.show', $emp) }}" class="ems-btn ems-btn-icon ems-btn-sm"
                                        title="عرض الملف الشخصي" style="color: #0284c7;">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="color: var(--ems-text-muted); padding: 28px; font-size: 13px; font-style: italic;">
                                    لا يوجد موظفون مسجلون في هذه الدائرة حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal: إضافة قسم جديد --}}
    @can('create', App\Models\Section::class)
        <div class="ems-modal-overlay" id="modal-add-section">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">إضافة قسم تخصصي جديد</div>
                    <button class="ems-modal-close" onclick="closeModal('modal-add-section')">✕</button>
                </div>
                <form action="{{ route('sections.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="department_id" value="{{ $department->id }}">
                    <div class="ems-modal-body">
                        <div class="ems-form-group">
                            <label for="new_sec_name" class="ems-label"><span class="required">*</span> اسم القسم التخصصي</label>
                            <input type="text" id="new_sec_name" name="name" class="ems-input"
                                placeholder="مثال: قسم تطوير الأنظمة" required>
                        </div>

                        <div class="ems-form-group">
                            <label for="new_sec_head" class="ems-label">المسؤول المباشر / رئيس القسم</label>
                            <select id="new_sec_head" name="section_head_employee_id" class="ems-select">
                                <option value="">-- غير محدد (شاغر) --</option>
                                @foreach ($department->employees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->full_name }}{{ $emp->position ? ' — (' . $emp->position . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="ems-form-group">
                            <label class="ems-label">الحالة</label>
                            <select name="is_active" class="ems-select">
                                <option value="1">نشط</option>
                                <option value="0">غير نشط</option>
                            </select>
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-add-section')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ القسم</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Modal: تعديل قسم --}}
    @can('edit-section')
        <div class="ems-modal-overlay" id="modal-edit-section">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">تعديل بيانات القسم التخصصي</div>
                    <button class="ems-modal-close" onclick="closeModal('modal-edit-section')">✕</button>
                </div>
                <form action="" method="POST" id="editSectionForm">
                    @csrf
                    @method('PUT')
                    <div class="ems-modal-body">
                        <div class="ems-form-group">
                            <label for="edit_sec_name" class="ems-label"><span class="required">*</span> اسم القسم</label>
                            <input type="text" id="edit_sec_name" name="name" class="ems-input" required>
                        </div>
                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                        <div class="ems-form-group">
                            <label for="edit_sec_head" class="ems-label">المسؤول المباشر / رئيس القسم</label>
                            <select id="edit_sec_head" name="section_head_employee_id" class="ems-select">
                                <option value="">-- غير محدد (شاغر) --</option>
                                @foreach ($department->employees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->full_name }}{{ $emp->position ? ' — (' . $emp->position . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label class="ems-label">الحالة</label>
                            <select id="edit_sec_is_active" name="is_active" class="ems-select">
                                <option value="1">نشط</option>
                                <option value="0">غير نشط</option>
                            </select>
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-edit-section')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Modal: نقل / تعيين موظف --}}
    @can('edit-department', $department)
        <div class="ems-modal-overlay" id="modal-assign-employee">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">نقل أو تعيين موظف إلى {{ $department->name }}</div>
                    <button class="ems-modal-close" onclick="closeModal('modal-assign-employee')">✕</button>
                </div>
                <form id="assignEmployeeForm" method="POST">
                    @csrf
                    <div class="ems-modal-body">
                        <div class="ems-form-group">
                            <label for="assignment_employee_id" class="ems-label"><span class="required">*</span> الموظف</label>
                            <select id="assignment_employee_id" name="employee_id" class="ems-select" required>
                                <option value="">-- اختر موظفاً --</option>
                                @foreach ($availableEmployees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }}{{ $employee->user ? ' — ' . $employee->user->username : '' }}
                                        (من: {{ $employee->department?->name ?? 'بدون دائرة' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label for="assignment_section_id" class="ems-label">القسم (اختياري)</label>
                            <select id="assignment_section_id" name="section_id" class="ems-select">
                                <option value="">-- بدون قسم --</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="department_id" value="{{ $department->id }}">
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-assign-employee')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">تعيين الموظف</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

@endsection

@push('scripts')
<script>
    function openEditSectionModal(section) {
        const form = document.getElementById('editSectionForm');
        form.action = "{{ url('sections') }}/" + section.id;
        document.getElementById('edit_sec_name').value = section.name || '';
        document.getElementById('edit_sec_head').value = section.section_head_employee_id || '';
        document.getElementById('edit_sec_is_active').value = section.is_active !== undefined ? section.is_active : 1;
        openModal('modal-edit-section');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // ربط نموذج نقل الموظف بالـ action الصحيح
        const assignForm = document.getElementById('assignEmployeeForm');
        if (assignForm) {
            assignForm.addEventListener('submit', function () {
                const empId = document.getElementById('assignment_employee_id').value;
                if (empId) {
                    assignForm.action = "{{ url('departments/employees') }}/" + empId + "/assign";
                }
            });
        }
    });
</script>
@endpush
