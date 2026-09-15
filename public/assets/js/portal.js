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


// Add Users Modal

 // Modal Dialog Handlers
  window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove("hidden");
      modal.classList.add("flex");
    }
  };


  window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add("hidden");
      modal.classList.remove("flex");
    }
  };

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
