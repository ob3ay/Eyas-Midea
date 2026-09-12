@extends('layouts.portal')
@section('title', 'المشاريع والإنتاج')

@section('content')
    <div class="ems-content">
        <div class="ems-page-header">
            <div class="ems-page-title-wrap">
                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>+ مشروع إنتاجي جديد</span>
                </button>
            </div>
        </div>

        <!-- إحصائيات المشاريع -->
        <div class="ems-stats-grid">
            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">إجمالي المشاريع</span>
                    <span class="ems-stat-value">{{ $stats['total'] ?? 0 }}</span>
                    <span class="ems-stat-subtext">{{ $stats['software_count'] ?? 0 }} برمجية •
                        {{ $stats['media_count'] ?? 0 }} إعلامية</span>
                </div>
                <div class="ems-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">قيد التنفيذ والبرمجة</span>
                    <span class="ems-stat-value" style="color: #d97706;">{{ $stats['in_progress'] ?? 0 }}</span>
                    <span class="ems-stat-subtext">تطوير مستمر بالـ Sprints</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #fef3c7; border-color: #fde68a; color: #b45309;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">المراجعة والاعتماد النهائي</span>
                    <span class="ems-stat-value" style="color: #6d28d9;">{{ $stats['in_review'] ?? 0 }}</span>
                    <span class="ems-stat-subtext">فحص الـ QA والتدقيق</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">مكتملة ومسلمة</span>
                    <span class="ems-stat-value" style="color: #10b981;">{{ $stats['completed'] ?? 0 }}</span>
                    <span class="ems-stat-subtext">تم الإطلاق بنجاح 100%</span>
                </div>
                <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6L9 17l-5-5" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- جدول بيانات المشاريع -->
        <div class="ems-card">
            <div class="ems-card-header">
                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path
                            d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 8 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
                    </svg>
                    <span>سجل مشاريع مجموعة إياس الإعلامية</span>
                </div>
                <span class="ems-badge badge-gold">متوسط الإنجاز الكلي:
                    {{ number_format($averageProgress ?? 0, 0) }}%</span>
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
                            @forelse($projects as $project)
                                <tr>
                                    <td>
                                        <span
                                            style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">
                                            {{ $project->code }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 800; color: var(--ems-text-main);">{{ $project->name }}
                                        </div>
                                        <div style="font-size: 11.5px; color: var(--ems-text-muted);">
                                            {{ $project->description }}</div>
                                    </td>
                                    <td>{{ $project->department->name ?? 'غير محدد' }}</td>
                                    <td>{{ $project->manager->name ?? 'غير معين' }}</td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div class="ems-progress" style="height: 6px; flex-grow: 1;">
                                                <div class="ems-progress-bar {{ $project->progress == 100 ? 'success' : '' }}"
                                                    style="width: {{ $project->progress }}%;"></div>
                                            </div>
                                            <span
                                                style="font-size: 11px; font-weight: 700; font-family: var(--font-mono); {{ $project->progress == 100 ? 'color: #10b981;' : '' }}">
                                                {{ $project->progress }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($project->delivery_date)->format('d F Y') }}</td>
                                    <td>
                                        <span class="ems-badge {{ $project->status_badge_class }}">
                                            {{ $project->status_label }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 4px;">
                                            <!-- رابط المهام المرتبطة بالمشروع مطابق لملف المسارات -->
                                            <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
                                                class="ems-btn ems-btn-icon" title="عرض المهام المرتبطة">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="m9 11 3 3L22 4" />
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                                                </svg>
                                            </a>

                                            <!-- زر حذف المشروع -->
                                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                                onsubmit="return confirm('هل أنت تأكد من رغبتك في حذف هذا المشروع؟');"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ems-btn ems-btn-icon" title="حذف المشروع"
                                                    style="color: #ef4444;">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path
                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8"
                                        style="text-align: center; padding: 24px; color: var(--ems-text-muted);">
                                        لا توجد مشاريع مسجلة حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة إضافة مشروع جديد Modal -->
    <div class="ems-modal" id="modal-add-project" style="display: none;">
        <div class="ems-modal-content">
            <div class="ems-modal-header">
                <h3>إضافة مشروع جديد</h3>
                <button type="button" onclick="closeModal('modal-add-project')" class="ems-modal-close">&times;</button>
            </div>
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="ems-modal-body">
                    <div class="ems-form-group">
                        <label>اسم المشروع</label>
                        <input type="text" name="name" class="ems-input" required placeholder="أدخل اسم المشروع">
                    </div>

                    <div class="ems-form-group">
                        <label>نوع المشروع</label>
                        <select name="type" class="ems-input" required>
                            <option value="software">برمجي (Software)</option>
                            <option value="media">إعلامي (Media)</option>
                        </select>
                    </div>

                    <div class="ems-form-group">
                        <label>الوصف المختصر</label>
                        <textarea name="description" class="ems-input" rows="3" placeholder="تفاصيل واهداف المشروع"></textarea>
                    </div>

                    <div class="ems-form-group">
                        <label>تاريخ التسليم المتوقع</label>
                        <input type="date" name="delivery_date" class="ems-input" required>
                    </div>
                </div>
                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn" onclick="closeModal('modal-add-project')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">حفظ المشروع</button>
                </div>
            </form>
        </div>
    </div>

    <!-- سكربت التحكم بالـ Modal -->
    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('ems-modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
@endsection
