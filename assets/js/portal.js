/**
 * مجموعة إياس الإعلامية - Portal Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  initClock();
  initDropdowns();
  initOutsideClicks();
  initSearch();
});

/* الساعة ومؤقت الدوام المباشر (Real-time Attendance Clock) */
function initClock() {
  const clockElements = document.querySelectorAll('.ems-live-clock');
  if (clockElements.length === 0) return;

  function update() {
    const now = new Date();
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'م' : 'ص';
    hours = hours % 12;
    hours = hours ? hours : 12; // الساعة 12
    const strTime = `${String(hours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
    clockElements.forEach(el => el.textContent = strTime);
  }

  update();
  setInterval(update, 1000);
}

/* القوائم المنسدلة (Dropdowns) */
function toggleDropdown(id) {
  const dropdown = document.getElementById(id);
  if (!dropdown) return;
  
  // إغلاق أي قائمة مفتوحة أخرى
  document.querySelectorAll('.ems-dropdown-menu.active').forEach(menu => {
    if (menu.id !== id) menu.classList.remove('active');
  });

  dropdown.classList.toggle('active');
}

function initDropdowns() {
  document.querySelectorAll('[data-dropdown-toggle]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const targetId = btn.getAttribute('data-dropdown-toggle');
      toggleDropdown(targetId);
    });
  });
}

function initOutsideClicks() {
  document.addEventListener('click', (e) => {
    // إغلاق القوائم المنسدلة عند النقر في الخارج
    if (!e.target.closest('.ems-dropdown-menu') && !e.target.closest('[data-dropdown-toggle]')) {
      document.querySelectorAll('.ems-dropdown-menu.active').forEach(menu => {
        menu.classList.remove('active');
      });
    }

    // إغلاق النوافذ المنبثقة عند النقر على الخلفية
    if (e.target.classList.contains('ems-modal-overlay')) {
      e.target.classList.remove('active');
    }
  });
}

/* النوافذ المنبثقة (Modals) */
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}

/* القائمة الجانبية للشاشات الصغيرة (Mobile Sidebar) */
function toggleSidebar() {
  const sidebar = document.querySelector('.ems-sidebar');
  if (sidebar) {
    sidebar.classList.toggle('open');
  }
}

/* نظام الإشعارات السريعة (Toast Messages) */
function showToast(message, type = 'success') {
  let container = document.getElementById('ems-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'ems-toast-container';
    container.className = 'ems-toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `ems-toast ${type}`;
  toast.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      ${type === 'success' ? '<path d="M20 6L9 17l-5-5"/>' : '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'}
    </svg>
    <span>${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

/* البحث المباشر في الجداول (Live Table Search Filter) */
function initSearch() {
  const searchInputs = document.querySelectorAll('[data-table-search]');
  searchInputs.forEach(input => {
    const tableId = input.getAttribute('data-table-search');
    const table = document.getElementById(tableId);
    if (!table) return;

    input.addEventListener('input', () => {
      const query = input.value.trim().toLowerCase();
      const rows = table.querySelectorAll('tbody tr');

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(query)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  });
}

/* محاكاة تسجيل الحضور والانصراف السريع */
function quickAttendanceAction() {
  const now = new Date();
  const timeStr = now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
  showToast(`تم تسجيل البصمة بنجاح في تمام ${timeStr} - رئيس مجلس الإدارة صالح رزق حسنين`, 'success');
  closeModal('modal-quick-attendance');
}

/* تبديل حالات المهام (Task State Management: 5 Stages) */
function updateTaskState(taskId, newState) {
  const stateNames = {
    'draft': 'مسودة (Draft)',
    'todo': 'قيد الانتظار (To-Do)',
    'progress': 'قيد التنفيذ (In Progress)',
    'review': 'قيد المراجعة والاعتماد (Under Review)',
    'completed': 'مكتملة (Completed)'
  };
  showToast(`تم تغيير حالة المهمة #${taskId} إلى: ${stateNames[newState] || newState}`, 'success');
}


/* ==========================================================================
   دوال إدارة الهيكل الشجري وحذف الكيانات وتفاصيل المهام
   ========================================================================== */

/* تأكيد وحذف موظف */
function confirmDeleteEmployee(empId, empName) {
  if (confirm(هل أنت متأكد من رغبتك في حذف الموظف [] (رقم: ) من الهيكل الوظيفي؟)) {
    const el = document.getElementById(	ree-emp-);
    if (el) {
      el.style.opacity = '0';
      el.style.transform = 'scale(0.9)';
      setTimeout(() => el.remove(), 250);
    }
    showToast(تم حذف الموظف [] بنجاح, 'info');
  }
}

/* تأكيد وحذف قسم */
function confirmDeleteSection(secId, secName) {
  if (confirm(تحذير: هل أنت متأكد من حذف القسم [] وكافة ارتباطاته من الهيكل؟)) {
    const el = document.getElementById(	ree-sec-);
    if (el) {
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 250);
    }
    showToast(تم حذف القسم [] بنجاح, 'info');
  }
}

