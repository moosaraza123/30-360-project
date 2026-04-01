@extends('daycountcalculator::layouts.master')

@section('title', 'Day Count Calculator')

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <div class="hero-badge">Professional Financial Calculator</div>
        <h1 class="mb-1">Day Count <span class="gold-accent">Convention</span> Calculator</h1>
        <p class="lead">Calculate accrued interest and day count factors for bonds, loans &amp; derivatives</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">

        {{-- ── Calculator Form (Left) ──────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">

                    <form id="calculatorForm" action="{{ route('calculator.calculate') }}" method="POST">
                        @csrf

                        {{-- Convention Selector --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-navy mb-3" style="font-size:.9rem;text-transform:uppercase;letter-spacing:.05em;color:#0f172a;">
                                Select Day Count Convention
                            </label>
                            <div class="row g-2 convention-selector">
                                @foreach($conventions as $convention)
                                    <div class="col-md-6">
                                        <input type="radio"
                                               class="btn-check"
                                               name="convention_type"
                                               id="convention_{{ $loop->index }}"
                                               value="{{ $convention['type'] }}"
                                               required
                                               data-convention-info="{{ json_encode($convention) }}">
                                        <label class="btn convention-card w-100 text-start position-relative" for="convention_{{ $loop->index }}">
                                            <div class="convention-name">{{ $convention['name'] }}</div>
                                            <div class="convention-alias">{{ $convention['alias'] }}</div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('convention_type')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Date Inputs --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold" style="font-size:.875rem;color:#0f172a;">
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
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold" style="font-size:.875rem;color:#0f172a;">
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
                                           placeholder="e.g. 1,000,000"
                                           value="{{ old('principal') }}"
                                           style="border-color:#e2e8f0;border-radius:.5rem;">
                                    @error('principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="interest_rate" class="form-label" style="font-size:.875rem;">Annual Interest Rate (%)</label>
                                    <input type="number" class="form-control @error('interest_rate') is-invalid @enderror"
                                           id="interest_rate" name="interest_rate" step="0.001" min="0" max="100"
                                           placeholder="e.g. 5.5"
                                           value="{{ old('interest_rate') }}"
                                           style="border-color:#e2e8f0;border-radius:.5rem;">
                                    @error('interest_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gold btn-lg" id="calculateBtn">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                <i class="bi bi-calculator me-2"></i>Calculate
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Results --}}
            @if(session('result'))
                <div class="mt-4">
                    @include('daycountcalculator::calculator.partials.results', [
                        'result' => session('result'),
                        'calculationId' => session('calculation_id')
                    ])
                </div>
            @endif
        </div>

        {{-- ── Sidebar (Right) ─────────────────────────────────────── --}}
        <div class="col-lg-4">

            {{-- Convention Info Card (shown after selection) --}}
            <div class="card shadow-sm mb-3 convention-info-card" id="conventionInfoCard" style="display:none;">
                <div class="card-header card-header-navy d-flex align-items-center gap-2">
                    <div class="convention-icon">📐</div>
                    <div>
                        <div class="fw-bold" id="conventionName" style="font-size:.95rem;">Convention Details</div>
                        <small style="color:rgba(255,255,255,.55);font-size:.75rem;" id="conventionAlias"></small>
                    </div>
                </div>
                <div class="card-body p-3">
                    <p class="text-muted small mb-3" id="conventionDescription" style="line-height:1.6;"></p>
                    <div class="mb-3">
                        <div class="fw-semibold mb-1" style="font-size:.8rem;color:#0f172a;text-transform:uppercase;letter-spacing:.05em;">Formula</div>
                        <div class="formula-block" id="conventionFormula"></div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-semibold mb-2" style="font-size:.8rem;color:#0f172a;text-transform:uppercase;letter-spacing:.05em;">Common Uses</div>
                        <ul class="use-case-list mb-0 ps-3" id="conventionUseCases"></ul>
                    </div>
                    <a href="#" class="btn btn-outline-gold btn-sm w-100" id="learnMoreLink">
                        <i class="bi bi-book me-1"></i>Learn More
                    </a>
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="card shadow-sm mb-3 tips-card">
                <div class="card-body p-3">
                    <div class="tips-title fw-bold mb-2">💡 Quick Tips</div>
                    <ul class="mb-0 ps-3">
                        <li class="mb-1">Select a convention to see its description and formula</li>
                        <li class="mb-1">Dates use YYYY-MM-DD format or the native date picker</li>
                        <li class="mb-1">Principal &amp; rate are optional — basic calculation works without them</li>
                        <li class="mb-1">Results include a full step-by-step breakdown</li>
                        <li>Use the Compare tool to run all conventions side-by-side</li>
                    </ul>
                </div>
            </div>

            {{-- Recent Calculations --}}
            @if($recentCalculations->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header card-header-navy">
                        <h6 class="mb-0 fw-bold" style="font-size:.875rem;">Recent Calculations</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($recentCalculations as $calc)
                            <div class="px-3 py-2 border-bottom" style="border-color:#f1f5f9!important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold" style="font-size:.82rem;color:#0f172a;">{{ $calc->convention_type }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            {{ $calc->start_date->format('M d, Y') }} → {{ $calc->end_date->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <span class="badge badge-gold">{{ $calc->days_calculated }}d</span>
                                </div>
                            </div>
                        @endforeach
                        <div class="p-2">
                            <a href="{{ route('calculator.history') }}" class="btn btn-outline-gold btn-sm w-100" style="font-size:.8rem;">
                                View All History
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('input[name="convention_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const info = JSON.parse(this.dataset.conventionInfo);
            const card = document.getElementById('conventionInfoCard');

            document.getElementById('conventionName').textContent = info.name;
            document.getElementById('conventionAlias').textContent = info.alias;
            document.getElementById('conventionDescription').textContent = info.description;
            document.getElementById('conventionFormula').textContent = info.formula;

            const ul = document.getElementById('conventionUseCases');
            ul.innerHTML = '';
            info.use_cases.forEach(uc => {
                const li = document.createElement('li');
                li.textContent = uc;
                ul.appendChild(li);
            });

            document.getElementById('learnMoreLink').href = `/calculator/learn/${encodeURIComponent(info.type)}`;
            card.style.display = 'block';
        });
    });

    document.getElementById('calculatorForm').addEventListener('submit', function() {
        const btn = document.getElementById('calculateBtn');
        const spinner = btn.querySelector('.spinner-border');
        btn.disabled = true;
        spinner.classList.remove('d-none');
    });
</script>
@endpush
