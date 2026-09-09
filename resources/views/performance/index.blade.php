@extends('layouts.portal')
@section('title', 'الأداء والإنجاز')
@section('content')
<div class="ems-content">
        <div class="ems-page-header">
          <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
              <a href="dashboard.html">الرئيسية</a>
              <span class="ems-breadcrumb-separator">/</span>
              <span>الرقابة والتقييم</span>
              <span class="ems-breadcrumb-separator">/</span>
              <span style="color: var(--ems-text-main); font-weight: 700;">الأداء والإنجاز</span>
            </div>
            <h1 class="ems-page-title">مؤشرات الأداء والإنجاز المؤسسي (KPIs)</h1>
            <p class="ems-page-subtitle">تقييم الكفاءة والإنتاجية للدوائر والكوادر التابعة لـ مجموعة إياس الإعلامية</p>
          </div>
          <div class="ems-page-actions">
            <button class="ems-btn ems-btn-outline" onclick="showToast('توليد التقرير الربع سنوي للأداء...', 'info')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
              <span>تقرير الأداء الربع سنوي</span>
            </button>
            <button class="ems-btn ems-btn-primary" onclick="openModal('modal-evaluate-performance')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
              <span>+ إضافة تقييم أداء جديد</span>
            </button>
          </div>
        </div>

        <!-- البانر الرئيسي لمؤشر الأداء الكلي -->
        <div class="ems-kpi-banner">
          <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--ems-gold); text-transform: uppercase; margin-bottom: 6px;">مؤشر الإنتاجية العام للمجموعة (EMS Overall Score)</div>
            <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 6px;">كفاءة العمليات والإنتاج لعام 2026</h2>
            <p style="font-size: 13px; color: #94a3b8; max-width: 550px;">تم قياس الأداء بناءً على سرعة إنجاز المهام، جودة المخرجات البرمجية والمرئية، ونسب الالتزام بمواعيد التسليم.</p>
          </div>

          <div style="display: flex; align-items: center; gap: 24px;">
            <div style="text-align: center;">
              <div class="ems-kpi-big-num">88.4%</div>
              <span class="ems-badge badge-completed" style="margin-top: 4px;">تصنيف: ممتاز</span>
            </div>
          </div>
        </div>

        <!-- بطاقات مؤشرات الأداء الفرعية -->
        <div class="ems-stats-grid">
          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">التسليم في الموعد المحدد</span>
              <span class="ems-stat-value" style="color: #10b981;">92.5%</span>
              <span class="ems-stat-subtext">التزام عالي بالجداول الزمنية</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 10"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">معيار الجودة وخلو الأخطاء</span>
              <span class="ems-stat-value" style="color: #6d28d9;">94.0%</span>
              <span class="ems-stat-subtext">اجتياز فحص ضمان الجودة QA</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">معدل سرعة إغلاق المهام</span>
              <span class="ems-stat-value" style="color: var(--ems-gold-hover);">1.8 يوم</span>
              <span class="ems-stat-subtext">أسرع بـ 15% من الربع السابق</span>
            </div>
            <div class="ems-stat-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">معدل رضا العملاء والإدارة</span>
              <span class="ems-stat-value" style="color: #10b981;">4.9 / 5</span>
              <span class="ems-stat-subtext">تقييم مخرجات المشاريع</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
          </div>
        </div>

        <!-- جدول تقييم أداء الكوادر -->
        <div class="ems-card">
          <div class="ems-card-header">
            <div class="ems-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
              <span>سجل تقييم أداء كوادر مجموعة إياس الإعلامية (Q3 - 2026)</span>
            </div>
            <span class="ems-badge badge-gold">اعتماد الإدارة العامة</span>
          </div>

          <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
              <table class="ems-table" id="performanceTable">
                <thead>
                  <tr>
                    <th>الموظف</th>
                    <th>الدائرة والقسم</th>
                    <th>المهام المنجزة</th>
                    <th>الالتزام بالموعد</th>
                    <th>معيار الجودة</th>
                    <th>التقييم العام</th>
                    <th style="text-align: center;">الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#1e293b;">م</div>
                        <div>
                          <strong>محمد ابو شحمه</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">المدير التنفيذي (EMP-01)</div>
                        </div>
                      </div>
                    </td>
                    <td>الإدارة العليا والتنفيذية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">24 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">99%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 99%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">99%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز مرتفع (A+)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير التفصيلي" onclick="showToast('تقرير تقييم محمد ابو شحمه: معتمد من رئيس مجلس الإدارة', 'info')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      </button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#334155;">ب</div>
                        <div>
                          <strong>براء عمر</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">المستشار القانوني ونائب المدير (EMP-02)</div>
                        </div>
                      </div>
                    </td>
                    <td>الإدارة العليا والتنفيذية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">18 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">98%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 98%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">98%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز (A+)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#0284c7;">ا</div>
                        <div>
                          <strong>البراء ابو عامر</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">دائرة الإعلام - قسم المرئي (EMP-04)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة الإعلام</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">15 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">95%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 95%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">95%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز (A)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#0369a1;">ل</div>
                        <div>
                          <strong>لؤي بركة</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">دائرة الإعلام - منسق الرقمي (EMP-05)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة الإعلام</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">14 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">94%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 94%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">94%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز (A)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#3b82f6;">أ</div>
                        <div>
                          <strong>أحمد حبيب</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">مسؤول تطوير الويب (EMP-06)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">18 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">96%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 96%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">96%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز (A+)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#ea580c;">ر</div>
                        <div>
                          <strong>رانيا أبو شعبان</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">مهندسة تطبيقات هواتف (EMP-12)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">16 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">95%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 95%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">95%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز (A)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#d97706;">ع</div>
                        <div>
                          <strong>عمر العشي</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">منسق التسويق الرقمي (EMP-21)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة التسويق</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">17 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">96%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 96%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">96%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز (A+)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#b45309;">ك</div>
                        <div>
                          <strong>كمال أبو رمضان</strong>
                          <div style="font-size: 11px; color: var(--ems-text-muted);">مدير الحملات الإعلانية (EMP-26)</div>
                        </div>
                      </div>
                    </td>
                    <td>دائرة التسويق</td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700;">14 مهمة</span></td>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: #15803d;">92%</span></td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <div class="ems-progress" style="width: 70px; height: 6px;"><div class="ems-progress-bar" style="width: 92%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">92%</span>
                      </div>
                    </td>
                    <td><span class="ems-badge badge-completed">ممتاز (A)</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="عرض التقرير"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
@endsection

