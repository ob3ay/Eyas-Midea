@extends('layouts.portal')
@section('title', 'الحضور والانصراف')
@section('content')
<div class="ems-content">
        <div class="ems-page-header">
          <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
              <a href="dashboard.html">الرئيسية</a>
              <span class="ems-breadcrumb-separator">/</span>
              <span>الموارد والرقابة</span>
              <span class="ems-breadcrumb-separator">/</span>
              <span style="color: var(--ems-text-main); font-weight: 700;">الحضور والانصراف</span>
            </div>
            <h1 class="ems-page-title">سجل الحضور والانصراف والبصمة الإلكترونية</h1>
            <p class="ems-page-subtitle">متابعة دوام كوادر مجموعة إياس الإعلامية وساعات العمل الفعلية والتأخيرات</p>
          </div>
          <div class="ems-page-actions">
            <button class="ems-btn ems-btn-outline" onclick="showToast('جاري تصدير تقرير دوام الشهر الحالي PDF...', 'info')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span>تصدير التقرير الشهري</span>
            </button>
            <button class="ems-btn ems-btn-primary" onclick="openModal('modal-log-attendance')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span>+ تسجيل يدوي / إذن دوام</span>
            </button>
          </div>
        </div>

        <!-- صندوق تسجيل الحضور السريع المباشر -->
        <div class="ems-attendance-widget-card">
          <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 58px; height: 58px; border-radius: var(--radius-md); background: #ffffff; border: 1px solid var(--ems-gold-border); display: flex; align-items: center; justify-content: center; color: var(--ems-gold-hover); box-shadow: var(--shadow-sm);">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
              <div style="font-size: 13px; font-weight: 700; color: var(--ems-text-muted);">التوقيت الفعلي المعتمد (القدس / غزة GMT+3)</div>
              <div class="ems-time-display ems-live-clock">--:--:--</div>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="text-align: left; padding-left: 16px; border-left: 1px solid var(--ems-border);">
              <div style="font-size: 11.5px; color: var(--ems-text-muted);">المستخدم الحالي:</div>
              <div style="font-size: 13.5px; font-weight: 800; color: var(--ems-text-main);">صالح رزق محمد حسنين</div>
              <span class="ems-badge badge-completed" style="font-size: 10px; margin-top: 3px;">حاضر (08:52 ص)</span>
            </div>
            <button class="ems-btn ems-btn-primary" onclick="quickAttendanceAction()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
              <span>تسجيل بصمة الآن</span>
            </button>
          </div>
        </div>

        <!-- بطاقات إحصائيات الدوام -->
        <div class="ems-stats-grid">
          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">نسبة الالتزام بالدوام</span>
              <span class="ems-stat-value" style="color: #10b981;">90.6%</span>
              <span class="ems-stat-subtext">29 من أصل 32 موظفاً</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">حضور في الوقت المحدد</span>
              <span class="ems-stat-value">26</span>
              <span class="ems-stat-subtext">قبل أو عند الساعة 09:00 ص</span>
            </div>
            <div class="ems-stat-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">حالات تأخير اليوم</span>
              <span class="ems-stat-value" style="color: #d97706;">3</span>
              <span class="ems-stat-subtext">أقل من 30 دقيقة (مقبول)</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">إجازات ومهمات عمل</span>
              <span class="ems-stat-value" style="color: #6d28d9;">3</span>
              <span class="ems-stat-subtext">مهمة تصوير ميدانية وإجازات</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 2v4"/><path d="M8 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/></svg>
            </div>
          </div>
        </div>

        <!-- جدول الحضور والانصراف اليومي -->
        <div class="ems-card">
          <div class="ems-card-header">
            <div class="ems-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <span>سجل الحضور اليومي - اليوم: الأحد 06 سبتمبر 2026</span>
            </div>
            <span class="ems-badge badge-gold">دوام الصباح والمساء</span>
          </div>

          <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
              <table class="ems-table" id="attendanceTable">
                <thead>
                  <tr>
                    <th>الموظف</th>
                    <th>الدائرة</th>
                    <th>وقت الحضور (In)</th>
                    <th>وقت الانصراف (Out)</th>
                    <th>ساعات العمل</th>
                    <th>حالة الحضور</th>
                    <th>ملاحظات</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background: #c5a059;">ص</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">صالح رزق محمد حسنين</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">رئيس مجلس الإدارة (EMP-00)</div>
                        </div>
                      </div>
                    </td>
                    <td>الإدارة العليا</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">08:52 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.5 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">حضور كامل</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#1e293b;">م</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">محمد ابو شحمه</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">المدير التنفيذي (EMP-01)</div>
                        </div>
                      </div>
                    </td>
                    <td>الإدارة العليا والتنفيذية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">08:55 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.4 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">-</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#334155;">ب</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">براء عمر</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">المستشار القانوني ونائب المدير (EMP-02)</div>
                        </div>
                      </div>
                    </td>
                    <td>الإدارة العليا والتنفيذية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">09:00 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.3 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">-</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#475569;">ر</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">رواء صلاح</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">السكرتارية التنفيذية (EMP-03)</div>
                        </div>
                      </div>
                    </td>
                    <td>الإدارة العليا والتنفيذية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">08:48 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.5 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">-</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#0284c7;">ا</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">البراء ابو عامر</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">دائرة الإعلام - قسم المرئي (EMP-04)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة الإعلام</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">09:05 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.2 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">-</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#0369a1;">ل</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">لؤي بركة</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">دائرة الإعلام - منسق الرقمي (EMP-05)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة الإعلام</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">09:08 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.1 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">-</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#3b82f6;">أ</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">أحمد حبيب</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">مسؤول تطوير الويب (EMP-06)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">08:58 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.2 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">-</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#ea580c;">ر</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">رانيا أبو شعبان</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">مهندسة تطبيقات هواتف (EMP-12)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #6d28d9;">09:30 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">--</span></td>
                    <td><span class="ems-badge badge-review">مهمة خارجية</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">ربط منصة البنك الرقمي</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#d97706;">ع</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">عمر العشي</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">منسق التسويق الرقمي (EMP-21)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة التسويق</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">09:02 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">7.0 س</span></td>
                    <td><span class="ems-badge badge-completed">في الوقت المحدد</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">-</span></td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#b45309;">ك</div>
                        <div>
                          <strong style="color: var(--ems-text-main);">كمال أبو رمضان</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">مدير الحملات الإعلانية (EMP-26)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة التسويق</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #b45309;">09:15 ص</span></td>
                    <td><span style="font-family: var(--font-mono); color: var(--ems-text-muted);">--:--</span></td>
                    <td><span style="font-family: var(--font-mono);">6.8 س</span></td>
                    <td><span class="ems-badge badge-progress">تأخير 15 دقيقة</span></td>
                    <td><span style="font-size: 11.5px; color: var(--ems-text-muted);">عذر مقبول (ازدحام سير)</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
@endsection

