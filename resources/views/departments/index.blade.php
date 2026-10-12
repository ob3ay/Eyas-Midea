@extends('layouts.portal')
@section('title', 'الدوائر والأقسام')
@section('content')
    <div class="ems-page-header">
            <div class="ems-page-title-wrap">
                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span>الهيكلية المؤسسية</span>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span style="color: var(--ems-text-main); font-weight: 700;">الدوائر والأقسام</span>
                </div>
                <h1 class="ems-page-title">الدوائر والأقسام التخصصية</h1>
                <p class="ems-page-subtitle">الهيكل التنظيمي المعتمد لمجموعة إياس الإعلامية وتوزيع الكوادر والمسؤوليات</p>
            </div>
            <div class="ems-page-actions" style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('departments.structure') }}" class="ems-btn ems-btn-secondary"
                    style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="6" rx="1.5" />
                        <rect x="14" y="3" width="7" height="6" rx="1.5" />
                        <rect x="8.5" y="15" width="7" height="6" rx="1.5" />
                        <path d="M6.5 9v3a1 1 0 0 0 1 1h9a1 1 0 0 0 1-1V9" />
                        <path d="M12 13v2" />
                    </svg>
                    <span>الهيكل التنظيمي الكامل</span>
                </a>
                @can('edit-department')
                    <button class="ems-btn ems-btn-secondary" onclick="openModal('modal-assign-employee')">
                        <span>نقل / تعيين موظف</span>
                    </button>
                @endcan
                @can('create-department')
                    <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-department')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>إضافة دائرة جديدة</span>
                    </button>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block; padding:10px 14px; margin-bottom:14px;">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="ems-badge"
                style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c;">
                {{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-bottom: 14px; color: #b91c1c; font-size: 13px;">
                <div style="font-weight: 700; margin-bottom: 4px;">يرجى مراجعة وتصحيح الملاحظات التالية:</div>
                <ul style="margin: 0; padding-right: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- شبكة بطاقات الدوائر (ديناميكية) -->
        <div class="ems-dept-grid">
            @php
                // نفس الألوان والأيقونات الأربعة الأصلية، بتتكرر بالتناوب لو الدوائر أكتر من 4
                $deptThemes = [
                    [
                        'bg' => '#fef3c7',
                        'color' => '#b45309',
                        'border' => '#fde68a',
                        'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
                    ],
                    [
                        'bg' => '#e0f2fe',
                        'color' => '#0369a1',
                        'border' => '#bae6fd',
                        'icon' =>
                            '<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>',
                    ],
                    [
                        'bg' => '#eff6ff',
                        'color' => '#1d4ed8',
                        'border' => '#bfdbfe',
                        'icon' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
                    ],
                    [
                        'bg' => '#fdf4ff',
                        'color' => '#a21caf',
                        'border' => '#f5d0fe',
                        'icon' =>
                            '<path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>',
                    ],
                ];
            @endphp

            @forelse ($departments as $index => $department)
                @php $theme = $deptThemes[$index % 4]; @endphp
                <div class="ems-dept-card">
                    <div>
                        <div class="ems-dept-header">
                            <div class="ems-dept-icon"
                                style="background:{{ $theme['bg'] }}; color:{{ $theme['color'] }}; border-color:{{ $theme['border'] }};">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">{!! $theme['icon'] !!}</svg>
                            </div>
                            <span
                                class="ems-badge badge-gold">{{ $department->code ?? 'DEPT-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h2 class="ems-dept-title">{{ $department->name }}</h2>
                        <p class="ems-dept-desc">{{ $department->description ?? 'لا يوجد وصف مضاف لهذه الدائرة حالياً.' }}</p>
                    </div>
                    <div>
                        <div class="ems-dept-meta">
                            <div>
                                <div class="ems-dept-meta-item-label">الكادر / الحسابات</div>
                                <div class="ems-dept-meta-item-val" style="display: inline-flex; align-items: baseline; gap: 3px;">
                                    <span style="font-weight: 700;">{{ $department->employees_count }}</span>
                                    <span style="font-size: 10.5px; color: var(--ems-text-muted);">موظف</span>
                                    <span style="color: var(--ems-border, #cbd5e1); margin: 0 1px;">•</span>
                                    <span style="font-weight: 700;">{{ $department->users_count }}</span>
                                    <span style="font-size: 10.5px; color: var(--ems-text-muted);">حساب</span>
                                </div>
                            </div>
                            <div>
                                <div class="ems-dept-meta-item-label">الحالة</div>
                                <div class="ems-dept-meta-item-val">{{ $department->is_active ? 'نشطة' : 'غير نشطة' }}</div>
                            </div>
                            <div>
                                <div class="ems-dept-meta-item-label">عدد الأقسام</div>
                                <div class="ems-dept-meta-item-val">{{ $department->sections_count }}</div>
                            </div>
                            <div>
                                <div class="ems-dept-meta-item-label">المدير المسؤول</div>
                                <div
                                    style="font-size: 11px; font-weight: 700; color: var(--ems-gold-hover); margin-top: 2px;">
                                    {{ $department->manager->full_name ?? 'غير محدد' }}
                                    @if ($department->manager?->user)
                                        <small>({{ $department->manager->user->username }})</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            {{-- <a href="{{ route('departments.structure', ['department' => $department->id]) }}"
                                class="ems-btn ems-btn-secondary ems-btn-sm"
                                style="flex: 1; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                <span>استعراض الهيكل</span>
                                <span style="font-size: 14px;">←</span>
                            </a> --}}
                            <a href="{{ route('departments.show', ['department' => $department->id]) }}" class="ems-btn ems-btn-secondary ems-btn-sm"
                                style="flex: 1; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                <span>عرض تفاصيل الدائرة</span>
                                <span style="font-size: 14px;">→</span>
                            </a>
                            @can('edit-department', $department)
                            <button type="button" class="ems-btn ems-btn-icon ems-btn-sm" title="تعديل الدائرة"
                                onclick="openEditDepartmentModal({{ json_encode([
                                    'id' => $department->id,
                                    'code' => $department->code,
                                    'name' => $department->name,
                                    'manager_employee_id' => $department->manager_employee_id,
                                ]) }})">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </button>
                            @endcan
                            @can('delete', $department)
                            <form action="{{ route('departments.destroy', $department->id) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف دائرة ({{ $department->name }})؟');" style="margin: 0; display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ems-btn ems-btn-icon ems-btn-sm" title="حذف الدائرة"
                                    style="color: var(--ems-danger, #ef4444);">
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <p style="font-size: 13px; color: var(--ems-text-muted);">لا توجد دوائر مسجلة حالياً.</p>
            @endforelse
        </div>



    <!-- نافذة إضافة دائرة جديدة وأقسامها التابعة -->
    <div class="ems-modal-overlay" id="modal-add-department">
        <div class="ems-modal">
            <div class="ems-modal-header">
                <div class="ems-modal-title">إضافة دائرة إدارية جديدة وتحديد أقسامها</div>
                <button class="ems-modal-close" onclick="closeModal('modal-add-department')">✕</button>
            </div>
            <form action="{{ route('departments.store') }}" method="POST" id="addDepartmentForm">
                @csrf
                <div class="ems-modal-body">
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="dept_code" class="ems-label">رمز / كود الدائرة</label>
                            <input type="text" id="dept_code" name="code" class="ems-input"
                                value="{{ $nextDeptCode }}" placeholder="مثال: DEPT-05"
                                style="font-family: var(--font-mono); font-weight: 600;">
                        </div>
                        <div class="ems-form-group">
                            <label for="dept_name" class="ems-label"><span class="required">*</span> اسم الدائرة</label>
                            <input type="text" id="dept_name" name="name" class="ems-input"
                                placeholder="مثال: دائرة الإنتاج والعمليات" required>
                        </div>
                    </div>

                    <div class="ems-form-group" style="margin-bottom: 16px;">
                        <label for="dept_manager" class="ems-label">مدير الدائرة المسؤول</label>
                        <select id="dept_manager" name="manager_employee_id" class="ems-select">
                            <option value="">-- حدد مدير الدائرة (اختياري) --</option>
                            @foreach ($availableEmployees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->full_name }} {{ $emp->position ? '— (' . $emp->position . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ems-form-group" style="margin-bottom: 16px;">
                        <label for="dept_desc" class="ems-label">وصف ومسؤوليات الدائرة</label>
                        <textarea id="dept_desc" name="description" class="ems-input" rows="2"
                            placeholder="نبذة موجزة عن مهام الدائرة واختصاصاتها..." style="resize: vertical; height: auto; padding: 10px;"></textarea>
                    </div>

                    <!-- الأقسام التابعة للدائرة -->
                    <div style="background: var(--ems-surface-alt); border: 1px solid var(--ems-border); border-radius: var(--radius-md); padding: 16px; margin-bottom: 8px;">
                        <label class="ems-label" style="margin-bottom: 6px; font-weight: 700; color: var(--ems-text-main);">الأقسام والوحدات التابعة لهذه الدائرة</label>
                        <p style="font-size: 11.5px; color: var(--ems-text-muted); margin-bottom: 12px;">
                            يمكنك كتابة أسماء الأقسام التي تتبع هذه الدائرة لإضافتها مباشرة ضمن هيكلها:
                        </p>

                        <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                            <input type="text" id="new_section_input" class="ems-input" placeholder="اسم القسم الجديد (مثال: قسم التنسيق والمتابعة)" style="background: #ffffff;">
                            <button type="button" class="ems-btn ems-btn-secondary" onclick="addSectionToList()" style="white-space: nowrap; padding: 0 16px;">
                                <span>+ إضافة قسم</span>
                            </button>
                        </div>

                        <div id="sections_container" style="display: flex; flex-wrap: wrap; gap: 8px; min-height: 36px; align-items: center;">
                            <span id="no_sections_hint" style="font-size: 12px; color: var(--ems-text-muted); font-style: italic;">
                                لم يتم إضافة أقسام بعد (يمكنك حفظ الدائرة وإضافة أقسام لاحقاً).
                            </span>
                        </div>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary"
                        onclick="closeModal('modal-add-department')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ الدائرة والأقسام</button>
                </div>
            </form>
        </div>
    </div>

    <!-- نافذة تعديل بيانات القسم التخصصي -->
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
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="edit_sec_code" class="ems-label">رمز القسم</label>
                            <input type="text" id="edit_sec_code" class="ems-input" readonly
                                style="background-color: var(--ems-surface-alt); font-family: var(--font-mono); font-weight: 600; cursor: not-allowed;">
                        </div>
                        <div class="ems-form-group">
                            <label for="edit_sec_name" class="ems-label"><span class="required">*</span> اسم القسم التخصصي</label>
                            <input type="text" id="edit_sec_name" name="name" class="ems-input" required>
                        </div>
                    </div>

                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="edit_sec_department_id" class="ems-label"><span class="required">*</span> الدائرة الإدارية التابع لها</label>
                            <select id="edit_sec_department_id" name="department_id" class="ems-select" required>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code ?? 'DEPT' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label for="edit_sec_head_employee_id" class="ems-label">المسؤول المباشر / رئيس القسم</label>
                            <select id="edit_sec_head_employee_id" name="section_head_employee_id" class="ems-select">
                                <option value="">-- غير محدد (شاغر) --</option>
                                @foreach ($availableEmployees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->full_name }} {{ $emp->position ? '— (' . $emp->position . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary"
                        onclick="closeModal('modal-edit-section')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    <!-- نافذة تعديل بيانات الدائرة الإدارية -->
    <div class="ems-modal-overlay" id="modal-edit-department">
        <div class="ems-modal">
            <div class="ems-modal-header">
                <div class="ems-modal-title">تعديل بيانات الدائرة الإدارية</div>
                <button class="ems-modal-close" onclick="closeModal('modal-edit-department')">✕</button>
            </div>
            <form action="" method="POST" id="editDepartmentForm">
                @csrf
                @method('PUT')
                <div class="ems-modal-body">
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="edit_dept_code" class="ems-label">رمز / كود الدائرة</label>
                            <input type="text" id="edit_dept_code" name="code" class="ems-input"
                                style="font-family: var(--font-mono); font-weight: 600;">
                        </div>
                        <div class="ems-form-group">
                            <label for="edit_dept_name" class="ems-label"><span class="required">*</span> اسم الدائرة</label>
                            <input type="text" id="edit_dept_name" name="name" class="ems-input" required>
                        </div>
                    </div>

                    <div class="ems-form-group" style="margin-bottom: 16px;">
                        <label for="edit_dept_manager" class="ems-label">مدير الدائرة المسؤول</label>
                        <select id="edit_dept_manager" name="manager_employee_id" class="ems-select">
                            <option value="">-- حدد مدير الدائرة (اختياري) --</option>
                            @foreach ($availableEmployees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->full_name }} {{ $emp->position ? '— (' . $emp->position . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ems-form-group" style="margin-bottom: 16px;">
                        <label for="edit_dept_desc" class="ems-label">وصف ومسؤوليات الدائرة</label>
                        <textarea id="edit_dept_desc" name="description" class="ems-input" rows="2"
                            placeholder="نبذة موجزة عن مهام الدائرة واختصاصاتها..." style="resize: vertical; height: auto; padding: 10px;"></textarea>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary"
                        onclick="closeModal('modal-edit-department')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    <!-- نافذة إضافة قسم تخصصي جديد -->
    <div class="ems-modal-overlay" id="modal-add-section">
        <div class="ems-modal">
            <div class="ems-modal-header">
                <div class="ems-modal-title">إضافة قسم تخصصي جديد</div>
                <button class="ems-modal-close" onclick="closeModal('modal-add-section')">✕</button>
            </div>
            <form action="{{ route('sections.store') }}" method="POST" id="addSectionForm">
                @csrf
                <div class="ems-modal-body">
                    <div class="ems-form-group" style="margin-bottom: 16px;">
                        <label for="new_sec_name" class="ems-label"><span class="required">*</span> اسم القسم التخصصي</label>
                        <input type="text" id="new_sec_name" name="name" class="ems-input"
                            placeholder="مثال: قسم التنسيق والمتابعة" required>
                    </div>

                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="new_sec_department_id" class="ems-label"><span class="required">*</span> الدائرة التابع لها</label>
                            <select id="new_sec_department_id" name="department_id" class="ems-select" required>
                                <option value="">-- حدد الدائرة الإدارية --</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code ?? 'DEPT' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label for="new_sec_head_employee_id" class="ems-label">المسؤول المباشر / رئيس القسم</label>
                            <select id="new_sec_head_employee_id" name="section_head_employee_id" class="ems-select">
                                <option value="">-- غير محدد (شاغر) --</option>
                                @foreach ($availableEmployees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->full_name }} {{ $emp->position ? '— (' . $emp->position . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary"
                        onclick="closeModal('modal-add-section')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">إضافة القسم</button>
                </div>
            </form>
        </div>
    </div>

    @can('edit-department')
        <div class="ems-modal-overlay" id="modal-assign-employee">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">نقل أو تعيين موظف إلى دائرة وقسم</div>
                    <button class="ems-modal-close" onclick="closeModal('modal-assign-employee')">✕</button>
                </div>
                <form id="assignEmployeeForm" method="POST">
                    @csrf
                    <div class="ems-modal-body">
                        <div class="ems-form-group">
                            <label for="assignment_employee_id" class="ems-label">الموظف / حساب المستخدم</label>
                            <select id="assignment_employee_id" name="employee_id" class="ems-select" required>
                                <option value="">-- اختر موظفاً --</option>
                                @foreach ($availableEmployees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name }}{{ $employee->user ? ' — ' . $employee->user->username : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label for="assignment_department_id" class="ems-label">الدائرة</label>
                                <select id="assignment_department_id" name="department_id" class="ems-select" required>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ems-form-group">
                                <label for="assignment_section_id" class="ems-label">القسم</label>
                                <select id="assignment_section_id" name="section_id" class="ems-select">
                                    <option value="">بدون قسم</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" data-department="{{ $section->department_id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-assign-employee')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ التعيين</button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

@endsection

@push('scripts')
<script>
    function openEditDepartmentModal(dept) {
        const form = document.getElementById('editDepartmentForm');
        form.action = "{{ url('departments') }}/" + dept.id;
        document.getElementById('edit_dept_code').value = dept.code || '';
        document.getElementById('edit_dept_name').value = dept.name || '';
        document.getElementById('edit_dept_manager').value = dept.manager_employee_id || '';
        document.getElementById('edit_dept_desc').value = dept.description || '';
        openModal('modal-edit-department');
    }

    function openEditSectionModal(section) {
        const form = document.getElementById('editSectionForm');
        form.action = "{{ url('sections') }}/" + section.id;
        document.getElementById('edit_sec_code').value = section.code || '';
        document.getElementById('edit_sec_name').value = section.name || '';
        document.getElementById('edit_sec_department_id').value = section.department_id || '';
        document.getElementById('edit_sec_head_employee_id').value = section.section_head_employee_id || '';
        openModal('modal-edit-section');
    }

    function addSectionToList() {
        const input = document.getElementById('new_section_input');
        const sectionName = input.value.trim();
        if (!sectionName) return;

        const container = document.getElementById('sections_container');
        const hint = document.getElementById('no_sections_hint');
        if (hint) hint.style.display = 'none';

        const tag = document.createElement('div');
        tag.className = 'ems-badge badge-gold';
        tag.style.cssText = 'display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; font-size: 12.5px;';
        const label = document.createElement('span');
        label.textContent = sectionName;
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'sections[]';
        hiddenInput.value = sectionName;
        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.style.cssText = 'cursor:pointer;color:#ef4444;font-weight:bold;background:none;border:none;';
        removeButton.textContent = '✕';
        removeButton.addEventListener('click', () => {
            tag.remove();
            checkEmptySections();
        });
        tag.append(label, hiddenInput, removeButton);
        container.appendChild(tag);
        input.value = '';
        input.focus();
    }

    function checkEmptySections() {
        const container = document.getElementById('sections_container');
        const inputs = container.querySelectorAll('input[type="hidden"]');
        const hint = document.getElementById('no_sections_hint');
        if (inputs.length === 0 && hint) {
            hint.style.display = 'inline';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const assignmentDepartment = document.getElementById('assignment_department_id');
        const assignmentSection = document.getElementById('assignment_section_id');
        const assignmentForm = document.getElementById('assignEmployeeForm');

        function filterAssignmentSections() {
            const departmentId = assignmentDepartment?.value;
            assignmentSection?.querySelectorAll('option[data-department]').forEach((option) => {
                option.hidden = option.dataset.department !== departmentId;
            });
            if (assignmentSection?.selectedOptions[0]?.hidden) {
                assignmentSection.value = '';
            }
        }

        assignmentDepartment?.addEventListener('change', filterAssignmentSections);
        assignmentForm?.addEventListener('submit', () => {
            assignmentForm.action = "{{ url('departments/employees') }}/" + document.getElementById('assignment_employee_id').value + "/assign";
        });
        filterAssignmentSections();

        const secInput = document.getElementById('new_section_input');
        if (secInput) {
            secInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addSectionToList();
                }
            });
        }
    });
</script>
@endpush
