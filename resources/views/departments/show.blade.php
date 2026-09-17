@extends('layouts.portal')
@section('title', 'تفاصيل الدائرة | مجموعة إياس الإعلامية')

@section('content')
    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <a href="{{ route('departments.index') }}">الهيكلية المؤسسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">{{ $department->name }}</span>
            </div>
            <h1 class="ems-page-title">{{ $department->name }}</h1>
            <p class="ems-page-subtitle">تفاصيل الدائرة، الأقسام التابعة، الموظفين والمشاريع</p>
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

    <section class="ems-welcome-banner">
        <div>
            <h1 class="department-name">مرحبا بك في {{ $department->name }}</h1>
            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.88); max-width: 760px;">
                {{ $department->description ?? 'لا يوجد وصف مضاف لهذه الدائرة بعد.' }}
            </p>
        </div>

        <div class="department-content">
            <span class="manager">رئيس الدائرة</span>
            <h2 class="manager-name">{{ $department->manager?->full_name ?? 'غير محدد' }}</h2>
            @if ($department->manager?->user)
                <span class="manager-status">{{ $department->manager->user->username }}</span>
            @endif
        </div>
    </section>

    <style>
        .ems-welcome-banner {
            justify-content: space-around;
        }

        .ems-welcome-banner .department-name {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
        }

        .ems-welcome-banner .manager {
            font-size: 12px;
            font-weight: 500;
            color: #fff;
        }

        .ems-welcome-banner .manager-name {
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .ems-welcome-banner .manager-status {
            font-size: 10px;
            font-weight: 200;
            color: #fff;
        }

        .ems-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }
    </style>

    <div class="ems-stats-grid">
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الأقسام التابعة</span>
                <span class="ems-stat-value" style="color: #0284c7;">{{ $department->sections->count() }}</span>
                <span class="ems-stat-subtext" style="color: #0284c7;">قسم تابع لهذه الدائرة</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #e0f2fe; border-color: #bae6fd; color: #0369a1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي الموظفين</span>
                <span class="ems-stat-value" style="color: #10b981;">{{ $department->employees->count() }}</span>
                <span class="ems-stat-subtext" style="color: #10b981;">موظفاً ومسؤولاً نشطاً</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
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
                <span class="ems-stat-label">إجمالي المشاريع</span>
                <span class="ems-stat-value" style="color: #8b5cf6;">{{ $department->projects->count() }}</span>
                <span class="ems-stat-subtext" style="color: #8b5cf6;">مشاريع مرتبطة بهذه الدائرة</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            </div>
        </div>
    </div>


    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">أقسام دائرة الإدارة العامة</div>
            <button class="ems-btn ems-btn-primary" onclick="addDepartment()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                اضافة قسم
            </button>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="employeesTable" style="text-align: center;">
                    <thead>
                        <tr>
                            <th>اسم القسم</th>
                            <th>عدد الموظفين</th>
                            <th>المسؤول عن القسم</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>قسم الموارد البشرية</td>
                            <td>5</td>
                            <td>أ. أحمد علي</td>
                            <td>نشط</td>
                            <td style="text-align: center;">
                                <div style="display:flex; justify-content:center; gap:6px;">

                                    <a href="{{ route('sections.show', ['section' => 1]) }}"
                                        class="ems-btn ems-btn-icon bg-blue-500 text-white" title="عرض التفاصيل">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <button type="button" class="ems-btn ems-btn-icon bg-green-500 text-white"
                                        title="تعديل" onclick="editProject()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>

                                    <form action="" method="POST" style="display:inline;"
                                        onsubmit="return confirm('هل تريد حذف هذا القسم؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ems-btn ems-btn-icon bg-red-500 text-white"
                                            title="حذف">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1-2-2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                        </tr>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">مشاريع دائرة الإدارة العامة</div>
            <button class="ems-btn ems-btn-primary" onclick="addProject()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                اضافة مشروع
            </button>
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="projectsTable" style="text-align: center;">
                    <thead>
                        <tr>
                            <th>اسم المشروع</th>
                            <th>اسم المسؤول</th>
                            <th>نسبة الإنجاز</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الانتهاء</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>مشروع تطوير النظام الداخلي</td>
                            <td>فريق B </td>
                            {{-- Progress --}}

                            @php
                                $progress = 75; //مؤقتة لحد م تسحبها انت
                            @endphp
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

                            <td>2024-01-15</td>
                            <td>2024-06-30</td>
                            <td>مكتمل</td>
                            <td style="text-align: center;">
                                <div style="display:flex; justify-content:center; gap:6px;">

                                    <a href="#" class="ems-btn ems-btn-icon bg-blue-500 text-white"
                                        title="عرض التفاصيل">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <button type="button" class="ems-btn ems-btn-icon bg-green-500 text-white"
                                        title="تعديل" onclick="editProject()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>

                                    <form action="" method="POST" style="display:inline;"
                                        onsubmit="return confirm('هل تريد حذف هذا الموظف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ems-btn ems-btn-icon bg-red-500 text-white"
                                            title="حذف">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">موظفي دائرة الإدارة العامة</div>
            @can('edit-department')
                <button class="ems-btn ems-btn-secondary" onclick="openModal('modal-assign-employee')">
                    <span>نقل / تعيين موظف</span>
                </button>
            @endcan
        </div>
        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="employeesTable" style="text-align: center;">
                    <thead>
                        <tr>
                            <th>اسم الموظف</th>
                            <th>الوظيفة</th>
                            <th>تاريخ الانضمام</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>أ. أحمد علي</td>
                            <td>مدير الموارد البشرية</td>
                            <td>2022-05-10</td>
                            <td>نشط</td>
                            <td style="text-align: center;">
                                <div style="display:flex; justify-content:center; gap:6px;">

                                    <a href="#" class="ems-btn ems-btn-icon bg-blue-500 text-white"
                                        title="عرض التفاصيل">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <button type="button" class="ems-btn ems-btn-icon bg-green-500 text-white"
                                        title="تعديل" onclick="editProject()">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>

                                    <form action="" method="POST" style="display:inline;"
                                        onsubmit="return confirm('هل تريد حذف هذا الموظف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ems-btn ems-btn-icon bg-red-500 text-white"
                                            title="حذف">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- شريط أدوات الفلترة والبحث -->
            {{-- <div class="ems-card" style="margin-bottom: 24px; padding: 14px 20px;">
                <div
                    style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center; justify-content: space-between;">
                    <div
                        style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; flex: 1; min-width: 280px;">
                        <label
                            style="font-size: 13px; font-weight: 700; color: var(--ems-text-main); white-space: nowrap;">عرض
                            وتحديد دائرة:</label>
                        <select id="deptFilterSelect" class="ems-select"
                            style="max-width: 280px; padding: 8px 12px; font-size: 13px;"
                            onchange="focusDepartment(this.value)">
                            <option value="all">كل الدوائر التنظيمية (المخطط الكامل)</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ (string) $selectedDeptId === (string) $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code ?? 'DEPT' }})
                                </option>
                            @endforeach
                        </select>

                        <div style="position: relative; flex: 1; min-width: 220px; max-width: 360px;">
                            <input type="text" id="orgSearchInput" class="ems-input"
                                placeholder="بحث فوري (دائرة، قسم، موظف)..."
                                style="padding: 8px 36px 8px 12px; font-size: 13px;" oninput="searchOrgChart(this.value)">
                            <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--ems-text-muted); pointer-events: none;"
                                width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    </div>

                </div>

            </div> --}}


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
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->full_name }}{{ $employee->user ? ' — ' . $employee->user->username : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="ems-form-row">
                                    <div class="ems-form-group">
                                        <label for="assignment_department_id" class="ems-label">الدائرة</label>
                                        <select id="assignment_department_id" name="department_id" class="ems-select"
                                            required>
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        </select>
                                    </div>
                                    <div class="ems-form-group">
                                        <label for="assignment_section_id" class="ems-label">القسم</label>
                                        <select id="assignment_section_id" name="section_id" class="ems-select">
                                            <option value="">بدون قسم</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}"
                                                    data-department="{{ $section->department_id }}">{{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="ems-modal-footer">
                                <button type="button" class="ems-btn ems-btn-secondary"
                                    onclick="closeModal('modal-assign-employee')">إلغاء</button>
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
                    tag.style.cssText =
                        'display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; font-size: 12.5px;';
                    tag.innerHTML = `
            <span>${sectionName}</span>
            <input type="hidden" name="sections[]" value="${sectionName}">
            <button type="button" style="cursor: pointer; color: #ef4444; font-weight: bold; background: none; border: none;" onclick="this.parentElement.remove(); checkEmptySections();">✕</button>
        `;
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
                        assignmentForm.action = "{{ url('departments/employees') }}/" + document.getElementById(
                            'assignment_employee_id').value + "/assign";
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
