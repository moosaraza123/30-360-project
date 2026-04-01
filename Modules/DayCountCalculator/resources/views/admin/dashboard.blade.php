@extends('daycountcalculator::layouts.master')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold">Admin Dashboard</h1>
            <p class="text-muted">Analytics and statistics overview</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <!-- Calculations Stats -->
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Calculations</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($calculationStats['total_calculations']) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calculator text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-success">
                            <i class="bi bi-arrow-up"></i> {{ $calculationStats['today'] }} today
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Subscribers</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($subscriberStats['total']) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-success">
                            <i class="bi bi-arrow-up"></i> {{ $subscriberStats['today'] }} today
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">This Week</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($calculationStats['this_week']) }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        Calculations this week
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">This Month</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($calculationStats['this_month']) }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-month text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        Calculations this month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Calculations Timeline -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Calculations Timeline (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="calculationsTimelineChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Popular Conventions -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Popular Conventions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach(array_slice($popularConventions, 0, 5, true) as $convention => $count)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="small">{{ $convention }}</span>
                                <span class="badge bg-primary rounded-pill">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Convention Distribution & Subscriber Growth -->
    <div class="row g-4 mb-4">
        <!-- Convention Distribution -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Convention Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="conventionDistributionChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Subscriber Growth -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Subscriber Growth (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="subscriberGrowthChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats Tables -->
    <div class="row g-4">
        <!-- Subscriber Stats -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Subscriber Details</h5>
                    <a href="{{ route('admin.subscribers') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Total Subscribers</td>
                                    <td class="text-end fw-bold">{{ number_format($subscriberStats['total']) }}</td>
                                </tr>
                                <tr>
                                    <td>Verified</td>
                                    <td class="text-end">
                                        <span class="badge bg-success">{{ number_format($subscriberStats['verified']) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Unverified</td>
                                    <td class="text-end">
                                        <span class="badge bg-warning">{{ number_format($subscriberStats['unverified']) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Active</td>
                                    <td class="text-end">
                                        <span class="badge bg-success">{{ number_format($subscriberStats['active']) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Inactive</td>
                                    <td class="text-end">
                                        <span class="badge bg-secondary">{{ number_format($subscriberStats['inactive']) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calculation Stats -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Calculation Details</h5>
                    <a href="{{ route('admin.calculations') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Total Calculations</td>
                                    <td class="text-end fw-bold">{{ number_format($calculationStats['total_calculations']) }}</td>
                                </tr>
                                <tr>
                                    <td>Unique Users</td>
                                    <td class="text-end">{{ number_format($calculationStats['total_users']) }}</td>
                                </tr>
                                <tr>
                                    <td>Unique Sessions</td>
                                    <td class="text-end">{{ number_format($calculationStats['total_sessions']) }}</td>
                                </tr>
                                <tr>
                                    <td>Today</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ number_format($calculationStats['today']) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>This Week</td>
                                    <td class="text-end">
                                        <span class="badge bg-info">{{ number_format($calculationStats['this_week']) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Quick Actions</h5>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <a href="{{ route('admin.export', 'calculations') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-download"></i> Export Calculations
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.export', 'subscribers') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-download"></i> Export Subscribers
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.calculations') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-bar-chart"></i> View Analytics
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('calculator.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-calculator"></i> Back to Calculator
                            </a>
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
    // Calculations Timeline Chart
    const timelineCtx = document.getElementById('calculationsTimelineChart').getContext('2d');
    const timelineData = @json($calculationsTimeline);
    new Chart(timelineCtx, {
        type: 'line',
        data: {
            labels: Object.keys(timelineData),
            datasets: [{
                label: 'Calculations',
                data: Object.values(timelineData),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Convention Distribution Chart
    const distributionCtx = document.getElementById('conventionDistributionChart').getContext('2d');
    const distributionData = @json($conventionDistribution);
    new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(distributionData),
            datasets: [{
                data: Object.values(distributionData),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(14, 165, 233)',
                    'rgb(16, 185, 129)',
                    'rgb(251, 191, 36)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                    'rgb(168, 85, 247)',
                    'rgb(236, 72, 153)',
                    'rgb(148, 163, 184)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // Subscriber Growth Chart
    const subscriberCtx = document.getElementById('subscriberGrowthChart').getContext('2d');
    const subscriberData = @json($subscriberGrowth);
    new Chart(subscriberCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(subscriberData),
            datasets: [{
                label: 'New Subscribers',
                data: Object.values(subscriberData),
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
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
