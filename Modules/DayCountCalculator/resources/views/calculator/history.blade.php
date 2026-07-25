@extends('layouts.platform')

@section('title', 'Calculation History')

@section('content')
<div class="mx-auto max-w-content px-4 py-10 sm:px-6">

    {{-- Page header --}}
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="inline-block rounded-full bg-brand-light px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand">Your Records</span>
            <h1 class="mt-3 text-2xl font-bold text-ink sm:text-3xl">Calculation <span class="text-gold">History</span></h1>
            <p class="mt-2 text-ink-faint">
                @auth
                    Your calculation history — {{ $calculations->count() }} total
                @else
                    Recent calculations from this session — {{ $calculations->count() }} total
                @endauth
            </p>
        </div>
        <a href="{{ route('calculator.index') }}" class="btn-primary hidden md:inline-flex">New Calculation</a>
    </div>

    @if($calculations->isEmpty())
        <div class="card p-10 text-center">
            <svg class="mx-auto h-12 w-12 text-ink-faint" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"/>
            </svg>
            <h2 class="mt-4 text-lg font-bold text-ink">No Calculations Yet</h2>
            <p class="mt-1 text-sm text-ink-faint">Your calculation history will appear here once you perform calculations.</p>
            <div class="mt-5">
                <a href="{{ route('calculator.index') }}" class="btn-primary">Get Started</a>
            </div>
        </div>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-line text-sm">
                    <thead class="bg-surface-muted">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-ink-faint">Convention</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-ink-faint">Start Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-ink-faint">End Date</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-ink-faint">Days</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-ink-faint">Factor</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-ink-faint">Interest</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-ink-faint">Date</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-ink-faint">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-white">
                        @foreach($calculations as $calculation)
                            <tr class="hover:bg-surface-muted">
                                <td class="px-4 py-3 font-semibold text-ink">{{ $calculation->convention_type }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-ink-soft">{{ $calculation->start_date->format('M d, Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-ink-soft">{{ $calculation->end_date->format('M d, Y') }}</td>
                                <td class="tabular px-4 py-3 text-right font-mono">
                                    <span class="rounded-full bg-gold/10 px-2.5 py-0.5 text-xs font-bold text-gold-dark">{{ $calculation->days_calculated }}</span>
                                </td>
                                <td class="tabular px-4 py-3 text-right font-mono text-ink">{{ number_format($calculation->day_count_factor, 6) }}</td>
                                <td class="tabular px-4 py-3 text-right font-mono text-ink">
                                    @if($calculation->interest_amount)
                                        ${{ number_format($calculation->interest_amount, 2) }}
                                    @else
                                        <span class="text-ink-faint">—</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-ink-faint">{{ $calculation->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button"
                                                class="rounded-lg border border-line p-2 text-ink-soft transition hover:border-brand hover:text-brand"
                                                onclick='viewCalculation(@json($calculation))'
                                                title="View Details">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.4 12S5.9 5.25 12 5.25 21.6 12 21.6 12 18.1 18.75 12 18.75 2.4 12 2.4 12Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                        @auth
                                            @if(!$calculation->savedCalculations()->where('user_id', auth()->id())->exists())
                                                <button type="button"
                                                        class="rounded-lg border border-line p-2 text-ink-soft transition hover:border-brand hover:text-brand"
                                                        data-save-btn="{{ $calculation->id }}"
                                                        onclick="saveCalculation({{ $calculation->id }})"
                                                        title="Save">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12a1 1 0 0 1 1 1v16l-7-4-7 4V5a1 1 0 0 1 1-1Z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @guest
            <div class="mt-6 rounded-lg border border-gold/30 bg-gold/5 px-4 py-3 text-sm text-ink-soft">
                <strong class="text-ink">Tip:</strong>
                <a href="{{ route('register') }}" class="font-semibold text-gold-dark underline decoration-gold/40 hover:text-gold">Create an account</a>
                to save your calculations permanently and access them from any device.
            </div>
        @endguest
    @endif
</div>

{{-- Calculation Details Modal (Alpine) --}}
<div x-data="{ open: false }"
     x-on:open-details-modal.window="open = true"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-ink/50" @click="open = false"></div>
    <div class="card relative flex max-h-[85vh] w-full max-w-2xl flex-col overflow-hidden" @keydown.escape.window="open = false">
        <div class="flex items-center justify-between border-b border-line bg-brand px-5 py-4">
            <h2 class="text-base font-bold text-white">Calculation Details</h2>
            <button type="button" class="rounded-lg p-1 text-white/70 hover:text-white" @click="open = false" aria-label="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <div class="overflow-y-auto p-5" id="calculationDetailsContent"></div>
    </div>
</div>

{{-- Save Calculation Modal (Alpine) --}}
@auth
<div x-data="{ open: false }"
     x-on:open-save-modal.window="open = true"
     x-on:close-save-modal.window="open = false"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-ink/50" @click="open = false"></div>
    <div class="card relative w-full max-w-md overflow-hidden" @keydown.escape.window="open = false">
        <div class="flex items-center justify-between border-b border-line bg-brand px-5 py-4">
            <h2 class="text-base font-bold text-white">Save Calculation</h2>
            <button type="button" class="rounded-lg p-1 text-white/70 hover:text-white" @click="open = false" aria-label="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <form id="saveCalculationForm">
            <input type="hidden" id="save_calculation_id" name="calculation_id">
            <div class="space-y-4 p-5">
                <div>
                    <label for="calculation_name" class="field-label">Name</label>
                    <input type="text" class="field-input" id="calculation_name" name="name" required
                           placeholder="e.g. Q1 2024 Bond Calculation">
                </div>
                <div>
                    <label for="calculation_notes" class="field-label">Notes <span class="font-normal text-ink-faint">(Optional)</span></label>
                    <textarea class="field-input" id="calculation_notes" name="notes" rows="3"
                              placeholder="Add any notes about this calculation..."></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink" for="is_favorite">
                    <input type="checkbox" id="is_favorite" name="is_favorite" value="1"
                           class="h-4 w-4 rounded border-line text-brand focus:ring-brand">
                    <span><span class="text-gold">★</span> Mark as favourite</span>
                </label>
            </div>
            <div class="flex justify-end gap-2 px-5 pb-5">
                <button type="button" class="btn-secondary" @click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save Calculation</button>
            </div>
        </form>
    </div>
</div>
@endauth

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
<script>
    function notify(msg, ok = true) {
        window.dispatchEvent(new CustomEvent('toast', { detail: { msg, ok } }));
    }

    function viewCalculation(data) {
        const content = document.getElementById('calculationDetailsContent');

        let html = `
            <div class="mb-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-ink">Convention Type</h3>
                <p class="mt-1 text-sm text-ink-soft">${data.convention_type}</p>
            </div>
            <div class="mb-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ink">Start Date</h3>
                    <p class="mt-1 text-sm text-ink-soft" dir="ltr">${data.start_date}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ink">End Date</h3>
                    <p class="mt-1 text-sm text-ink-soft" dir="ltr">${data.end_date}</p>
                </div>
            </div>
            <div class="mb-6 grid gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                    <div class="tabular font-mono text-xl font-bold text-ink" dir="ltr">${data.days_calculated}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Days</div>
                </div>
                <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                    <div class="tabular font-mono text-base font-bold text-ink" dir="ltr">${parseFloat(data.day_count_factor).toFixed(10)}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Day Count Factor</div>
                </div>
                <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                    <div class="tabular font-mono text-xl font-bold text-ink" dir="ltr">${data.interest_amount ? '$' + parseFloat(data.interest_amount).toFixed(2) : 'N/A'}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Interest Amount</div>
                </div>
            </div>
        `;

        if (data.calculation_steps && data.calculation_steps.length > 0) {
            html += `<h3 class="mb-3 text-sm font-bold text-ink">Calculation Steps</h3><div class="space-y-2">`;
            data.calculation_steps.forEach((step, i) => {
                html += `
                    <details class="overflow-hidden rounded-lg border border-line" ${i === 0 ? 'open' : ''}>
                        <summary class="flex cursor-pointer items-center gap-2 bg-surface-muted px-4 py-3 text-sm font-semibold text-ink hover:bg-brand-light">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-light text-xs font-bold text-brand">${i + 1}</span>
                            <span>${step.title}</span>
                            ${step.applied ? '<span class="rounded-full border border-gold/25 bg-gold/10 px-2 py-0.5 text-[0.7rem] font-bold text-gold-dark">Applied</span>' : ''}
                        </summary>
                        <div class="border-t border-line px-4 py-3">
                            <p class="mb-2 text-sm text-ink-soft">${step.description}</p>
                            <div class="rounded-lg border-l-4 border-gold bg-ink px-3.5 py-2.5 font-mono text-xs leading-relaxed text-gold" dir="ltr">${step.formula}</div>
                        </div>
                    </details>
                `;
            });
            html += '</div>';
        }

        content.innerHTML = html;
        window.dispatchEvent(new CustomEvent('open-details-modal'));
    }

    @auth
    function saveCalculation(calculationId) {
        document.getElementById('save_calculation_id').value = calculationId;
        document.getElementById('calculation_name').value = '';
        document.getElementById('calculation_notes').value = '';
        document.getElementById('is_favorite').checked = false;

        window.dispatchEvent(new CustomEvent('open-save-modal'));
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('saveCalculationForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const calculationId = document.getElementById('save_calculation_id').value;
            const formData = new FormData(this);

            fetch(`/calculator/save/${calculationId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('close-save-modal'));
                    notify('Calculation saved successfully!');
                    // The calculation is now saved — remove its Save button instead of reloading.
                    document.querySelector(`[data-save-btn="${calculationId}"]`)?.remove();
                } else {
                    notify('Error: ' + (data.message || 'Unknown error'), false);
                }
            })
            .catch(() => notify('An error occurred while saving the calculation.', false));
        });
    });
    @endauth
</script>
@endpush
