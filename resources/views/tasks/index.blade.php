@extends('layouts.portal')

@section('title', 'المهام وتتبع الحالات')

@section('content')
<div class="ems-page-header">
  <div class="ems-page-title-wrap">
    <div class="ems-breadcrumb">
      <a href="{{ route('dashboard') }}">الرئيسية</a>
      <span class="ems-breadcrumb-separator">/</span>
      <span>سير العمل والإنتاج</span>
      <span class="ems-breadcrumb-separator">/</span>
      <span style="color: var(--ems-text-main); font-weight: 700;">المهام وتتبع الحالات</span>
    </div>
    <h1 class="ems-page-title">إدارة المهام ودورة حياة الإنتاج (5 مراحل)</h1>
    <p class="ems-page-subtitle">مسودة (Draft) ➔ قيد الانتظار (To-Do) ➔ قيد التنفيذ (In Progress) ➔ قيد المراجعة (Under Review) ➔ مكتمل (Completed)</p>
  </div>
  
  <div class="ems-page-actions">
    <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-task')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <span>+ إضافة مهمة جديدة</span>
    </button>
  </div>
</div>

<!-- لوحة الكانبان خماسية المراحل -->
<div class="ems-kanban-board">
  <!-- 1. Draft -->
  <div class="ems-kanban-column">
    <div class="ems-kanban-header">
      <div class="ems-kanban-title">1. مسودة (Draft)</div>
      <span class="ems-kanban-count">2</span>
    </div>
    <div class="ems-kanban-cards">
      <div class="ems-task-card">
        <div class="ems-task-header"><span class="ems-task-id">TSK-1041</span><span class="ems-badge badge-draft">عادي</span></div>
        <h4 class="ems-task-title">صياغة بروتوكول البث المباشر</h4>
        <div class="ems-task-footer">
          <span>محمد النجار</span>
          <button class="ems-btn ems-btn-sm ems-btn-outline" onclick="updateTaskState('1041', 'todo')">اعتماد ➔</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. To-Do -->
  <div class="ems-kanban-column">
    <div class="ems-kanban-header">
      <div class="ems-kanban-title">2. قيد الانتظار (To-Do)</div>
      <span class="ems-kanban-count">3</span>
    </div>
    <div class="ems-kanban-cards">
      <div class="ems-task-card">
        <div class="ems-task-header"><span class="ems-task-id">TSK-1035</span><span class="ems-badge badge-danger">عاجل</span></div>
        <h4 class="ems-task-title">إعداد خدمة Webhooks للدفع</h4>
        <div class="ems-task-footer">
          <span>محمود المصري</span>
          <button class="ems-btn ems-btn-sm ems-btn-outline" onclick="updateTaskState('1035', 'progress')">بدء ➔</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. In Progress -->
  <div class="ems-kanban-column">
    <div class="ems-kanban-header">
      <div class="ems-kanban-title">3. قيد التنفيذ (In Progress)</div>
      <span class="ems-kanban-count">4</span>
    </div>
    <div class="ems-kanban-cards">
      <div class="ems-task-card">
        <div class="ems-task-header"><span class="ems-task-id">TSK-1030</span><span class="ems-badge badge-progress">نشط</span></div>
        <h4 class="ems-task-title">برمجة نقاط النهاية REST APIs</h4>
        <div class="ems-task-footer">
          <span>أحمد حبيب</span>
          <button class="ems-btn ems-btn-sm ems-btn-outline" onclick="updateTaskState('1030', 'review')">مراجعة ➔</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. Under Review -->
  <div class="ems-kanban-column">
    <div class="ems-kanban-header">
      <div class="ems-kanban-title">4. قيد المراجعة (Under Review)</div>
      <span class="ems-kanban-count">3</span>
    </div>
    <div class="ems-kanban-cards">
      <div class="ems-task-card">
        <div class="ems-task-header"><span class="ems-task-id">TSK-1025</span><span class="ems-badge badge-review">فحص QA</span></div>
        <h4 class="ems-task-title">اختبار أمان ثغرات المصادقة</h4>
        <div class="ems-task-footer">
          <span>رانيا شعبان</span>
          <button class="ems-btn ems-btn-sm ems-btn-primary" onclick="updateTaskState('1025', 'completed')">اعتماد ✓</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 5. Completed -->
  <div class="ems-kanban-column">
    <div class="ems-kanban-header">
      <div class="ems-kanban-title">5. مكتملة (Completed)</div>
      <span class="ems-kanban-count">2</span>
    </div>
    <div class="ems-kanban-cards">
      <div class="ems-task-card" style="border-right: 3px solid #10b981;">
        <div class="ems-task-header"><span class="ems-task-id">TSK-1019</span><span class="ems-badge badge-completed">100%</span></div>
        <h4 class="ems-task-title">تجهيز عوازل الصوت والاستوديو</h4>
        <div class="ems-task-footer">
          <span>محمد النجار</span>
          <span style="color: #10b981; font-weight: bold;">منجز ✓</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- نافذة إضافة مهمة -->
