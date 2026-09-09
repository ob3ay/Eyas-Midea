@extends('layouts.portal')
@section('title', 'المستخدمون ومصفوفة الصلاحيات')
@section('content')
<div class="ems-content">
        <div class="ems-page-header">
          <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
              <a href="dashboard.html">الرئيسية</a>
              <span class="ems-breadcrumb-separator">/</span>
              <span>الأمان والتحكم</span>
              <span class="ems-breadcrumb-separator">/</span>
              <span style="color: var(--ems-text-main); font-weight: 700;">المستخدمون ومصفوفة الصلاحيات</span>
            </div>
            <h1 class="ems-page-title">إدارة المستخدمين ومصفوفة الصلاحيات (RBAC & Scopes)</h1>
            <p class="ems-page-subtitle">التحكم في أدوار النظام، نطاقات الوصول، وإدارة حسابات بوابة مجموعة إياس الإعلامية</p>
          </div>
          <div class="ems-page-actions">
            <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-user')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
              <span>+ إضافة حساب مستخدم جديد</span>
            </button>
          </div>
        </div>

        <!-- بطاقة رئيس مجلس الإدارة المميزة (Super Admin / Full Scope) -->
        <div class="ems-super-admin-card">
          <div style="display: flex; align-items: center; gap: 18px;">
            <div class="ems-avatar" style="width: 58px; height: 58px; font-size: 22px;">ص</div>
            <div>
              <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h2 style="font-size: 18px; font-weight: 800; color: var(--ems-text-main);">صالح رزق محمد حسنين</h2>
                <span class="ems-badge badge-gold">Super Admin</span>
                <span class="ems-badge badge-completed">Full Scope (نطاق كامل)</span>
              </div>
              <div style="font-size: 13px; color: var(--ems-text-muted);">
                رئيس مجلس الإدارة • المشرف العام على نظام مجموعة إياس الإعلامية (EMS)
              </div>
              <div style="font-size: 11.5px; color: var(--ems-text-light); margin-top: 4px; font-family: var(--font-mono);">
                saleh@eyasmedia.com • صلاحية الوصول لجميع الدوائر والمشاريع والاعتمادات المالية والرقابية
              </div>
            </div>
          </div>

          <div>
            <span class="ems-badge badge-gold" style="padding: 8px 16px; font-size: 13px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <span>كامل الصلاحيات دون قيود (100%)</span>
            </span>
          </div>
        </div>

        <!-- مصفوفة الصلاحيات التفاعلية (Role-Based Access Control Matrix) -->
        <div class="ems-card">
          <div class="ems-card-header">
            <div class="ems-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              <span>مصفوفة الصلاحيات المعتمدة ونطاقات الوصول (Access Matrix)</span>
            </div>
            <span class="ems-badge badge-gold">5 مستويات إدارية</span>
          </div>

          <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
              <table class="ems-table ems-matrix-table">
                <thead>
                  <tr>
                    <th>الوحدة الوظيفية / الصلاحية</th>
                    <th>
                      <div>رئيس مجلس الإدارة</div>
                      <div class="scope-pill" style="color: var(--ems-gold-hover); margin-top: 4px;">Full Scope</div>
                    </th>
                    <th>
                      <div>المدير العام / التنفيذي</div>
                      <div class="scope-pill" style="margin-top: 4px;">Executive Scope</div>
                    </th>
                    <th>
                      <div>مدراء الدوائر</div>
                      <div class="scope-pill" style="margin-top: 4px;">Department Scope</div>
                    </th>
                    <th>
                      <div>الموظفون والمنفذون</div>
                      <div class="scope-pill" style="margin-top: 4px;">Assigned Scope</div>
                    </th>
                    <th>
                      <div>التدقيق والمشاهدة</div>
                      <div class="scope-pill" style="margin-top: 4px;">Audit Scope</div>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>1. إدارة شؤون الموظفين (إضافة، تعديل، أجور)</strong></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ تعديل/عرض</span></td>
                    <td><span class="check-icon">✓ كادر الدائرة</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span style="font-size: 12px; color: var(--ems-text-muted);">قراءة فقط</span></td>
                  </tr>

                  <tr>
                    <td><strong>2. الدوائر والأقسام (تعديل الهيكل والميزانيات)</strong></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span style="font-size: 12px; color: var(--ems-text-muted);">قراءة فقط</span></td>
                  </tr>

                  <tr>
                    <td><strong>3. المشاريع البرمجية والإعلامية (إنشاء واعتماد)</strong></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ مشاريع الدائرة</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span style="font-size: 12px; color: var(--ems-text-muted);">قراءة فقط</span></td>
                  </tr>

                  <tr>
                    <td><strong>4. إدارة المهام (تكليف، مراجعة، نقل الحالات الخمس)</strong></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ كامل الدائرة</span></td>
                    <td><span class="check-icon">✓ المهام المسندة</span></td>
                    <td><span style="font-size: 12px; color: var(--ems-text-muted);">قراءة فقط</span></td>
                  </tr>

                  <tr>
                    <td><strong>5. الحضور والانصراف والبصمة (الرقابة والتعديل)</strong></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ كامل</span></td>
                    <td><span class="check-icon">✓ تقارير الدائرة</span></td>
                    <td><span class="check-icon">✓ بصمته الشخصية</span></td>
                    <td><span style="font-size: 12px; color: var(--ems-text-muted);">قراءة فقط</span></td>
                  </tr>

                  <tr>
                    <td><strong>6. تقييم الأداء والإنجاز (KPIs والمصادقة)</strong></td>
                    <td><span class="check-icon">✓ اعتماد نهائي</span></td>
                    <td><span class="check-icon">✓ تقييم شامل</span></td>
                    <td><span class="check-icon">✓ تقييم الكادر</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span style="font-size: 12px; color: var(--ems-text-muted);">قراءة فقط</span></td>
                  </tr>

                  <tr>
                    <td><strong>7. إدارة المستخدمين والحسابات ومصفوفة الأمان</strong></td>
                    <td><span class="check-icon">✓ حصري</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span class="cross-icon">✕</span></td>
                    <td><span class="cross-icon">✕</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- جدول حسابات المستخدمين في النظام -->
        <div class="ems-card">
          <div class="ems-card-header">
            <div class="ems-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              <span>حسابات مستخدمي بوابة مجموعة إياس الإعلامية</span>
            </div>
            <span class="ems-badge badge-gold">8 حسابات قيادية وتنفيذية نشطة</span>
          </div>

          <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
              <table class="ems-table" id="usersTable">
                <thead>
                  <tr>
                    <th>المستخدم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور المعتمد</th>
                    <th>نطاق الصلاحية (Scope)</th>
                    <th>حالة الحساب</th>
                    <th>آخر تسجيل دخول</th>
                    <th style="text-align: center;">الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  <tr style="background-color: #fdfbf7;">
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background: #c5a059;">ص</div>
                        <div>
                          <strong>صالح رزق محمد حسنين</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-00</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">saleh@eyasmedia.com</span></td>
                    <td><span class="ems-badge badge-gold">رئيس مجلس الإدارة</span></td>
                    <td><span class="scope-pill" style="color: var(--ems-gold-hover); font-weight: 800;">Full Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 08:52 ص</td>
                    <td style="text-align: center;"><span style="font-size: 12px; color: var(--ems-gold-hover); font-weight: 700;">المشرف الرئيسي</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#1e293b;">م</div>
                        <div>
                          <strong>محمد ابو شحمه</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-01</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">m.shahme@eyasmedia.com</span></td>
                    <td><span class="ems-badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">المدير التنفيذي</span></td>
                    <td><span class="scope-pill" style="color:#1d4ed8; font-weight:700;">Executive Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 08:55 ص</td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل الدور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#334155;">ب</div>
                        <div>
                          <strong>براء عمر</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-02</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">b.omar@eyasmedia.com</span></td>
                    <td><span class="ems-badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1;">المستشار القانوني ونائب المدير</span></td>
                    <td><span class="scope-pill" style="color:#334155; font-weight:700;">Executive Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 09:00 ص</td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل الدور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#475569;">ر</div>
                        <div>
                          <strong>رواء صلاح</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-03</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">r.salah@eyasmedia.com</span></td>
                    <td>السكرتارية التنفيذية ومتابعة المكاتب</td>
                    <td><span class="scope-pill">Operational Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 08:48 ص</td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل الدور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#0284c7;">ا</div>
                        <div>
                          <strong>البراء ابو عامر</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-04</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">b.amer@eyasmedia.com</span></td>
                    <td>دائرة الإعلام - قسم المرئي</td>
                    <td><span class="scope-pill">Department Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 09:10 ص</td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل الدور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#0369a1;">ل</div>
                        <div>
                          <strong>لؤي بركة</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-05</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">l.baraka@eyasmedia.com</span></td>
                    <td>دائرة الإعلام - منسق الرقمي</td>
                    <td><span class="scope-pill">Department Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 09:12 ص</td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل الدور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#3b82f6;">أ</div>
                        <div>
                          <strong>أحمد حبيب</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-06</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">ahmed.habib@eyasmedia.com</span></td>
                    <td>دائرة البرمجة والتقنية - مسؤول الويب</td>
                    <td><span class="scope-pill">Department Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 08:58 ص</td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل الدور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#d97706;">ع</div>
                        <div>
                          <strong>عمر العشي</strong>
                          <span style="font-size: 10.5px; color: var(--ems-text-muted); display: block; font-family: var(--font-mono);">EMP-21</span>
                        </div>
                      </div>
                    </td>
                    <td><span style="font-family: var(--font-mono);">o.ashi@eyasmedia.com</span></td>
                    <td>دائرة التسويق - منسق التسويق الرقمي</td>
                    <td><span class="scope-pill">Department Scope</span></td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td>اليوم، 09:15 ص</td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل الدور"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
@endsection

