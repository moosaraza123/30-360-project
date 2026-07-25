@extends('layouts.platform')

@section('title', 'Comparison Tool')
@section('meta_description', 'Compare all day count conventions side-by-side with identical dates — days, day count factors and interest amounts, with charts and PDF, Excel or CSV export.')

@section('content')
<div class="mx-auto max-w-content px-4 py-10 sm:px-6">

    {{-- Page header --}}
    <div class="mb-8">
        <span class="inline-block rounded-full bg-brand-light px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand">Side-by-Side Analysis</span>
        <h1 class="mt-3 text-2xl font-bold text-ink sm:text-3xl">Convention <span class="text-gold">Comparison</span> Tool</h1>
        <p class="mt-2 text-ink-faint">Compare multiple day count conventions with identical dates</p>
    </div>

    <div class="card mb-6 p-6 sm:p-8">
        <form id="comparisonForm" action="{{ route('comparison.calculate') }}" method="POST">
            @csrf

            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <div>
                    <label for="start_date" class="field-label">Start Date <span class="text-red-600">*</span></label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date') }}" required
                           class="field-input @error('start_date') border-red-400 @enderror" dir="ltr">
                    @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="end_date" class="field-label">End Date <span class="text-red-600">*</span></label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date') }}" required
                           class="field-input @error('end_date') border-red-400 @enderror" dir="ltr">
                    @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full" id="compareBtn">
                        <svg id="compareSpinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
                        </svg>
                        Compare All
                    </button>
                </div>
            </div>

            {{-- Optional Interest --}}
            <div class="mb-6 rounded-lg border border-line bg-surface-muted p-4">
                <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-ink">
                    Interest Calculation
                    <span class="ml-1 font-normal normal-case tracking-normal text-ink-faint">Optional</span>
                </h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="principal" class="field-label">Principal Amount ($)</label>
                        <input type="number" id="principal" name="principal" step="0.01" min="0"
                               placeholder="e.g. 1,000,000" value="{{ old('principal') }}"
                               class="field-input @error('principal') border-red-400 @enderror" dir="ltr">
                        @error('principal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="interest_rate" class="field-label">Interest Rate (%)</label>
                        <input type="number" id="interest_rate" name="interest_rate" step="0.001" min="0" max="100"
                               placeholder="e.g. 5.5" value="{{ old('interest_rate') }}"
                               class="field-input @error('interest_rate') border-red-400 @enderror" dir="ltr">
                        @error('interest_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Convention Selection --}}
            <div class="rounded-lg border border-line bg-surface-muted p-4">
                <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-ink">
                    Select Conventions
                    <span class="ml-1 font-normal normal-case tracking-normal text-ink-faint">Leave blank for all</span>
                </h2>
                <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($conventions as $convention)
                        <label class="flex items-center gap-2 text-sm text-ink" for="conv_{{ $loop->index }}">
                            <input type="checkbox" name="conventions[]" value="{{ $convention }}"
                                   id="conv_{{ $loop->index }}"
                                   class="h-4 w-4 rounded border-line text-brand focus:ring-brand">
                            {{ $convention }}
                        </label>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    {{-- Results Placeholder --}}
    <div id="comparisonResults"></div>
</div>

{{-- Toast --}}
<div x-data="{ show: false, msg: '', ok: true }"
     x-on:toast.window="msg = $event.detail.msg; ok = $event.detail.ok ?? true; show = true; clearTimeout($el._t); $el._t = setTimeout(() => show = false, 3500)"
     x-show="show" x-cloak x-transition.opacity
     class="fixed bottom-6 left-1/2 z-[60] -translate-x-1/2 rounded-lg px-4 py-3 text-sm font-semibold text-white shadow-card-hover"
     :class="ok ? 'bg-ink' : 'bg-red-600'">
    <span x-text="msg"></span>
</div>
@endsection

@push('scripts')
{{-- Module bundle: provides window.Chart (Chart.js) for the comparison chart --}}
@vite(['Modules/DayCountCalculator/resources/assets/js/app.js'])
<script>
    function notify(msg, ok = true) {
        window.dispatchEvent(new CustomEvent('toast', { detail: { msg, ok } }));
    }

    document.getElementById('comparisonForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('compareBtn');
        const spinner = document.getElementById('compareSpinner');
        btn.disabled = true;
        spinner.classList.remove('hidden');

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
                notify('Error: ' + (data.message || 'Unknown error'), false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            notify('An error occurred while processing the comparison.', false);
        })
        .finally(() => {
            btn.disabled = false;
            spinner.classList.add('hidden');
        });
    });

    function displayComparisonResults(comparison) {
        const resultsDiv = document.getElementById('comparisonResults');

        let html = `
            <div class="card overflow-hidden">
                <div class="border-b border-line bg-brand px-5 py-4">
                    <h2 class="text-base font-bold text-white">Comparison Results</h2>
                    <div class="text-xs text-white/60" dir="ltr">From ${comparison.start_date} to ${comparison.end_date}</div>
                </div>
                <div class="p-5 sm:p-6">
                    <div class="mb-6 overflow-x-auto rounded-lg border border-line">
                        <table class="min-w-full divide-y divide-line text-sm">
                            <thead class="bg-surface-muted">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-ink-faint">Convention</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-ink-faint">Days</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-ink-faint">Day Count Factor</th>
                                    ${comparison.principal ? '<th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-ink-faint">Interest Amount</th>' : ''}
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-white">
        `;

        const results = Object.values(comparison.results);
        results.forEach(result => {
            html += `
                <tr class="hover:bg-surface-muted">
                    <td class="px-4 py-3 font-semibold text-ink">${result.convention_type}</td>
                    <td class="tabular px-4 py-3 text-right font-mono"><span class="rounded-full bg-gold/10 px-2.5 py-0.5 text-xs font-bold text-gold-dark">${result.days}</span></td>
                    <td class="tabular px-4 py-3 text-right font-mono text-ink">${result.day_count_factor_formatted}</td>
                    ${result.interest_amount !== null ? `<td class="tabular px-4 py-3 text-right font-mono text-ink">${result.interest_amount_formatted}</td>` : ''}
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-4">
                        <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                            <div class="tabular font-mono text-2xl font-bold text-ink">${comparison.statistics.min_days}</div>
                            <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Min Days</div>
                        </div>
                        <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                            <div class="tabular font-mono text-2xl font-bold text-ink">${comparison.statistics.max_days}</div>
                            <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Max Days</div>
                        </div>
                        <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                            <div class="tabular font-mono text-2xl font-bold text-ink">${comparison.statistics.days_range}</div>
                            <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Days Range</div>
                        </div>
                        <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                            <div class="tabular font-mono text-2xl font-bold text-ink">${Object.keys(comparison.results).length}</div>
                            <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Conventions</div>
                        </div>
                    </div>

                    <div class="mb-6 rounded-lg border border-line bg-surface-muted p-4">
                        <h3 class="mb-3 text-sm font-bold text-ink">Visual Comparison</h3>
                        <canvas id="comparisonChart" height="80"></canvas>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-3">
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-5 py-2.5 text-sm font-semibold text-red-600 transition hover:border-red-400 hover:bg-red-50" onclick="exportComparison('pdf')">
                            Export PDF
                        </button>
                        <button type="button" class="btn-secondary" onclick="exportComparison('excel')">
                            Export Excel
                        </button>
                        <button type="button" class="btn-secondary" onclick="exportComparison('csv')">
                            Export CSV
                        </button>
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
            notify('No comparison data available', false);
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
