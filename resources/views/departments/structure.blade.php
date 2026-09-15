@extends('layouts.portal')
@section('title', 'الهيكل التنظيمي التفاعلي | مجموعة إياس الإعلامية')

@section('content')


    <!-- مسار التنقل وترويسة الصفحة -->
    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <a href="{{ route('departments.index') }}">الهيكلية المؤسسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">الهيكل التنظيمي</span>
            </div>
            <h1 class="ems-page-title">الهيكل التنظيمي المؤسسي (EMS Org Chart)</h1>
            <p class="ems-page-subtitle">المخطط الهرمي والتسلسل القيادي لمجموعة إياس الإعلامية والدوائر وأقسامها</p>
        </div>
        <div class="ems-page-actions">
            <a href="{{ route('departments.index') }}" class="ems-btn ems-btn-secondary" style="text-decoration: none;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 19 12 12 5"></polyline>
                </svg>
                <span>سجل الدوائر</span>
            </a>
            <button class="ems-btn ems-btn-secondary" onclick="window.print()">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>طباعة الهيكل</span>
            </button>
            <button class="ems-btn ems-btn-primary" onclick="toggleAllSections()" id="toggleAllBtn">
                <span id="toggleAllText">طي الأقسام</span>
            </button>
        </div>
    </div>

    <!-- 4 بطاقات إحصائية متناسقة مع هوية النظام -->
    <div class="ems-stats-grid">
        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الدوائر الإدارية</span>
                <span class="ems-stat-value" style="color: var(--ems-gold);">{{ $departments->count() }}</span>
                <span class="ems-stat-subtext" style="color: #64748b;">دوائر تخصصية استراتيجية</span>
            </div>
            <div class="ems-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18" />
                    <path d="M3 7v14" />
                    <path d="M21 7v14" />
                    <path d="M6 3h12l3 4H3z" />
                </svg>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">الأقسام والوحدات</span>
                <span class="ems-stat-value" style="color: #0284c7;">{{ $totalSections }}</span>
                <span class="ems-stat-subtext" style="color: #64748b;">وحدات تشغيلية معتمدة</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #e0f2fe; border-color: #bae6fd; color: #0369a1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">إجمالي الكوادر</span>
                <span class="ems-stat-value" style="color: #10b981;">{{ $totalEmployees }}</span>
                <span class="ems-stat-subtext" style="color: #10b981;">موظفاً ومسؤولاً نشطاً</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #dcfce7; border-color: #bbf7d0; color: #15803d;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
        </div>

        <div class="ems-stat-card">
            <div class="ems-stat-info">
                <span class="ems-stat-label">التسكين القيادي</span>
                <span class="ems-stat-value" style="color: #8b5cf6;">{{ $ratio }}%</span>
                <span class="ems-stat-subtext" style="color: #8b5cf6;">{{ $assignedManagers }} من
                    {{ $departments->count() }} دوائر بمدير</span>
            </div>
            <div class="ems-stat-icon" style="background-color: #ede9fe; border-color: #ddd6fe; color: #6d28d9;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- شريط أدوات الفلترة والبحث -->
    <div class="ems-card" style="margin-bottom: 24px; padding: 14px 20px;">
        <div style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center; justify-content: space-between;">
            <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; flex: 1; min-width: 280px;">
                <label style="font-size: 13px; font-weight: 700; color: var(--ems-text-main); white-space: nowrap;">عرض
                    وتحديد دائرة:</label>
                <select id="deptFilterSelect" class="ems-select"
                    style="max-width: 280px; padding: 8px 12px; font-size: 13px;" onchange="focusDepartment(this.value)">
                    <option value="all">كل الدوائر التنظيمية (المخطط الكامل)</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}"
                            {{ (string) $selectedDeptId === (string) $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code ?? 'DEPT' }})
                        </option>
                    @endforeach
                </select>

                <div style="position: relative; flex: 1; min-width: 220px; max-width: 360px;">
                    <input type="text" id="orgSearchInput" class="ems-input"
                        placeholder="بحث فوري (دائرة، قسم، موظف)..." style="padding: 8px 36px 8px 12px; font-size: 13px;"
                        oninput="searchOrgChart(this.value)">
                    <svg style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--ems-text-muted); pointer-events: none;"
                        width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>

            <div style="display: flex; gap: 8px; align-items: center;">
                <button type="button" class="ems-btn ems-btn-sm view-btn active" id="treeViewBtn"
                    onclick="switchView('tree')" style="padding: 7px 14px; font-size: 12.5px;">
                    <span>المخطط الشجري (Org Tree)</span>
                </button>
                <button type="button" class="ems-btn ems-btn-sm view-btn ems-btn-secondary" id="gridViewBtn"
                    onclick="switchView('grid')" style="padding: 7px 14px; font-size: 12.5px;">
                    <span>عرض البطاقات (Cards)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 1. المخطط الشجري التفاعلي (Org Tree View) -->
    <div id="orgTreeView" class="org-chart-wrapper">
        <!-- قمة الهرم: مجلس الإدارة والقيادة العامة -->
        <div class="org-root-node">
            <div class="org-card-root">
                <span class="org-badge-gold">مجلس الإدارة والقيادة التنفيذية</span>
                <div class="org-root-title">مجموعة إياس الإعلامية • EYAS Media Group</div>
                <div class="org-root-leader">
                    <div class="org-avatar-root">ص</div>
                    <div style="text-align: right;">
                        <div class="org-leader-name">صالح رزق محمد حسنين</div>
                        <div class="org-leader-role">رئيس مجلس الإدارة والمدير العام</div>
                    </div>
                </div>
            </div>
            <!-- خط الربط العمودي -->
            <div class="org-stem-line"></div>
        </div>

        <!-- خط التوزيع الأفقي -->
        <div class="org-horizontal-bar"></div>

        <!-- فروع الدوائر التنظيمية الخمسة -->
        <div class="org-branches-grid" id="deptNodesContainer">
            @foreach ($departments as $idx => $dept)
                @php
                    $theme = $deptThemes[$idx % count($deptThemes)];
                    $isTargeted = (string) $selectedDeptId === (string) $dept->id;
                @endphp
                <div class="org-dept-branch {{ $isTargeted ? 'targeted' : '' }}" id="dept-branch-{{ $dept->id }}"
                    data-dept-id="{{ $dept->id }}">
                    <!-- الخط الواصل بالدائرة -->
                    <div class="org-branch-connector"></div>

                    <!-- كرت الدائرة -->
                    <div class="org-card-dept {{ $isTargeted ? 'highlight-dept' : '' }}"
                        style="border-top: 4px solid {{ $theme['color'] }};">
                        <div class="org-dept-top">
                            <span class="ems-badge"
                                style="background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; font-weight: 700;">
                                {{ $dept->code ?? 'DEPT' }}
                            </span>
                            <div style="font-size: 11px; color: var(--ems-text-muted); font-weight: 600;">
                                👥 {{ $dept->employees_count }} كادر
                            </div>
                        </div>

                        <h3 class="org-dept-title">{{ $dept->name }}</h3>
                        <p class="org-dept-desc">
                            {{ Str::limit($dept->description ?? 'دائرة إدارية استراتيجية ضمن هيكل المجموعة.', 75) }}</p>

                        <!-- المدير المسؤول -->
                        <div class="org-manager-box">
                            <div class="org-manager-sub">المدير المسؤول</div>
                            <div class="org-manager-row">
                                <div class="org-avatar-sm"
                                    style="background: {{ $theme['bg'] }}; color: {{ $theme['color'] }};">
                                    {{ mb_substr($dept->manager->full_name ?? 'غ', 0, 1) }}
                                </div>
                                <div>
                                    <div class="org-manager-name">{{ $dept->manager->full_name ?? 'غير محدد حالياً' }}
                                    </div>
                                    <div class="org-manager-role">{{ $dept->manager->position ?? 'مدير الدائرة' }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- زر استعراض الأقسام -->
                        <button type="button" class="ems-btn ems-btn-secondary ems-btn-sm"
                            onclick="toggleDeptSections({{ $dept->id }})"
                            style="width: 100%; font-size: 11.5px; padding: 4px; justify-content: center; margin-top: 8px;">
                            <span>الأقسام والوحدات ({{ $dept->sections->count() }})</span>
                        </button>
                    </div>

                    <!-- حاوية الأقسام التابعة -->
                    <div class="org-sections-tree" id="sections-cluster-{{ $dept->id }}">
                        <div class="org-sec-stem"></div>
                        <div class="org-sections-stack">
                            @forelse ($dept->sections as $sec)
                                <div class="org-card-section" data-section-name="{{ $sec->name }}">
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                        <strong
                                            style="font-size: 12px; color: var(--ems-text-main);">{{ $sec->name }}</strong>
                                        <span class="ems-badge badge-neutral"
                                            style="font-size: 10px;">{{ $sec->employees->count() }} كادر</span>
                                    </div>
                                    <div style="font-size: 11px; color: var(--ems-text-muted);">
                                        المسؤول: <span
                                            style="color: var(--ems-text-main); font-weight: 600;">{{ $sec->head->full_name ?? 'شاغر' }}</span>
                                    </div>

                                    @if ($sec->employees->count() > 0)
                                        <div class="org-staff-chips">
                                            @foreach ($sec->employees->take(2) as $emp)
                                                <span class="org-chip"
                                                    title="{{ $emp->position }}">{{ $emp->full_name }}</span>
                                            @endforeach
                                            @if ($sec->employees->count() > 2)
                                                <span class="org-chip"
                                                    style="font-weight: 700;">+{{ $sec->employees->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="org-card-section-empty">
                                    دائرية قيادية بدون أقسام فرعية
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 2. عرض البطاقات التفصيلية (Cards View) -->
    <div id="orgGridView" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px;">
            @foreach ($departments as $idx => $dept)
                @php
                    $theme = $deptThemes[$idx % count($deptThemes)];
                @endphp
                <div class="ems-card" data-grid-dept-id="{{ $dept->id }}">
                    <div class="ems-card-header" style="border-bottom: 1px solid var(--ems-border); padding: 16px 20px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="background: {{ $theme['bg'] }}; color: {{ $theme['color'] }}; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-family: var(--font-mono); font-size: 13px;">
                                {{ $dept->code ?? 'DEPT' }}
                            </div>
                            <div>
                                <h3 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--ems-text-main);">
                                    {{ $dept->name }}</h3>
                                <span style="font-size: 12px; color: var(--ems-text-muted);">المدير:
                                    {{ $dept->manager->full_name ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                        <span class="ems-badge badge-gold">{{ $dept->employees_count }} كادر</span>
                    </div>

                    <div class="ems-card-body" style="padding: 16px 20px;">
                        <p style="font-size: 12.5px; color: var(--ems-text-muted); line-height: 1.5; margin-bottom: 16px;">
                            {{ $dept->description ?? 'دائرة إدارية معتمدة في الهيكل التنظيمي للمجموعة.' }}
                        </p>

                        <h4 style="font-size: 13px; font-weight: 700; color: var(--ems-text-main); margin-bottom: 10px;">
                            الأقسام التابعة ({{ $dept->sections->count() }}):
                        </h4>

                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @forelse ($dept->sections as $sec)
                                <div
                                    style="background: var(--ems-surface-alt); border: 1px solid var(--ems-border); border-radius: var(--radius-sm); padding: 10px 12px; display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <div style="font-size: 13px; font-weight: 600; color: var(--ems-text-main);">
                                            {{ $sec->name }}</div>
                                        <div style="font-size: 11px; color: var(--ems-text-muted); margin-top: 2px;">
                                            المسؤول: {{ $sec->head->full_name ?? 'شاغر' }}
                                        </div>
                                    </div>
                                    <span class="ems-badge badge-neutral"
                                        style="font-size: 11px;">{{ $sec->employees->count() }} موظف</span>
                                </div>
                            @empty
                                <div
                                    style="font-size: 12px; color: var(--ems-text-muted); font-style: italic; padding: 4px 0;">
                                    لا توجد أقسام فرعية مسجلة لهذه الدائرة.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        /* حاوية الهيكل التنظيمي الشجري */
        .org-chart-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px 60px;
            background: #ffffff;
            border: 1px solid var(--ems-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xs);
            overflow-x: auto;
            min-height: 600px;
        }

        /* قمة الهرم (القيادة العليا) */
        .org-root-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
        }

        .org-card-root {
            background: #ffffff;
            border: 2px solid var(--ems-gold);
            border-radius: var(--radius-lg);
            padding: 16px 24px;
            box-shadow: var(--shadow-md);
            min-width: 320px;
            max-width: 380px;
            text-align: center;
        }

        .org-badge-gold {
            display: inline-block;
            background: var(--ems-gold-light);
            color: var(--ems-gold-hover);
            border: 1px solid var(--ems-gold-border);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: var(--radius-full);
            margin-bottom: 6px;
        }

        .org-root-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--ems-text-main);
            margin-bottom: 10px;
        }

        .org-root-leader {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: var(--ems-surface-alt);
            padding: 8px 14px;
            border-radius: var(--radius-md);
        }

        .org-avatar-root {
            background: var(--ems-gold-gradient);
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .org-leader-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--ems-text-main);
        }

        .org-leader-role {
            font-size: 11px;
            color: var(--ems-gold-hover);
            font-weight: 600;
        }

        /* الخطوط الرابطة */
        .org-stem-line {
            width: 2px;
            height: 28px;
            background: var(--ems-gold);
        }

        .org-horizontal-bar {
            width: 88%;
            max-width: 1120px;
            height: 2px;
            background: var(--ems-gold);
        }

        /* شبكة الفروع والدوائر */
        .org-branches-grid {
            display: flex;
            justify-content: center;
            gap: 20px;
            width: 100%;
            flex-wrap: wrap;
        }

        .org-dept-branch {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 240px;
            transition: all 0.3s ease;
        }

        .org-branch-connector {
            width: 2px;
            height: 24px;
            background: var(--ems-gold);
        }

        /* كرت الدائرة */
        .org-card-dept {
            background: #ffffff;
            border: 1px solid var(--ems-border);
            border-radius: var(--radius-md);
            padding: 14px;
            width: 100%;
            box-shadow: var(--shadow-xs);
            transition: all 0.25s ease;
            text-align: right;
        }

        .org-card-dept:hover,
        .org-card-dept.highlight-dept {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(197, 160, 89, 0.22);
            border-color: var(--ems-gold);
        }

        .org-dept-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .org-dept-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--ems-text-main);
            margin: 0 0 4px 0;
            line-height: 1.3;
        }

        .org-dept-desc {
            font-size: 11px;
            color: var(--ems-text-muted);
            line-height: 1.4;
            margin: 0 0 10px 0;
            min-height: 32px;
        }

        .org-manager-box {
            background: var(--ems-surface-alt);
            border-radius: var(--radius-sm);
            padding: 8px 10px;
        }

        .org-manager-sub {
            font-size: 10px;
            font-weight: 700;
            color: var(--ems-gold-hover);
            margin-bottom: 4px;
        }

        .org-manager-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .org-avatar-sm {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 11px;
            flex-shrink: 0;
        }

        .org-manager-name {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--ems-text-main);
            line-height: 1.2;
        }

        .org-manager-role {
            font-size: 10px;
            color: var(--ems-text-muted);
        }

        /* فروع الأقسام */
        .org-sections-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            transition: all 0.25s ease;
        }

        .org-sec-stem {
            width: 2px;
            height: 18px;
            background: var(--ems-border);
        }

        .org-sections-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        .org-card-section {
            background: #ffffff;
            border: 1px solid var(--ems-border);
            border-right: 3px solid var(--ems-gold);
            border-radius: var(--radius-sm);
            padding: 8px 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            text-align: right;
            transition: all 0.2s ease;
        }

        .org-card-section:hover {
            border-color: var(--ems-gold);
        }

        .org-card-section-empty {
            background: var(--ems-surface-alt);
            border: 1px dashed var(--ems-border);
            border-radius: var(--radius-sm);
            padding: 10px 8px;
            font-size: 11px;
            color: var(--ems-text-muted);
            text-align: center;
        }

        .org-staff-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 4px;
        }

        .org-chip {
            font-size: 9.5px;
            background: var(--ems-surface-alt);
            color: var(--ems-text-muted);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .view-btn.active {
            background: var(--ems-gold);
            color: #ffffff;
            border-color: var(--ems-gold);
        }

        @media print {

            .ems-sidebar,
            .ems-topbar,
            .ems-page-actions,
            .ems-card,
            .ems-stats-grid {
                display: none !important;
            }

            .org-chart-wrapper {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>

    @push('scripts')
        <script>
            let allSectionsOpen = true;

            function toggleDeptSections(deptId) {
                const cluster = document.getElementById(`sections-cluster-${deptId}`);
                if (!cluster) return;
                cluster.style.display = (cluster.style.display === 'none') ? 'flex' : 'none';
            }

            function toggleAllSections() {
                const clusters = document.querySelectorAll('.org-sections-tree');
                const btnText = document.getElementById('toggleAllText');
                allSectionsOpen = !allSectionsOpen;

                clusters.forEach(c => c.style.display = allSectionsOpen ? 'flex' : 'none');
                if (btnText) {
                    btnText.textContent = allSectionsOpen ? 'طي الأقسام' : 'توسيع الأقسام';
                }
            }

            function focusDepartment(deptId) {
                const branches = document.querySelectorAll('.org-dept-branch');
                const gridCards = document.querySelectorAll('[data-grid-dept-id]');

                branches.forEach(b => {
                    const card = b.querySelector('.org-card-dept');
                    b.style.display = 'flex';
                    b.style.opacity = '1';

                    if (deptId !== 'all' && b.dataset.deptId === deptId) {
                        card.classList.add('highlight-dept');
                        b.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    } else {
                        card.classList.remove('highlight-dept');
                    }
                });

                gridCards.forEach(c => {
                    c.style.display = 'flex';
                    if (deptId !== 'all' && c.dataset.gridDeptId === deptId) {
                        c.style.borderColor = 'var(--ems-gold)';
                        c.style.boxShadow = '0 0 0 2px rgba(197, 160, 89, 0.35)';
                        c.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    } else {
                        c.style.borderColor = '';
                        c.style.boxShadow = '';
                    }
                });
            }

            function searchOrgChart(query) {
                query = query.trim().toLowerCase();
                const branches = document.querySelectorAll('.org-dept-branch');

                if (!query) {
                    focusDepartment(document.getElementById('deptFilterSelect').value);
                    return;
                }

                branches.forEach(b => {
                    const match = b.innerText.toLowerCase().includes(query);
                    b.style.display = match ? 'flex' : 'none';
                    b.style.opacity = '1';
                });
            }

            function switchView(view) {
                const treeView = document.getElementById('orgTreeView');
                const gridView = document.getElementById('orgGridView');
                const treeBtn = document.getElementById('treeViewBtn');
                const gridBtn = document.getElementById('gridViewBtn');

                if (view === 'tree') {
                    treeView.style.display = 'flex';
                    gridView.style.display = 'none';
                    treeBtn.classList.add('active');
                    treeBtn.classList.remove('ems-btn-secondary');
                    gridBtn.classList.remove('active');
                    gridBtn.classList.add('ems-btn-secondary');
                } else {
                    treeView.style.display = 'none';
                    gridView.style.display = 'block';
                    gridBtn.classList.add('active');
                    gridBtn.classList.remove('ems-btn-secondary');
                    treeBtn.classList.remove('active');
                    treeBtn.classList.add('ems-btn-secondary');
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                const urlParams = new URLSearchParams(window.location.search);
                const deptParam = urlParams.get('department');
                if (deptParam) {
                    focusDepartment(deptParam);
                }
            });
        </script>
    @endpush
@endsection
