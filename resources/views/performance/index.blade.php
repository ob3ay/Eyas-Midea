@extends('layouts.portal')
@section('title', 'الأداء والإنجاز')
@section('content')
    <div class="ems-content">
        <div class="ems-page-header">
            <div class="ems-page-title-wrap">
                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span>الرقابة والتقييم</span>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span style="color: var(--ems-text-main); font-weight: 700;">الأداء والإنجاز</span>
                </div>
                <h1 class="ems-page-title">مؤشرات الأداء والإنجاز المؤسسي (KPIs)</h1>
                <p class="ems-page-subtitle">تقييم الكفاءة والإنتاجية للدوائر والكوادر التابعة لـ مجموعة إياس الإعلامية</p>
            </div>
            @if ($canCreate)
                <div class="ems-page-actions">
                    <button class="ems-btn ems-btn-primary" onclick="openModal('modal-evaluate-performance')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon
                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                        </svg>
                        <span>إضافة تقييم أداء جديد</span>
                    </button>
                </div>
            @endif
        </div>

        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block; padding:10px 14px; margin-bottom:14px;">
                {{ session('success') }}</div>
        @endif

        <div class="ems-kpi-banner">
            <div>
                <div
                    style="font-size: 12px; font-weight: 700; color: var(--ems-gold); text-transform: uppercase; margin-bottom: 6px;">
                    مؤشر الإنتاجية العام (EMS Overall Score)</div>
                <h2 style="font-size: 22px; font-weight: 800; margin-bottom: 6px;">متوسط التقييمات المعتمدة ضمن نطاقك</h2>
                <p style="font-size: 13px; color: #94a3b8; max-width: 550px;">محسوب من متوسط آخر تقييمات الأداء المعتمدة من
                    المدراء المباشرين للموظفين ضمن نطاقك.</p>
            </div>
            <div style="display: flex; align-items: center; gap: 24px;">
                <div style="text-align: center;">
                    <div class="ems-kpi-big-num">{{ $overallScore !== null ? $overallScore . '%' : '--' }}</div>
                    @if ($overallScore !== null)
                        <span class="ems-badge badge-completed" style="margin-top: 4px;">
                            تصنيف:
                            {{ $overallScore >= 90 ? 'ممتاز' : ($overallScore >= 75 ? 'جيد جداً' : ($overallScore >= 60 ? 'جيد' : 'بحاجة تحسين')) }}
                        </span>
                    @else
                        <span class="ems-badge" style="margin-top: 4px;">ما في تقييمات معتمدة بعد</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="ems-card">
            <div class="ems-card-header">
                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M12 20V10" />
                        <path d="M18 20V4" />
                        <path d="M6 20v-4" />
                    </svg>
                    <span>سجل تقييم أداء الكوادر</span>
                </div>
                <span class="ems-badge badge-gold">{{ $rows->count() }} موظف</span>
            </div>

            <div class="ems-card-body" style="padding: 0;">
                <div class="ems-table-responsive">
                    <table class="ems-table" id="performanceTable">
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>الدائرة والقسم</th>
                                <th>المهام المنجزة</th>
                                <th>الالتزام بالموعد</th>
                                <th>آخر تقييم معتمد</th>
                                <th style="text-align: center;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $avatarColors = ['#c5a059', '#1e293b', '#334155', '#475569', '#0284c7', '#0369a1', '#3b82f6', '#d97706']; @endphp
                            @forelse ($rows as $index => $row)
                                @php
                                    $score = $row->evaluation?->score;
                                    $grade =
                                        $score === null
                                            ? null
                                            : ($score >= 90
                                                ? 'ممتاز (A+)'
                                                : ($score >= 80
                                                    ? 'ممتاز (A)'
                                                    : ($score >= 65
                                                        ? 'جيد جداً (B)'
                                                        : ($score >= 50
                                                            ? 'جيد (C)'
                                                            : 'بحاجة تحسين (D)'))));
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div class="ems-avatar"
                                                style="width: 32px; height: 32px; font-size: 12px; background: {{ $avatarColors[$index % count($avatarColors)] }};">
                                                {{ mb_substr($row->employee->full_name, 0, 1) }}</div>
                                            <div>
                                                <strong>{{ $row->employee->full_name }}</strong>
                                                <div style="font-size: 11px; color: var(--ems-text-muted);">
                                                    {{ $row->employee->position ?? '-' }}
                                                    ({{ $row->employee->employee_code ?? '-' }})</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $row->employee->department->name ?? '-' }}{{ $row->employee->section ? ' - ' . $row->employee->section->name : '' }}
                                    </td>
                                    <td><span
                                            style="font-family: var(--font-mono); font-weight: 700;">{{ $row->completed_tasks }}
                                            مهمة</span></td>
                                    <td>
                                        @if ($row->on_time_rate !== null)
                                            <span
                                                style="font-family: var(--font-mono); font-weight: 700; color: {{ $row->on_time_rate >= 80 ? '#15803d' : '#b45309' }};">{{ $row->on_time_rate }}%</span>
                                        @else
                                            <span style="color: var(--ems-text-muted);">لا توجد بيانات كافية</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->evaluation)
                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                <div class="ems-progress" style="width: 70px; height: 6px;">
                                                    <div class="ems-progress-bar" style="width: {{ $score }}%;">
                                                    </div>
                                                </div>
                                                <span class="ems-badge badge-completed">{{ $grade }}</span>
                                            </div>
                                            <div style="font-size:10.5px; color:var(--ems-text-muted); margin-top:4px;">
                                                {{ $row->evaluation->period }} -
                                                {{ $row->evaluation->evaluation_date->format('Y-m-d') }}</div>
                                        @else
                                            <span style="color: var(--ems-text-muted); font-size: 12px;">ما في تقييم
                                                بعد</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if ($row->evaluation && $row->evaluation->notes)
                                            <button class="ems-btn ems-btn-icon" title="ملاحظات التقييم"
                                                onclick="showToast({{ Js::from($row->evaluation->notes) }}, 'info')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </button>
                                        @else
                                            <span style="color:var(--ems-text-muted);">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align:center; color: var(--ems-text-muted);">ما في موظفين
                                        ضمن نطاقك.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($canCreate)
        <div class="ems-modal-overlay" id="modal-evaluate-performance">
            <div class="ems-modal">
                <div class="ems-modal-header">
                    <div class="ems-modal-title">إضافة تقييم أداء جديد</div>
                    <button class="ems-modal-close" onclick="closeModal('modal-evaluate-performance')">✕</button>
                </div>
                <form action="{{ route('performance.evaluate') }}" method="POST">
                    @csrf
                    <div class="ems-modal-body">
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الموظف</label>
                                <select name="employee_id" class="ems-select" required>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> الفترة (مثال: Q3-2026)</label>
                                <input type="text" name="period" class="ems-input" placeholder="Q3-2026" required>
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> تاريخ التقييم</label>
                                <input type="date" name="evaluation_date" class="ems-input"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> النسبة العامة (0-100)</label>
                                <input type="number" name="score" class="ems-input" min="0" max="100"
                                    step="0.1" required>
                            </div>
                        </div>
                        <div class="ems-form-row">
                            <div class="ems-form-group" style="flex: 1;">
                                <label class="ems-label">ملاحظات (اختياري)</label>
                                <textarea name="notes" class="ems-input" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="ems-modal-footer">
                        <button type="button" class="ems-btn ems-btn-secondary"
                            onclick="closeModal('modal-evaluate-performance')">إلغاء</button>
                        <button type="submit" class="ems-btn ems-btn-primary">اعتماد التقييم</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
