@extends('layouts.portal')
@section('title', 'الأداء والإنجاز')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
.perf-kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
@media(max-width:1100px){.perf-kpi-grid{grid-template-columns:repeat(2,1fr);}}
@media(max-width:600px){.perf-kpi-grid{grid-template-columns:1fr;}}
.perf-kpi-card{background:var(--ems-surface);border:1px solid var(--ems-border);border-radius:16px;padding:20px 22px;display:flex;align-items:flex-start;gap:16px;position:relative;overflow:hidden;transition:transform .22s ease,box-shadow .22s ease;}
.perf-kpi-card::before{content:'';position:absolute;top:0;right:0;width:4px;height:100%;border-radius:0 16px 16px 0;}
.perf-kpi-card:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(0,0,0,.12);}
.perf-kpi-card.gold::before{background:linear-gradient(180deg,#c5a059,#e2c97e);}
.perf-kpi-card.green::before{background:linear-gradient(180deg,#15803d,#22c55e);}
.perf-kpi-card.blue::before{background:linear-gradient(180deg,#0284c7,#38bdf8);}
.perf-kpi-card.purple::before{background:linear-gradient(180deg,#7c3aed,#a78bfa);}
.perf-kpi-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.perf-kpi-icon.gold{background:rgba(197,160,89,.13);color:#c5a059;}
.perf-kpi-icon.green{background:rgba(21,128,61,.13);color:#15803d;}
.perf-kpi-icon.blue{background:rgba(2,132,199,.13);color:#0284c7;}
.perf-kpi-icon.purple{background:rgba(124,58,237,.13);color:#7c3aed;}
.perf-kpi-icon svg{width:22px;height:22px;}
.perf-kpi-label{font-size:11.5px;font-weight:600;color:var(--ems-text-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.05em;}
.perf-kpi-value{font-size:28px;font-weight:900;font-family:var(--font-mono,monospace);color:var(--ems-text-main);line-height:1;margin-bottom:4px;}
.perf-kpi-sub{font-size:11px;color:var(--ems-text-muted);}
.perf-charts-row{display:grid;grid-template-columns:1fr 340px;gap:20px;margin-bottom:24px;}
@media(max-width:1050px){.perf-charts-row{grid-template-columns:1fr;}}
.perf-leaderboard{background:var(--ems-surface);border:1px solid var(--ems-border);border-radius:16px;overflow:hidden;}
.perf-leaderboard-header{padding:16px 20px;border-bottom:1px solid var(--ems-border);display:flex;align-items:center;gap:8px;}
.perf-lb-item{display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid var(--ems-border);transition:background .15s;}
.perf-lb-item:last-child{border-bottom:none;}
.perf-lb-item:hover{background:var(--ems-surface-alt,#f8fafc);}
.perf-lb-rank{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;flex-shrink:0;}
.perf-lb-rank.rank-1{background:linear-gradient(135deg,#f59e0b,#fde68a);color:#78350f;}
.perf-lb-rank.rank-2{background:linear-gradient(135deg,#94a3b8,#e2e8f0);color:#334155;}
.perf-lb-rank.rank-3{background:linear-gradient(135deg,#cd7f32,#e4aa70);color:#431407;}
.perf-lb-rank.rank-other{background:var(--ems-surface-alt,#f1f5f9);color:var(--ems-text-muted);font-size:12px;}
.perf-lb-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;}
.perf-lb-info{flex:1;min-width:0;}
.perf-lb-name{font-size:13px;font-weight:700;color:var(--ems-text-main);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.perf-lb-dept{font-size:11px;color:var(--ems-text-muted);}
.perf-lb-score-wrap{text-align:center;}
.perf-lb-score{font-size:17px;font-weight:900;font-family:var(--font-mono,monospace);color:var(--ems-gold,#c5a059);}
.perf-lb-grade{font-size:10px;color:var(--ems-text-muted);}
.perf-filter-bar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;padding:14px 18px;background:var(--ems-surface-alt,#f8fafc);border:1px solid var(--ems-border);border-radius:12px;margin-bottom:16px;}
.perf-filter-bar select,.perf-filter-bar input{border:1px solid var(--ems-border);border-radius:8px;padding:7px 12px;font-size:13px;background:var(--ems-surface);color:var(--ems-text-main);outline:none;transition:border-color .2s;}
.perf-filter-bar select:focus,.perf-filter-bar input:focus{border-color:var(--ems-gold,#c5a059);}
.perf-filter-label{font-size:12px;font-weight:600;color:var(--ems-text-muted);}
.score-bar-wrap{display:flex;align-items:center;gap:8px;}
.score-bar-track{flex:1;max-width:80px;height:7px;background:var(--ems-border);border-radius:999px;overflow:hidden;}
.score-bar-fill{height:100%;border-radius:999px;transition:width .5s ease;}
.score-bar-fill.excellent{background:linear-gradient(90deg,#15803d,#4ade80);}
.score-bar-fill.very-good{background:linear-gradient(90deg,#0284c7,#38bdf8);}
.score-bar-fill.good{background:linear-gradient(90deg,#c5a059,#e2c97e);}
.score-bar-fill.poor{background:linear-gradient(90deg,#dc2626,#f87171);}
.grade-badge{display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap;}
.grade-excellent{background:rgba(21,128,61,.12);color:#15803d;}
.grade-very-good{background:rgba(2,132,199,.12);color:#0284c7;}
.grade-good{background:rgba(197,160,89,.15);color:#92702a;}
.grade-poor{background:rgba(220,38,38,.10);color:#dc2626;}
.grade-none{background:var(--ems-border);color:var(--ems-text-muted);}
.perf-export-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;border:1px solid var(--ems-border);background:var(--ems-surface);color:var(--ems-text-main);transition:all .2s;}
.perf-export-btn:hover{background:var(--ems-gold,#c5a059);color:#fff;border-color:transparent;}
.perf-export-btn svg{width:15px;height:15px;}
.ontime-ring-wrap{display:flex;align-items:center;gap:6px;}
.ontime-ring{width:34px;height:34px;position:relative;}
.ontime-ring svg{width:34px;height:34px;transform:rotate(-90deg);}
.ontime-ring .ring-track{fill:none;stroke:var(--ems-border);stroke-width:3;}
.ontime-ring .ring-fill{fill:none;stroke-width:3;stroke-linecap:round;transition:stroke-dashoffset .6s ease;}
.ontime-ring-val{font-size:12px;font-weight:800;font-family:var(--font-mono,monospace);}
.perf-chart-card{background:var(--ems-surface);border:1px solid var(--ems-border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;}
.perf-chart-card .chart-header{padding:16px 20px;border-bottom:1px solid var(--ems-border);display:flex;align-items:center;justify-content:space-between;}
.perf-chart-card .chart-body{padding:20px;flex:1;display:flex;align-items:center;min-height:260px;}
.perf-overall-banner{background:linear-gradient(135deg,#1e293b 0%,#0f172a 50%,#1e293b 100%);border:1px solid rgba(197,160,89,.3);border-radius:18px;padding:24px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;position:relative;overflow:hidden;}
.perf-overall-banner::after{content:'KPI';position:absolute;left:-10px;top:50%;transform:translateY(-50%);font-size:120px;font-weight:900;color:rgba(197,160,89,.04);pointer-events:none;font-family:var(--font-mono,monospace);}
.perf-overall-tag{font-size:11px;font-weight:700;color:var(--ems-gold,#c5a059);letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px;}
.perf-overall-title{font-size:22px;font-weight:800;color:#fff;margin-bottom:4px;}
.perf-overall-sub{font-size:13px;color:#94a3b8;max-width:480px;}
.perf-overall-score-wrap{display:flex;align-items:center;gap:20px;}
.perf-overall-num{font-size:58px;font-weight:900;font-family:var(--font-mono,monospace);color:var(--ems-gold,#c5a059);line-height:1;}
.perf-overall-ring{position:relative;width:80px;height:80px;}
.perf-overall-ring svg{width:80px;height:80px;transform:rotate(-90deg);}
.perf-overall-ring .ring-track{fill:none;stroke:rgba(255,255,255,.08);stroke-width:5;}
.perf-overall-ring .ring-fill{fill:none;stroke:var(--ems-gold,#c5a059);stroke-width:5;stroke-linecap:round;transition:stroke-dashoffset 1.2s ease;}
.perf-overall-ring-label{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:13px;font-weight:800;color:#fff;font-family:var(--font-mono,monospace);}
</style>
@endpush

@section('content')
@php
    $avatarColors = ['#c5a059','#1e293b','#334155','#475569','#0284c7','#0369a1','#3b82f6','#d97706','#7c3aed','#0f766e'];
    $totalEvaluated = $rows->filter(fn($r) => $r->evaluation !== null)->count();
    $avgOnTimeArr   = $rows->filter(fn($r) => $r->on_time_rate !== null)->pluck('on_time_rate');
    $avgOnTime      = $avgOnTimeArr->count() > 0 ? round($avgOnTimeArr->avg(), 1) : null;
    $totalCompleted = $rows->sum('completed_tasks');
    $topPerformers  = $rows->filter(fn($r) => $r->evaluation !== null)->sortByDesc(fn($r) => $r->evaluation->score)->values();
    $getGrade = function($score) {
        if ($score === null) return ['label' => 'غير مقيّم', 'class' => 'grade-none'];
        if ($score >= 90)   return ['label' => 'ممتاز (A+)',     'class' => 'grade-excellent'];
        if ($score >= 80)   return ['label' => 'ممتاز (A)',      'class' => 'grade-excellent'];
        if ($score >= 65)   return ['label' => 'جيد جداً (B)',   'class' => 'grade-very-good'];
        if ($score >= 50)   return ['label' => 'جيد (C)',        'class' => 'grade-good'];
        return ['label' => 'بحاجة تحسين (D)', 'class' => 'grade-poor'];
    };
    $getBarClass = function($score) {
        if ($score === null) return '';
        if ($score >= 80) return 'excellent';
        if ($score >= 65) return 'very-good';
        if ($score >= 50) return 'good';
        return 'poor';
    };
    $dist = [
        'ممتاز (≥90)'        => $rows->filter(fn($r) => $r->evaluation && $r->evaluation->score >= 90)->count(),
        'ممتاز (80-89)'      => $rows->filter(fn($r) => $r->evaluation && $r->evaluation->score >= 80 && $r->evaluation->score < 90)->count(),
        'جيد جداً (65-79)'   => $rows->filter(fn($r) => $r->evaluation && $r->evaluation->score >= 65 && $r->evaluation->score < 80)->count(),
        'جيد (50-64)'        => $rows->filter(fn($r) => $r->evaluation && $r->evaluation->score >= 50 && $r->evaluation->score < 65)->count(),
        'بحاجة تحسين (<50)'  => $rows->filter(fn($r) => $r->evaluation && $r->evaluation->score < 50)->count(),
    ];
@endphp

<div class="ems-content">

    <div class="ems-page-header">
        <div class="ems-page-title-wrap">
            <div class="ems-breadcrumb">
                <a href="{{ route('dashboard') }}">الرئيسية</a>
                <span class="ems-breadcrumb-separator">/</span>
                <span>الرقابة والتقييم</span>
                <span class="ems-breadcrumb-separator">/</span>
                <span style="color:var(--ems-text-main);font-weight:700;">الأداء والإنجاز</span>
            </div>
            <h1 class="ems-page-title">مؤشرات الأداء والإنجاز المؤسسي (KPIs)</h1>
            <p class="ems-page-subtitle">تقييم الكفاءة والإنتاجية للدوائر والكوادر التابعة لـ مجموعة إياس الإعلامية</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <button class="perf-export-btn" onclick="exportTableToCSV()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                تصدير CSV
            </button>
            <button class="perf-export-btn" onclick="window.print()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                طباعة
            </button>
            @if ($canCreate)
            <button class="ems-btn ems-btn-primary" onclick="openModal('modal-evaluate-performance')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>إضافة تقييم أداء</span>
            </button>
            @endif
        </div>
    </div>

    <div class="perf-overall-banner">
        <div>
            <div class="perf-overall-tag" style="display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                <span>مؤشر الكفاءة والإنتاجية العام</span>
            </div>
            <div class="perf-overall-title">متوسط التقييمات المعتمدة ضمن نطاقك</div>
            <p class="perf-overall-sub">محسوب من متوسط آخر تقييمات الأداء المعتمدة من المدراء المباشرين للموظفين ضمن نطاقك الإداري.</p>
        </div>
        <div class="perf-overall-score-wrap">
            <div>
                <div class="perf-overall-num">{{ $overallScore !== null ? $overallScore . '%' : '--' }}</div>
                @if ($overallScore !== null)
                    @php $og = $getGrade($overallScore); @endphp
                    <span class="grade-badge {{ $og['class'] }}" style="margin-top:6px;">{{ $og['label'] }}</span>
                @else
                    <span class="grade-badge grade-none" style="margin-top:6px;">لا توجد تقييمات معتمدة</span>
                @endif
            </div>
            @if ($overallScore !== null)
            <div class="perf-overall-ring">
                <svg viewBox="0 0 80 80">
                    <circle class="ring-track" cx="40" cy="40" r="34"/>
                    <circle class="ring-fill" cx="40" cy="40" r="34"
                        id="overall-ring-fill"
                        stroke-dasharray="{{ round(2*3.14159*34,2) }}"
                        stroke-dashoffset="{{ round(2*3.14159*34,2) }}"/>
                </svg>
                <div class="perf-overall-ring-label">{{ $overallScore }}%</div>
            </div>
            @endif
        </div>
    </div>

    <div class="perf-kpi-grid">
        <div class="perf-kpi-card gold">
            <div class="perf-kpi-icon gold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div>
                <div class="perf-kpi-label">متوسط الأداء العام</div>
                <div class="perf-kpi-value">{{ $overallScore !== null ? $overallScore . '%' : '--' }}</div>
                <div class="perf-kpi-sub">من {{ $totalEvaluated }} تقييم معتمد</div>
            </div>
        </div>
        @php $topEmp = $topPerformers->first(); @endphp
        <div class="perf-kpi-card green">
            <div class="perf-kpi-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
            </div>
            <div style="min-width:0;flex:1;">
                <div class="perf-kpi-label">أعلى أداء موظف</div>
                @if ($topEmp)
                    <div class="perf-kpi-value" style="font-size:22px;">{{ round($topEmp->evaluation->score,1) }}%</div>
                    <div class="perf-kpi-sub" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $topEmp->employee->full_name }}</div>
                @else
                    <div class="perf-kpi-value">--</div>
                    <div class="perf-kpi-sub">لا توجد تقييمات</div>
                @endif
            </div>
        </div>
        <div class="perf-kpi-card blue">
            <div class="perf-kpi-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="perf-kpi-label">إجمالي المهام المنجزة</div>
                <div class="perf-kpi-value">{{ number_format($totalCompleted) }}</div>
                <div class="perf-kpi-sub">عبر {{ $rows->count() }} موظف</div>
            </div>
        </div>
        <div class="perf-kpi-card purple">
            <div class="perf-kpi-icon purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="perf-kpi-label">متوسط الالتزام بالمواعيد</div>
                <div class="perf-kpi-value">{{ $avgOnTime !== null ? $avgOnTime . '%' : '--' }}</div>
                <div class="perf-kpi-sub">الإنجاز في الوقت المحدد</div>
            </div>
        </div>
    </div>

    <div class="perf-charts-row">
        <div class="perf-chart-card">
            <div class="chart-header">
                <div class="ems-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                    <span>توزيع درجات الأداء</span>
                </div>
                <span class="ems-badge badge-gold">{{ $totalEvaluated }} مقيّم</span>
            </div>
            <div class="chart-body">
                @if ($totalEvaluated > 0)
                    <canvas id="perfDistChart" style="max-height:240px;width:100%;"></canvas>
                @else
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--ems-text-muted);gap:10px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity=".4"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                        <p style="margin:0;font-size:13px;">لا توجد تقييمات لعرض الرسم البياني</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="perf-leaderboard">
            <div class="perf-leaderboard-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--ems-gold,#c5a059);"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span style="font-size:14px;font-weight:800;color:var(--ems-text-main);">أفضل الموظفين أداءً</span>
            </div>
            @forelse ($topPerformers->take(7) as $i => $r)
                @php
                    $score     = (float)$r->evaluation->score;
                    $rankClass = $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-other'));
                    $ac        = $avatarColors[$i % count($avatarColors)];
                @endphp
                <div class="perf-lb-item">
                    <div class="perf-lb-rank {{ $rankClass }}">{{ $i + 1 }}</div>
                    <div class="perf-lb-avatar" style="background:{{ $ac }};">{{ mb_substr($r->employee->full_name, 0, 1) }}</div>
                    <div class="perf-lb-info">
                        <div class="perf-lb-name">{{ $r->employee->full_name }}</div>
                        <div class="perf-lb-dept">{{ $r->employee->department?->name ?? '-' }}</div>
                    </div>
                    <div class="perf-lb-score-wrap">
                        <div class="perf-lb-score">{{ $score }}%</div>
                        <div class="perf-lb-grade">{{ $r->evaluation->period }}</div>
                    </div>
                </div>
            @empty
                <div style="padding:32px 20px;text-align:center;color:var(--ems-text-muted);font-size:13px;">لا توجد تقييمات معتمدة بعد</div>
            @endforelse
        </div>
    </div>

    <div class="ems-card">
        <div class="ems-card-header">
            <div class="ems-card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                <span>سجل تقييم أداء الكوادر</span>
            </div>
            <span class="ems-badge badge-gold">{{ $rows->count() }} موظف</span>
        </div>
        <div style="padding:14px 18px 0;">
            <div class="perf-filter-bar">
                <span class="perf-filter-label" style="display:inline-flex;align-items:center;gap:5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>تصفية:</span>
                </span>
                <input type="text" id="filterName" placeholder="ابحث بالاسم..." oninput="applyFilters()" style="min-width:160px;">
                <select id="filterGrade" onchange="applyFilters()">
                    <option value="">جميع التقديرات</option>
                    <option value="ممتاز">ممتاز</option>
                    <option value="جيد جداً">جيد جداً</option>
                    <option value="جيد">جيد</option>
                    <option value="بحاجة تحسين">بحاجة تحسين</option>
                    <option value="غير مقيّم">غير مقيّم</option>
                </select>
                <select id="filterDept" onchange="applyFilters()">
                    <option value="">جميع الدوائر</option>
                    @foreach($rows->map(fn($r) => $r->employee->department?->name)->filter()->unique()->sort() as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
                <button class="perf-export-btn" onclick="clearFilters()" style="margin-right:auto;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    مسح الفلتر
                </button>
            </div>
        </div>
        <div class="ems-card-body" style="padding:0;">
            <div class="ems-table-responsive">
                <table class="ems-table" id="performanceTable">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>الموظف</th>
                            <th>الدائرة والقسم</th>
                            <th>المهام المنجزة</th>
                            <th>الالتزام بالمواعيد</th>
                            <th>آخر تقييم معتمد</th>
                            <th>التقدير</th>
                            <th style="text-align:center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="perfTableBody">
                        @forelse ($rows as $index => $row)
                            @php
                                $score      = $row->evaluation?->score;
                                $scoreFloat = $score !== null ? (float)$score : null;
                                $grade      = $getGrade($scoreFloat);
                                $barClass   = $getBarClass($scoreFloat);
                                $ac         = $avatarColors[$index % count($avatarColors)];
                                $deptName   = $row->employee->department?->name ?? '-';
                                $onTime     = $row->on_time_rate;
                                $circ       = round(2 * 3.14159 * 14, 2);
                                $offset     = $onTime !== null ? round($circ * (1 - $onTime / 100), 2) : $circ;
                                $onTimeColor= $onTime !== null ? ($onTime >= 80 ? '#15803d' : ($onTime >= 60 ? '#c5a059' : '#dc2626')) : '#94a3b8';
                            @endphp
                            <tr data-name="{{ strtolower($row->employee->full_name) }}"
                                data-grade="{{ $grade['label'] }}"
                                data-dept="{{ $deptName }}">
                                <td style="color:var(--ems-text-muted);font-size:12px;font-family:var(--font-mono,monospace);">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <div class="ems-avatar" style="width:36px;height:36px;font-size:13px;background:{{ $ac }};">
                                            {{ mb_substr($row->employee->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <strong style="font-size:13.5px;">{{ $row->employee->full_name }}</strong>
                                            <div style="font-size:11px;color:var(--ems-text-muted);">
                                                {{ $row->employee->position ?? '-' }}
                                                @if($row->employee->employee_code) &bull; {{ $row->employee->employee_code }} @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size:13px;">{{ $deptName }}</div>
                                    @if($row->employee->section)
                                        <div style="font-size:11px;color:var(--ems-text-muted);">{{ $row->employee->section->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:6px;">
                                        <span style="font-family:var(--font-mono,monospace);font-weight:800;font-size:16px;">{{ $row->completed_tasks }}</span>
                                        <span style="font-size:11px;color:var(--ems-text-muted);">مهمة</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($onTime !== null)
                                        <div class="ontime-ring-wrap">
                                            <div class="ontime-ring">
                                                <svg viewBox="0 0 34 34">
                                                    <circle class="ring-track" cx="17" cy="17" r="14"/>
                                                    <circle class="ring-fill" cx="17" cy="17" r="14"
                                                        stroke="{{ $onTimeColor }}"
                                                        stroke-dasharray="{{ $circ }}"
                                                        stroke-dashoffset="{{ $offset }}"/>
                                                </svg>
                                            </div>
                                            <span class="ontime-ring-val" style="color:{{ $onTimeColor }};">{{ $onTime }}%</span>
                                        </div>
                                    @else
                                        <span style="font-size:11.5px;color:var(--ems-text-muted);">لا توجد بيانات</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row->evaluation)
                                        <div class="score-bar-wrap">
                                            <div style="font-family:var(--font-mono,monospace);font-size:15px;font-weight:800;">{{ $scoreFloat }}%</div>
                                            <div class="score-bar-track">
                                                <div class="score-bar-fill {{ $barClass }}" style="width:{{ $scoreFloat }}%;"></div>
                                            </div>
                                        </div>
                                        <div style="font-size:10.5px;color:var(--ems-text-muted);margin-top:3px;">
                                            {{ $row->evaluation->period }} &bull; {{ $row->evaluation->evaluation_date->format('Y-m-d') }}
                                        </div>
                                    @else
                                        <span style="color:var(--ems-text-muted);font-size:12px;">لا يوجد تقييم</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="grade-badge {{ $grade['class'] }}">{{ $grade['label'] }}</span>
                                </td>
                                <td style="text-align:center;">
                                    @if ($row->evaluation)
                                        <button type="button" class="ems-btn ems-btn-icon" title="عرض تفاصيل التقييم"
                                            onclick="showEvaluationDetails({{ Js::from([
                                                'employee_name'   => $row->employee->full_name,
                                                'employee_code'   => $row->employee->employee_code,
                                                'position'        => $row->employee->position ?? '-',
                                                'department'      => ($row->employee->department?->name ?? '-') . ($row->employee->section ? ' - ' . $row->employee->section->name : ''),
                                                'period'          => $row->evaluation->period,
                                                'date'            => $row->evaluation->evaluation_date?->translatedFormat('d F Y') ?? $row->evaluation->evaluation_date?->format('Y-m-d'),
                                                'score'           => (float) $row->evaluation->score,
                                                'grade'           => $grade['label'],
                                                'grade_class'     => $grade['class'],
                                                'evaluator'       => $row->evaluation->evaluator?->username ?? 'الإدارة',
                                                'notes'           => $row->evaluation->notes ?? 'لا توجد ملاحظات إضافية مسجلة.',
                                                'completed_tasks' => $row->completed_tasks,
                                                'on_time_rate'    => $row->on_time_rate !== null ? $row->on_time_rate . '%' : 'غير محدد',
                                            ]) }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    @elseif ($canCreate)
                                        <button type="button" class="ems-btn ems-btn-icon" title="إضافة تقييم"
                                            onclick="openEvaluateForEmployee({{ $row->employee->id }}, '{{ addslashes($row->employee->full_name) }}')"
                                            style="color:var(--ems-gold);">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <line x1="12" y1="8" x2="12" y2="16"/>
                                                <line x1="8" y1="12" x2="16" y2="12"/>
                                            </svg>
                                        </button>
                                    @else
                                        <span style="color:var(--ems-text-muted);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;color:var(--ems-text-muted);padding:40px;">
                                    لا يوجد موظفون ضمن نطاقك.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="noFilterResults" style="display:none;text-align:center;padding:40px;color:var(--ems-text-muted);">
                <p>لا توجد نتائج تطابق معايير الفلتر.</p>
            </div>
        </div>
    </div>
</div>

@if ($canCreate)
<div class="ems-modal-overlay" id="modal-evaluate-performance">
    <div class="ems-modal">
        <div class="ems-modal-header">
            <div class="ems-modal-title">إضافة تقييم أداء جديد</div>
            <button class="ems-modal-close" onclick="closeModal('modal-evaluate-performance')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <form action="{{ route('performance.evaluate') }}" method="POST">
            @csrf
            <div class="ems-modal-body">
                <div class="ems-form-row">
                    <div class="ems-form-group">
                        <label class="ems-label"><span class="required">*</span> الموظف</label>
                        <select name="employee_id" id="modal_employee_id" class="ems-select" required>
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
                        <input type="date" name="evaluation_date" class="ems-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="ems-form-group">
                        <label class="ems-label"><span class="required">*</span> النسبة العامة (0-100)</label>
                        <input type="number" name="score" class="ems-input" min="0" max="100" step="0.1" required>
                    </div>
                </div>
                <div class="ems-form-row">
                    <div class="ems-form-group" style="flex:1;">
                        <label class="ems-label">ملاحظات (اختياري)</label>
                        <textarea name="notes" class="ems-input" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="ems-modal-footer">
                <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-evaluate-performance')">إلغاء</button>
                <button type="submit" class="ems-btn ems-btn-primary">اعتماد التقييم</button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="ems-modal-overlay" id="modal-view-evaluation">
    <div class="ems-modal" style="max-width:600px;">
        <div class="ems-modal-header">
            <div class="ems-modal-title" style="display:flex;align-items:center;gap:8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--ems-gold-hover);"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                <span>تقرير وتفاصيل تقييم الأداء</span>
            </div>
            <button type="button" class="ems-modal-close" onclick="closeModal('modal-view-evaluation')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="ems-modal-body" style="padding:24px;">
            <div style="background:var(--ems-surface-alt,#f8fafc);border:1px solid var(--ems-border);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div>
                    <h4 id="eval_emp_name" style="margin:0 0 4px 0;font-size:16px;font-weight:800;color:var(--ems-text-main);"></h4>
                    <div id="eval_emp_meta" style="font-size:12px;color:var(--ems-text-muted);"></div>
                </div>
                <span class="ems-badge badge-gold" id="eval_period_badge"></span>
            </div>
            <div style="background:linear-gradient(135deg,rgba(197,160,89,.1),rgba(197,160,89,.03));border:1px solid var(--ems-gold-light,#e2d2b4);border-radius:14px;padding:18px;text-align:center;margin-bottom:20px;">
                <div style="font-size:12px;font-weight:700;color:var(--ems-gold-hover);margin-bottom:4px;">النتيجة والتقييم المعتمد</div>
                <div id="eval_score_val" style="font-size:48px;font-weight:900;font-family:var(--font-mono,monospace);color:var(--ems-text-main);line-height:1.1;"></div>
                <div style="display:flex;justify-content:center;margin-top:8px;">
                    <span id="eval_grade_badge" class="grade-badge" style="font-size:13px;padding:5px 16px;"></span>
                </div>
                <div class="ems-progress" style="height:10px;max-width:280px;margin:14px auto 0;border-radius:999px;">
                    <div id="eval_progress_bar" class="ems-progress-bar" style="width:0%;transition:width .8s ease;"></div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:20px;">
                <div style="background:var(--ems-surface-alt,#f8fafc);border:1px solid var(--ems-border);border-radius:10px;padding:12px;">
                    <div style="font-size:11px;color:var(--ems-text-muted);margin-bottom:4px;display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>تاريخ الاعتماد</span>
                    </div>
                    <strong id="eval_date_val" style="font-size:13px;color:var(--ems-text-main);"></strong>
                </div>
                <div style="background:var(--ems-surface-alt,#f8fafc);border:1px solid var(--ems-border);border-radius:10px;padding:12px;">
                    <div style="font-size:11px;color:var(--ems-text-muted);margin-bottom:4px;display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>المسؤول المقيّم</span>
                    </div>
                    <strong id="eval_evaluator_val" style="font-size:13px;color:var(--ems-text-main);"></strong>
                </div>
                <div style="background:var(--ems-surface-alt,#f8fafc);border:1px solid var(--ems-border);border-radius:10px;padding:12px;">
                    <div style="font-size:11px;color:var(--ems-text-muted);margin-bottom:4px;display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>المهام المنجزة</span>
                    </div>
                    <strong id="eval_tasks_val" style="font-size:13px;color:var(--ems-text-main);"></strong>
                </div>
                <div style="background:var(--ems-surface-alt,#f8fafc);border:1px solid var(--ems-border);border-radius:10px;padding:12px;">
                    <div style="font-size:11px;color:var(--ems-text-muted);margin-bottom:4px;display:flex;align-items:center;gap:4px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>الالتزام بالموعد</span>
                    </div>
                    <strong id="eval_ontime_val" style="font-size:13px;color:var(--ems-text-main);"></strong>
                </div>
            </div>
            <div style="border:1px solid var(--ems-border);border-radius:12px;padding:14px 16px;background:var(--ems-surface);">
                <div style="display:flex;align-items:center;gap:6px;font-size:12.5px;font-weight:700;color:var(--ems-text-main);margin-bottom:8px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--ems-gold-hover);"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>ملاحظات وتوصيات التقييم الإداري:</span>
                </div>
                <p id="eval_notes_val" style="margin:0;font-size:13px;line-height:1.8;color:var(--ems-text-main);white-space:pre-line;background:var(--ems-surface-alt,#f8fafc);padding:12px;border-radius:8px;border-right:3px solid var(--ems-gold);"></p>
            </div>
        </div>
        <div class="ems-modal-footer" style="display:flex;justify-content:flex-end;">
            <button type="button" class="ems-btn ems-btn-secondary" onclick="closeModal('modal-view-evaluation')">إغلاق</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
@if ($totalEvaluated > 0)
(function(){
    const ctx = document.getElementById('perfDistChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($dist)),
            datasets: [{
                label: 'عدد الموظفين',
                data: @json(array_values($dist)),
                backgroundColor: [
                    'rgba(21,128,61,.75)',
                    'rgba(21,128,61,.5)',
                    'rgba(2,132,199,.7)',
                    'rgba(197,160,89,.75)',
                    'rgba(220,38,38,.65)',
                ],
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ` ${c.parsed.y} موظف` } }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Cairo, sans-serif', size: 11 } } },
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Cairo, sans-serif', size: 11 } }, grid: { color: 'rgba(0,0,0,.06)' } }
            }
        }
    });
})();
@endif

(function(){
    const fill = document.getElementById('overall-ring-fill');
    if (!fill) return;
    const score = {{ $overallScore ?? 0 }};
    const circumference = parseFloat(fill.getAttribute('stroke-dasharray'));
    const offset = circumference * (1 - score / 100);
    setTimeout(() => { fill.style.strokeDashoffset = offset; }, 400);
})();

function showEvaluationDetails(data) {
    document.getElementById('eval_emp_name').textContent    = data.employee_name || '';
    document.getElementById('eval_emp_meta').textContent    = (data.position || '') + ' (' + (data.employee_code || '') + ') • ' + (data.department || '');
    document.getElementById('eval_period_badge').textContent= data.period || 'الفترة الحالية';
    document.getElementById('eval_score_val').textContent   = data.score != null ? data.score + '%' : '--';
    const gb = document.getElementById('eval_grade_badge');
    gb.textContent = data.grade || 'معتمد';
    gb.className   = 'grade-badge ' + (data.grade_class || '');
    setTimeout(() => {
        document.getElementById('eval_progress_bar').style.width = Math.min(100, Math.max(0, data.score || 0)) + '%';
    }, 100);
    document.getElementById('eval_date_val').textContent      = data.date || '-';
    document.getElementById('eval_evaluator_val').textContent = data.evaluator || 'الإدارة العامة';
    document.getElementById('eval_tasks_val').textContent     = (data.completed_tasks || 0) + ' مهمة مكتملة';
    document.getElementById('eval_ontime_val').textContent    = data.on_time_rate || 'لا توجد بيانات';
    document.getElementById('eval_notes_val').textContent     = data.notes || 'لا توجد ملاحظات إضافية مسجلة على هذا التقييم.';
    openModal('modal-view-evaluation');
}

function openEvaluateForEmployee(id, name) {
    const sel = document.getElementById('modal_employee_id');
    if (sel) sel.value = id;
    openModal('modal-evaluate-performance');
}

function applyFilters() {
    const name  = (document.getElementById('filterName').value  || '').toLowerCase().trim();
    const grade = (document.getElementById('filterGrade').value || '').trim();
    const dept  = (document.getElementById('filterDept').value  || '').trim();
    const rows  = document.querySelectorAll('#perfTableBody tr[data-name]');
    let visible = 0;
    rows.forEach(tr => {
        const ok = (!name  || (tr.dataset.name  || '').includes(name))
                && (!grade || (tr.dataset.grade || '').includes(grade))
                && (!dept  || (tr.dataset.dept  || '') === dept);
        tr.style.display = ok ? '' : 'none';
        if (ok) visible++;
    });
    document.getElementById('noFilterResults').style.display = visible === 0 ? 'block' : 'none';
}

function clearFilters() {
    document.getElementById('filterName').value  = '';
    document.getElementById('filterGrade').value = '';
    document.getElementById('filterDept').value  = '';
    applyFilters();
}

function exportTableToCSV() {
    const rows = document.querySelectorAll('#performanceTable tr');
    const csv  = [];
    rows.forEach(row => {
        if (row.style.display === 'none') return;
        const cols = row.querySelectorAll('th, td');
        const data = Array.from(cols).map(col => {
            let t = col.innerText.trim().replace(/\n/g, ' ');
            if (t.includes(',')) t = '"' + t + '"';
            return t;
        });
        csv.push(data.join(','));
    });
    const content = '\uFEFF' + csv.join('\n');
    const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = 'performance_report.csv'; a.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection