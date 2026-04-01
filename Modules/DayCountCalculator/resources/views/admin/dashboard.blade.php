@extends('daycountcalculator::layouts.master')

@section('title', 'Admin Dashboard')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="admin-shell">

    @include('daycountcalculator::admin.partials.sidebar')

    {{-- Main Content --}}
    <main class="admin-main">

        {{-- Header --}}
        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Dashboard</h1>
                <p class="admin-page-subtitle">Overview as of {{ now()->format('D, d M Y · H:i') }}</p>
            </div>
        </div>

        {{-- KPI Row --}}
        <div class="row g-3 mb-4">

            {{-- Total Calculations --}}
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(201,162,39,.12);color:#c9a227;">
                        <i class="bi bi-calculator-fill"></i>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">Total Calculations</div>
                        <div class="kpi-value">{{ number_format($calculationStats['total_calculations']) }}</div>
                        <div class="kpi-sub">
                            @php $todayChange = $calculationStats['today_change'] ?? null; @endphp
                            @if($todayChange !== null)
                                <span class="{{ $todayChange >= 0 ? 'kpi-trend-up' : 'kpi-trend-down' }}">
                                    <i class="bi bi-arrow-{{ $todayChange >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($todayChange) }}% vs yesterday
                                </span>
                            @else
                                <span class="text-muted">{{ number_format($calculationStats['today']) }} today</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Registered Users --}}
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(16,185,129,.12);color:#10b981;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">Registered Users</div>
                        <div class="kpi-value">{{ number_format($userStats['total']) }}</div>
                        <div class="kpi-sub">
                            @if($userStats['today'] > 0)
                                <span class="kpi-trend-up"><i class="bi bi-arrow-up-short"></i>{{ $userStats['today'] }} new today</span>
                            @else
                                <span class="text-muted">{{ $userStats['this_week'] }} this week</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subscribers --}}
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(99,102,241,.12);color:#6366f1;">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">Email Subscribers</div>
                        <div class="kpi-value">{{ number_format($subscriberStats['total']) }}</div>
                        <div class="kpi-sub">
                            <span class="{{ $subscriberStats['verified'] > 0 ? 'kpi-trend-up' : 'text-muted' }}">
                                {{ number_format($subscriberStats['verified']) }} verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- This Week --}}
            <div class="col-6 col-xl-3">
                <div class="kpi-card">
                    <div class="kpi-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="kpi-body">
                        <div class="kpi-label">This Week</div>
                        <div class="kpi-value">{{ number_format($calculationStats['this_week']) }}</div>
                        <div class="kpi-sub">
                            @php $wc = $calculationStats['week_change'] ?? null; @endphp
                            @if($wc !== null)
                                <span class="{{ $wc >= 0 ? 'kpi-trend-up' : 'kpi-trend-down' }}">
                                    <i class="bi bi-arrow-{{ $wc >= 0 ? 'up' : 'down' }}-short"></i>{{ abs($wc) }}% vs last week
                                </span>
                            @else
                                <span class="text-muted">{{ number_format($calculationStats['this_month']) }} this month</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Secondary Stats Row --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-pill">
                    <div class="stat-pill-label">Avg / Day (30d)</div>
                    <div class="stat-pill-value">{{ number_format($calculationStats['avg_per_day_30d']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill">
                    <div class="stat-pill-label">Auth Calculations</div>
                    <div class="stat-pill-value">{{ number_format($calculationStats['auth_calculations']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill">
                    <div class="stat-pill-label">Guest Calculations</div>
                    <div class="stat-pill-value">{{ number_format($calculationStats['guest_calculations']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill">
                    <div class="stat-pill-label">Saved Calculations</div>
                    <div class="stat-pill-value">{{ number_format($userStats['saved_calculations']) }}</div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row g-3 mb-4">
            {{-- Timeline Chart --}}
            <div class="col-lg-8">
                <div class="admin-card h-100">
                    <div class="admin-card-header">
                        <span>Calculations — Last 30 Days</span>
                    </div>
                    <div class="admin-card-body">
                        <canvas id="calculationsTimelineChart" height="90"></canvas>
                    </div>
                </div>
            </div>

            {{-- Popular Conventions --}}
            <div class="col-lg-4">
                <div class="admin-card h-100">
                    <div class="admin-card-header">
                        <span>Top Conventions (30d)</span>
                    </div>
                    <div class="admin-card-body p-0">
                        @php $total30 = array_sum($popularConventions) ?: 1; $rank = 0; @endphp
                        @forelse(array_slice($popularConventions, 0, 6, true) as $convention => $count)
                            @php $rank++; $pct = round(($count / $total30) * 100, 1); @endphp
                            <div class="convention-row">
                                <span class="convention-rank">{{ $rank }}</span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="convention-name">{{ $convention }}</span>
                                        <span class="convention-count">{{ number_format($count) }}</span>
                                    </div>
                                    <div class="convention-bar-track">
                                        <div class="convention-bar-fill" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted" style="font-size:.85rem;">No data yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Distribution + Subscriber Growth --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-5">
                <div class="admin-card h-100">
                    <div class="admin-card-header">
                        <span>Convention Distribution (All-Time)</span>
                    </div>
                    <div class="admin-card-body d-flex align-items-center justify-content-center">
                        <canvas id="conventionDistributionChart" height="160"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="admin-card h-100">
                    <div class="admin-card-header">
                        <span>Subscriber Growth — Last 30 Days</span>
                    </div>
                    <div class="admin-card-body">
                        <canvas id="subscriberGrowthChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Tables --}}
        <div class="row g-3 mb-4">
            {{-- User & Calc Stats --}}
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="admin-card-header d-flex justify-content-between align-items-center">
                        <span>Calculation Breakdown</span>
                        <a href="{{ route('admin.calculations') }}" class="admin-link-btn">View All →</a>
                    </div>
                    <div class="admin-card-body p-0">
                        <table class="admin-table">
                            <tbody>
                                <tr><td>Total Calculations</td><td><strong>{{ number_format($calculationStats['total_calculations']) }}</strong></td></tr>
                                <tr><td>By Registered Users</td><td>{{ number_format($calculationStats['auth_calculations']) }}</td></tr>
                                <tr><td>By Guests</td><td>{{ number_format($calculationStats['guest_calculations']) }}</td></tr>
                                <tr><td>Unique Registered Users</td><td>{{ number_format($calculationStats['unique_users']) }}</td></tr>
                                <tr><td>Unique Guest Sessions</td><td>{{ number_format($calculationStats['unique_sessions']) }}</td></tr>
                                <tr><td>Today</td><td><span class="badge-stat">{{ number_format($calculationStats['today']) }}</span></td></tr>
                                <tr><td>This Week</td><td><span class="badge-stat">{{ number_format($calculationStats['this_week']) }}</span></td></tr>
                                <tr><td>This Month</td><td><span class="badge-stat">{{ number_format($calculationStats['this_month']) }}</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Subscriber Stats --}}
            <div class="col-lg-6">
                <div class="admin-card">
                    <div class="admin-card-header d-flex justify-content-between align-items-center">
                        <span>Subscriber Breakdown</span>
                        <a href="{{ route('admin.subscribers') }}" class="admin-link-btn">View All →</a>
                    </div>
                    <div class="admin-card-body p-0">
                        <table class="admin-table">
                            <tbody>
                                <tr><td>Total Subscribers</td><td><strong>{{ number_format($subscriberStats['total']) }}</strong></td></tr>
                                <tr><td>Verified</td><td><span class="badge-stat badge-success">{{ number_format($subscriberStats['verified']) }}</span></td></tr>
                                <tr><td>Unverified</td><td><span class="badge-stat badge-warn">{{ number_format($subscriberStats['unverified']) }}</span></td></tr>
                                <tr><td>Active</td><td>{{ number_format($subscriberStats['active']) }}</td></tr>
                                <tr><td>Inactive</td><td>{{ number_format($subscriberStats['inactive']) }}</td></tr>
                                <tr><td>Today</td><td><span class="badge-stat">{{ number_format($subscriberStats['today']) }}</span></td></tr>
                                <tr><td>This Week</td><td><span class="badge-stat">{{ number_format($subscriberStats['this_week']) }}</span></td></tr>
                                <tr><td>This Month</td><td><span class="badge-stat">{{ number_format($subscriberStats['this_month']) }}</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
@endsection

@push('styles')
@include('daycountcalculator::admin.partials.styles')
@endpush

@push('scripts')
<script>
const GOLD   = '#c9a227';
const NAVY   = '#0f172a';
const SLATE  = '#64748b';

// ── Timeline Chart ──────────────────────────────────────────────────────
const timelineData = @json($calculationsTimeline);
new Chart(document.getElementById('calculationsTimelineChart'), {
    type: 'line',
    data: {
        labels: Object.keys(timelineData),
        datasets: [{
            label: 'Calculations',
            data: Object.values(timelineData),
            borderColor: GOLD,
            backgroundColor: 'rgba(201,162,39,.08)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: GOLD,
            pointHoverRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, color: SLATE, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { color: SLATE, font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
        }
    }
});

// ── Doughnut Chart ────────────────────────────────────────────────────────
const distributionData = @json($conventionDistribution);
const PALETTE = ['#c9a227','#0f172a','#1e3a5f','#2563eb','#64748b','#94a3b8','#f59e0b','#10b981','#6366f1'];
new Chart(document.getElementById('conventionDistributionChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(distributionData),
        datasets: [{
            data: Object.values(distributionData),
            backgroundColor: PALETTE,
            borderColor: '#fff',
            borderWidth: 2,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '62%',
        plugins: {
            legend: {
                position: 'right',
                labels: { font: { size: 11 }, color: '#334155', padding: 10, boxWidth: 12 }
            }
        }
    }
});

// ── Subscriber Growth Chart ───────────────────────────────────────────────
const subscriberData = @json($subscriberGrowth);
new Chart(document.getElementById('subscriberGrowthChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(subscriberData),
        datasets: [{
            label: 'New Subscribers',
            data: Object.values(subscriberData),
            backgroundColor: 'rgba(99,102,241,.7)',
            borderColor: '#6366f1',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, color: SLATE, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { color: SLATE, font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
        }
    }
});
</script>
@endpush
