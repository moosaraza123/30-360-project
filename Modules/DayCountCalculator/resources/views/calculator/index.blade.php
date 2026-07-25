@extends('layouts.platform')

@section('title', 'Day Count Calculator')
@section('meta_description', 'Calculate accrued interest and day count factors for bonds, loans and derivatives using 9 professional day count conventions with step-by-step breakdowns.')

@section('content')
<div class="mx-auto max-w-content px-4 py-10 sm:px-6">

    {{-- Page header --}}
    <div class="mb-8">
        <span class="inline-block rounded-full bg-brand-light px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand">Professional Financial Calculator</span>
        <h1 class="mt-3 text-2xl font-bold text-ink sm:text-3xl">Day Count <span class="text-gold">Convention</span> Calculator</h1>
        <p class="mt-2 text-ink-faint">Calculate accrued interest and day count factors for bonds, loans &amp; derivatives</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ── Calculator Form (Left) ──────────────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="card p-6 sm:p-8">
                <form id="calculatorForm" action="{{ route('calculator.calculate') }}" method="POST">
                    @csrf

                    {{-- Convention Selector --}}
                    <div class="mb-6">
                        <span class="mb-3 block text-xs font-bold uppercase tracking-wider text-ink">Select Day Count Convention</span>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($conventions as $convention)
                                <label class="block cursor-pointer" for="convention_{{ $loop->index }}">
                                    <input type="radio"
                                           class="peer sr-only"
                                           name="convention_type"
                                           id="convention_{{ $loop->index }}"
                                           value="{{ $convention['type'] }}"
                                           required
                                           data-convention-info="{{ json_encode($convention) }}">
                                    <span class="block rounded-lg border border-line bg-white px-4 py-3 transition hover:border-brand/60 peer-checked:border-brand peer-checked:bg-brand-light peer-checked:ring-1 peer-checked:ring-brand peer-focus-visible:ring-2 peer-focus-visible:ring-brand">
                                        <span class="block text-sm font-semibold text-ink">{{ $convention['name'] }}</span>
                                        <span class="block text-xs text-ink-faint">{{ $convention['alias'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('convention_type')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date Inputs --}}
                    <div class="mb-6 grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="start_date" class="field-label">Start Date <span class="text-red-600">*</span></label>
                            <input type="date" id="start_date" name="start_date"
                                   value="{{ old('start_date') }}" required
                                   class="field-input @error('start_date') border-red-400 @enderror" dir="ltr">
                            @error('start_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="field-label">End Date <span class="text-red-600">*</span></label>
                            <input type="date" id="end_date" name="end_date"
                                   value="{{ old('end_date') }}" required
                                   class="field-input @error('end_date') border-red-400 @enderror" dir="ltr">
                            @error('end_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
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
                                <label for="interest_rate" class="field-label">Annual Interest Rate (%)</label>
                                <input type="number" id="interest_rate" name="interest_rate" step="0.001" min="0" max="100"
                                       placeholder="e.g. 5.5" value="{{ old('interest_rate') }}"
                                       class="field-input @error('interest_rate') border-red-400 @enderror" dir="ltr">
                                @error('interest_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Convention Options --}}
                    <div class="mb-6 rounded-lg border border-line bg-surface-muted p-4">
                        <h2 class="mb-3 text-xs font-bold uppercase tracking-wider text-ink">
                            Convention Options
                            <span class="ml-1 font-normal normal-case tracking-normal text-ink-faint">Optional</span>
                        </h2>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3" for="apply_eom_adjustment">
                                <input type="checkbox" value="1" id="apply_eom_adjustment" name="apply_eom_adjustment"
                                       @checked(old('apply_eom_adjustment'))
                                       class="mt-0.5 h-4 w-4 rounded border-line text-brand focus:ring-brand">
                                <span class="text-sm text-ink">
                                    Apply end-of-month (EOM) adjustment
                                    <span class="block text-xs text-ink-faint">30/360 US only: treats end-of-February dates as the 30th (NASD EOM rule)</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-3" for="end_date_is_maturity">
                                <input type="checkbox" value="1" id="end_date_is_maturity" name="end_date_is_maturity"
                                       @checked(old('end_date_is_maturity'))
                                       class="mt-0.5 h-4 w-4 rounded border-line text-brand focus:ring-brand">
                                <span class="text-sm text-ink">
                                    End date is the maturity / termination date
                                    <span class="block text-xs text-ink-faint">30E/360 ISDA only: a maturity date falling on the last day of February is not rolled to the 30th</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-primary w-full" id="calculateBtn">
                        <svg id="calculateSpinner" class="hidden h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
                        </svg>
                        Calculate
                    </button>
                </form>
            </div>

            {{-- Results --}}
            @if(session('result'))
                <div class="mt-6">
                    @include('daycountcalculator::calculator.partials.results', [
                        'result' => session('result'),
                        'calculationId' => session('calculation_id')
                    ])
                </div>
            @endif
        </div>

        {{-- ── Sidebar (Right) ─────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Convention Info Card (shown after selection) --}}
            <div class="card hidden overflow-hidden" id="conventionInfoCard">
                <div class="flex items-center gap-3 border-b border-line bg-brand px-4 py-3 text-white">
                    <div class="text-xl" aria-hidden="true">📐</div>
                    <div>
                        <div class="text-sm font-bold" id="conventionName">Convention Details</div>
                        <div class="text-xs text-white/60" id="conventionAlias"></div>
                    </div>
                </div>
                <div class="p-4">
                    <p class="mb-4 text-sm leading-relaxed text-ink-faint" id="conventionDescription"></p>
                    <div class="mb-4">
                        <div class="mb-1 text-xs font-bold uppercase tracking-wider text-ink">Formula</div>
                        <div class="rounded-lg border-l-4 border-gold bg-ink px-3 py-2 font-mono text-xs leading-relaxed text-gold" id="conventionFormula" dir="ltr"></div>
                    </div>
                    <div class="mb-4">
                        <div class="mb-2 text-xs font-bold uppercase tracking-wider text-ink">Common Uses</div>
                        <ul class="list-disc space-y-1 pl-5 text-sm text-ink-soft" id="conventionUseCases"></ul>
                    </div>
                    <a href="#" class="btn-secondary w-full" id="learnMoreLink">Learn More</a>
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="card p-4">
                <div class="mb-2 text-sm font-bold text-ink">💡 Quick Tips</div>
                <ul class="list-disc space-y-1.5 pl-5 text-sm text-ink-soft">
                    <li>Select a convention to see its description and formula</li>
                    <li>Dates use YYYY-MM-DD format or the native date picker</li>
                    <li>Principal &amp; rate are optional — basic calculation works without them</li>
                    <li>Results include a full step-by-step breakdown</li>
                    <li>Use the Compare tool to run all conventions side-by-side</li>
                </ul>
            </div>

            {{-- Recent Calculations --}}
            @if($recentCalculations->count() > 0)
                <div class="card overflow-hidden">
                    <div class="border-b border-line bg-brand px-4 py-3">
                        <h2 class="text-sm font-bold text-white">Recent Calculations</h2>
                    </div>
                    <div class="divide-y divide-line">
                        @foreach($recentCalculations as $calc)
                            <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                                <div>
                                    <div class="text-sm font-semibold text-ink">{{ $calc->convention_type }}</div>
                                    <div class="text-xs text-ink-faint" dir="ltr">
                                        {{ $calc->start_date->format('M d, Y') }} → {{ $calc->end_date->format('M d, Y') }}
                                    </div>
                                </div>
                                <span class="tabular shrink-0 rounded-full bg-gold/10 px-2.5 py-0.5 text-xs font-bold text-gold-dark">{{ $calc->days_calculated }}d</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-line p-3">
                        <a href="{{ route('calculator.history') }}" class="btn-secondary w-full text-xs">View All History</a>
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

            document.getElementById('learnMoreLink').href = `/calculator/learn/${info.slug}`;
            card.classList.remove('hidden');
        });
    });

    document.getElementById('calculatorForm').addEventListener('submit', function() {
        const btn = document.getElementById('calculateBtn');
        btn.disabled = true;
        document.getElementById('calculateSpinner').classList.remove('hidden');
    });
</script>
@endpush
