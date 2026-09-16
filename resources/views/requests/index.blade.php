@extends('layouts.portal')

@section('title', 'طلباتي والخدمات الذاتية')

@section('content')

    <div class="ems-content">

        {{-- Page Header --}}
        <div class="ems-page-header">
            <div class="ems-page-title-wrap">
                <div class="ems-breadcrumb">
                    <a href="{{ route('dashboard') }}">الرئيسية</a>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span>المتابعة والموظف</span>
                    <span class="ems-breadcrumb-separator">/</span>
                    <span style="color: var(--ems-text-main); font-weight: 700;">طلباتي والخدمات الذاتية</span>
                </div>

                <h1 class="ems-page-title">
                    طلباتي والخدمات الذاتية للموظف
                </h1>

                <p class="ems-page-subtitle">
                    تقديم ومتابعة طلبات الإجازات الاعتيادية والمرضية، السلف المالية، شهادات التعريف بالراتب، ومتابعة ردود الإدارة المباشرة
                </p>
            </div>

            <div class="ems-page-actions">
                <button type="button" class="ems-btn ems-btn-primary" onclick="openModal('modal-add-request')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>تقديم طلب جديد</span>
                </button>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="ems-badge badge-completed" style="display:block; padding:12px 16px; margin-bottom:18px; font-size:13px;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="ems-badge badge-danger" style="display:block; padding:12px 16px; margin-bottom:18px; font-size:13px; background:#fee2e2; color:#b91c1c;">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="ems-badge badge-danger" style="display:block; padding:12px 16px; margin-bottom:18px; font-size:13px; background:#fee2e2; color:#b91c1c;">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Statistics Grid --}}
        <div class="ems-stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">إجمالي طلباتي</span>
                    <span class="ems-stat-value">{{ $stats['total'] }}</span>
                    <span class="ems-stat-subtext">كافة الطلبات المسجلة</span>
                </div>
                <div class="ems-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">قيد المراجعة والانتظار</span>
                    <span class="ems-stat-value" style="color: #d97706;">{{ $stats['pending'] }}</span>
                    <span class="ems-stat-subtext">بانتظار البت من الإدارة</span>
                </div>
                <div class="ems-stat-icon" style="background: #fef3c7; border-color: #fde68a; color: #d97706;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">طلبات معتمدة ومقبولة</span>
                    <span class="ems-stat-value" style="color: #10b981;">{{ $stats['approved'] }}</span>
                    <span class="ems-stat-subtext">تمت الموافقة الرسمية</span>
                </div>
                <div class="ems-stat-icon" style="background: #d1fae5; border-color: #a7f3d0; color: #10b981;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
            </div>

            <div class="ems-stat-card">
                <div class="ems-stat-info">
                    <span class="ems-stat-label">طلبات مرفوضة</span>
                    <span class="ems-stat-value" style="color: #ef4444;">{{ $stats['rejected'] }}</span>
                    <span class="ems-stat-subtext">مع ملاحظات الإدارة</span>
                </div>
                <div class="ems-stat-icon" style="background: #fee2e2; border-color: #fecaca; color: #ef4444;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
            </div>

        </div>

        {{-- Requests Table Card --}}
        <div class="ems-card">
            <div class="ems-card-header">
                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>سجل الطلبات الإدارية والمالية</span>
                </div>
                <span class="ems-badge badge-gold">{{ $requests->count() }} طلب</span>
            </div>

            <div class="ems-card-body" style="padding: 0;">
                <div class="ems-table-responsive">
                    <table class="ems-table">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>نوع الطلب</th>
                                <th>موضوع الطلب</th>
                                <th>البيانات والتفاصيل</th>
                                <th>تاريخ التقديم</th>
                                <th>الحالة</th>
                                <th style="text-align: center;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $typeLabels = [
                                    'leave' => ['label' => 'طلب إجازة', 'class' => 'badge-gold'],
                                    'advance' => ['label' => 'طلب سلفة مالية', 'class' => 'badge-review'],
                                    'certificate' => ['label' => 'تعريف راتب / شهادة', 'class' => 'badge-progress'],
                                    'other' => ['label' => 'طلب عام / أخرى', 'class' => 'badge-draft'],
                                ];

                                $statusLabels = [
                                    'pending' => ['label' => 'قيد المراجعة', 'class' => 'badge-gold'],
                                    'approved' => ['label' => 'معتمد ومقبول', 'class' => 'badge-completed'],
                                    'rejected' => ['label' => 'مرفوض', 'class' => 'badge-danger'],
                                    'cancelled' => ['label' => 'ملغي', 'class' => 'badge-draft'],
                                ];
                            @endphp

                            @forelse ($requests as $req)
                                @php
                                    $tInfo = $typeLabels[$req->type] ?? ['label' => $req->type, 'class' => ''];
                                    $sInfo = $statusLabels[$req->status] ?? ['label' => $req->status, 'class' => ''];
                                @endphp
                                <tr>
                                    {{-- Code --}}
                                    <td>
                                        <span style="font-family: var(--font-mono); font-weight: 700; color: var(--ems-gold-hover);">
                                            REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>

                                    {{-- Type --}}
                                    <td>
                                        <span class="ems-badge {{ $tInfo['class'] }}" style="font-size: 11.5px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 5px;">
                                            @if ($req->type === 'leave')
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                            @elseif ($req->type === 'advance')
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M12 6v12M9 9h6a1.5 1.5 0 0 1 0 3H9a1.5 1.5 0 0 0 0 3h6"></path></svg>
                                            @elseif ($req->type === 'certificate')
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                            @else
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            @endif
                                            <span>{{ $tInfo['label'] }}</span>
                                        </span>
                                    </td>

                                    {{-- Title & Reason --}}
                                    <td>
                                        <div style="font-weight: 700; color: var(--ems-text-main);">
                                            {{ $req->title }}
                                        </div>
                                        @if ($req->reason)
                                            <div style="font-size: 11.5px; color: var(--ems-text-muted); margin-top: 3px;">
                                                {{ Str::limit($req->reason, 65) }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Details based on type --}}
                                    <td>
                                        @if ($req->type === 'leave' && $req->start_date)
                                            <div style="font-size: 12px; font-weight: 600; color: var(--ems-text-main);">
                                                {{ $req->days_count ? $req->days_count . ' يوم/أيام' : 'إجازة' }}
                                            </div>
                                            <div style="font-size: 11px; color: var(--ems-text-muted);">
                                                من {{ $req->start_date->format('d/m/Y') }} إلى {{ $req->end_date ? $req->end_date->format('d/m/Y') : '-' }}
                                            </div>
                                        @elseif ($req->type === 'advance' && $req->amount)
                                            <div style="font-size: 13px; font-weight: 800; font-family: var(--font-mono); color: #059669;">
                                                {{ number_format($req->amount, 2) }} ₪
                                            </div>
                                            <div style="font-size: 11px; color: var(--ems-text-muted);">
                                                سلفة مالية
                                            </div>
                                        @else
                                            <span style="font-size: 12px; color: var(--ems-text-muted);">طلب إداري</span>
                                        @endif
                                    </td>

                                    {{-- Date --}}
                                    <td>
                                        <div style="font-size: 12px; font-weight: 600; color: var(--ems-text-main);">
                                            {{ $req->created_at?->translatedFormat('d F Y') }}
                                        </div>
                                        <div style="font-size: 11px; color: var(--ems-text-muted); font-family: var(--font-mono);">
                                            {{ $req->created_at?->format('H:i') }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        <span class="ems-badge {{ $sInfo['class'] }}">
                                            {{ $sInfo['label'] }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td style="text-align: center;">
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">

                                            {{-- Eye View Details --}}
                                            <button type="button" class="ems-btn ems-btn-icon" title="عرض تفاصيل الطلب"
                                                onclick="showRequestDetails({{ Js::from([
                                                    'id' => 'REQ-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
                                                    'type_label' => $tInfo['label'],
                                                    'type_class' => $tInfo['class'],
                                                    'type_icon' => $tInfo['icon'],
                                                    'title' => $req->title,
                                                    'reason' => $req->reason ?? 'لا توجد تفاصيل إضافية مكتوبة.',
                                                    'start_date' => $req->start_date ? $req->start_date->format('Y-m-d') : null,
                                                    'end_date' => $req->end_date ? $req->end_date->format('Y-m-d') : null,
                                                    'days_count' => $req->days_count,
                                                    'amount' => $req->amount ? number_format($req->amount, 2) . ' ₪' : null,
                                                    'created_at' => $req->created_at ? $req->created_at->format('Y-m-d H:i') : '-',
                                                    'status' => $req->status,
                                                    'status_label' => $sInfo['label'],
                                                    'status_class' => $sInfo['class'],
                                                    'reviewer' => $req->reviewer?->username ?? 'الإدارة',
                                                    'reviewed_at' => $req->reviewed_at ? $req->reviewed_at->format('Y-m-d H:i') : null,
                                                    'admin_notes' => $req->admin_notes ?? 'لا توجد ملاحظات مسجلة بعد.',
                                                ]) }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>

                                            {{-- Cancel Action if Pending --}}
                                            @if ($req->status === 'pending')
                                                <form action="{{ route('requests.cancel', $req) }}" method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء هذا الطلب؟');"
                                                    style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="ems-btn ems-btn-icon" title="إلغاء الطلب" style="color: #ef4444;">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                    <td colspan="7" style="text-align: center; padding: 36px; color: var(--ems-text-muted);">
                                        <div style="font-size: 28px; margin-bottom: 8px;"></div>
                                        <strong>لا توجد لديك أي طلبات مسجلة حالياً.</strong>
                                        <div style="font-size: 12px; margin-top: 4px;">يمكنك الضغط على زر "تقديم طلب جديد" بالأعلى لتقديم طلب إجازة أو سلفة.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Add Request Modal --}}
    <div class="ems-modal-overlay" id="modal-add-request">
        <div class="ems-modal" style="max-width: 580px;">
            <div class="ems-modal-header">
                <div class="ems-modal-title" style="display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ems-gold-hover);">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>تقديم طلب إداري أو مالي جديد</span>
                </div>
                <button type="button" class="ems-modal-close" onclick="closeModal('modal-add-request')">✕</button>
            </div>

            <form action="{{ route('requests.store') }}" method="POST">
                @csrf
                <div class="ems-modal-body" style="padding: 24px;">

                    {{-- Request Type Selector --}}
                    <div class="ems-form-group">
                        <label class="ems-label"><span class="required">*</span> نوع الطلب</label>
                        <select name="type" id="request_type_select" class="ems-input" required onchange="handleRequestTypeChange(this.value)">
                            <option value="leave"> طلب إجازة (سنوية / مرضية / طارئة)</option>
                            <option value="advance"> طلب سلفة مالية من الراتب</option>
                            <option value="certificate"> طلب شهادة تعريف بالراتب أو الخدمة</option>
                            <option value="other"> طلب عام / أخرى</option>
                        </select>
                    </div>

                    {{-- Title --}}
                    <div class="ems-form-group">
                        <label class="ems-label"><span class="required">*</span> عنوان أو موضوع الطلب</label>
                        <input type="text" name="title" id="request_title_input" class="ems-input" required
                            placeholder="مثال: إجازة اعتيادية 3 أيام، سلفة راتب شهر سبتمبر...">
                    </div>

                    {{-- Leave Dates (Shown only for leave) --}}
                    <div id="leave_fields_group" style="display: block;">
                        <div class="ems-form-row">
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> تاريخ بداية الإجازة</label>
                                <input type="date" name="start_date" id="leave_start_date" class="ems-input"
                                    value="{{ date('Y-m-d') }}" onchange="calculateLeaveDays()">
                            </div>
                            <div class="ems-form-group">
                                <label class="ems-label"><span class="required">*</span> تاريخ نهاية الإجازة</label>
                                <input type="date" name="end_date" id="leave_end_date" class="ems-input"
                                    value="{{ date('Y-m-d') }}" onchange="calculateLeaveDays()">
                            </div>
                        </div>

                        <div id="leave_days_summary" style="background: rgba(197, 160, 89, 0.1); border: 1px dashed var(--ems-gold); border-radius: 8px; padding: 8px 12px; margin-bottom: 16px; font-size: 12px; color: var(--ems-gold-hover); font-weight: 700; display: flex; align-items: center; justify-content: space-between;">
                            <span>إجمالي مدة الإجازة المحسوبة:</span>
                            <span id="leave_days_calc_text">يوم واحد (1)</span>
                        </div>
                    </div>

                    {{-- Advance Amount (Shown only for advance) --}}
                    <div id="advance_fields_group" style="display: none;">
                        <div class="ems-form-group">
                            <label class="ems-label"><span class="required">*</span> المبلغ المالي المطلوب (بالشيكل ₪)</label>
                            <input type="number" name="amount" id="advance_amount_input" class="ems-input"
                                min="1" step="50" placeholder="مثال: 1000">
                            <span style="font-size: 11px; color: var(--ems-text-muted); margin-top: 4px; display: block;">
                                سيتم خصم السلفة وفقاً للائحة المالية المعتمدة في مجموعة إياس.
                            </span>
                        </div>
                    </div>

                    {{-- Reason & Details --}}
                    <div class="ems-form-group">
                        <label class="ems-label">مبررات وتفاصيل الطلب</label>
                        <textarea name="reason" class="ems-input" rows="3"
                            placeholder="اكتب أية مبررات أو توضيحات ترغب في إطلاع الإدارة عليها..."></textarea>
                    </div>

                </div>

                <div class="ems-modal-footer">
                    <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-add-request')">إلغاء</button>
                    <button type="submit" class="ems-btn ems-btn-primary">إرسال الطلب للاعتماد</button>
                </div>
            </form>
        </div>
    </div>

    {{-- View Request Modal --}}
    <div class="ems-modal-overlay" id="modal-view-request">
        <div class="ems-modal" style="max-width: 540px;">
            <div class="ems-modal-header">
                <div class="ems-modal-title" style="display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--ems-gold-hover);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>تفاصيل الطلب ورد الإدارة</span>
                </div>
                <button type="button" class="ems-modal-close" onclick="closeModal('modal-view-request')">✕</button>
            </div>

            <div class="ems-modal-body" style="padding: 24px;">

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span id="view_req_id" style="font-family: var(--font-mono); font-weight: 800; color: var(--ems-gold-hover); font-size: 13px;"></span>
                        <h3 id="view_req_title" style="margin: 4px 0 0 0; font-size: 16px; font-weight: 800; color: var(--ems-text-main);"></h3>
                    </div>
                    <span id="view_req_status_badge" class="ems-badge"></span>
                </div>

                <div style="background: var(--ems-surface-alt, #f8fafc); border: 1px solid var(--ems-border); border-radius: 10px; padding: 14px; margin-bottom: 16px; font-size: 12.5px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <span style="color: var(--ems-text-muted); display: block; font-size: 11px;">نوع الطلب:</span>
                            <strong id="view_req_type" style="color: var(--ems-text-main);"></strong>
                        </div>
                        <div>
                            <span style="color: var(--ems-text-muted); display: block; font-size: 11px;">تاريخ التقديم:</span>
                            <strong id="view_req_created_at" style="color: var(--ems-text-main);"></strong>
                        </div>
                        <div id="view_req_dates_wrap">
                            <span style="color: var(--ems-text-muted); display: block; font-size: 11px;">الفترة / المدة:</span>
                            <strong id="view_req_dates" style="color: var(--ems-text-main);"></strong>
                        </div>
                        <div id="view_req_amount_wrap">
                            <span style="color: var(--ems-text-muted); display: block; font-size: 11px;">المبلغ المطلوب:</span>
                            <strong id="view_req_amount" style="color: #059669; font-family: var(--font-mono);"></strong>
                        </div>
                    </div>
                </div>

                {{-- Employee Reason --}}
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 12px; font-weight: 700; color: var(--ems-text-main); margin-bottom: 6px;">مبررات وتفاصيل الطلب:</div>
                    <div id="view_req_reason" style="background: var(--ems-surface); border: 1px solid var(--ems-border); border-radius: 8px; padding: 10px 12px; font-size: 12.5px; line-height: 1.6; color: var(--ems-text-main);"></div>
                </div>

                {{-- Admin Response --}}
                <div style="background: linear-gradient(135deg, rgba(197, 160, 89, 0.08), rgba(197, 160, 89, 0.02)); border: 1px solid var(--ems-gold-light); border-radius: 10px; padding: 12px 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="font-size: 12px; font-weight: 800; color: var(--ems-gold-hover);">رد وملاحظات الإدارة:</span>
                        <span id="view_req_reviewed_at" style="font-size: 11px; color: var(--ems-text-muted);"></span>
                    </div>
                    <p id="view_req_admin_notes" style="margin: 0; font-size: 12.5px; line-height: 1.6; color: var(--ems-text-main);"></p>
                </div>

            </div>

            <div class="ems-modal-footer">
                <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-view-request')">إغلاق</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function handleRequestTypeChange(type) {
                const leaveGroup = document.getElementById('leave_fields_group');
                const advanceGroup = document.getElementById('advance_fields_group');
                const titleInput = document.getElementById('request_title_input');

                if (type === 'leave') {
                    leaveGroup.style.display = 'block';
                    advanceGroup.style.display = 'none';
                    if (!titleInput.value || titleInput.value.includes('سلفة') || titleInput.value.includes('شهادة')) {
                        titleInput.value = 'طلب إجازة اعتيادية';
                    }
                } else if (type === 'advance') {
                    leaveGroup.style.display = 'none';
                    advanceGroup.style.display = 'block';
                    if (!titleInput.value || titleInput.value.includes('إجازة') || titleInput.value.includes('شهادة')) {
                        titleInput.value = 'طلب سلفة راتب';
                    }
                } else if (type === 'certificate') {
                    leaveGroup.style.display = 'none';
                    advanceGroup.style.display = 'none';
                    if (!titleInput.value || titleInput.value.includes('إجازة') || titleInput.value.includes('سلفة')) {
                        titleInput.value = 'طلب شهادة تعريف بالراتب والخدمة';
                    }
                } else {
                    leaveGroup.style.display = 'none';
                    advanceGroup.style.display = 'none';
                }
            }

            function calculateLeaveDays() {
                const startVal = document.getElementById('leave_start_date').value;
                const endVal = document.getElementById('leave_end_date').value;
                const calcText = document.getElementById('leave_days_calc_text');

                if (!startVal || !endVal) return;

                const start = new Date(startVal);
                const end = new Date(endVal);

                if (end < start) {
                    calcText.textContent = 'تاريخ النهاية يجب أن يكون بعد تاريخ البدء!';
                    calcText.style.color = '#ef4444';
                    return;
                }

                calcText.style.color = 'var(--ems-gold-hover)';
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                calcText.textContent = diffDays + ' يوم/أيام';
            }

            function showRequestDetails(data) {
                document.getElementById('view_req_id').textContent = data.id;
                document.getElementById('view_req_title').textContent = data.title;
                document.getElementById('view_req_type').textContent = (data.type_icon || '') + ' ' + (data.type_label || '');
                document.getElementById('view_req_created_at').textContent = data.created_at || '-';
                document.getElementById('view_req_reason').textContent = data.reason || 'لا توجد مبررات مكتوبة.';
                document.getElementById('view_req_admin_notes').textContent = data.admin_notes || 'لا توجد ملاحظات مسجلة بعد.';
                document.getElementById('view_req_reviewed_at').textContent = data.reviewed_at ? 'تاريخ البت: ' + data.reviewed_at : 'قيد المراجعة';

                const badge = document.getElementById('view_req_status_badge');
                badge.className = 'ems-badge ' + (data.status_class || '');
                badge.textContent = data.status_label || data.status;

                // Dates
                const datesWrap = document.getElementById('view_req_dates_wrap');
                if (data.start_date) {
                    datesWrap.style.display = 'block';
                    document.getElementById('view_req_dates').textContent = (data.days_count ? data.days_count + ' يوم (' : '') + data.start_date + ' إلى ' + (data.end_date || '') + (data.days_count ? ')' : '');
                } else {
                    datesWrap.style.display = 'none';
                }

                // Amount
                const amountWrap = document.getElementById('view_req_amount_wrap');
                if (data.amount) {
                    amountWrap.style.display = 'block';
                    document.getElementById('view_req_amount').textContent = data.amount;
                } else {
                    amountWrap.style.display = 'none';
                }

                openModal('modal-view-request');
            }
        </script>
    @endpush

@endsection
