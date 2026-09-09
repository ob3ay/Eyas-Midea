@extends('layouts.portal')
@section('title', 'المشاريع والإنتاج')
@section('content')
<div class="ems-content">
        <div class="ems-page-header">
          <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
              <a href="dashboard.html">الرئيسية</a>
              <span class="ems-breadcrumb-separator">/</span>
              <span>إدارة الإنتاج</span>
              <span class="ems-breadcrumb-separator">/</span>
              <span style="color: var(--ems-text-main); font-weight: 700;">المشاريع والإنتاج</span>
            </div>
            <h1 class="ems-page-title">إدارة المشاريع البرمجية والإعلامية</h1>
            <p class="ems-page-subtitle">متابعة خطط التنفيذ، الميزانيات، ونسب الإنجاز لمشاريع مجموعة إياس الإعلامية</p>
          </div>
          <div class="ems-page-actions">
            <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-project')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span>+ مشروع إنتاجي جديد</span>
            </button>
          </div>
        </div>

        <!-- إحصائيات المشاريع -->
        <div class="ems-stats-grid">
          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">إجمالي المشاريع</span>
              <span class="ems-stat-value">8</span>
              <span class="ems-stat-subtext">5 برمجية • 3 إعلامية</span>
            </div>
            <div class="ems-stat-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">قيد التنفيذ والبرمجة</span>
              <span class="ems-stat-value" style="color: #d97706;">5</span>
              <span class="ems-stat-subtext">تطوير مستمر بالـ Sprints</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">المراجعة والاعتماد النهائي</span>
              <span class="ems-stat-value" style="color: #6d28d9;">2</span>
              <span class="ems-stat-subtext">فحص الـ QA والتدقيق</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
          </div>

          <div class="ems-stat-card">
            <div class="ems-stat-info">
              <span class="ems-stat-label">مكتملة ومسلمة</span>
              <span class="ems-stat-value" style="color: #10b981;">1</span>
              <span class="ems-stat-subtext">تم الإطلاق بنجاح 100%</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
          </div>
        </div>

        <!-- جدول بيانات المشاريع -->
        <div class="ems-card">
          <div class="ems-card-header">
            <div class="ems-card-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
              <span>سجل مشاريع مجموعة إياس الإعلامية</span>
            </div>
            <span class="ems-badge badge-gold">متوسط الإنجاز الكلي: 72%</span>
          </div>

          <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
              <table class="ems-table" id="projectsTable">
                <thead>
                  <tr>
                    <th>رمز المشروع</th>
                    <th>اسم المشروع ونوعه</th>
                    <th>الدائرة المسؤولة</th>
                    <th>مدير المشروع</th>
                    <th style="min-width: 140px;">نسبة الإنجاز</th>
                    <th>موعد التسليم</th>
                    <th>الحالة</th>
                    <th style="text-align: center;">الإجراءات</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">PRJ-201</span></td>
                    <td>
                      <div style="font-weight: 800; color: var(--ems-text-main);">بوابة الدفع والتجارة الرقمية الموحدة</div>
                      <div style="font-size: 11.5px; color: var(--ems-text-muted);">حلول برمجية وربط بنكي للمتاجر</div>
                    </td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td>أحمد حبيب</td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="ems-progress" style="height: 6px;"><div class="ems-progress-bar" style="width: 85%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">85%</span>
                      </div>
                    </td>
                    <td>30 أكتوبر 2026</td>
                    <td><span class="ems-badge badge-progress">قيد التنفيذ</span></td>
                    <td style="text-align: center;">
                      <a href="tasks.html" class="ems-btn ems-btn-icon" title="عرض المهام المرتبطة"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></a>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">PRJ-202</span></td>
                    <td>
                      <div style="font-weight: 800; color: var(--ems-text-main);">تطبيق منصة إياس للهواتف الذكية</div>
                      <div style="font-size: 11.5px; color: var(--ems-text-muted);">Flutter Android & iOS</div>
                    </td>
                    <td>دائرة البرمجة والتقنية</td>
                    <td>رانيا أبو شعبان</td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="ems-progress" style="height: 6px;"><div class="ems-progress-bar" style="width: 60%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">60%</span>
                      </div>
                    </td>
                    <td>15 نوفمبر 2026</td>
                    <td><span class="ems-badge badge-progress">قيد التنفيذ</span></td>
                    <td style="text-align: center;">
                      <a href="tasks.html" class="ems-btn ems-btn-icon" title="عرض المهام"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></a>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">PRJ-203</span></td>
                    <td>
                      <div style="font-weight: 800; color: var(--ems-text-main);">سلسلة وثائقيات الهوية والتراث الفلسطيني</div>
                      <div style="font-size: 11.5px; color: var(--ems-text-muted);">إنتاج وتصوير مرئي 4K سينمائي</div>
                    </td>
                    <td>دائرة الإنتاج الإعلامي</td>
                    <td>محمد النجار</td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="ems-progress" style="height: 6px;"><div class="ems-progress-bar" style="width: 70%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">70%</span>
                      </div>
                    </td>
                    <td>01 ديسمبر 2026</td>
                    <td><span class="ems-badge badge-progress">قيد التنفيذ</span></td>
                    <td style="text-align: center;">
                      <a href="tasks.html" class="ems-btn ems-btn-icon" title="عرض المهام"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></a>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">PRJ-204</span></td>
                    <td>
                      <div style="font-weight: 800; color: var(--ems-text-main);">حملة الهوية البصرية وإعادة التموضع المؤسسي</div>
                      <div style="font-size: 11.5px; color: var(--ems-text-muted);">هوية بصرية شاملة وعناصر الميديا</div>
                    </td>
                    <td>دائرة التصميم والمونتاج</td>
                    <td>سارة الخزندار</td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="ems-progress" style="height: 6px;"><div class="ems-progress-bar" style="width: 95%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono);">95%</span>
                      </div>
                    </td>
                    <td>20 سبتمبر 2026</td>
                    <td><span class="ems-badge badge-review">قيد المراجعة</span></td>
                    <td style="text-align: center;">
                      <a href="tasks.html" class="ems-btn ems-btn-icon" title="عرض المهام"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></a>
                    </td>
                  </tr>

                  <tr>
                    <td><span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">PRJ-205</span></td>
                    <td>
                      <div style="font-weight: 800; color: var(--ems-text-main);">تجهيز وتدشين استوديو البودكاست المرئي</div>
                      <div style="font-size: 11.5px; color: var(--ems-text-muted);">عزل صوتي، كاميرات بث، ومكسرات صوت</div>
                    </td>
                    <td>دائرة الإنتاج الإعلامي</td>
                    <td>صالح حسنين (إشراف)</td>
                    <td>
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="ems-progress" style="height: 6px;"><div class="ems-progress-bar success" style="width: 100%;"></div></div>
                        <span style="font-size: 11px; font-weight: 700; font-family: var(--font-mono); color: #10b981;">100%</span>
                      </div>
                    </td>
                    <td>01 أغسطس 2026</td>
                    <td><span class="ems-badge badge-completed">مكتمل ومسلّم</span></td>
                    <td style="text-align: center;">
                      <button class="ems-btn ems-btn-icon" title="تفاصيل"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
@endsection