/* تأكيد وحذف دائرة رئيسية */
function confirmDeleteDepartment(deptId, deptName) {
  if (deptId === 'DEPT-01') {
    alert('تنبيه أمني: لا يمكن حذف الإدارة العليا والتنفيذية للمجموعة.');
    return;
  }
  if (confirm(تحذير عالي الأهمية: هل أنت متأكد من حذف [] وجميع الأقسام والكوادر التابعة لها؟)) {
    const el = document.getElementById(	ree-dept-);
    if (el) {
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 250);
    }
    showToast(تم حذف [] بنجاح, 'info');
  }
}

/* عرض تفاصيل القسم والمهام */
function viewSectionTasks(secCode, secName) {
  const modal = document.getElementById('modal-section-tasks');
  const titleEl = document.getElementById('sectionTasksModalTitle');
  const bodyEl = document.getElementById('sectionTasksModalBody');
  
  if (titleEl) titleEl.textContent = مهام ومشاريع:  ();
  
  // بيانات المهام التخصصية حسب القسم
  const tasksMap = {
    'SEC-001': [
      { id: 'TSK-1001', title: 'اعتماد الموازنة التشغيلية للربع السنوي', assignee: 'صالح حسنين', state: 'مكتمل', stateClass: 'badge-completed', deadline: '2026-09-01' },
      { id: 'TSK-1002', title: 'صياغة اللائحة الإدارية الموحدة لمجموعة إياس', assignee: 'براء عمر', state: 'قيد المراجعة', stateClass: 'badge-review', deadline: '2026-09-15' },
      { id: 'TSK-1003', title: 'تنظيم جدولة اجتماعات مجلس الإدارة وتنسيق المقابلات', assignee: 'رواء صلاح', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-18' }
    ],
    'SEC-101': [
      { id: 'TSK-1030', title: 'برمجة نقاط النهاية REST APIs ونظام التوثيق', assignee: 'أحمد حبيب', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-18' },
      { id: 'TSK-1035', title: 'إعداد خدمة Webhooks لإشعارات الدفع الفورية', assignee: 'محمود المصري', state: 'قيد الانتظار', stateClass: 'badge-todo', deadline: '2026-09-12' },
      { id: 'TSK-1038', title: 'بناء واجهات بوابة إياس التفاعلية بـ Pure CSS', assignee: 'إبراهيم الصالحي', state: 'مكتمل', stateClass: 'badge-completed', deadline: '2026-09-08' },
      { id: 'TSK-1040', title: 'تصميم تجربة المستخدم UI/UX لصفحات الهيكلية والأدوار', assignee: 'سارة الخزندار', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-14' }
    ],
    'SEC-102': [
      { id: 'TSK-1025', title: 'فحص واختبار أمان الثغرات وصلاحيات الأدوار', assignee: 'رانيا أبو شعبان', state: 'قيد المراجعة', stateClass: 'badge-review', deadline: '2026-09-10' },
      { id: 'TSK-1028', title: 'تطوير شاشة الإشعارات المباشرة بتطبيق Flutter', assignee: 'يوسف الشوا', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-16' },
      { id: 'TSK-1029', title: 'تجهيز حزم النشر على منصتي App Store و Google Play', assignee: 'طارق الهسي', state: 'قيد الانتظار', stateClass: 'badge-todo', deadline: '2026-09-20' }
    ],
    'SEC-103': [
      { id: 'TSK-1020', title: 'تحسين استعلامات قاعدة البيانات وفهارس البحث السريع', assignee: 'ميساء الغصين', state: 'مكتمل', stateClass: 'badge-completed', deadline: '2026-09-05' },
      { id: 'TSK-1022', title: 'إعداد بيئة الاستضافة السحابية وخطوط الأتمتة CI/CD', assignee: 'حمزة الريس', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-14' },
      { id: 'TSK-1023', title: 'جدولة النسخ الاحتياطي التلقائي ومراجعة جدران الحماية', assignee: 'خالد البورنو', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-15' }
    ],
    'SEC-201': [
      { id: 'TSK-1015', title: 'تصوير الحلقة الوثائقية الميدانية في البلدة القديمة', assignee: 'البراء ابو عامر', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-16' },
      { id: 'TSK-1016', title: 'مونتاج ومعالجة الصوت لفيلم هوية القدس الوثائقي', assignee: 'البراء ابو عامر', state: 'قيد الانتظار', stateClass: 'badge-todo', deadline: '2026-09-22' }
    ],
    'SEC-202': [
      { id: 'TSK-1018', title: 'تنسيق نشر التغطيات الإعلامية عبر منصات البودكاست', assignee: 'لؤي بركة', state: 'مكتمل', stateClass: 'badge-completed', deadline: '2026-09-07' },
      { id: 'TSK-1019', title: 'جدولة البث المباشر للمؤتمر السنوي لمجموعة إياس', assignee: 'لؤي بركة', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-17' }
    ],
    'SEC-301': [
      { id: 'TSK-1010', title: 'خطة استهداف الحملات وتوسيع قاعدة المشتركين', assignee: 'عمر العشي', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-15' },
      { id: 'TSK-1011', title: 'تحسين سيو محركات البحث والمقالات الإخبارية', assignee: 'هبة سكيك', state: 'مكتمل', stateClass: 'badge-completed', deadline: '2026-09-06' },
      { id: 'TSK-1012', title: 'كتابة محتوى البوستات التسويقية لخدمات الاستضافة', assignee: 'زياد مرتجى', state: 'قيد المراجعة', stateClass: 'badge-review', deadline: '2026-09-12' }
    ],
    'SEC-302': [
      { id: 'TSK-1008', title: 'إطلاق وتتبع الحملة الإعلانية الممولة للمنصات الرقمية', assignee: 'كمال أبو رمضان', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-19' },
      { id: 'TSK-1009', title: 'إدارة ميزانيات منصات Meta و Google Ads وتحليل العائد', assignee: 'وسيم كحيل', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-18' }
    ],
    'SEC-303': [
      { id: 'TSK-1005', title: 'توقيع اتفاقيات الشراكة والتبادل الإعلامي مع المؤسسات', assignee: 'ماجد صيام', state: 'قيد المراجعة', stateClass: 'badge-review', deadline: '2026-09-14' },
      { id: 'TSK-1006', title: 'التشبيك واستضافة الإعلاميين والمؤثرين بالبودكاست', assignee: 'نداء حلس', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-16' },
      { id: 'TSK-1007', title: 'إعداد تقرير الرصد الإعلامي للأثر المجتمعي لإنتاجات إياس', assignee: 'عادل دلول', state: 'مكتمل', stateClass: 'badge-completed', deadline: '2026-09-08' }
    ]
  };

  const tasks = tasksMap[secCode] || [
    { id: 'TSK-2001', title: 'مهمة تشغيلية عامة للقسم', assignee: 'كادر القسم', state: 'قيد التنفيذ', stateClass: 'badge-progress', deadline: '2026-09-20' }
  ];

  let html = 
    <div class="ems-table-responsive">
      <table class="ems-table">
        <thead>
          <tr>
            <th>رمز المهمة</th>
            <th>عنوان المهمة</th>
            <th>المسؤول المباشر</th>
            <th>الحالة</th>
            <th>الموعد النهائي</th>
            <th style="text-align: center;">إجراء</th>
          </tr>
        </thead>
        <tbody>
  ;

  tasks.forEach(t => {
    html += 
      <tr>
        <td><strong style="font-family: var(--font-mono); color: var(--ems-gold-hover);"></strong></td>
        <td><strong></strong></td>
        <td></td>
        <td><span class="ems-badge "></span></td>
        <td></td>
        <td style="text-align: center;">
          <a href="tasks.html" class="ems-btn ems-btn-secondary ems-btn-sm" style="padding: 4px 8px; font-size: 11px;">متابعة في المهام ←</a>
        </td>
      </tr>
    ;
  });

  html += 
        </tbody>
      </table>
    </div>
  ;

  if (bodyEl) bodyEl.innerHTML = html;
  openModal('modal-section-tasks');
}