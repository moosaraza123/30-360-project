<div class="card overflow-hidden" id="resultsCard">
    {{-- Header --}}
    <div class="flex items-center gap-3 border-b border-line bg-brand px-5 py-4 text-white">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-lg text-gold" aria-hidden="true">✦</div>
        <div>
            <h2 class="text-base font-bold">Calculation Results</h2>
            <div class="text-xs text-white/60">{{ $result->conventionType }}</div>
        </div>
        <div class="ml-auto">
            <span class="tabular rounded-full bg-gold/20 px-3 py-1 text-xs font-bold text-gold">{{ $result->days }} days</span>
        </div>
    </div>

    <div class="p-5 sm:p-6">
        {{-- Metric Boxes --}}
        <div class="mb-6 grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                <div class="tabular font-mono text-2xl font-bold text-ink" dir="ltr">{{ $result->days }}</div>
                <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Days Calculated</div>
            </div>
            <div class="rounded-lg border border-line bg-surface-muted p-4 text-center">
                <div class="tabular font-mono text-xl font-bold text-ink" dir="ltr">{{ $result->getFormattedFactor(6) }}</div>
                <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Day Count Factor</div>
            </div>
            @if($result->interestAmount !== null)
                <div class="rounded-lg border border-gold/30 bg-gold/5 p-4 text-center">
                    <div class="tabular font-mono text-2xl font-bold text-gold-dark" dir="ltr">${{ number_format($result->interestAmount, 2) }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-ink-faint">Interest Amount</div>
                </div>
            @endif
        </div>

        {{-- Calculation Steps (Alpine collapse) --}}
        <div class="overflow-hidden rounded-lg border border-line" x-data="{ open: true }">
            <button type="button"
                    class="flex w-full items-center justify-between gap-2 bg-surface-muted px-4 py-3 text-left text-sm font-semibold text-ink hover:bg-brand-light print:hidden"
                    @click="open = !open">
                <span>View Step-by-Step Breakdown</span>
                <svg class="h-4 w-4 shrink-0 text-ink-faint transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                </svg>
            </button>
            <div x-show="open" class="border-t border-line px-4 pt-4 print:!block">
                @foreach($result->steps as $index => $step)
                    <div class="mb-4 flex items-start gap-3 pb-4 {{ !$loop->last ? 'border-b border-line' : '' }}">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-light text-xs font-bold text-brand">{{ $index + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <div class="mb-1 flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold text-ink">{{ $step['title'] }}</span>
                                @if($step['applied'])
                                    <span class="rounded-full border border-gold/25 bg-gold/10 px-2 py-0.5 text-[0.7rem] font-bold text-gold-dark">Applied</span>
                                @else
                                    <span class="rounded-full bg-surface-muted px-2 py-0.5 text-[0.7rem] font-semibold text-ink-faint">Skipped</span>
                                @endif
                            </div>
                            <p class="mb-2 text-sm leading-relaxed text-ink-faint">{{ $step['description'] }}</p>
                            <div class="rounded-lg border-l-4 border-gold bg-ink px-3.5 py-2.5 font-mono text-xs leading-relaxed text-gold" dir="ltr">
                                {!! nl2br(e($step['formula'])) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Guest signup CTA --}}
        @guest
        <div class="mt-6 flex items-center gap-4 rounded-lg border border-gold/30 bg-gold/5 px-4 py-3.5 print:hidden">
            <svg class="h-6 w-6 shrink-0 text-gold" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 3a2 2 0 0 0-2 2v16l8-4 8 4V5a2 2 0 0 0-2-2H6zm6 4 1.2 2.4 2.7.4-1.95 1.9.45 2.7L12 13.1l-2.4 1.3.45-2.7L8.1 9.8l2.7-.4L12 7z"/>
            </svg>
            <div class="flex-1">
                <div class="text-sm font-semibold text-ink">Sign up to save your calculations</div>
                <div class="text-xs text-ink-faint">Create a free account to save, name, and revisit any calculation.</div>
            </div>
            <a href="{{ route('register') }}" class="btn-primary shrink-0 px-4 py-2 text-xs">Sign Up Free</a>
        </div>
        @endguest

        {{-- Action Buttons --}}
        <div class="mt-6 grid grid-cols-2 gap-2 md:grid-cols-4 print:hidden">
            <button type="button" class="btn-secondary text-xs" onclick="window.print()">Print</button>
            <a href="{{ route('comparison.index') }}" class="btn-secondary text-xs">Compare</a>
            @auth
                <button type="button" class="btn-secondary text-xs"
                        onclick="window.dispatchEvent(new CustomEvent('open-save-modal'))">Save</button>
            @endauth
            <button type="button" class="btn-secondary text-xs" onclick="shareCalculation()">Share</button>
        </div>
    </div>
</div>

{{-- Save Modal (Alpine) --}}
@auth
<div x-data="{ open: false }"
     x-on:open-save-modal.window="open = true"
     x-on:close-save-modal.window="open = false"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 print:hidden">
    <div class="absolute inset-0 bg-ink/50" @click="open = false"></div>
    <div class="card relative w-full max-w-md overflow-hidden" @keydown.escape.window="open = false">
        <div class="flex items-center justify-between border-b border-line bg-brand px-5 py-4">
            <h2 class="text-base font-bold text-white">Save Calculation</h2>
            <button type="button" class="rounded-lg p-1 text-white/70 hover:text-white" @click="open = false" aria-label="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <form id="saveCalculationForm" action="{{ route('calculator.save', $calculationId ?? 0) }}" method="POST">
            @csrf
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
     class="fixed bottom-6 left-1/2 z-[60] -translate-x-1/2 rounded-lg px-4 py-3 text-sm font-semibold text-white shadow-card-hover print:hidden"
     :class="ok ? 'bg-ink' : 'bg-red-600'">
    <span x-text="msg"></span>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resultsCard = document.getElementById('resultsCard');
        if (resultsCard) {
            resultsCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    function notify(msg, ok = true) {
        window.dispatchEvent(new CustomEvent('toast', { detail: { msg, ok } }));
    }

    function shareCalculation() {
        const url = window.location.href;
        if (navigator.share) {
            navigator.share({ title: 'Day Count Calculation', url }).catch(() => {});
        } else {
            navigator.clipboard.writeText(url).then(() => notify('Link copied to clipboard!'));
        }
    }

    @auth
    document.getElementById('saveCalculationForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
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
                this.reset();
            } else {
                notify('Error: ' + (data.message || 'Unknown error'), false);
            }
        })
        .catch(() => notify('An error occurred while saving.', false));
    });
    @endauth
</script>
@endpush
