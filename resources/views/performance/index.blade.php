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
                                        @if ($row->evaluation)
                                            <button type="button" class="ems-btn ems-btn-icon" title="عرض تفاصيل التقييم"
                                                onclick="showEvaluationDetails({{ Js::from([
                                                    'employee_name' => $row->employee->full_name,
                                                    'employee_code' => $row->employee->employee_code,
                                                    'position' => $row->employee->position ?? '-',
                                                    'department' => ($row->employee->department?->name ?? '-') . ($row->employee->section ? ' - ' . $row->employee->section->name : ''),
                                                    'period' => $row->evaluation->period,
                                                    'date' => $row->evaluation->evaluation_date?->translatedFormat('d F Y') ?? $row->evaluation->evaluation_date?->format('Y-m-d'),
                                                    'score' => (float) $row->evaluation->score,
                                                    'grade' => $grade ?? '-',
                                                    'evaluator' => $row->evaluation->evaluator?->username ?? 'الإدارة',
                                                    'notes' => $row->evaluation->notes ?? 'لا توجد ملاحظات إضافية مسجلة.',
                                                    'completed_tasks' => $row->completed_tasks,
                                                    'on_time_rate' => $row->on_time_rate !== null ? $row->on_time_rate . '%' : 'غير محدد',
                                                ]) }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
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

    {{-- View Evaluation Modal --}}
    <div class="ems-modal-overlay" id="modal-view-evaluation">
        <div class="ems-modal" style="max-width: 580px;">
            <div class="ems-modal-header">
                <div class="ems-modal-title" style="display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ems-gold-hover);">
                        <path d="M12 20V10" />
                        <path d="M18 20V4" />
                        <path d="M6 20v-4" />
                    </svg>
                    <span>تقرير وتفاصيل تقييم الأداء</span>
                </div>
                <button type="button" class="ems-modal-close" onclick="closeModal('modal-view-evaluation')">✕</button>
            </div>

            <div class="ems-modal-body" style="padding: 24px;">

                {{-- Employee Info Banner --}}
                <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border, #e2e8f0); border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h4 id="eval_emp_name" style="margin: 0 0 4px 0; font-size: 16px; font-weight: 800; color: var(--ems-text-main);"></h4>
                        <div id="eval_emp_meta" style="font-size: 12px; color: var(--ems-text-muted);"></div>
                    </div>
                    <span class="ems-badge badge-gold" id="eval_period_badge"></span>
                </div>

                {{-- Big Score Card --}}
                <div style="background: linear-gradient(135deg, rgba(197, 160, 89, 0.1), rgba(197, 160, 89, 0.03)); border: 1px solid var(--ems-gold-light, #e2d2b4); border-radius: 14px; padding: 18px; text-align: center; margin-bottom: 20px;">
                    <div style="font-size: 12px; font-weight: 700; color: var(--ems-gold-hover); margin-bottom: 4px;">
                        النتيجة والتقييم المعتمد
                    </div>
                    <div id="eval_score_val" style="font-size: 38px; font-weight: 900; font-family: var(--font-mono); color: var(--ems-text-main); line-height: 1.1;"></div>
                    <div style="display: flex; justify-content: center; margin-top: 6px;">
                        <span id="eval_grade_badge" class="ems-badge badge-completed" style="font-size: 12px; padding: 4px 14px;"></span>
                    </div>

                    <div class="ems-progress" style="height: 8px; max-width: 260px; margin: 12px auto 0; border-radius: 999px;">
                        <div id="eval_progress_bar" class="ems-progress-bar" style="width: 0%;"></div>
                    </div>
                </div>

                {{-- Detail Grid --}}
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px;">
                    <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 10px; padding: 12px;">
                        <div style="font-size: 11px; color: var(--ems-text-muted); margin-bottom: 4px;">تاريخ الاعتماد</div>
                        <strong id="eval_date_val" style="font-size: 13px; color: var(--ems-text-main);"></strong>
                    </div>

                    <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 10px; padding: 12px;">
                        <div style="font-size: 11px; color: var(--ems-text-muted); margin-bottom: 4px;">المسؤول المقيّم</div>
                        <strong id="eval_evaluator_val" style="font-size: 13px; color: var(--ems-text-main);"></strong>
                    </div>

                    <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 10px; padding: 12px;">
                        <div style="font-size: 11px; color: var(--ems-text-muted); margin-bottom: 4px;">المهام المنجزة</div>
                        <strong id="eval_tasks_val" style="font-size: 13px; color: var(--ems-text-main);"></strong>
                    </div>

                    <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 10px; padding: 12px;">
                        <div style="font-size: 11px; color: var(--ems-text-muted); margin-bottom: 4px;">الالتزام بالموعد</div>
                        <strong id="eval_ontime_val" style="font-size: 13px; color: var(--ems-text-main);"></strong>
                    </div>
                </div>

                {{-- Notes Section --}}
                <div style="border: 1px solid var(--ems-border); border-radius: 12px; padding: 14px 16px; background: var(--ems-surface);">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 700; color: var(--ems-text-main); margin-bottom: 8px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ems-gold-hover);">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                        </svg>
                        <span>ملاحظات وتوصيات التقييم الإداري:</span>
                    </div>
                    <p id="eval_notes_val" style="margin: 0; font-size: 13px; line-height: 1.7; color: var(--ems-text-main); white-space: pre-line; background: var(--ems-surface-alt, #f8fafc); padding: 12px; border-radius: 8px; border-right: 3px solid var(--ems-gold);"></p>
                </div>

            </div>

            <div class="ems-modal-footer" style="display: flex; justify-content: flex-end;">
                <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-view-evaluation')">إغلاق</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function showEvaluationDetails(data) {
                document.getElementById('eval_emp_name').textContent = data.employee_name || '';
                document.getElementById('eval_emp_meta').textContent = (data.position || '') + ' (' + (data.employee_code || '') + ') • ' + (data.department || '');
                document.getElementById('eval_period_badge').textContent = data.period || 'الفترة الحالية';
                document.getElementById('eval_score_val').textContent = (data.score !== undefined && data.score !== null ? data.score + '%' : '--');
                document.getElementById('eval_grade_badge').textContent = data.grade || 'معتمد';
                document.getElementById('eval_progress_bar').style.width = Math.min(100, Math.max(0, data.score || 0)) + '%';
                document.getElementById('eval_date_val').textContent = data.date || '-';
                document.getElementById('eval_evaluator_val').textContent = data.evaluator || 'الإدارة العامة';
                document.getElementById('eval_tasks_val').textContent = (data.completed_tasks || 0) + ' مهمة مكتملة';
                document.getElementById('eval_ontime_val').textContent = data.on_time_rate || 'لا توجد بيانات';
                document.getElementById('eval_notes_val').textContent = data.notes || 'لا توجد ملاحظات إضافية مسجلة على هذا التقييم.';

                openModal('modal-view-evaluation');
            }
        </script>
    @endpush
@endsection
