@extends('daycountcalculator::layouts.master')

@section('title', 'Comparison Tool')

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <div class="hero-badge">Side-by-Side Analysis</div>
        <h1 class="mb-1">Convention <span class="gold-accent">Comparison</span> Tool</h1>
        <p class="lead">Compare multiple day count conventions with identical dates</p>
    </div>
</div>

<div class="container pb-5">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <form id="comparisonForm" action="{{ route('comparison.calculate') }}" method="POST">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-semibold" style="font-size:.875rem;">
                            Start Date <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               class="form-control form-control-lg @error('start_date') is-invalid @enderror"
                               id="start_date" name="start_date"
                               value="{{ old('start_date') }}" required
                               style="border-color:#e2e8f0;border-radius:.5rem;">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-semibold" style="font-size:.875rem;">
                            End Date <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               class="form-control form-control-lg @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date"
                               value="{{ old('end_date') }}" required
                               style="border-color:#e2e8f0;border-radius:.5rem;">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold d-block" style="font-size:.875rem;">&nbsp;</label>
                        <button type="submit" class="btn btn-gold btn-lg w-100" id="compareBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                            <i class="bi bi-bar-chart me-2"></i>Compare All
                        </button>
                    </div>
                </div>

                {{-- Optional Interest --}}
                <div class="rounded-3 p-3 mb-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <h6 class="fw-bold mb-3" style="color:#0f172a;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                        Interest Calculation
                        <span class="fw-normal ms-1" style="color:#94a3b8;font-size:.78rem;text-transform:none;letter-spacing:0;">Optional</span>
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="principal" class="form-label" style="font-size:.875rem;">Principal Amount ($)</label>
                            <input type="number" class="form-control @error('principal') is-invalid @enderror"
                                   id="principal" name="principal" step="0.01" min="0"
                                   placeholder="e.g. 1,000,000" value="{{ old('principal') }}"
                                   style="border-color:#e2e8f0;border-radius:.5rem;">
                            @error('principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="interest_rate" class="form-label" style="font-size:.875rem;">Interest Rate (%)</label>
                            <input type="number" class="form-control @error('interest_rate') is-invalid @enderror"
                                   id="interest_rate" name="interest_rate" step="0.001" min="0" max="100"
                                   placeholder="e.g. 5.5" value="{{ old('interest_rate') }}"
                                   style="border-color:#e2e8f0;border-radius:.5rem;">
                            @error('interest_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Convention Selection --}}
                <div class="rounded-3 p-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <h6 class="fw-bold mb-3" style="color:#0f172a;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                        Select Conventions
                        <span class="fw-normal ms-1" style="color:#94a3b8;font-size:.78rem;text-transform:none;letter-spacing:0;">Leave blank for all</span>
                    </h6>
                    <div class="row g-2">
                        @foreach($conventions as $convention)
                            <div class="col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input comparison-convention-check" type="checkbox"
                                           name="conventions[]" value="{{ $convention }}"
                                           id="conv_{{ $loop->index }}"
                                           style="accent-color:var(--gold);">
                                    <label class="form-check-label" for="conv_{{ $loop->index }}" style="font-size:.85rem;">
                                        {{ $convention }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Placeholder --}}
    <div id="comparisonResults"></div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('comparisonForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('compareBtn');
        const spinner = btn.querySelector('.spinner-border');
        btn.disabled = true;
        spinner.classList.remove('d-none');

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayComparisonResults(data.comparison);
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the comparison.');
        })
        .finally(() => {
            btn.disabled = false;
            spinner.classList.add('d-none');
        });
    });

    function displayComparisonResults(comparison) {
        const resultsDiv = document.getElementById('comparisonResults');

        let html = `
            <div class="card shadow-sm">
                <div class="card-header-navy p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold">Comparison Results</h5>
                        <small style="color:rgba(255,255,255,.6);">From ${comparison.start_date} to ${comparison.end_date}</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive mb-4">
                        <table class="table comparison-results-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Convention</th>
                                    <th class="text-end">Days</th>
                                    <th class="text-end">Day Count Factor</th>
                                    ${comparison.principal ? '<th class="text-end">Interest Amount</th>' : ''}
                                </tr>
                            </thead>
                            <tbody>
        `;

        const results = Object.values(comparison.results);
        results.forEach(result => {
            html += `
                <tr>
                    <td><strong style="color:#0f172a;">${result.convention_type}</strong></td>
                    <td class="text-end font-monospace"><span class="badge badge-gold">${result.days}</span></td>
                    <td class="text-end font-monospace">${result.day_count_factor_formatted}</td>
                    ${result.interest_amount !== null ? `<td class="text-end font-monospace">${result.interest_amount_formatted}</td>` : ''}
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="metric-box">
                                <div class="metric-value">${comparison.statistics.min_days}</div>
                                <div class="metric-label">Min Days</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-box">
                                <div class="metric-value">${comparison.statistics.max_days}</div>
                                <div class="metric-label">Max Days</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-box">
                                <div class="metric-value">${comparison.statistics.days_range}</div>
                                <div class="metric-label">Days Range</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-box">
                                <div class="metric-value">${Object.keys(comparison.results).length}</div>
                                <div class="metric-label">Conventions</div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-3 mb-4" style="background:#f8fafc;">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color:#0f172a;font-size:.875rem;">Visual Comparison</h6>
                            <canvas id="comparisonChart" height="80"></canvas>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="exportComparison('pdf')">
                                <i class="bi bi-file-pdf me-1"></i> Export PDF
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-gold w-100" onclick="exportComparison('excel')">
                                <i class="bi bi-file-excel me-1"></i> Export Excel
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-gold w-100" onclick="exportComparison('csv')">
                                <i class="bi bi-file-text me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        resultsDiv.innerHTML = html;
        resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        createComparisonChart(comparison.results);
        window.comparisonData = comparison;
    }

    function createComparisonChart(results) {
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        const labels = Object.keys(results);
        const daysData = Object.values(results).map(r => r.days);
        const factorData = Object.values(results).map(r => r.day_count_factor);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Days Calculated',
                    data: daysData,
                    backgroundColor: 'rgba(201, 162, 39, 0.8)',
                    borderColor: 'rgba(201, 162, 39, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            afterLabel: function(context) {
                                const factor = factorData[context.dataIndex];
                                return 'Factor: ' + factor.toFixed(10);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Days' }
                    },
                    x: {
                        ticks: { maxRotation: 45, minRotation: 45 }
                    }
                }
            }
        });
    }

    function exportComparison(format) {
        if (!window.comparisonData) {
            alert('No comparison data available');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/comparison/export/${format}`;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrfInput);

        const dataInput = document.createElement('input');
        dataInput.type = 'hidden';
        dataInput.name = 'comparison_data';
        dataInput.value = JSON.stringify(window.comparisonData);
        form.appendChild(dataInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>
@endpush
