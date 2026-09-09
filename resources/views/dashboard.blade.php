@extends('layouts.portal')

@section('title', 'لوحة التحكم التنفيذية')

@section('content')
<!-- بانر الترحيب التنفيذي -->
<div class="ems-welcome-banner">
  <div>
    <span class="ems-badge badge-gold" style="margin-bottom: 8px;">بوابة الإدارة الرقمية الموحدة (EMS)</span>
    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 6px;">
      مرحباً بك، رئيس مجلس الإدارة: صالح رزق محمد حسنين
    </h1>
    <p style="font-size: 13.5px; color: #94a3b8; max-width: 600px;">
      نظرة تنفيذية شاملة على سير العمليات والإنتاج وشؤون الكوادر في مجموعة إياس الإعلامية بكافة دوائرها التخصصية.
    </p>
  </div>

  <div style="display: flex; align-items: center; gap: 12px;">
    <a href="{{ route('projects.index') }}" class="ems-btn ems-btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>+ مشروع جديد</span>
    </a>
    <a href="{{ route('tasks.index') }}" class="ems-btn ems-btn-secondary" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: #fff;">
      <span>متابعة المهام (14)</span>
    </a>
  </div>
</div>

<!-- 4 بطاقات إحصائية رئيسية -->
<div class="ems-stats-grid">
  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">إجمالي الكادر والقيادة</span>
      <span class="ems-stat-value">36</span>
      <span class="ems-stat-subtext" style="color: #10b981;">4 دوائر تخصصية رئيسية</span>
    </div>
    <div class="ems-stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
  </div>

  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">المشاريع البرمجية والإعلامية</span>
      <span class="ems-stat-value">8</span>
      <span class="ems-stat-subtext" style="color: #d97706;">5 مشاريع قيد التطوير</span>
    </div>
    <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
    </div>
  </div>

  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">المهام في دورة العمل</span>
      <span class="ems-stat-value">14</span>
      <span class="ems-stat-subtext" style="color: #6d28d9;">3 مهام قيد المراجعة</span>
    </div>
    <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
    </div>
  </div>

  <div class="ems-stat-card">
    <div class="ems-stat-info">
      <span class="ems-stat-label">نسبة الالتزام بالدوام اليوم</span>
      <span class="ems-stat-value" style="color: #10b981;">90.6%</span>
      <span class="ems-stat-subtext">29 حاضراً في الميدان والمقر</span>
    </div>
    <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
  </div>
</div>

