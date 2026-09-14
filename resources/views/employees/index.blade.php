@extends('layouts.portal')

@section('title', 'سجل الموظفين والكوادر')

@section('content')
    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span>الموارد البشرية</span>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">سجل الموظفين</span>
            </div>
            <h1 class="ems-page-title">إدارة شؤون الموظفين والكوادر</h1>
            <p class="ems-page-subtitle">الهيكل الإداري الكامل والبيانات الوظيفية لكوادر مجموعة إياس الإعلامية</p>
        </div>
        <div class="ems-page-actions">
            @if ($canCreate)
                <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-employee')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>إضافة موظف جديد</span>
                </button>
            @endif
        </div>
    </div>

    {{-- نموذج البحث والفلاتر --}}
    <form method="GET" action="{{ route('employees.index') }}" class="ems-card" style="margin-bottom: 18px;">
        <div class="ems-card-body">
            <div class="ems-form-row">
                <div class="ems-form-group">
                    <label for="search" class="ems-label">بحث</label>
                    <input id="search" name="search" type="search" class="ems-input" value="{{ $search }}"
                        placeholder="الاسم، الرقم الوظيفي، الهوية أو المسمى الوظيفي">
                </div>
                <div class="ems-form-group">
                    <label for="filter_department_id" class="ems-label">الدائرة</label>
                    <select id="filter_department_id" name="department_id" class="ems-select">
                        <option value="">كل الدوائر</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected($departmentId === $department->id)>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="ems-form-group">
                    <label for="employment_status" class="ems-label">الحالة</label>
                    <select id="employment_status" name="employment_status" class="ems-select">
                        <option value="">كل الحالات</option>
                        <option value="active" @selected($employmentStatus === 'active')>على رأس العمل</option>
                        <option value="on_leave" @selected($employmentStatus === 'on_leave')>في إجازة</option>
                        <option value="inactive" @selected($employmentStatus === 'inactive')>غير نشط</option>
                    </select>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-start; gap:8px; margin-top:12px;">
                <button type="submit" class="ems-btn ems-btn-primary">تطبيق الفلاتر</button>
                <a href="{{ route('employees.index') }}" class="ems-btn ems-btn-secondary">إعادة ضبط</a>
            </div>
        </div>
    </form>

    {{-- الرسائل والتنبيهات --}}
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

    {{-- بطاقات الإحصائيات --}}
    <div class="ems-stats-grid">
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي الكادر والقيادة</span>
                <span class="ems-stat-value">{{ $totalCount }}</span>
                <span class="ems-stat-subtext" style="color: #10b981;">موزعين على {{ $departmentCount }} دوائر</span>
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
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">على رأس عملهم</span>
                <span class="ems-stat-value" style="color: #10b981;">{{ $activeCount }}</span>
                <span class="ems-stat-subtext">حسب الحالة الوظيفية الحالية</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5" />
                </svg>
            </div>
        </div>
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">في إجازات</span>
                <span class="ems-stat-value" style="color: #d97706;">{{ $onLeaveCount }}</span>
                <span class="ems-stat-subtext">إجازة معتمدة حالياً</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
            </div>
        </div>
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الدوائر المتخصصة</span>
                <span class="ems-stat-value">{{ $departmentCount }}</span>
                <span class="ems-stat-subtext">
                    <a href="{{ route('departments.index') }}" style="color: var(--ems-gold-hover);">عرض التفاصيل ←</a>
                </span>
            </div>
            <div class="ems-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18" />
                    <path d="M3 7v14" />
                    <path d="M21 7v14" />
                    <path d="M6 3h12l3 4H3z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- جدول بيانات الموظفين --}}
    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">قائمة كوادر مجموعة إياس الإعلامية</div>
            <span class="ems-badge badge-gold">{{ $employees->total() }} من {{ $totalCount }} كادراً ومسؤولاً</span>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="employeesTable">
                    <thead>
                        <tr>
                            <th>الرقم الوظيفي</th>
                            <th>الموظف</th>
                            <th>المسمى الوظيفي</th>
                            <th>الدائرة والقسم</th>
                            <th>تاريخ التعيين</th>
                            <th>الحالة</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $avatarColors = [
                                '#c5a059',
                                '#1e293b',
                                '#334155',
                                '#475569',
                                '#0284c7',
                                '#0369a1',
                                '#3b82f6',
                                '#d97706',
                            ];
                            $statusLabels = [
                                'active' => ['text' => 'على رأس عمله', 'class' => 'badge-completed'],
                                'on_leave' => ['text' => 'في إجازة', 'class' => 'badge-gold'],
                                'inactive' => ['text' => 'غير نشط', 'class' => ''],
                            ];
                        @endphp

                        @forelse ($employees as $index => $employee)
                            @php
                                $initial = mb_substr($employee->full_name, 0, 1);
                                $status = $statusLabels[$employee->employment_status] ?? [
                                    'text' => $employee->employment_status,
                                    'class' => '',
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <span
                                        style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">
                                        {{ $employee->employee_code ?? 'EMP-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="ems-avatar"
                                            style="width: 36px; height: 36px; background: {{ $avatarColors[$index % count($avatarColors)] }};">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <a href="{{ route('employees.show', $employee) }}"
                                                style="font-weight: 700; color: var(--ems-text-main);">
                                                {{ $employee->full_name }}
                                            </a>
                                            <div style="font-size: 11.5px; color: var(--ems-text-muted);">
                                                {{ $employee->user->email ?? 'بدون حساب دخول' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: var(--ems-gold-hover);">
                                        {{ $employee->position ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $employee->department->name ?? '-' }}
                                    {{ $employee->section ? ' / ' . $employee->section->name : '' }}
                                </td>
                                <td>{{ $employee->hire_date?->translatedFormat('d F Y') ?? '-' }}</td>
                                <td>
                                    <span class="ems-badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @if ($canUpdate)
                                        @php
                                            $employeeData = [
                                                'id' => $employee->id,
                                                'full_name' => $employee->full_name,
                                                'position' => $employee->position,
                                                'department_id' => $employee->department_id,
                                                'section_id' => $employee->section_id,
                                                'phone' => $employee->phone,
                                                'employment_status' => $employee->employment_status,
                                                'update_url' => route('employees.update', $employee),
                                            ];
                                        @endphp
                                        <button type="button" class="ems-btn ems-btn-icon" title="تعديل"
                                            onclick='openEditEmployeeModal(@json($employeeData))'>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                    @endif

                                    @if ($canDelete)
                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                                            style="display:inline;" onsubmit="return confirm('هل تريد حذف هذا الموظف؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ems-btn ems-btn-icon" title="حذف">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; color: var(--ems-text-muted);">
                                    ما في موظفين مسجلين بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- الترقيم (Pagination) --}}
    <div style="display:flex; justify-space-between; align-items:center; margin:14px 0;">
        <span style="color:var(--ems-text-muted);">
            عرض {{ $employees->firstItem() ?? 0 }} إلى {{ $employees->lastItem() ?? 0 }} من {{ $employees->total() }}
        </span>
        {{ $employees->links('pagination::simple-bootstrap-5') }}
    </div>

    {{-- نافذة إضافة موظف --}}
    <div class="ems-modal-overlay" id="modal-add-employee">
        <div class="ems-modal">
            <div class="ems-modal-header">
                <div class="ems-modal-title">إضافة موظف جديد لكادر مجموعة إياس الإعلامية</div>
                <button class="ems-modal-close" onclick="closeModal('modal-add-employee')">✕</button>
            </div>
            <form action="{{ route('employees.store') }}" method="POST" id="addEmployeeForm">
                @csrf
                <div class="ems-modal-body">
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="emp_code" class="ems-label"><span class="required">*</span> الرقم الوظيفي</label>
                            <input type="text" id="emp_code" name="emp_code" class="ems-input"
                                value="{{ $nextEmployeeCode }}" required readonly
                                style="background-color: var(--ems-surface-alt);">
                        </div>
                        <div class="ems-form-group">
                            <label for="national_id" class="ems-label"><span class="required">*</span> رقم الهوية /
                                الإقامة</label>
                            <input type="text" id="national_id" name="national_id" class="ems-input"
                                placeholder="مثال: 900123456" required>
                        </div>
                    </div>
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="first_name" class="ems-label"><span class="required">*</span> الاسم الأول</label>
                            <input type="text" id="first_name" name="first_name" class="ems-input" required>
                        </div>
                        <div class="ems-form-group">
                            <label for="last_name" class="ems-label"><span class="required">*</span> اسم العائلة</label>
                            <input type="text" id="last_name" name="last_name" class="ems-input" required>
                        </div>
                    </div>
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="email" class="ems-label"><span class="required">*</span> البريد المؤسسي (لحساب
                                الدخول)</label>
                            <input type="email" id="email" name="email" class="ems-input" required
                                placeholder="name@eyasmedia.com">
                        </div>
                        <div class="ems-form-group">
                            <label for="phone" class="ems-label"><span class="required">*</span> رقم الهاتف</label>
                            <input type="tel" id="phone" name="phone" class="ems-input" required>
                        </div>
                    </div>
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="department_id" class="ems-label"><span class="required">*</span> الدائرة
                                الإدارية</label>
                            <select id="department_id" name="department_id" class="ems-select" required>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label for="job_title" class="ems-label"><span class="required">*</span> المسمى
                                الوظيفي</label>
                            <input type="text" id="job_title" name="job_title" class="ems-input" required>
                        </div>
                    </div>
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="section_id" class="ems-label">القسم التخصصي</label>
                            <select id="section_id" name="section_id" class="ems-select">
                                <option value="">بدون قسم</option>
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label for="hire_date" class="ems-label"><span class="required">*</span> تاريخ
                                التعيين</label>
                            <input type="date" id="hire_date" name="hire_date" class="ems-input"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary"
                        onclick="closeModal('modal-add-employee')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ وبيانات الموظف</button>
                </div>
            </form>
        </div>
    </div>

    {{-- نافذة تعديل موظف --}}
    @if ($canUpdate)
        <div class="ems-modal-overlay" id="modal-edit-employee">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">تعديل بيانات الموظف</div>
                    <button class="ems-modal-close" onclick="closeModal('modal-edit-employee')">✕</button>
                </div>
                <form id="editEmployeeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="ems-modal-body">
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label for="edit_full_name" class="ems-label"><span class="required">*</span> الاسم
                                    الكامل</label>
                                <input type="text" id="edit_full_name" name="full_name" class="ems-input" required>
                            </div>
                            <div class="ems-form-group">
                                <label for="edit_position" class="ems-label">المسمى الوظيفي</label>
                                <input type="text" id="edit_position" name="position" class="ems-input">
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label for="edit_department_id" class="ems-label"><span class="required">*</span>
                                    الدائرة</label>
                                <select id="edit_department_id" name="department_id" class="ems-select" required>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ems-form-group">
                                <label for="edit_phone" class="ems-label">رقم الهاتف</label>
                                <input type="tel" id="edit_phone" name="phone" class="ems-input">
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label for="edit_section_id" class="ems-label">القسم التخصصي</label>
                                <select id="edit_section_id" name="section_id" class="ems-select">
                                    <option value="">بدون قسم</option>
                                </select>
                            </div>
                            <div class="ems-form-group">
                                <label for="edit_employment_status" class="ems-label"><span class="required">*</span>
                                    الحالة الوظيفية</label>
                                <select id="edit_employment_status" name="employment_status" class="ems-select" required>
                                    <option value="active">على رأس عمله</option>
                                    <option value="on_leave">في إجازة</option>
                                    <option value="inactive">غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary"
                            onclick="closeModal('modal-edit-employee')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>

        @php
            $formattedDepartmentSections = $departments->mapWithKeys(function ($department) {
                return [
                    $department->id => $department->sections
                        ->map(function ($section) {
                            return [
                                'id' => $section->id,
                                'name' => $section->name,
                            ];
                        })
                        ->values(),
                ];
            });
        @endphp

        <script>
            const departmentSections = {!! json_encode($formattedDepartmentSections) !!};

            function populateSections(selectId, departmentId, selectedSectionId = '') {
                const select = document.getElementById(selectId);
                if (!select) return;

                select.innerHTML = '<option value="">بدون قسم</option>';

                const sections = departmentSections[departmentId] || [];
                sections.forEach((section) => {
                    const option = new Option(section.name, section.id);
                    option.selected = String(section.id) === String(selectedSectionId);
                    select.add(option);
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                const createDept = document.getElementById('department_id');
                if (createDept) {
                    createDept.addEventListener('change', (e) => {
                        populateSections('section_id', e.target.value);
                    });
                    populateSections('section_id', createDept.value);
                }

                const editDept = document.getElementById('edit_department_id');
                if (editDept) {
                    editDept.addEventListener('change', (e) => {
                        populateSections('edit_section_id', e.target.value);
                    });
                }
            });

            function openEditEmployeeModal(data) {
                const form = document.getElementById('editEmployeeForm');
                if (form) form.action = data.update_url;

                const fields = {
                    'edit_full_name': data.full_name ?? '',
                    'edit_position': data.position ?? '',
                    'edit_department_id': data.department_id ?? '',
                    'edit_phone': data.phone ?? '',
                    'edit_employment_status': data.employment_status ?? 'active'
                };

                Object.entries(fields).forEach(([id, value]) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value;
                });

                populateSections('edit_section_id', data.department_id, data.section_id);

                if (typeof openModal === 'function') {
                    openModal('modal-edit-employee');
                }
            }
        </script>
    @endif
@endsection
