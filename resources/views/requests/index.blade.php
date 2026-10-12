@extends('layouts.portal')

@section('title', $isAdmin ? 'إدارة طلبات الموظفين' : 'طلباتي والخدمات الذاتية')

@push('styles')
<style>
.req-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 1100px) {
    .req-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .req-kpi-grid { grid-template-columns: 1fr; }
}

.req-kpi-card {
    background: var(--ems-surface);
    border: 1px solid var(--ems-border);
    border-radius: 16px;
    padding: 20px 22px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    position: relative;
    overflow: hidden;
    transition: transform .22s ease, box-shadow .22s ease;
}
.req-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 4px;
    height: 100%;
    border-radius: 0 16px 16px 0;
}
.req-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, .08);
}
.req-kpi-card.gold::before { background: linear-gradient(180deg, #c5a059, #e2c97e); }
.req-kpi-card.orange::before { background: linear-gradient(180deg, #d97706, #f59e0b); }
.req-kpi-card.green::before { background: linear-gradient(180deg, #15803d, #22c55e); }
.req-kpi-card.red::before { background: linear-gradient(180deg, #dc2626, #f87171); }

.req-kpi-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.req-kpi-icon.gold { background: rgba(197, 160, 89, .13); color: #c5a059; }
.req-kpi-icon.orange { background: rgba(217, 119, 6, .13); color: #d97706; }
.req-kpi-icon.green { background: rgba(21, 128, 61, .13); color: #15803d; }
.req-kpi-icon.red { background: rgba(220, 38, 38, .12); color: #dc2626; }
.req-kpi-icon svg { width: 22px; height: 22px; }

.req-kpi-label {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--ems-text-muted);
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.req-kpi-value {
    font-size: 28px;
    font-weight: 900;
    font-family: var(--font-mono, monospace);
    color: var(--ems-text-main);
    line-height: 1;
    margin-bottom: 4px;
}
.req-kpi-sub {
    font-size: 11px;
    color: var(--ems-text-muted);
}

.req-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 14px 18px;
    background: var(--ems-surface-alt, #f8fafc);
    border: 1px solid var(--ems-border);
    border-radius: 12px;
    margin-bottom: 16px;
}
.req-filter-bar select, .req-filter-bar input {
    border: 1px solid var(--ems-border);
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 13px;
    background: var(--ems-surface);
    color: var(--ems-text-main);
    outline: none;
    transition: border-color .2s;
}
.req-filter-bar select:focus, .req-filter-bar input:focus {
    border-color: var(--ems-gold, #c5a059);
}
.req-filter-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--ems-text-muted);
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.ems-avatar {
    width: 36px !important;
    height: 36px !important;
    min-width: 36px !important;
    min-height: 36px !important;
    max-width: 36px !important;
    max-height: 36px !important;
    flex-shrink: 0 !important;
    aspect-ratio: 1 / 1 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: 1 !important;
    text-align: center !important;
    box-sizing: border-box !important;
    font-size: 13px !important;
    font-weight: 800 !important;
    color: #fff !important;
}

.req-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--ems-border);
    background: var(--ems-surface);
    color: var(--ems-text-main);
    cursor: pointer;
    transition: all .18s;
}
.req-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, .06);
}
.req-action-btn.approve {
    color: #15803d;
    border-color: rgba(21, 128, 61, 0.3);
}
.req-action-btn.approve:hover {
    background: #15803d;
    color: #fff;
    border-color: #15803d;
}
.req-action-btn.reject {
    color: #dc2626;
    border-color: rgba(220, 38, 38, 0.3);
}
.req-action-btn.reject:hover {
    background: #dc2626;
    color: #fff;
    border-color: #dc2626;
}

.req-export-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid var(--ems-border);
    background: var(--ems-surface);
    color: var(--ems-text-main);
    transition: all .2s;
}
.req-export-btn:hover {
    background: var(--ems-gold, #c5a059);
    color: #fff;
    border-color: transparent;
}
.req-export-btn svg {
    width: 15px;
    height: 15px;
}
</style>
@endpush

@section('content')
<div class="ems-content">

    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span>{{ $isAdmin ? 'الشؤون الإدارية' : 'المتابعة والموظف' }}</span>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color: var(--ems-text-main); font-weight: 700;">{{ $isAdmin ? 'إدارة طلبات الموظفين' : 'طلباتي والخدمات الذاتية' }}</span>
            </div>

            <h1 class="ems-page-title">
                {{ $isAdmin ? 'إدارة ومتابعة طلبات الموظفين' : 'طلباتي والخدمات الذاتية للموظف' }}
            </h1>

            <p class="ems-page-subtitle">
                {{ $isAdmin
                    ? 'مراجعة واعتماد طلبات الإجازات والسلف المالية والشهادات الرسمية المقدمة من كوادر المؤسسة'
                    : 'تقديم ومتابعة طلبات الإجازات الاعتيادية والمرضية، السلف المالية، شهادات التعريف بالراتب، ومتابعة ردود الإدارة المباشرة' }}
            </p>
        </div>

        <div class="ems-page-actions">
            @if ($employee && $canCreateRequest)
                <button type="button" class="ems-btn ems-btn-primary" onclick="openModal('modal-add-request')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>تقديم طلب جديد</span>
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="ems-badge badge-completed" style="display:inline-flex; align-items:center; gap:8px; padding:12px 18px; margin-bottom:18px; font-size:13.5px; border-radius:10px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if (session('error'))
        <div class="ems-badge badge-danger" style="display:inline-flex; align-items:center; gap:8px; padding:12px 18px; margin-bottom:18px; font-size:13.5px; border-radius:10px; background:#fee2e2; color:#b91c1c;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="req-kpi-grid">
        <div class="req-kpi-card gold">
            <div class="req-kpi-icon gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
            </div>
            <div>
                <div class="req-kpi-label">{{ $isAdmin ? 'إجمالي الطلبات' : 'إجمالي طلباتي' }}</div>
                <div class="req-kpi-value">{{ $stats['total'] }}</div>
                <div class="req-kpi-sub">كافة الطلبات المسجلة</div>
            </div>
        </div>

        <div class="req-kpi-card orange">
            <div class="req-kpi-icon orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <div class="req-kpi-label">قيد المراجعة</div>
                <div class="req-kpi-value" style="color: #d97706;">{{ $stats['pending'] }}</div>
                <div class="req-kpi-sub">بانتظار البت والاعتماد</div>
            </div>
        </div>

        <div class="req-kpi-card green">
            <div class="req-kpi-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div>
                <div class="req-kpi-label">معتمدة ومقبولة</div>
                <div class="req-kpi-value" style="color: #15803d;">{{ $stats['approved'] }}</div>
                <div class="req-kpi-sub">تمت الموافقة الرسمية</div>
            </div>
        </div>

        <div class="req-kpi-card red">
            <div class="req-kpi-icon red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
            <div>
                <div class="req-kpi-label">طلبات مرفوضة</div>
                <div class="req-kpi-value" style="color: #dc2626;">{{ $stats['rejected'] }}</div>
                <div class="req-kpi-sub">مع الملاحظات الإدارية</div>
            </div>
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>{{ $isAdmin ? 'سجل طلبات الكوادر الواردة' : 'سجل الطلبات الإدارية والمالية' }}</span>
            </div>
            <span class="ems-badge badge-gold">{{ $stats['total'] }} طلب</span>
        </div>

        <div style="padding: 14px 18px 0;">
            <div class="req-filter-bar">
                <span class="req-filter-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>تصفية:</span>
                </span>
                <input type="text" id="reqSearchInput" placeholder="ابحث بالاسم أو الموضوع..." oninput="filterRequestsTable()" style="min-width: 170px;">
                <select id="reqFilterStatus" onchange="filterRequestsTable()">
                    <option value="">جميع الحالات</option>
                    <option value="pending">قيد المراجعة</option>
                    <option value="approved">معتمد ومقبول</option>
                    <option value="rejected">مرفوض</option>
                    <option value="cancelled">ملغي</option>
                </select>
                <select id="reqFilterType" onchange="filterRequestsTable()">
                    <option value="">جميع أنواع الطلبات</option>
                    <option value="leave">طلب إجازة</option>
                    <option value="advance">طلب سلفة مالية</option>
                    <option value="certificate">شهادة تعريف بالراتب</option>
                    <option value="other">طلب إداري / أخرى</option>
                </select>
                @if ($isAdmin)
                <select id="reqFilterDept" onchange="filterRequestsTable()">
                    <option value="">جميع الدوائر</option>
                    @foreach($requests->map(fn($r) => $r->employee?->department?->name)->filter()->unique()->sort() as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
                @endif
                <button type="button" class="req-export-btn" onclick="resetRequestFilters()" style="margin-right:auto;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    <span>مسح الفلتر</span>
                </button>
            </div>
        </div>

        <div class="ems-card-body" style="padding: 0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="requestsTable">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            @if ($isAdmin)
                                <th>الموظف</th>
                                <th>الدائرة والقسم</th>
                            @endif
                            <th>نوع الطلب</th>
                            <th>موضوع وبيانات الطلب</th>
                            <th>تاريخ التقديم</th>
                            <th>الحالة</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="requestsTableBody">
                        @php
                            $avatarColors = ['#c5a059','#1e293b','#334155','#475569','#0284c7','#0369a1','#3b82f6','#d97706','#7c3aed','#0f766e'];
                            $typeMeta = [
                                'leave' => ['label' => 'طلب إجازة', 'class' => 'badge-gold'],
                                'advance' => ['label' => 'سلفة مالية', 'class' => 'badge-review'],
                                'certificate' => ['label' => 'شهادة تعريف', 'class' => 'badge-progress'],
                                'other' => ['label' => 'طلب إداري', 'class' => 'badge-draft'],
                            ];
                            $statusMeta = [
                                'pending' => ['label' => 'قيد المراجعة', 'class' => 'badge-gold'],
                                'approved' => ['label' => 'معتمد ومقبول', 'class' => 'badge-completed'],
                                'rejected' => ['label' => 'مرفوض', 'class' => 'badge-danger'],
                                'cancelled' => ['label' => 'ملغي', 'class' => 'badge-draft'],
                            ];
                        @endphp

                        @forelse ($requests as $idx => $req)
                            @php
                                $t = $typeMeta[$req->type] ?? ['label' => $req->type, 'class' => ''];
                                $s = $statusMeta[$req->status] ?? ['label' => $req->status, 'class' => ''];
                                $ac = $avatarColors[$idx % count($avatarColors)];
                                $deptName = $req->employee?->department?->name ?? '-';
                                $secName  = $req->employee?->section?->name;
                            @endphp
                            <tr data-status="{{ $req->status }}"
                                data-type="{{ $req->type }}"
                                data-dept="{{ $deptName }}"
                                data-search="{{ strtolower(($req->employee?->full_name ?? '') . ' ' . $req->title . ' ' . ($req->reason ?? '')) }}">
                                <td style="color:var(--ems-text-muted); font-size:12px; font-family:var(--font-mono,monospace);">
                                    {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}
                                </td>

                                @if ($isAdmin)
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <div class="ems-avatar" style="background: {{ $ac }};">
                                                {{ mb_substr($req->employee?->full_name ?? 'م', 0, 1) }}
                                            </div>
                                            <div>
                                                <strong style="font-size: 13.5px; color: var(--ems-text-main);">{{ $req->employee?->full_name ?? 'موظف غير محدد' }}</strong>
                                                <div style="font-size: 11px; color: var(--ems-text-muted);">
                                                    {{ $req->employee?->position ?? '-' }}
                                                    @if ($req->employee?->employee_code) &bull; {{ $req->employee->employee_code }} @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 13px; font-weight: 600;">{{ $deptName }}</div>
                                        @if ($secName)
                                            <div style="font-size: 11px; color: var(--ems-text-muted);">{{ $secName }}</div>
                                        @endif
                                    </td>
                                @endif

                                <td>
                                    <span class="ems-badge {{ $t['class'] }}" style="font-size: 11px; padding: 3px 9px;">
                                        {{ $t['label'] }}
                                    </span>
                                </td>

                                <td>
                                    <div style="font-weight: 700; font-size: 13px; color: var(--ems-text-main);">
                                        {{ $req->title }}
                                    </div>
                                    <div style="font-size: 11px; color: var(--ems-text-muted); margin-top: 3px;">
                                        @if ($req->type === 'leave' && $req->start_date)
                                            <span style="font-family:var(--font-mono); font-weight:700; color:var(--ems-gold-hover);">
                                                {{ $req->days_count ? $req->days_count . ' يوم' : 'إجازة' }}
                                            </span>
                                            ({{ $req->start_date->format('Y-m-d') }} &larr; {{ $req->end_date ? $req->end_date->format('Y-m-d') : '-' }})
                                        @elseif ($req->type === 'advance' && $req->amount)
                                            <span style="font-family:var(--font-mono); font-weight:800; color:#15803d;">
                                                {{ number_format($req->amount, 2) }} ₪
                                            </span>
                                            &bull; سلفة مالية
                                        @elseif ($req->reason)
                                            {{ Str::limit($req->reason, 60) }}
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div style="font-size: 11.5px; font-weight: 600; color: var(--ems-text-main); font-family: var(--font-mono);">
                                        {{ $req->created_at?->format('Y-m-d') }}
                                    </div>
                                    <div style="font-size: 10px; color: var(--ems-text-muted); font-family: var(--font-mono);">
                                        {{ $req->created_at?->format('H:i') }}
                                    </div>
                                </td>

                                <td>
                                    <span class="ems-badge {{ $s['class'] }}" style="font-size: 11px;">
                                        {{ $s['label'] }}
                                    </span>
                                </td>

                                <td style="text-align: center;">
                                    <div style="display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                        <button type="button" class="req-action-btn" title="عرض تفاصيل الطلب"
                                            onclick="showRequestDetails({{ Js::from([
                                                'id' => 'REQ-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
                                                'employee_name' => $req->employee?->full_name ?? 'موظف غير محدد',
                                                'department' => $deptName . ($secName ? ' - ' . $secName : ''),
                                                'position' => $req->employee?->position ?? '-',
                                                'type_label' => $t['label'] ?? '',
                                                'type_class' => $t['class'] ?? '',
                                                'title' => $req->title,
                                                'reason' => $req->reason ?? 'لا توجد تفاصيل إضافية مسجلة.',
                                                'start_date' => $req->start_date ? $req->start_date->format('Y-m-d') : null,
                                                'end_date' => $req->end_date ? $req->end_date->format('Y-m-d') : null,
                                                'days_count' => $req->days_count,
                                                'amount' => $req->amount ? number_format($req->amount, 2) . ' ₪' : null,
                                                'created_at' => $req->created_at ? $req->created_at->format('Y-m-d H:i') : '-',
                                                'status' => $req->status,
                                                'status_label' => $s['label'],
                                                'status_class' => $s['class'],
                                                'reviewer' => $req->reviewer?->username ?? null,
                                                'reviewed_at' => $req->reviewed_at ? $req->reviewed_at->format('Y-m-d H:i') : null,
                                                'admin_notes' => $req->admin_notes ?? null,
                                            ]) }})">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </button>

                                        @if ($isAdmin && $canApproveRequest && $req->status === 'pending')
                                            <button type="button" class="req-action-btn approve" title="اعتماد وقبول الطلب"
                                                onclick="openApproveModal({{ $req->id }}, {{ Js::from($req->title) }}, {{ Js::from($req->employee?->full_name ?? '') }})">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </button>
                                        @endif

                                        @if ($isAdmin && $canRejectRequest && $req->status === 'pending')
                                            <button type="button" class="req-action-btn reject" title="رفض الطلب"
                                                onclick="openRejectModal({{ $req->id }}, {{ Js::from($req->title) }}, {{ Js::from($req->employee?->full_name ?? '') }})">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </button>
                                        @endif

                                        @if ($canCancelRequest && $req->employee_id === $employee?->id && $req->status === 'pending')
                                            <form action="{{ route('requests.cancel', $req) }}" method="POST"
                                                onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟');" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="req-action-btn reject" title="إلغاء الطلب">
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 8 : 6 }}" style="text-align: center; padding: 44px 20px; color: var(--ems-text-muted);">
                                    <div style="font-size: 14px; font-weight: 700; color: var(--ems-text-main); margin-bottom: 4px;">
                                        {{ $isAdmin ? 'لا توجد طلبات موظفين مسجلة حالياً.' : 'لا توجد لديك أي طلبات مسجلة حالياً.' }}
                                    </div>
                                    <div style="font-size: 12px;">
                                        {{ $isAdmin ? 'ستظهر هنا كافة طلبات الإجازات والسلف المقدمة من الكوادر فور إرسالها.' : 'يمكنك تقديم طلب جديد عبر الزر بالأعلى.' }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="noReqFilterResults" style="display: none; text-align: center; padding: 36px; color: var(--ems-text-muted); font-size: 13px;">
                لا توجد طلبات تطابق معايير التصفية المحددة.
            </div>
            @if ($requests->hasPages())
                <div style="padding: 16px 20px;">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

{{-- MODAL: View Details --}}
<div class="ems-modal-overlay" id="modal-view-request">
    <div class="ems-modal" style="max-width: 560px;">
        <div class="ems-modal-header">
            <div class="ems-modal-title" style="display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ems-gold-hover);">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <span>تفاصيل وبيانات الطلب</span>
            </div>
            <button type="button" class="ems-modal-close" onclick="closeModal('modal-view-request')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="ems-modal-body" style="padding: 24px;">
            <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 12px; padding: 14px 18px; margin-bottom: 18px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div>
                    <span id="dtl_req_id" style="font-family: var(--font-mono); font-weight: 800; color: var(--ems-gold-hover); font-size: 13px;"></span>
                    <h3 id="dtl_req_title" style="margin: 4px 0 0; font-size: 16px; font-weight: 800; color: var(--ems-text-main);"></h3>
                </div>
                <span id="dtl_status_badge" class="ems-badge" style="font-size: 12px; padding: 4px 12px;"></span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px;">
                <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 10px; padding: 12px;">
                    <div style="font-size: 11px; color: var(--ems-text-muted); margin-bottom: 4px; display:flex; align-items:center; gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>الموظف صاحب الطلب</span>
                    </div>
                    <strong id="dtl_emp_name" style="font-size: 13px; color: var(--ems-text-main);"></strong>
                    <div id="dtl_emp_dept" style="font-size: 11px; color: var(--ems-text-muted); margin-top: 2px;"></div>
                </div>

                <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 10px; padding: 12px;">
                    <div style="font-size: 11px; color: var(--ems-text-muted); margin-bottom: 4px; display:flex; align-items:center; gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>تاريخ تقديم الطلب</span>
                    </div>
                    <strong id="dtl_created_at" style="font-size: 13px; color: var(--ems-text-main); font-family: var(--font-mono);"></strong>
                </div>
            </div>

            <div id="dtl_specific_details" style="background: rgba(197, 160, 89, 0.08); border: 1px solid var(--ems-gold-light, #e2d2b4); border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; display: none;">
                <div style="font-size: 11.5px; font-weight: 700; color: var(--ems-gold-hover); margin-bottom: 4px;">بيانات إضافية:</div>
                <div id="dtl_specific_text" style="font-size: 14px; font-weight: 800; color: var(--ems-text-main);"></div>
            </div>

            <div style="border: 1px solid var(--ems-border); border-radius: 10px; padding: 14px 16px; margin-bottom: 16px;">
                <div style="font-size: 12px; font-weight: 700; color: var(--ems-text-main); margin-bottom: 6px;">شرح وتفاصيل الطلب:</div>
                <p id="dtl_reason" style="margin: 0; font-size: 13px; line-height: 1.8; color: var(--ems-text-main); white-space: pre-line;"></p>
            </div>

            <div id="dtl_review_block" style="border: 1px solid var(--ems-border); border-radius: 10px; padding: 14px 16px; background: var(--ems-surface-alt, #f8fafc); display: none;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <div style="font-size: 12px; font-weight: 700; color: var(--ems-text-main);">قرار الإدارة:</div>
                    <span id="dtl_reviewer_info" style="font-size: 11px; color: var(--ems-text-muted);"></span>
                </div>
                <p id="dtl_admin_notes" style="margin: 0; font-size: 13px; line-height: 1.7; color: var(--ems-text-main);"></p>
            </div>
        </div>
        <div class="ems-modal-footer">
            <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-view-request')">إغلاق</button>
        </div>
    </div>
</div>

@if ($canApproveRequest)
{{-- MODAL: Approve Request --}}
<div class="ems-modal-overlay" id="modal-approve-request">
    <div class="ems-modal" style="max-width: 480px;">
        <div class="ems-modal-header">
            <div class="ems-modal-title" style="display: flex; align-items: center; gap: 8px; color: #15803d;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>اعتماد وقبول طلب الموظف</span>
            </div>
            <button type="button" class="ems-modal-close" onclick="closeModal('modal-approve-request')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-approve-request" method="POST">
            @csrf
            <div class="ems-modal-body" style="padding: 22px;">
                <p style="margin: 0 0 14px; font-size: 14px; color: var(--ems-text-main);">
                    أنت بصدد <strong>الموافقة الرسمية</strong> على:
                    <br>
                    <span id="approve_req_title" style="color: var(--ems-gold-hover); font-weight: 700;"></span>
                    <br>
                    <span style="font-size: 12px; color: var(--ems-text-muted);" id="approve_emp_name"></span>
                </p>

                <div class="ems-form-group">
                    <label class="ems-label">ملاحظات الاعتماد للموظف (اختياري)</label>
                    <textarea name="admin_notes" class="ems-input" rows="3" placeholder="أدخل أي توجيهات أو ملاحظات رسمية للموظف..."></textarea>
                </div>
            </div>
            <div class="ems-modal-footer">
                <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-approve-request')">إلغاء</button>
                <button type="submit" class="ems-btn ems-btn-primary" style="background: #15803d; border-color: #15803d;">تأكيد الاعتماد والقبول</button>
            </div>
        </form>
    </div>
</div>

@endif

@if ($canRejectRequest)
{{-- MODAL: Reject Request --}}
<div class="ems-modal-overlay" id="modal-reject-request">
    <div class="ems-modal" style="max-width: 480px;">
        <div class="ems-modal-header">
            <div class="ems-modal-title" style="display: flex; align-items: center; gap: 8px; color: #dc2626;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                <span>رفض طلب الموظف</span>
            </div>
            <button type="button" class="ems-modal-close" onclick="closeModal('modal-reject-request')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form id="form-reject-request" method="POST">
            @csrf
            <div class="ems-modal-body" style="padding: 22px;">
                <p style="margin: 0 0 14px; font-size: 14px; color: var(--ems-text-main);">
                    أنت بصدد <strong>رفض الطلب</strong> التالي:
                    <br>
                    <span id="reject_req_title" style="color: #dc2626; font-weight: 700;"></span>
                    <br>
                    <span style="font-size: 12px; color: var(--ems-text-muted);" id="reject_emp_name"></span>
                </p>

                <div class="ems-form-group">
                    <label class="ems-label">سبب الرفض والملاحظات الإدارية</label>
                    <textarea name="admin_notes" class="ems-input" rows="3" placeholder="وضح سبب رفض الطلب للموظف..." required></textarea>
                </div>
            </div>
            <div class="ems-modal-footer">
                <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-reject-request')">إلغاء</button>
                <button type="submit" class="ems-btn ems-btn-primary" style="background: #dc2626; border-color: #dc2626;">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>
@endif

@if ($employee && $canCreateRequest)
{{-- MODAL: Add Request --}}
<div class="ems-modal-overlay" id="modal-add-request">
    <div class="ems-modal" style="max-width: 580px;">
        <div class="ems-modal-header">
            <div class="ems-modal-title" style="display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ems-gold-hover);">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>تقديم طلب إداري أو مالي</span>
            </div>
            <button type="button" class="ems-modal-close" onclick="closeModal('modal-add-request')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form action="{{ route('requests.store') }}" method="POST">
            @csrf
            <div class="ems-modal-body" style="padding: 24px;">
                <div class="ems-form-group">
                    <label class="ems-label"><span class="required">*</span> نوع الطلب</label>
                    <select name="type" id="request_type_select" class="ems-input" required onchange="handleRequestTypeChange(this.value)">
                        <option value="leave">طلب إجازة (سنوية / مرضية / عارضة)</option>
                        <option value="advance">طلب سلفة مالية من الراتب</option>
                        <option value="certificate">طلب شهادة تعريف بالراتب أو الخدمة</option>
                        <option value="other">طلب عهدة / أجهزة / طلب إداري عام</option>
                    </select>
                </div>

                <div class="ems-form-group">
                    <label class="ems-label"><span class="required">*</span> عنوان أو موضوع الطلب</label>
                    <input type="text" name="title" id="request_title_input" class="ems-input" required
                        placeholder="مثال: إجازة اعتيادية 3 أيام، سلفة راتب شهر سبتمبر...">
                </div>

                <div id="leave_fields_group" style="display: block;">
                    <div class="ems-form-row">
                        <div class="ems-form-group">
                            <label class="ems-label"><span class="required">*</span> تاريخ بداية الإجازة</label>
                            <input type="date" name="start_date" id="leave_start_input" class="ems-input" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="ems-form-group">
                            <label class="ems-label"><span class="required">*</span> تاريخ نهاية الإجازة</label>
                            <input type="date" name="end_date" id="leave_end_input" class="ems-input" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div id="advance_fields_group" style="display: none;">
                    <div class="ems-form-group">
                        <label class="ems-label"><span class="required">*</span> المبلغ المالي المطلوب (بالشيكل ₪)</label>
                        <input type="number" name="amount" id="advance_amount_input" class="ems-input" min="1" step="50" placeholder="مثال: 1500">
                    </div>
                </div>

                <div class="ems-form-group">
                    <label class="ems-label">التفاصيل والأسباب (اختياري)</label>
                    <textarea name="reason" class="ems-input" rows="3" placeholder="اكتب أي تفاصيل إضافية لتسهيل مراجعة الإدارة للطلب..."></textarea>
                </div>
            </div>

            <div class="ems-modal-footer">
                <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-add-request')">إلغاء</button>
                <button type="submit" class="ems-btn ems-btn-primary">إرسال الطلب للإدارة</button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
function handleRequestTypeChange(val) {
    const leaveGroup = document.getElementById('leave_fields_group');
    const advanceGroup = document.getElementById('advance_fields_group');
    if (leaveGroup) leaveGroup.style.display = (val === 'leave') ? 'block' : 'none';
    if (advanceGroup) advanceGroup.style.display = (val === 'advance') ? 'block' : 'none';
}

function showRequestDetails(data) {
    document.getElementById('dtl_req_id').textContent = data.id || '';
    document.getElementById('dtl_req_title').textContent = data.title || '';
    const sb = document.getElementById('dtl_status_badge');
    sb.textContent = data.status_label || '';
    sb.className = 'ems-badge ' + (data.status_class || '');

    document.getElementById('dtl_emp_name').textContent = data.employee_name || '';
    document.getElementById('dtl_emp_dept').textContent = (data.department || '') + ' • ' + (data.position || '');
    document.getElementById('dtl_created_at').textContent = data.created_at || '';
    document.getElementById('dtl_reason').textContent = data.reason || 'لا توجد تفاصيل إضافية.';

    const specBlock = document.getElementById('dtl_specific_details');
    const specText = document.getElementById('dtl_specific_text');
    if (data.amount) {
        specBlock.style.display = 'block';
        specText.textContent = 'مبلغ السلفة: ' + data.amount;
    } else if (data.days_count || data.start_date) {
        specBlock.style.display = 'block';
        specText.textContent = 'مدة الإجازة: ' + (data.days_count ? data.days_count + ' يوم' : '') + ' (من ' + (data.start_date || '') + ' إلى ' + (data.end_date || '') + ')';
    } else {
        specBlock.style.display = 'none';
    }

    const revBlock = document.getElementById('dtl_review_block');
    if (data.reviewer || data.admin_notes) {
        revBlock.style.display = 'block';
        document.getElementById('dtl_reviewer_info').textContent = (data.reviewer ? 'المراجع: ' + data.reviewer : '') + (data.reviewed_at ? ' • ' + data.reviewed_at : '');
        document.getElementById('dtl_admin_notes').textContent = data.admin_notes || 'تم اتخاذ القرار دون ملاحظات مسجلة.';
    } else {
        revBlock.style.display = 'none';
    }

    openModal('modal-view-request');
}

function openApproveModal(id, title, empName) {
    const form = document.getElementById('form-approve-request');
    form.action = '/requests/' + id + '/approve';
    document.getElementById('approve_req_title').textContent = title;
    document.getElementById('approve_emp_name').textContent = empName ? 'الموظف: ' + empName : '';
    openModal('modal-approve-request');
}

function openRejectModal(id, title, empName) {
    const form = document.getElementById('form-reject-request');
    form.action = '/requests/' + id + '/reject';
    document.getElementById('reject_req_title').textContent = title;
    document.getElementById('reject_emp_name').textContent = empName ? 'الموظف: ' + empName : '';
    openModal('modal-reject-request');
}

function filterRequestsTable() {
    const search = (document.getElementById('reqSearchInput').value || '').toLowerCase().trim();
    const status = (document.getElementById('reqFilterStatus').value || '').trim();
    const type = (document.getElementById('reqFilterType').value || '').trim();
    const deptSelect = document.getElementById('reqFilterDept');
    const dept = deptSelect ? deptSelect.value.trim() : '';

    const rows = document.querySelectorAll('#requestsTableBody tr[data-status]');
    let count = 0;

    rows.forEach(tr => {
        const trStatus = tr.dataset.status || '';
        const trType = tr.dataset.type || '';
        const trDept = tr.dataset.dept || '';
        const trSearch = tr.dataset.search || '';

        const ok = (!search || trSearch.includes(search))
                && (!status || trStatus === status)
                && (!type || trType === type)
                && (!dept || trDept === dept);

        tr.style.display = ok ? '' : 'none';
        if (ok) count++;
    });

    const noRes = document.getElementById('noReqFilterResults');
    if (noRes) noRes.style.display = (count === 0 && rows.length > 0) ? 'block' : 'none';
}

function resetRequestFilters() {
    document.getElementById('reqSearchInput').value = '';
    document.getElementById('reqFilterStatus').value = '';
    document.getElementById('reqFilterType').value = '';
    const deptSelect = document.getElementById('reqFilterDept');
    if (deptSelect) deptSelect.value = '';
    filterRequestsTable();
}
</script>
@endpush
@endsection
