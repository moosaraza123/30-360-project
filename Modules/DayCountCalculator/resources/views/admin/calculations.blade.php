@extends('daycountcalculator::layouts.master')

@section('title', 'Calculation Analytics')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold">Calculation Analytics</h1>
                <p class="text-muted mb-0">Detailed calculation statistics and trends</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <a href="{{ route('admin.export', 'calculations') }}" class="btn btn-primary">
                    <i class="bi bi-download"></i> Export Data
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Calculations</h6>
                    <h2 class="fw-bold text-primary">{{ number_format($stats['total_calculations']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">Unique Users</h6>
                    <h2 class="fw-bold text-success">{{ number_format($stats['total_users']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">This Week</h6>
                    <h2 class="fw-bold text-info">{{ number_format($stats['this_week']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted">This Month</h6>
                    <h2 class="fw-bold text-warning">{{ number_format($stats['this_month']) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Calculations Timeline (Last 90 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="timelineChart" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Convention Analysis -->
    <div class="row g-4">
        <!-- Popular Conventions Table -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Popular Conventions (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Convention</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = array_sum($popularConventions);
                                    $rank = 1;
                                @endphp
                                @foreach($popularConventions as $convention => $count)
                                    <tr>
                                        <td>
                                            @if($rank === 1)
                                                <i class="bi bi-trophy-fill text-warning"></i>
                                            @elseif($rank === 2)
                                                <i class="bi bi-trophy-fill text-secondary"></i>
                                            @elseif($rank === 3)
                                                <i class="bi bi-trophy-fill" style="color: #CD7F32;"></i>
                                            @else
                                                {{ $rank }}
                                            @endif
                                        </td>
                                        <td><strong>{{ $convention }}</strong></td>
                                        <td class="text-end">{{ number_format($count) }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-primary">{{ $total > 0 ? number_format(($count / $total) * 100, 1) : 0 }}%</span>
                                        </td>
                                    </tr>
                                    @php $rank++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Convention Distribution Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">All-Time Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="distributionChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-lightbulb text-info"></i> Key Insights
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0">
                                @php
                                    $mostPopular = array_key_first($popularConventions);
                                    $avgPerDay = $stats['total_calculations'] > 0 ? round($stats['total_calculations'] / 30) : 0;
                                @endphp
                                <li class="mb-2">
                                    <strong>Most Popular Convention:</strong> {{ $mostPopular }}
                                    ({{ number_format($popularConventions[$mostPopular]) }} uses in last 30 days)
                                </li>
                                <li class="mb-2">
                                    <strong>Average Daily Calculations:</strong> ~{{ number_format($avgPerDay) }} per day
                                </li>
                                <li>
                                    <strong>User Engagement:</strong>
                                    {{ $stats['total_users'] > 0 ? number_format($stats['total_calculations'] / $stats['total_users'], 1) : 0 }}
                                    calculations per user
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0">
                                <li class="mb-2">
                                    <strong>Growth This Week:</strong>
                                    {{ $stats['this_week'] }} calculations
                                    @if($stats['this_week'] > 0)
                                        <span class="text-success"><i class="bi bi-arrow-up"></i></span>
                                    @endif
                                </li>
                                <li class="mb-2">
                                    <strong>Monthly Trend:</strong>
                                    {{ $stats['this_month'] }} calculations this month
                                </li>
                                <li>
                                    <strong>Total Conventions:</strong> {{ count($distribution) }} different conventions used
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Timeline Chart
    const timelineCtx = document.getElementById('timelineChart').getContext('2d');
    const timelineData = @json($timeline);
    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: Object.keys(timelineData),
            datasets: [{
                label: 'Daily Calculations',
                data: Object.values(timelineData),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Calculations'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Distribution Chart
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    const distributionData = @json($distribution);
    new Chart(distributionCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(distributionData),
            datasets: [{
                label: 'Total Uses',
                data: Object.values(distributionData),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(148, 163, 184, 0.8)'
                ],
                borderColor: [
                    'rgb(59, 130, 246)',
                    'rgb(14, 165, 233)',
                    'rgb(16, 185, 129)',
                    'rgb(251, 191, 36)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                    'rgb(168, 85, 247)',
                    'rgb(236, 72, 153)',
                    'rgb(148, 163, 184)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
