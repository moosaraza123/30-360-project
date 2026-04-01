@extends('daycountcalculator::layouts.master')

@section('title', 'Subscriber Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold">Subscriber Management</h1>
                <p class="text-muted mb-0">Manage email subscribers and view growth metrics</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <a href="{{ route('admin.export', 'subscribers') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Export Data
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted small">Total</h6>
                    <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted small">Verified</h6>
                    <h3 class="fw-bold text-success mb-0">{{ number_format($stats['verified']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted small">Unverified</h6>
                    <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['unverified']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted small">Today</h6>
                    <h3 class="fw-bold text-info mb-0">{{ number_format($stats['today']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted small">This Week</h6>
                    <h3 class="fw-bold text-info mb-0">{{ number_format($stats['this_week']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted small">This Month</h6>
                    <h3 class="fw-bold text-info mb-0">{{ number_format($stats['this_month']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Chart -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Subscriber Growth (Last 90 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="growthChart" height="80"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Source Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="sourceChart" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Subscribers Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Recent Subscribers (Last 50)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Email</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Subscribed</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent as $subscriber)
                                    <tr>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $subscriber->source }}</span>
                                        </td>
                                        <td>
                                            @if($subscriber->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($subscriber->isVerified())
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                <small class="text-muted">{{ $subscriber->email_verified_at->format('M d, Y') }}</small>
                                            @else
                                                <i class="bi bi-x-circle text-warning"></i>
                                                <small class="text-muted">Not verified</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $subscriber->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @if(!$subscriber->isVerified())
                                                    <button class="btn btn-outline-success btn-sm" title="Mark as Verified">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                @endif
                                                @if($subscriber->is_active)
                                                    <button class="btn btn-outline-warning btn-sm" title="Deactivate">
                                                        <i class="bi bi-pause"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-success btn-sm" title="Activate">
                                                        <i class="bi bi-play"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No subscribers yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-lightbulb text-success"></i> Subscriber Insights
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0">
                                @php
                                    $verificationRate = $stats['total'] > 0 ? round(($stats['verified'] / $stats['total']) * 100, 1) : 0;
                                    $mostPopularSource = !empty($sourceDistribution) ? array_key_first($sourceDistribution) : 'N/A';
                                @endphp
                                <li class="mb-2">
                                    <strong>Verification Rate:</strong> {{ $verificationRate }}%
                                    @if($verificationRate >= 70)
                                        <span class="text-success"><i class="bi bi-emoji-smile"></i> Excellent</span>
                                    @elseif($verificationRate >= 50)
                                        <span class="text-warning"><i class="bi bi-emoji-neutral"></i> Good</span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-emoji-frown"></i> Needs Improvement</span>
                                    @endif
                                </li>
                                <li class="mb-2">
                                    <strong>Most Popular Source:</strong> {{ $mostPopularSource }}
                                </li>
                                <li>
                                    <strong>Active Subscribers:</strong> {{ $stats['active'] }} ({{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0 }}%)
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0">
                                <li class="mb-2">
                                    <strong>Weekly Growth:</strong> {{ $stats['this_week'] }} new subscribers
                                </li>
                                <li class="mb-2">
                                    <strong>Monthly Growth:</strong> {{ $stats['this_month'] }} new subscribers
                                </li>
                                <li>
                                    <strong>Unverified:</strong> {{ $stats['unverified'] }} subscribers need verification
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
    // Growth Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    const growthData = @json($growth);
    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: Object.keys(growthData),
            datasets: [{
                label: 'New Subscribers',
                data: Object.values(growthData),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
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

    // Source Distribution Chart
    const sourceCtx = document.getElementById('sourceChart').getContext('2d');
    const sourceData = @json($sourceDistribution);
    new Chart(sourceCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(sourceData),
            datasets: [{
                data: Object.values(sourceData),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)',
                    'rgb(168, 85, 247)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
