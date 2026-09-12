@extends('layouts.portal')
@section('title', 'الدوائر والأقسام')
@section('content')
    <div class="ems-content">
        <div class="ems-page-header">
            <div class="ems-page-title-wrap">
                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span>الهيكلية المؤسسية</span>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span style="color: var(--ems-text-main); font-weight: 700;">الدوائر والأقسام</span>
                </div>
                <h1 class="ems-page-title">الدوائر والأقسام التخصصية</h1>
                <p class="ems-page-subtitle">الهيكل التنظيمي المعتمد لمجموعة إياس الإعلامية وتوزيع الكوادر والمسؤوليات</p>
            </div>
            <div class="ems-page-actions">
                <button class="ems-btn ems-btn-primary" onclick="openModal('modal-add-department')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    <span>+ إضافة دائرة أو قسم جديد</span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block; padding:10px 14px; margin-bottom:14px;">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="ems-badge"
                style="display:block; padding:10px 14px; margin-bottom:14px; background:#fee2e2; color:#b91c1c;">
                {{ session('error') }}</div>
        @endif

        <!-- شبكة بطاقات الدوائر (ديناميكية) -->
        <div class="ems-dept-grid">
            @php
                // نفس الألوان والأيقونات الأربعة الأصلية، بتتكرر بالتناوب لو الدوائر أكتر من 4
                $deptThemes = [
                    [
                        'bg' => '#fef3c7',
                        'color' => '#b45309',
                        'border' => '#fde68a',
                        'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
                    ],
                    [
                        'bg' => '#e0f2fe',
                        'color' => '#0369a1',
                        'border' => '#bae6fd',
                        'icon' =>
                            '<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>',
                    ],
                    [
                        'bg' => '#eff6ff',
                        'color' => '#1d4ed8',
                        'border' => '#bfdbfe',
                        'icon' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
                    ],
                    [
                        'bg' => '#fdf4ff',
                        'color' => '#a21caf',
                        'border' => '#f5d0fe',
                        'icon' =>
                            '<path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>',
                    ],
                ];
            @endphp

            @forelse ($departments as $index => $department)
                @php $theme = $deptThemes[$index % 4]; @endphp
                <div class="ems-dept-card">
                    <div>
                        <div class="ems-dept-header">
                            <div class="ems-dept-icon"
                                style="background:{{ $theme['bg'] }}; color:{{ $theme['color'] }}; border-color:{{ $theme['border'] }};">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">{!! $theme['icon'] !!}</svg>
                            </div>
                            <span
                                class="ems-badge badge-gold">{{ $department->code ?? 'DEPT-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h2 class="ems-dept-title">{{ $department->name }}</h2>
                        <p class="ems-dept-desc">{{ $department->description ?? 'ما في وصف مضاف لهاي الدائرة بعد.' }}</p>
                    </div>
                    <div>
                        <div class="ems-dept-meta">
                            <div>
                                <div class="ems-dept-meta-item-label">عدد الموظفين</div>
                                <div class="ems-dept-meta-item-val">{{ $department->employees_count }}</div>
                            </div>
                            <div>
                                <div class="ems-dept-meta-item-label">عدد الأقسام</div>
                                <div class="ems-dept-meta-item-val">{{ $department->sections_count }}</div>
                            </div>
                            <div>
                                <div class="ems-dept-meta-item-label">المدير المسؤول</div>
                                <div
                                    style="font-size: 11px; font-weight: 700; color: var(--ems-gold-hover); margin-top: 2px;">
                                    {{ $department->manager->full_name ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        <button class="ems-btn ems-btn-secondary ems-btn-sm" style="width: 100%;"
                            onclick="showToast('{{ $department->name }}: {{ $department->employees_count }} موظف موزعين على {{ $department->sections_count }} قسم', 'info')">استعراض
                            الهيكل ←</button>
                    </div>
                </div>
            @empty
                <p style="font-size: 13px; color: var(--ems-text-muted);">ما في دوائر مضافة بعد.</p>
            @endforelse
        </div>

        <!-- جدول الأقسام التفصيلي (ديناميكي) -->
        <div class="ems-card">
            <div class="ems-card-header">
                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M3 21h18" />
                        <path d="M3 7v14" />
                        <path d="M21 7v14" />
                    </svg>
                    <span>سجل الأقسام التخصصية والوحدات التنفيذية المعتمدة</span>
                </div>
                <span class="ems-badge badge-completed">{{ $sections->count() }} وحدات تخصصية</span>
            </div>

            <div class="ems-card-body" style="padding: 0;">
                <div class="ems-table-responsive">
                    <table class="ems-table" id="departmentsTable">
                        <thead>
                            <tr>
                                <th>رمز القسم</th>
                                <th>اسم القسم</th>
                                <th>الدائرة الرئيسية</th>
                                <th>المسؤول المباشر</th>
                                <th>عدد الكوادر</th>
                                <th>الحالة</th>
                                <th style="text-align: center;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sections as $section)
                                <tr>
                                    <td><span
                                            style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">SEC-{{ str_pad($section->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td><strong style="color: var(--ems-text-main);">{{ $section->name }}</strong></td>
                                    <td>{{ $section->department->name ?? '-' }}</td>
                                    <td>{{ $section->head->full_name ?? 'غير محدد' }}</td>
                                    <td>{{ $section->employees_count }} موظف</td>
                                    <td><span class="ems-badge badge-completed">نشط</span></td>
                                    <td style="text-align: center;">
                                        <button class="ems-btn ems-btn-icon" title="تعديل"><svg viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align:center; color: var(--ems-text-muted);">ما في أقسام
                                        مضافة بعد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