<div class="ems-modal-overlay" id="modal-add-task">
  <div class="ems-modal">
    <div class="ems-modal-header">
      <div class="ems-modal-title">إسناد مهمة جديدة</div>
      <button class="ems-modal-close" onclick="closeModal('modal-add-task')">✕</button>
    </div>
    <form action="{{ route('tasks.store') }}" method="POST" id="addTaskForm">
      @csrf
      <div class="ems-modal-body">
        <div class="ems-form-row">
          <div class="ems-form-group">
            <label for="task_code" class="ems-label"><span class="required">*</span> رمز المهمة</label>
            <input type="text" id="task_code" name="task_code" class="ems-input" value="TSK-1043" required readonly style="background-color: var(--ems-surface-alt);">
          </div>
          <div class="ems-form-group">
            <label for="stage" class="ems-label"><span class="required">*</span> مرحلة سير العمل</label>
            <select id="stage" name="stage" class="ems-select" required>
              <option value="draft">1. مسودة (Draft)</option>
              <option value="todo" selected>2. قيد الانتظار (To-Do)</option>
              <option value="in_progress">3. قيد التنفيذ (In Progress)</option>
              <option value="under_review">4. قيد المراجعة والاعتماد (Under Review)</option>
              <option value="completed">5. مكتملة (Completed)</option>
            </select>
          </div>
        </div>

        <div class="ems-form-group">
          <label for="title" class="ems-label"><span class="required">*</span> عنوان المهمة</label>
          <input type="text" id="title" name="title" class="ems-input" required>
        </div>

        <div class="ems-form-row">
          <div class="ems-form-group">
            <label for="project_id" class="ems-label"><span class="required">*</span> المشروع التابع</label>
            <select id="project_id" name="project_id" class="ems-select" required>
              <option value="201">بوابة الدفع والتجارة الرقمية الموحدة</option>
              <option value="202">تطبيق منصة إياس للهواتف الذكية</option>
              <option value="203">سلسلة وثائقيات الهوية والتراث</option>
            </select>
          </div>
          <div class="ems-form-group">
            <label for="assigned_to" class="ems-label"><span class="required">*</span> الموظف المكلف</label>
            <select id="assigned_to" name="assigned_to" class="ems-select" required>
              <option value="1">أحمد حبيب (Full Stack)</option>
              <option value="2">سارة الخزندار (UI/UX)</option>
              <option value="3">محمود المصري (Backend)</option>
              <option value="4">رانيا أبو شعبان (QA)</option>
            </select>
          </div>
        </div>
      </div>
      <div class="ems-modal-footer">
        <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-add-task')">إلغاء</button>
        <button type="submit" class="ems-btn ems-btn-primary">إسناد المهمة</button>
      </div>
    </form>
  </div>
</div>
@endsection
