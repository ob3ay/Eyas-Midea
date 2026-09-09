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
    <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-employee')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>+ إضافة موظف جديد</span>
    </button>
  </div>
</div>

<!-- بطاقات الإحصائيات -->
<div class="ems-stats-grid">
  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">إجمالي الكادر والقيادة</span>
      <span class="ems-stat-value">36</span>
      <span class="ems-stat-subtext" style="color: #10b981;">35 كادراً ورئيس مجلس الإدارة</span>
    </div>
    <div class="ems-stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
  </div>

  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">على رأس عملهم اليوم</span>
      <span class="ems-stat-value" style="color: #10b981;">33</span>
      <span class="ems-stat-subtext">نسبة حضور 94.3%</span>
    </div>
    <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
  </div>

  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">في إجازات ومهمات</span>
      <span class="ems-stat-value" style="color: #d97706;">3</span>
      <span class="ems-stat-subtext">2 إجازة معتمدة • 1 مهمة ربط</span>
    </div>
    <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
  </div>

  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">الدوائر المتخصصة</span>
      <span class="ems-stat-value">4</span>
      <span class="ems-stat-subtext">الإدارة، الإعلام، البرمجة (15)، التسويق (15)</span>
    </div>
    <div class="ems-stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M3 7v14"/><path d="M21 7v14"/><path d="M6 3h12l3 4H3z"/></svg>
    </div>
  </div>
</div>

