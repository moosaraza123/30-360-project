@extends('daycountcalculator::layouts.master')

@section('title', 'Calculations — Admin')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="admin-shell">

    @include('daycountcalculator::admin.partials.sidebar')

    <main class="admin-main">

        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Calculations</h1>
                <p class="admin-page-subtitle">Analytics & convention usage</p>
            </div>
            <a href="{{ route('admin.export', 'calculations') }}" class="btn btn-gold btn-sm px-3">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
        </div>

        {{-- KPI Row --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(201,162,39,.12);color:#c9a227;"><i class="bi bi-calculator-fill"></i></div>
                    <div class="kpi-body">
                        <div class="kpi-label">Total</div>
                        <div class="kpi-value">{{ number_format($stats['total_calculations']) }}</div>
                        <div class="kpi-sub text-muted">{{ number_format($stats['today']) }} today</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(99,102,241,.12);color:#6366f1;"><i class="bi bi-person-check-fill"></i></div>
                    <div class="kpi-body">
                        <div class="kpi-label">By Users</div>
                        <div class="kpi-value">{{ number_format($stats['auth_calculations']) }}</div>
                        <div class="kpi-sub text-muted">{{ number_format($stats['unique_users']) }} unique</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i class="bi bi-incognito"></i></div>
                    <div class="kpi-body">
                        <div class="kpi-label">By Guests</div>
                        <div class="kpi-value">{{ number_format($stats['guest_calculations']) }}</div>
                        <div class="kpi-sub text-muted">{{ number_format($stats['unique_sessions']) }} sessions</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(16,185,129,.12);color:#10b981;"><i class="bi bi-graph-up-arrow"></i></div>
                    <div class="kpi-body">
                        <div class="kpi-label">This Month</div>
                        <div class="kpi-value">{{ number_format($stats['this_month']) }}</div>
                        <div class="kpi-sub text-muted">{{ number_format($stats['this_week']) }} this week</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timeline Chart --}}
        <div class="admin-card mb-4">
            <div class="admin-card-header">Calculations Timeline — Last 90 Days</div>
            <div class="admin-card-body">
                <canvas id="timelineChart" height="70"></canvas>
            </div>
        </div>

        {{-- Convention Analysis --}}
        <div class="row g-3 mb-4">
            {{-- Popular Conventions Table --}}
            <div class="col-lg-6">
                <div class="admin-card h-100">
                    <div class="admin-card-header">Popular Conventions (Last 30 Days)</div>
                    <div class="table-responsive">
                        <table class="admin-data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Convention</th>
                                    <th style="text-align:right;">Uses</th>
                                    <th style="text-align:right;">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = array_sum($popularConventions) ?: 1; $rank = 0; @endphp
                                @forelse($popularConventions as $convention => $count)
                                    @php $rank++; @endphp
                                    <tr>
                                        <td>
                                            @if($rank === 1) <i class="bi bi-trophy-fill" style="color:#c9a227;"></i>
                                            @elseif($rank === 2) <i class="bi bi-trophy-fill" style="color:#94a3b8;"></i>
                                            @elseif($rank === 3) <i class="bi bi-trophy-fill" style="color:#b45309;"></i>
                                            @else <span style="color:#cbd5e1;font-size:.78rem;">{{ $rank }}</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $convention }}</strong></td>
                                        <td style="text-align:right;">{{ number_format($count) }}</td>
                                        <td style="text-align:right;"><span class="badge-stat">{{ number_format(($count / $total) * 100, 1) }}%</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No data yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Distribution Chart --}}
            <div class="col-lg-6">
                <div class="admin-card h-100">
                    <div class="admin-card-header">All-Time Distribution</div>
                    <div class="admin-card-body">
                        <canvas id="distributionChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Key Insights --}}
        <div class="admin-card">
            <div class="admin-card-header">Key Insights</div>
            <div class="admin-card-body">
                <div class="row g-3">
                    @php
                        $mostPopular = array_key_first($popularConventions ?? []);
                        $avgPerDay = $stats['avg_per_day_30d'] ?? 0;
                        $engagementRate = $stats['unique_users'] > 0
                            ? number_format($stats['total_calculations'] / $stats['unique_users'], 1)
                            : '—';
                    @endphp
                    <div class="col-md-4">
                        <div class="insight-block">
                            <div class="insight-label">Most Used Convention</div>
                            <div class="insight-value">{{ $mostPopular ?? '—' }}</div>
                            @if($mostPopular && isset($popularConventions[$mostPopular]))
                                <div class="insight-sub">{{ number_format($popularConventions[$mostPopular]) }} uses in last 30 days</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="insight-block">
                            <div class="insight-label">Avg Daily Calculations (30d)</div>
                            <div class="insight-value">{{ $avgPerDay }}</div>
                            <div class="insight-sub">per day on average</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="insight-block">
                            <div class="insight-label">Avg per Registered User</div>
                            <div class="insight-value">{{ $engagementRate }}</div>
                            <div class="insight-sub">calculations per user (all-time)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
@endsection

@push('styles')
@include('daycountcalculator::admin.partials.styles')
<style>
.admin-data-table { width:100%; border-collapse:collapse; }
.admin-data-table th { padding:.625rem 1.25rem; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; border-bottom:1px solid #f1f5f9; background:#fafafa; }
.admin-data-table td { padding:.65rem 1.25rem; font-size:.83rem; color:#475569; border-bottom:1px solid #f8fafc; }
.admin-data-table tr:last-child td { border-bottom:none; }

.insight-block { background:#f8fafc; border-radius:.75rem; padding:1rem 1.25rem; }
.insight-label { font-size:.72rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem; }
.insight-value { font-size:1.5rem; font-weight:800; color:#0f172a; line-height:1.1; margin-bottom:.2rem; }
.insight-sub { font-size:.75rem; color:#94a3b8; }
</style>
@endpush

@push('scripts')
<script>
const GOLD = '#c9a227', SLATE = '#64748b';

new Chart(document.getElementById('timelineChart'), {
    type: 'line',
    data: {
        labels: Object.keys(@json($timeline)),
        datasets: [{
            label: 'Calculations',
            data: Object.values(@json($timeline)),
            borderColor: GOLD,
            backgroundColor: 'rgba(201,162,39,.08)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 2,
            pointBackgroundColor: GOLD,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, color: SLATE, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { color: SLATE, font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('distributionChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(@json($distribution)),
        datasets: [{
            data: Object.values(@json($distribution)),
            backgroundColor: ['#c9a227','#0f172a','#1e3a5f','#2563eb','#64748b','#94a3b8','#f59e0b','#10b981','#6366f1'],
            borderRadius: 4,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { precision: 0, color: SLATE, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
            y: { ticks: { color: SLATE, font: { size: 11 } }, grid: { display: false } }
        }
    }
});
</script>
@endpush
