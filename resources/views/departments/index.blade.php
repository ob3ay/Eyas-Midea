@extends('layouts.portal')
@section('title', 'الدوائر والأقسام')
@section('content')
<div class="ems-content">
        <div class="ems-page-header">
          <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
              <a href="dashboard.html">الرئيسية</a>
              <span class="ems-breadcrumb-separator">/</span>
              <span>الهيكلية المؤسسية</span>
              <span class="ems-breadcrumb-separator">/</span>
              <span style="color: var(--ems-text-main); font-weight: 700;">الدوائر والأقسام</span>
            </div>
            <h1 class="ems-page-title">الدوائر والأقسام التخصصية</h1>
            <p class="ems-page-subtitle">الهيكل التنظيمي المعتمد لمجموعة إياس الإعلامية وتوزيع الكوادر والمسؤوليات</p>
          </div>
          <div class="ems-page-actions">
            <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-department')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span>+ إضافة دائرة أو قسم جديد</span>
            </button>
          </div>
        </div>

        <!-- شبكة بطاقات الدوائر الأربع الأساسية المعتمدة -->
        <div class="ems-dept-grid">
          
          <!-- الدائرة 1: الإدارة العليا والتنفيذية -->
          <div class="ems-dept-card">
            <div>
              <div class="ems-dept-header">
                <div class="ems-dept-icon" style="background:#fef3c7; color:#b45309; border-color:#fde68a;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="ems-badge badge-gold">DEPT-01</span>
              </div>
              <h2 class="ems-dept-title">الإدارة العليا والتنفيذية</h2>
              <p class="ems-dept-desc">القيادة التنفيذية، صياغة السياسات الإعلامية والتحول الرقمي، الإشراف القانوني والرقابي، والأمانة التنفيذية للمجموعة.</p>
            </div>
            <div>
              <div class="ems-dept-meta">
                <div>
                  <div class="ems-dept-meta-item-label">الكادر القيادي</div>
                  <div class="ems-dept-meta-item-val">4</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">الصلاحيات</div>
                  <div class="ems-dept-meta-item-val" style="font-size:12px; color:var(--ems-gold-hover);">Full Scope</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">رئيس المجلس</div>
                  <div style="font-size: 11px; font-weight: 700; color: var(--ems-gold-hover); margin-top: 2px;">صالح حسنين</div>
                </div>
              </div>
              <button class="ems-btn ems-btn-secondary ems-btn-sm" style="width: 100%;" onclick="showToast('الإدارة العليا: صالح حسنين، محمد ابو شحمه، براء عمر، رواء صلاح', 'info')">استعراض الهيكل القيادي ←</button>
            </div>
          </div>

          <!-- الدائرة 2: دائرة الإعلام -->
          <div class="ems-dept-card">
            <div>
              <div class="ems-dept-header">
                <div class="ems-dept-icon" style="background:#e0f2fe; color:#0369a1; border-color:#bae6fd;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                </div>
                <span class="ems-badge badge-gold">DEPT-02</span>
              </div>
              <h2 class="ems-dept-title">دائرة الإعلام</h2>
              <p class="ems-dept-desc">الإنتاج المرئي، استوديوهات التصوير الميداني، التنسيق الرقمي والنشر الصحفي، وتغطية الأحداث وصناعة التقارير التوثيقية.</p>
            </div>
            <div>
              <div class="ems-dept-meta">
                <div>
                  <div class="ems-dept-meta-item-label">الكادر</div>
                  <div class="ems-dept-meta-item-val">2</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">المشاريع</div>
                  <div class="ems-dept-meta-item-val">5</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">التنسيق والإشراف</div>
                  <div style="font-size: 11px; font-weight: 700; color: var(--ems-text-main); margin-top: 2px;">البراء ابو عامر / لؤي بركة</div>
                </div>
              </div>
              <button class="ems-btn ems-btn-secondary ems-btn-sm" style="width: 100%;" onclick="showToast('دائرة الإعلام: البراء ابو عامر (المرئي)، لؤي بركة (الرقمي)', 'info')">استعراض الأقسام والإنتاج ←</button>
            </div>
          </div>

          <!-- الدائرة 3: دائرة البرمجة والتقنية -->
          <div class="ems-dept-card">
            <div>
              <div class="ems-dept-header">
                <div class="ems-dept-icon" style="background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                </div>
                <span class="ems-badge badge-gold">DEPT-03</span>
              </div>
              <h2 class="ems-dept-title">دائرة البرمجة والتقنية</h2>
              <p class="ems-dept-desc">تطوير المنصات والأنظمة السحابية (Web)، هندسة تطبيقات الهواتف الذكية (Mobile)، وإدارة قواعد البيانات والسيرفرات وحماية المعلومات.</p>
            </div>
            <div>
              <div class="ems-dept-meta">
                <div>
                  <div class="ems-dept-meta-item-label">الكادر التقني</div>
                  <div class="ems-dept-meta-item-val">15</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">الأرقام الوظيفية</div>
                  <div class="ems-dept-meta-item-val" style="font-size:12px; color:#1d4ed8;">6 - 20</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">الأقسام</div>
                  <div style="font-size: 11px; font-weight: 700; color: var(--ems-text-main); margin-top: 2px;">ويب • تطبيقات • قواعد بيانات</div>
                </div>
              </div>
              <button class="ems-btn ems-btn-secondary ems-btn-sm" style="width: 100%;" onclick="showToast('دائرة البرمجة والتقنية: 15 كادراً متخصصاً بالأرقام 6 إلى 20', 'info')">استعراض كوادر التقنية (15) ←</button>
            </div>
          </div>

          <!-- الدائرة 4: دائرة التسويق -->
          <div class="ems-dept-card">
            <div>
              <div class="ems-dept-header">
                <div class="ems-dept-icon" style="background:#fdf4ff; color:#a21caf; border-color:#f5d0fe;">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                </div>
                <span class="ems-badge badge-gold">DEPT-04</span>
              </div>
              <h2 class="ems-dept-title">دائرة التسويق</h2>
              <p class="ems-dept-desc">التسويق الرقمي وإدارة الحملات الإعلانية الممولة، العلاقات العامة والاتصال المؤسسي، وتحليل انتشار المنصات وتوسيع الجمهور المستهدف.</p>
            </div>
            <div>
              <div class="ems-dept-meta">
                <div>
                  <div class="ems-dept-meta-item-label">الكادر التسويقي</div>
                  <div class="ems-dept-meta-item-val">15</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">الأرقام الوظيفية</div>
                  <div class="ems-dept-meta-item-val" style="font-size:12px; color:#a21caf;">21 - 35</div>
                </div>
                <div>
                  <div class="ems-dept-meta-item-label">الأقسام</div>
                  <div style="font-size: 11px; font-weight: 700; color: var(--ems-text-main); margin-top: 2px;">رقمي • حملات • علاقات عامة</div>
                </div>
              </div>
              <button class="ems-btn ems-btn-secondary ems-btn-sm" style="width: 100%;" onclick="showToast('دائرة التسويق: 15 كادراً متخصصاً بالأرقام 21 إلى 35', 'info')">استعراض كوادر التسويق (15) ←</button>
            </div>
          </div>

        </div>

        <!-- جدول الأقسام التفصيلي -->
        <div class="ems-card">
          <div class="ems-card-header">
            <div class="ems-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M3 7v14"/><path d="M21 7v14"/></svg>
              <span>سجل الأقسام التخصصية والوحدات التنفيذية المعتمدة</span>
            </div>
            <span class="ems-badge badge-completed">8 وحدات تخصصية</span>
          </div>

          <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
              <table class="ems-table" id="departmentsTable">
                <thead>
                  <tr>
                    <th>رمز القسم</th>
                    <th>اسم القسم</th>
                    <th>الدائرة الرئيسية</th>
                    <th>المسؤول المباشر</th>
                    <th>عدد الكوادر</th>
                    <th>المجال والتخصص</th>
                    <th>الحالة</th>
                    <th style="text-align: center;">الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-001</span></td>
                    <td><strong style="color: var(--ems-text-main);">الأمانة العامة والسكرتارية التنفيذية</strong></td>
                    <td>الإدارة العليا والتنفيذية</td>
                    <td>رواء صلاح / براء عمر</td>
                    <td>4 قياديين</td>
                    <td>المتابعة الإدارية والرقابة</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-101</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم تطوير الويب والمنصات (Web Development)</strong></td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td>أحمد حبيب</td>
                    <td>6 مطورين (#6 - #11)</td>
                    <td>Full-Stack & Laravel & Vue</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-102</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم تطبيقات الهواتف الذكية (Mobile Apps)</strong></td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td>رانيا أبو شعبان</td>
                    <td>5 مهندسين (#12 - #16)</td>
                    <td>Flutter & iOS & Android</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-103</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم هندسة السيرفرات وقواعد البيانات (Database & DevOps)</strong></td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td>حمزة الريس</td>
                    <td>4 مهندسين (#17 - #20)</td>
                    <td>SQL & Cloud & Security</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-201</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم الإنتاج والتصوير المرئي (Visual Media)</strong></td>
                    <td>دائرة الإعلام</td>
                    <td>البراء ابو عامر (#4)</td>
                    <td>كادر تخصصي</td>
                    <td>تصوير، إخراج، مونتاج مرئي</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-202</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم التنسيق الرقمي والنشر (Digital Coordination)</strong></td>
                    <td>دائرة الإعلام</td>
                    <td>لؤي بركة (#5)</td>
                    <td>كادر تخصصي</td>
                    <td>تنسيق إعلامي ونشر رقمي</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-301</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم التسويق الرقمي (Digital Marketing)</strong></td>
                    <td>دائرة التسويق</td>
                    <td>عمر العشي (#21)</td>
                    <td>5 مسوقين (#21 - #25)</td>
                    <td>قنوات التواصل، SEO، استراتيجيات</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-302</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم إدارة الحملات الإعلانية (Campaign Management)</strong></td>
                    <td>دائرة التسويق</td>
                    <td>كمال أبو رمضان (#26)</td>
                    <td>5 مسوقين (#26 - #30)</td>
                    <td>إعلانات ممولة، خطط ترويجية</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-303</span></td>
                    <td><strong style="color: var(--ems-text-main);">قسم العلاقات العامة (Public Relations)</strong></td>
                    <td>دائرة التسويق</td>
                    <td>ماجد صيام (#31)</td>
                    <td>5 أخصائيين (#31 - #35)</td>
                    <td>اتصال مؤسسي، شراكات ورعاية</td>
                    <td><span class="ems-badge badge-completed">نشط</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
@endsection