<!-- جدول بيانات الموظفين الرئيسي -->
<div class="ems-card">
  <div class="ems-card-header">
    <div class="ems-card-title">قائمة كوادر مجموعة إياس الإعلامية</div>
    <span class="ems-badge badge-gold">36 كادراً ومسؤولاً</span>
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
            <th>نوع العقد</th>
            <th>الحالة</th>
            <th style="text-align: center;">الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-00</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #c5a059;">ص</div>
                <div>
                  <div style="font-weight: 700;">صالح رزق محمد حسنين</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">saleh@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td><span style="font-weight: 700; color: var(--ems-gold-hover);">رئيس مجلس الإدارة</span></td>
            <td>الإدارة العليا</td>
            <td>01 يناير 2020</td>
            <td><span class="ems-badge badge-gold">دائم / قيادي</span></td>
            <td><span class="ems-badge badge-completed">Super Admin</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="عرض الملف"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
            </td>
          </tr>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-01</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #1e293b;">م</div>
                <div>
                  <div style="font-weight: 700;">محمد ابو شحمه</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">m.shahme@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td>المدير التنفيذي</td>
            <td>الإدارة العليا والتنفيذية</td>
            <td>01 يونيو 2021</td>
            <td>دوام كامل</td>
            <td><span class="ems-badge badge-completed">على رأس عمله</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            </td>
          </tr>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-02</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #334155;">ب</div>
                <div>
                  <div style="font-weight: 700;">براء عمر</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">b.omar@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td>المستشار القانوني ونائب المدير التنفيذي</td>
            <td>الإدارة العليا والتنفيذية</td>
            <td>01 أغسطس 2021</td>
            <td>دوام كامل</td>
            <td><span class="ems-badge badge-completed">على رأس عمله</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            </td>
          </tr>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-03</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #475569;">ر</div>
                <div>
                  <div style="font-weight: 700;">رواء صلاح</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">r.salah@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td>السكرتارية التنفيذية ومتابعة المكاتب</td>
            <td>الإدارة العليا والتنفيذية</td>
            <td>10 يناير 2022</td>
            <td>دوام كامل</td>
            <td><span class="ems-badge badge-completed">على رأس عملها</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            </td>
          </tr>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-04</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #0284c7;">ا</div>
                <div>
                  <div style="font-weight: 700;">البراء ابو عامر</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">b.amer@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td>مسؤول قسم المرئي</td>
            <td>دائرة الإعلام</td>
            <td>15 فبراير 2022</td>
            <td>دوام كامل</td>
            <td><span class="ems-badge badge-completed">على رأس عمله</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            </td>
          </tr>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-05</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #0369a1;">ل</div>
                <div>
                  <div style="font-weight: 700;">لؤي بركة</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">l.baraka@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td>منسق الرقمي والنشر</td>
            <td>دائرة الإعلام</td>
            <td>01 مارس 2022</td>
            <td>دوام كامل</td>
            <td><span class="ems-badge badge-completed">على رأس عمله</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            </td>
          </tr>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-06</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #3b82f6;">أ</div>
                <div>
                  <div style="font-weight: 700;">أحمد حبيب</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">ahmed.habib@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td>مسؤول قسم تطوير الويب والمنصات</td>
            <td>دائرة البرمجة والتقنية</td>
            <td>15 مارس 2022</td>
            <td>دوام كامل</td>
            <td><span class="ems-badge badge-completed">على رأس عمله</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            </td>
          </tr>
          <tr>
            <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">EMP-21</span></td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <div class="ems-avatar" style="width: 36px; height: 36px; background: #d97706;">ع</div>
                <div>
                  <div style="font-weight: 700;">عمر العشي</div>
                  <div style="font-size: 11.5px; color: var(--ems-text-muted);">o.ashi@eyasmedia.com</div>
                </div>
              </div>
            </td>
            <td>منسق التسويق الرقمي</td>
            <td>دائرة التسويق</td>
            <td>01 مايو 2022</td>
            <td>دوام كامل</td>
            <td><span class="ems-badge badge-completed">على رأس عمله</span></td>
            <td style="text-align: center;">
              <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- نافذة إضافة موظف -->
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
            <input type="text" id="emp_code" name="emp_code" class="ems-input" value="EMP-108" required readonly style="background-color: var(--ems-surface-alt);">
          </div>
          <div class="ems-form-group">
            <label for="national_id" class="ems-label"><span class="required">*</span> رقم الهوية / الإقامة</label>
            <input type="text" id="national_id" name="national_id" class="ems-input" placeholder="مثال: 900123456" required>
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
            <label for="email" class="ems-label"><span class="required">*</span> البريد المؤسسي</label>
            <input type="email" id="email" name="email" class="ems-input" required placeholder="name@eyasmedia.com">
          </div>
          <div class="ems-form-group">
            <label for="phone" class="ems-label"><span class="required">*</span> رقم الهاتف</label>
            <input type="tel" id="phone" name="phone" class="ems-input" required>
          </div>
        </div>

        <div class="ems-form-row">
          <div class="ems-form-group">
            <label for="department_id" class="ems-label"><span class="required">*</span> الدائرة الإدارية</label>
            <select id="department_id" name="department_id" class="ems-select" required>
              <option value="1">الإدارة العليا والتنفيذية</option>
              <option value="2">دائرة الإعلام</option>
              <option value="3">دائرة البرمجة والتقنية</option>
              <option value="4">دائرة التسويق</option>
            </select>
          </div>
          <div class="ems-form-group">
            <label for="job_title" class="ems-label"><span class="required">*</span> المسمى الوظيفي</label>
            <input type="text" id="job_title" name="job_title" class="ems-input" required>
          </div>
        </div>

        <div class="ems-form-row">
          <div class="ems-form-group">
            <label for="hire_date" class="ems-label"><span class="required">*</span> تاريخ التعيين</label>
            <input type="date" id="hire_date" name="hire_date" class="ems-input" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="ems-form-group">
            <label for="basic_salary" class="ems-label"><span class="required">*</span> الراتب الأساسي (USD)</label>
            <input type="number" id="basic_salary" name="basic_salary" class="ems-input" placeholder="1500" required>
          </div>
        </div>
      </div>
      <div class="ems-modal-footer">
        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-add-employee')">إلغاء</button>
        <button type="submit" class="ems-btn ems-btn-primary">حفظ وبيانات الموظف</button>
      </div>
    </form>
  </div>
</div>
@endsection