<div class="ems-two-columns">
  <div>
    <!-- بطاقة المشاريع الحيوية -->
    <div class="ems-card">
      <div class="ems-card-header">
        <div class="ems-card-title">مشاريع الإنتاج والتطوير النشطة</div>
        <a href="{{ route('projects.index') }}" style="font-size: 12.5px; font-weight: 700; color: var(--ems-gold-hover);">عرض الكل (8) ←</a>
      </div>
      <div class="ems-card-body">
        <div style="display: flex; flex-direction: column; gap: 18px;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <div>
                <strong style="font-size: 14px; color: var(--ems-text-main);">بوابة الدفع والتجارة الرقمية الموحدة</strong>
                <span class="ems-badge badge-gold" style="font-size: 10px; margin-right: 6px;">دائرة البرمجة والتقنية</span>
              </div>
              <span style="font-family: var(--font-mono); font-weight: 700; font-size: 13px; color: var(--ems-gold-hover);">85%</span>
            </div>
            <div class="ems-progress"><div class="ems-progress-bar" style="width: 85%;"></div></div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <div>
                <strong style="font-size: 14px; color: var(--ems-text-main);">سلسلة وثائقيات الهوية والتراث الفلسطيني</strong>
                <span class="ems-badge badge-gold" style="font-size: 10px; margin-right: 6px;">دائرة الإعلام</span>
              </div>
              <span style="font-family: var(--font-mono); font-weight: 700; font-size: 13px; color: var(--ems-gold-hover);">70%</span>
            </div>
            <div class="ems-progress"><div class="ems-progress-bar" style="width: 70%;"></div></div>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <div>
                <strong style="font-size: 14px; color: var(--ems-text-main);">تطبيق منصة إياس للهواتف الذكية</strong>
                <span class="ems-badge badge-gold" style="font-size: 10px; margin-right: 6px;">دائرة البرمجة والتقنية</span>
              </div>
              <span style="font-family: var(--font-mono); font-weight: 700; font-size: 13px; color: var(--ems-gold-hover);">60%</span>
            </div>
            <div class="ems-progress"><div class="ems-progress-bar" style="width: 60%;"></div></div>
          </div>
        </div>
      </div>
    </div>

    <!-- بطاقة حالات المهام الخمس -->
    <div class="ems-card">
      <div class="ems-card-header">
        <div class="ems-card-title">حالات المهام الخمس المعتمدة (Task Lifecycle)</div>
        <a href="{{ route('tasks.index') }}" style="font-size: 12.5px; font-weight: 700; color: var(--ems-gold-hover);">لوحة كانبان ←</a>
      </div>
      <div class="ems-card-body">
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; text-align: center;">
          <div style="background: var(--state-draft-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-draft-border);">
            <div style="font-size: 11px; font-weight: 700; color: var(--state-draft-text);">1. مسودة</div>
            <div style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">2</div>
          </div>
          <div style="background: var(--state-todo-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-todo-border);">
            <div style="font-size: 11px; font-weight: 700; color: var(--state-todo-text);">2. انتظار</div>
            <div style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">3</div>
          </div>
          <div style="background: var(--state-progress-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-progress-border);">
            <div style="font-size: 11px; font-weight: 700; color: var(--state-progress-text);">3. تنفيذ</div>
            <div style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">4</div>
          </div>
          <div style="background: var(--state-review-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-review-border);">
            <div style="font-size: 11px; font-weight: 700; color: var(--state-review-text);">4. مراجعة</div>
            <div style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">3</div>
          </div>
          <div style="background: var(--state-completed-bg); padding: 12px 6px; border-radius: var(--radius-md); border: 1px solid var(--state-completed-border);">
            <div style="font-size: 11px; font-weight: 700; color: var(--state-completed-text);">5. مكتمل</div>
            <div style="font-size: 20px; font-weight: 800; font-family: var(--font-mono); margin-top: 4px;">2</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div>
    <!-- سجل الأنشطة المباشرة -->
    <div class="ems-card">
      <div class="ems-card-header">
        <div class="ems-card-title">سجل الأنشطة التنفيذية المباشرة</div>
        <span class="ems-status-dot"></span>
      </div>
      <div class="ems-card-body">
        <div class="ems-feed-item">
          <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#c5a059;">م</div>
          <div style="flex: 1;">
            <div style="font-size: 13px; font-weight: 700; color: var(--ems-text-main);">محمد ابو شحمه اعتمد خطة العمل التشغيلية</div>
            <div style="font-size: 11.5px; color: var(--ems-text-muted);">الإدارة التنفيذية • منذ 15 دقيقة</div>
          </div>
        </div>
        <div class="ems-feed-item">
          <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#1e293b;">ب</div>
          <div style="flex: 1;">
            <div style="font-size: 13px; font-weight: 700; color: var(--ems-text-main);">براء عمر صادق على اتفاقيات الشراكة والإنتاج</div>
            <div style="font-size: 11.5px; color: var(--ems-text-muted);">المستشار القانوني ونائب المدير • منذ 40 دقيقة</div>
          </div>
        </div>
        <div class="ems-feed-item">
          <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#0284c7;">ا</div>
          <div style="flex: 1;">
            <div style="font-size: 13px; font-weight: 700; color: var(--ems-text-main);">البراء ابو عامر سلّم الماستر النهائي للمواد المرئية</div>
            <div style="font-size: 11.5px; color: var(--ems-text-muted);">دائرة الإعلام - قسم المرئي • منذ ساعتين</div>
          </div>
        </div>
        <div class="ems-feed-item">
          <div class="ems-avatar" style="width: 32px; height: 32px; font-size: 12px; background:#16a34a;">ر</div>
          <div style="flex: 1;">
            <div style="font-size: 13px; font-weight: 700; color: var(--ems-text-main);">رواء صلاح نظّمت جدول أعمال اجتماع مجلس الإدارة</div>
            <div style="font-size: 11.5px; color: var(--ems-text-muted);">السكرتارية التنفيذية ومتابعة المكاتب • اليوم 10:30 ص</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
