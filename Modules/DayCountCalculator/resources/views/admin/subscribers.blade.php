@extends('daycountcalculator::layouts.master')

@section('title', 'Subscribers — Admin')
@section('robots', 'noindex, nofollow')

@section('content')
<div class="admin-shell">

    @include('daycountcalculator::admin.partials.sidebar')

    <main class="admin-main">

        <div class="admin-page-header">
            <div>
                <h1 class="admin-page-title">Subscribers</h1>
                <p class="admin-page-subtitle">Email list — {{ number_format($stats['total']) }} total</p>
            </div>
            <a href="{{ route('admin.export', 'subscribers') }}" class="btn btn-gold btn-sm px-3">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
        </div>

        {{-- KPI Row --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="stat-pill">
                    <div class="stat-pill-label">Total</div>
                    <div class="stat-pill-value">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill">
                    <div class="stat-pill-label">Verified</div>
                    <div class="stat-pill-value" style="color:#10b981;">{{ number_format($stats['verified']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill">
                    <div class="stat-pill-label">Unverified</div>
                    <div class="stat-pill-value" style="color:#f59e0b;">{{ number_format($stats['unverified']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill">
                    <div class="stat-pill-label">Today</div>
                    <div class="stat-pill-value">{{ number_format($stats['today']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill">
                    <div class="stat-pill-label">This Week</div>
                    <div class="stat-pill-value">{{ number_format($stats['this_week']) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-pill">
                    <div class="stat-pill-label">This Month</div>
                    <div class="stat-pill-value">{{ number_format($stats['this_month']) }}</div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="admin-card">
                    <div class="admin-card-header">Subscriber Growth — Last 90 Days</div>
                    <div class="admin-card-body">
                        <canvas id="growthChart" height="90"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="admin-card h-100">
                    <div class="admin-card-header">Source Distribution</div>
                    <div class="admin-card-body d-flex align-items-center justify-content-center">
                        <canvas id="sourceChart" height="160"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Insights --}}
        @php
            $verificationRate = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100, 1) : 0;
            $mostPopularSource = !empty($sourceDistribution) ? array_key_first($sourceDistribution) : 'N/A';
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="admin-card">
                    <div class="admin-card-header">Verification Rate</div>
                    <div class="admin-card-body text-center">
                        <div style="font-size:2.5rem;font-weight:800;color:{{ $verificationRate >= 70 ? '#10b981' : ($verificationRate >= 50 ? '#f59e0b' : '#ef4444') }};">{{ $verificationRate }}%</div>
                        <div style="font-size:.8rem;color:#94a3b8;margin-top:.25rem;">
                            @if($verificationRate >= 70) Excellent
                            @elseif($verificationRate >= 50) Good
                            @else Needs Improvement
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="admin-card">
                    <div class="admin-card-header">Top Source</div>
                    <div class="admin-card-body text-center">
                        <div style="font-size:1.5rem;font-weight:800;color:#0f172a;text-transform:capitalize;">{{ $mostPopularSource }}</div>
                        <div style="font-size:.8rem;color:#94a3b8;margin-top:.25rem;">Most subscriptions from</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="admin-card">
                    <div class="admin-card-header">Active Rate</div>
                    <div class="admin-card-body text-center">
                        @php $activeRate = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0; @endphp
                        <div style="font-size:2.5rem;font-weight:800;color:#6366f1;">{{ $activeRate }}%</div>
                        <div style="font-size:.8rem;color:#94a3b8;margin-top:.25rem;">{{ number_format($stats['active']) }} active subscribers</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Subscribers Table --}}
        <div class="admin-card">
            <div class="admin-card-header">Recent Subscribers (Last 50)</div>
            <div class="table-responsive">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $subscriber)
                            <tr>
                                <td>{{ $subscriber->email }}</td>
                                <td><span class="source-badge">{{ $subscriber->source }}</span></td>
                                <td>
                                    @if($subscriber->is_active)
                                        <span class="status-dot status-active">Active</span>
                                    @else
                                        <span class="status-dot status-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subscriber->isVerified())
                                        <i class="bi bi-check-circle-fill" style="color:#10b981;"></i>
                                        <span style="font-size:.78rem;color:#94a3b8;">{{ $subscriber->email_verified_at->format('d M Y') }}</span>
                                    @else
                                        <i class="bi bi-clock" style="color:#f59e0b;"></i>
                                        <span style="font-size:.78rem;color:#94a3b8;">Pending</span>
                                    @endif
                                </td>
                                <td style="color:#94a3b8;font-size:.78rem;">{{ $subscriber->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted" style="font-size:.85rem;">No subscribers yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
.source-badge { background:rgba(15,23,42,.06); color:#475569; font-size:.72rem; font-weight:600; padding:.15rem .5rem; border-radius:.375rem; text-transform:capitalize; }
.status-dot { font-size:.75rem; font-weight:600; padding:.15rem .5rem; border-radius:.375rem; }
.status-active { background:rgba(16,185,129,.1); color:#059669; }
.status-inactive { background:rgba(100,116,139,.1); color:#64748b; }
</style>
@endpush

@push('scripts')
<script>
const SLATE = '#64748b';

new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
        labels: Object.keys(@json($growth)),
        datasets: [{
            label: 'New Subscribers',
            data: Object.values(@json($growth)),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,.08)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#6366f1',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, color: SLATE, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { color: SLATE, font: { size: 10 }, maxRotation: 45 }, grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('sourceChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(@json($sourceDistribution)),
        datasets: [{
            data: Object.values(@json($sourceDistribution)),
            backgroundColor: ['#c9a227','#6366f1','#10b981','#f59e0b','#ef4444'],
            borderColor: '#fff',
            borderWidth: 2,
            hoverOffset: 4,
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, color: '#334155', padding: 8, boxWidth: 10 } } }
    }
});
</script>
@endpush
