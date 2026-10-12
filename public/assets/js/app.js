// EYAS Media Group - Interactive Dashboard Controller

document.addEventListener("DOMContentLoaded", () => {
  // Initialize Lucide icons
  if (window.lucide) {
    lucide.createIcons();
  }

  // Toast Notification System
  window.showToast = function(message, type = "success") {
    let container = document.getElementById("toast-container");
    if (!container) {
      container = document.createElement("div");
      container.id = "toast-container";
      container.className = "fixed bottom-5 left-5 z-50 flex flex-col gap-2 pointer-events-none";
      document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    const bgColors = {
      success: "bg-slate-900 border-emerald-500 text-white",
      info: "bg-blue-900 border-blue-500 text-white",
      warning: "bg-amber-950 border-amber-500 text-white"
    };

    toast.className = `flex items-center gap-3 px-4 py-3 rounded-xl border shadow-xl text-sm font-medium transition-all duration-300 transform translate-y-2 opacity-0 pointer-events-auto ${bgColors[type] || bgColors.success}`;
    const dot = document.createElement('div');
    dot.className = `w-2 h-2 rounded-full ${type === 'warning' ? 'bg-amber-400' : 'bg-emerald-400'} animate-ping`;
    const label = document.createElement('span');
    label.textContent = String(message ?? '');
    toast.append(dot, label);

    container.appendChild(toast);
    requestAnimationFrame(() => {
      toast.classList.remove("translate-y-2", "opacity-0");
    });

    setTimeout(() => {
      toast.classList.add("opacity-0", "translate-y-2");
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  };

  // Team Search & Filter
  const teamSearchInput = document.getElementById("team-search");
  if (teamSearchInput) {
    teamSearchInput.addEventListener("input", (e) => {
      const query = e.target.value.trim().toLowerCase();
      const rows = document.querySelectorAll(".team-member-row");
      let visibleCount = 0;
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (text.includes(query)) {
          row.style.display = "";
          visibleCount++;
        } else {
          row.style.display = "none";
        }
      });
      const countEl = document.getElementById("team-visible-count");
      if (countEl) {
        countEl.textContent = `عرض ${visibleCount} من أصل 8 أعضاء في الفريق التقني`;
      }
    });
  }

  // Projects Search & Filter Tabs
  const projectSearchInput = document.getElementById("project-search");
  let currentProjectFilter = "all";

  function filterProjects() {
    const query = projectSearchInput ? projectSearchInput.value.trim().toLowerCase() : "";
    const rows = document.querySelectorAll(".project-row");
    let visibleCount = 0;

    rows.forEach(row => {
      const category = row.getAttribute("data-category");
      const text = row.innerText.toLowerCase();
      const matchesFilter = currentProjectFilter === "all" || category === currentProjectFilter;
      const matchesSearch = text.includes(query);

      if (matchesFilter && matchesSearch) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });

    const countEl = document.getElementById("projects-visible-count");
    if (countEl) {
      countEl.textContent = `عرض ${visibleCount} من أصل 5 مشاريع`;
    }
  }

  if (projectSearchInput) {
    projectSearchInput.addEventListener("input", filterProjects);
  }

  const projectTabButtons = document.querySelectorAll(".project-tab-btn");
  projectTabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      projectTabButtons.forEach(b => {
        b.classList.remove("bg-white", "text-slate-900", "shadow-sm", "font-bold");
        b.classList.add("text-slate-500", "hover:text-slate-800");
      });
      btn.classList.remove("text-slate-500", "hover:text-slate-800");
      btn.classList.add("bg-white", "text-slate-900", "shadow-sm", "font-bold");

      currentProjectFilter = btn.getAttribute("data-filter");
      filterProjects();
    });
  });

  // Tasks Search & Priority Filter Tabs
  const taskSearchInput = document.getElementById("task-search");
  let currentTaskFilter = "all";

  function filterTasks() {
    const query = taskSearchInput ? taskSearchInput.value.trim().toLowerCase() : "";
    const rows = document.querySelectorAll(".task-row");
    let visibleCount = 0;

    rows.forEach(row => {
      const priority = row.getAttribute("data-priority");
      const text = row.innerText.toLowerCase();
      const matchesFilter = currentTaskFilter === "all" || priority === currentTaskFilter;
      const matchesSearch = text.includes(query);

      if (matchesFilter && matchesSearch) {
        row.style.display = "";
        visibleCount++;
      } else {
        row.style.display = "none";
      }
    });

    const countEl = document.getElementById("tasks-visible-count");
    if (countEl) {
      countEl.textContent = `إجمالي المعروض: ${visibleCount}`;
    }
  }

  if (taskSearchInput) {
    taskSearchInput.addEventListener("input", filterTasks);
  }

  const taskTabButtons = document.querySelectorAll(".task-tab-btn");
  taskTabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      taskTabButtons.forEach(b => {
        b.classList.remove("bg-white", "text-slate-900", "shadow-sm", "font-bold");
        b.classList.add("text-slate-500", "hover:text-slate-800");
      });
      btn.classList.remove("text-slate-500", "hover:text-slate-800");
      btn.classList.add("bg-white", "text-slate-900", "shadow-sm", "font-bold");

      currentTaskFilter = btn.getAttribute("data-filter");
      filterTasks();
    });
  });

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

  // Handle SPA View Switching (if inside index.html)
  window.switchView = function(viewName) {
    const views = ["dashboard", "team", "projects", "tasks"];
    views.forEach(v => {
      const el = document.getElementById(`view-${v}`);
      const navBtn = document.getElementById(`nav-${v}`);
      if (el) {
        if (v === viewName) {
          el.classList.remove("hidden");
        } else {
          el.classList.add("hidden");
        }
      }
      if (navBtn) {
        if (v === viewName) {
          navBtn.className = "flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white font-semibold transition-all shadow-md shadow-blue-500/20";
        } else {
          navBtn.className = "flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium transition-all";
        }
      }
    });

    // Update document title and URL hash
    window.location.hash = viewName;
    if (window.lucide) {
      lucide.createIcons();
    }
  };

  // Check URL hash on load
  if (window.location.hash) {
    const hash = window.location.hash.replace("#", "");
    if (["dashboard", "team", "projects", "tasks"].includes(hash)) {
      window.switchView(hash);
    }
  }
});
