@extends('layouts.portal')

@section('title', 'المهام وتتبع الحالات')

@section('content')
    <style>
        /* تنسيقات حركات السحب والإفلات */
        .sortable-ghost {
            opacity: 0.4;
            background-color: var(--ems-surface-alt, #f3f4f6);
            border: 2px dashed #3b82f6 !important;
        }

        .kanban-dropzone {
            min-height: 150px;
            /* لضمان القدرة على الإفلات داخل الأعمدة الفارغة */
            padding-bottom: 20px;
        }

        .ems-task-card {
            cursor: grab;
            user-select: none;
        }

        .ems-task-card:active {
            cursor: grabbing;
        }
    </style>

    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span>سير العمل والإنتاج</span>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">المهام وتتبع الحالات</span>
            </div>
            <h1 class="ems-page-title">إدارة المهام ودورة حياة الإنتاج</h1>
            <p class="ems-page-subtitle">مسودة (Backlog) ➔ قيد الانتظار (To-Do) ➔ قيد التنفيذ (In Progress) ➔ قيد المراجعة
                (Review) ➔ مكتمل (Completed)</p>
        </div>

        <div class="ems-page-actions">
            <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-task')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>+ إضافة مهمة جديدة</span>
            </button>
        </div>
    </div>

    {{-- شريط التنبيه عند تصفية المهام حسب مشروع معين --}}
    @if ($selectedProject)
        <div class="ems-alert ems-alert-info"
            style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background-color: var(--ems-surface-alt, #f0f4f8); border-radius: 8px; border-right: 4px solid #3b82f6;">
            <div>
                🎯 عرض المهام المفلترة للمشروع: <strong>{{ $selectedProject->name ?? $selectedProject->title }}</strong>
            </div>
            <a href="{{ route('tasks.index') }}" class="ems-btn ems-btn-sm ems-btn-outline">إلغاء الفلتر ✕</a>
        </div>
    @endif

    @php
        // تجميع المهام ديناميكياً بحسب الحالة
        $groupedTasks = [
            'Backlog' => $tasks->filter(fn($t) => in_array($t->status, ['Backlog', 'Draft', 'draft'])),
            'Todo' => $tasks->filter(fn($t) => in_array($t->status, ['Todo', 'todo'])),
            'In Progress' => $tasks->filter(fn($t) => in_array($t->status, ['In Progress', 'in_progress'])),
            'Review' => $tasks->filter(fn($t) => in_array($t->status, ['Review', 'under_review', 'Under Review'])),
            'Completed' => $tasks->filter(fn($t) => in_array($t->status, ['Completed', 'completed'])),
        ];
    @endphp

    <!-- لوحة الكانبان خماسية المراحل -->
    <div class="ems-kanban-board">

        <!-- 1. Draft / Backlog -->
        <div class="ems-kanban-column">
            <div class="ems-kanban-header">
                <div class="ems-kanban-title">1. مسودة (Backlog)</div>
                <span class="ems-kanban-count">{{ $groupedTasks['Backlog']->count() }}</span>
            </div>
            <div class="ems-kanban-cards kanban-dropzone" data-status="Backlog">
                @foreach ($groupedTasks['Backlog'] as $task)
                    <div class="ems-task-card" data-task-id="{{ $task->id }}">
                        <div class="ems-task-header">
                            <span class="ems-task-id">{{ $task->task_code ?? 'TSK-' . $task->id }}</span>
                            <span class="ems-badge badge-draft">{{ $task->priority }}</span>
                        </div>
                        <h4 class="ems-task-title">{{ $task->title }}</h4>
                        <div class="ems-task-footer">
                            <span>{{ $task->assignee?->full_name ?? ($task->assignee?->name ?? 'غير مسند') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2. To-Do -->
        <div class="ems-kanban-column">
            <div class="ems-kanban-header">
                <div class="ems-kanban-title">2. قيد الانتظار (To-Do)</div>
                <span class="ems-kanban-count">{{ $groupedTasks['Todo']->count() }}</span>
            </div>
            <div class="ems-kanban-cards kanban-dropzone" data-status="Todo">
                @foreach ($groupedTasks['Todo'] as $task)
                    <div class="ems-task-card" data-task-id="{{ $task->id }}">
                        <div class="ems-task-header">
                            <span class="ems-task-id">{{ $task->task_code ?? 'TSK-' . $task->id }}</span>
                            <span class="ems-badge badge-danger">{{ $task->priority }}</span>
                        </div>
                        <h4 class="ems-task-title">{{ $task->title }}</h4>
                        <div class="ems-task-footer">
                            <span>{{ $task->assignee?->full_name ?? ($task->assignee?->name ?? 'غير مسند') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. In Progress -->
        <div class="ems-kanban-column">
            <div class="ems-kanban-header">
                <div class="ems-kanban-title">3. قيد التنفيذ (In Progress)</div>
                <span class="ems-kanban-count">{{ $groupedTasks['In Progress']->count() }}</span>
            </div>
            <div class="ems-kanban-cards kanban-dropzone" data-status="In Progress">
                @foreach ($groupedTasks['In Progress'] as $task)
                    <div class="ems-task-card" data-task-id="{{ $task->id }}">
                        <div class="ems-task-header">
                            <span class="ems-task-id">{{ $task->task_code ?? 'TSK-' . $task->id }}</span>
                            <span class="ems-badge badge-progress">{{ $task->priority }}</span>
                        </div>
                        <h4 class="ems-task-title">{{ $task->title }}</h4>
                        <div class="ems-task-footer">
                            <span>{{ $task->assignee?->full_name ?? ($task->assignee?->name ?? 'غير مسند') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4. Under Review -->
        <div class="ems-kanban-column">
            <div class="ems-kanban-header">
                <div class="ems-kanban-title">4. قيد المراجعة (Review)</div>
                <span class="ems-kanban-count">{{ $groupedTasks['Review']->count() }}</span>
            </div>
            <div class="ems-kanban-cards kanban-dropzone" data-status="Review">
                @foreach ($groupedTasks['Review'] as $task)
                    <div class="ems-task-card" data-task-id="{{ $task->id }}">
                        <div class="ems-task-header">
                            <span class="ems-task-id">{{ $task->task_code ?? 'TSK-' . $task->id }}</span>
                            <span class="ems-badge badge-review">{{ $task->priority }}</span>
                        </div>
                        <h4 class="ems-task-title">{{ $task->title }}</h4>
                        <div class="ems-task-footer">
                            <span>{{ $task->assignee?->full_name ?? ($task->assignee?->name ?? 'غير مسند') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 5. Completed -->
        <div class="ems-kanban-column">
            <div class="ems-kanban-header">
                <div class="ems-kanban-title">5. مكتملة (Completed)</div>
                <span class="ems-kanban-count">{{ $groupedTasks['Completed']->count() }}</span>
            </div>
            <div class="ems-kanban-cards kanban-dropzone" data-status="Completed">
                @foreach ($groupedTasks['Completed'] as $task)
                    <div class="ems-task-card" data-task-id="{{ $task->id }}" style="border-right: 3px solid #10b981;">
                        <div class="ems-task-header">
                            <span class="ems-task-id">{{ $task->task_code ?? 'TSK-' . $task->id }}</span>
                            <span class="ems-badge badge-completed">100%</span>
                        </div>
                        <h4 class="ems-task-title">{{ $task->title }}</h4>
                        <div class="ems-task-footer">
                            <span>{{ $task->assignee?->full_name ?? ($task->assignee?->name ?? 'غير مسند') }}</span>
                            <span style="color: #10b981; font-weight: bold;">منجز ✓</span>
                        </div>
                    </div>
                @endforeach
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
                            <label for="task_code" class="ems-label">رمز المهمة</label>
                            <input type="text" id="task_code" name="task_code" class="ems-input"
                                value="TSK-{{ rand(1050, 9999) }}" placeholder="أدخل رمز المهمة أو اتركه تلقائي">
                        </div>
                        <div class="ems-form-group">
                            <label for="stage" class="ems-label"><span class="required">*</span> مرحلة سير
                                العمل</label>
                            <select id="stage" name="stage" class="ems-select" required>
                                <option value="Backlog">1. مسودة (Backlog)</option>
                                <option value="Todo" selected>2. قيد الانتظار (To-Do)</option>
                                <option value="In Progress">3. قيد التنفيذ (In Progress)</option>
                                <option value="Review">4. قيد المراجعة والاعتماد (Review)</option>
                                <option value="Completed">5. مكتملة (Completed)</option>
                            </select>
                        </div>
                    </div>

                    <div class="ems-form-group">
                        <label for="title" class="ems-label"><span class="required">*</span> عنوان المهمة</label>
                        <input type="text" id="title" name="title" class="ems-input" required>
                    </div>

                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label for="project_id" class="ems-label">المشروع التابع</label>
                            <select id="project_id" name="project_id" class="ems-select">
                                <option value="">-- اختياري --</option>
                                @if (isset($projects))
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ $selectedProject?->id == $project->id ? 'selected' : '' }}>
                                            {{ $project->name ?? $project->title }}
                                        </option>
                                    @endforeach
                                @elseif($selectedProject)
                                    <option value="{{ $selectedProject->id }}" selected>
                                        {{ $selectedProject->name ?? $selectedProject->title }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="ems-form-group">
                            <label for="assigned_to" class="ems-label"><span class="required">*</span> الموظف
                                المكلف</label>
                            <select id="assigned_to" name="assigned_to" class="ems-select" required>
                                <option value="">-- اختر موظف --</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->full_name ?? $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary"
                        onclick="closeModal('modal-add-task')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">إسناد المهمة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- مكتبة SortableJS للسحب والإفلات -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropzones = document.querySelectorAll('.kanban-dropzone');

            dropzones.forEach(dropzone => {
                new Sortable(dropzone, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        const itemEl = evt.item;
                        const taskId = itemEl.getAttribute('data-task-id');
                        const newStatus = evt.to.getAttribute('data-status');

                        // إذا جرى النقل في نفس العمود
                        if (evt.from === evt.to) return;

                        // إرسال AJAX لتحديث الحالة مباشرة في السيرفر
                        fetch(`/tasks/${taskId}/state`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    status: newStatus
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('فشل تحديث الحالة');
                                }
                                return response.json();
                            })
                            .then(data => {
                                // تحديث أعداد الأعمدة تلقائياً بعد النقل
                                updateCounts(evt.from, evt.to);
                            })
                            .catch(error => {
                                alert('تعذر حفظ الحالة الجديدة، جاري إعادة تحميل الصفحة');
                                window.location.reload();
                            });
                    }
                });
            });

            function updateCounts(fromEl, toEl) {
                const fromCount = fromEl.closest('.ems-kanban-column').querySelector('.ems-kanban-count');
                const toCount = toEl.closest('.ems-kanban-column').querySelector('.ems-kanban-count');

                if (fromCount && toCount) {
                    fromCount.textContent = Math.max(0, parseInt(fromCount.textContent) - 1);
                    toCount.textContent = parseInt(toCount.textContent) + 1;
                }
            }
        });
    </script>
@endsection
