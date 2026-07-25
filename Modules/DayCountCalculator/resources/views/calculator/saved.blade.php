@extends('layouts.platform')

@section('title', 'Saved Calculations')

@section('content')
<div class="mx-auto max-w-content px-4 py-10 sm:px-6">

    {{-- Page header --}}
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="inline-block rounded-full bg-brand-light px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand">Your Collection</span>
            <h1 class="mt-3 text-2xl font-bold text-ink sm:text-3xl">Saved <span class="text-gold">Calculations</span></h1>
            <p class="mt-2 text-ink-faint">Your saved calculations — {{ $savedCalculations->count() }} total</p>
        </div>
        <a href="{{ route('calculator.index') }}" class="btn-primary hidden md:inline-flex">New Calculation</a>
    </div>

    @if($savedCalculations->isEmpty())
        <div class="card p-10 text-center">
            <svg class="mx-auto h-12 w-12 text-ink-faint" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h12a1 1 0 0 1 1 1v16l-7-4-7 4V5a1 1 0 0 1 1-1Z"/>
            </svg>
            <h2 class="mt-4 text-lg font-bold text-ink">No Saved Calculations</h2>
            <p class="mt-1 text-sm text-ink-faint">Save your important calculations to access them later.</p>
            <div class="mt-5">
                <a href="{{ route('calculator.index') }}" class="btn-primary">Create a Calculation</a>
            </div>
        </div>
    @else
        <div x-data="{ tab: 'all' }">
            {{-- Filter Tabs --}}
            <div class="mb-6 flex flex-wrap gap-2">
                <button type="button" @click="tab = 'all'"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition"
                        :class="tab === 'all' ? 'bg-brand text-white' : 'bg-white text-ink-soft border border-line hover:border-brand hover:text-brand'">
                    All
                    <span class="tabular rounded-full bg-gold px-2 py-0.5 text-xs font-bold text-ink" id="countAll">{{ $savedCalculations->count() }}</span>
                </button>
                <button type="button" @click="tab = 'favorites'"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition"
                        :class="tab === 'favorites' ? 'bg-brand text-white' : 'bg-white text-ink-soft border border-line hover:border-brand hover:text-brand'">
                    <span class="text-gold">★</span> Favorites
                    <span class="tabular rounded-full bg-gold px-2 py-0.5 text-xs font-bold text-ink" id="countFav">{{ $savedCalculations->where('is_favorite', true)->count() }}</span>
                </button>
            </div>

            {{-- All Calculations Tab --}}
            <div x-show="tab === 'all'">
                @include('daycountcalculator::calculator.partials.saved-list', ['calculations' => $savedCalculations])
            </div>

            {{-- Favorites Tab --}}
            <div x-show="tab === 'favorites'" x-cloak>
                @include('daycountcalculator::calculator.partials.saved-list', ['calculations' => $savedCalculations->where('is_favorite', true)])
            </div>
        </div>
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
        <div class="overflow-y-auto p-5" id="calculationDetailsContent">
            <p class="py-4 text-center text-sm text-ink-faint">Loading…</p>
        </div>
    </div>
</div>

{{-- Edit Saved Calculation Modal (Alpine) --}}
<div x-data="{ open: false }"
     x-on:open-edit-modal.window="open = true"
     x-on:close-edit-modal.window="open = false"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-ink/50" @click="open = false"></div>
    <div class="card relative w-full max-w-md overflow-hidden" @keydown.escape.window="open = false">
        <div class="flex items-center justify-between border-b border-line bg-brand px-5 py-4">
            <h2 class="text-base font-bold text-white">Edit Saved Calculation</h2>
            <button type="button" class="rounded-lg p-1 text-white/70 hover:text-white" @click="open = false" aria-label="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
        <form id="editSavedCalculationForm">
            <input type="hidden" id="edit_saved_id" name="saved_id">
            <div class="space-y-4 p-5">
                <div>
                    <label for="edit_name" class="field-label">Name</label>
                    <input type="text" class="field-input" id="edit_name" name="name" required>
                </div>
                <div>
                    <label for="edit_notes" class="field-label">Notes</label>
                    <textarea class="field-input" id="edit_notes" name="notes" rows="3"></textarea>
                </div>
                <label class="flex items-center gap-2 text-sm text-ink" for="edit_is_favorite">
                    <input type="checkbox" id="edit_is_favorite" name="is_favorite"
                           class="h-4 w-4 rounded border-line text-brand focus:ring-brand">
                    <span><span class="text-gold">★</span> Mark as favourite</span>
                </label>
            </div>
            <div class="flex justify-end gap-2 px-5 pb-5">
                <button type="button" class="btn-secondary" @click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal (Alpine) --}}
<div x-data="{ open: false, id: null, name: '' }"
     x-on:confirm-delete.window="id = $event.detail.id; name = $event.detail.name; open = true"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-ink/50" @click="open = false"></div>
    <div class="card relative w-full max-w-sm overflow-hidden p-6" @keydown.escape.window="open = false">
        <h2 class="text-base font-bold text-ink">Delete saved calculation?</h2>
        <p class="mt-2 text-sm text-ink-soft">
            "<span x-text="name" class="font-semibold text-ink"></span>" will be permanently removed.
        </p>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" class="btn-secondary" @click="open = false">Cancel</button>
            <button type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
                    @click="open = false; performDelete(id)">
                Delete
            </button>
        </div>
    </div>
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
<script>
    function notify(msg, ok = true) {
        window.dispatchEvent(new CustomEvent('toast', { detail: { msg, ok } }));
    }

    function viewCalculation(calculationId) {
        fetch(`/api/calculations/${calculationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
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
        })
        .catch(error => {
            console.error('Error:', error);
            notify('Error loading calculation details', false);
        });
    }

    function editSavedCalculation(savedId, name, notes, isFavorite) {
        document.getElementById('edit_saved_id').value = savedId;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_notes').value = notes || '';
        document.getElementById('edit_is_favorite').checked = isFavorite;

        window.dispatchEvent(new CustomEvent('open-edit-modal'));
    }

    function toggleFavorite(savedId) {
        fetch(`/api/saved-calculations/${savedId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tab membership (All vs Favorites) is server-rendered, so refresh.
                location.reload();
            } else {
                notify('Error toggling favorite', false);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteSavedCalculation(savedId, name) {
        window.dispatchEvent(new CustomEvent('confirm-delete', { detail: { id: savedId, name: name } }));
    }

    function performDelete(savedId) {
        fetch(`/api/saved-calculations/${savedId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the card(s) from both tab panes and update the counts.
                const cards = document.querySelectorAll(`[data-saved-id="${savedId}"]`);
                const wasFavorite = cards.length && cards[0].dataset.favorite === '1';
                cards.forEach(el => el.remove());

                const countAll = document.getElementById('countAll');
                if (countAll) countAll.textContent = Math.max(0, parseInt(countAll.textContent, 10) - 1);
                if (wasFavorite) {
                    const countFav = document.getElementById('countFav');
                    if (countFav) countFav.textContent = Math.max(0, parseInt(countFav.textContent, 10) - 1);
                }

                notify('Calculation deleted.');
            } else {
                notify('Error deleting calculation', false);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Edit form submission
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('editSavedCalculationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const savedId = document.getElementById('edit_saved_id').value;
            const formData = new FormData(this);

            fetch(`/api/saved-calculations/${savedId}`, {
                method: 'PUT',
                body: JSON.stringify({
                    name: formData.get('name'),
                    notes: formData.get('notes'),
                    is_favorite: formData.get('is_favorite') === 'on'
                }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Name/notes/favorite state are server-rendered in both tabs, so refresh.
                    location.reload();
                } else {
                    notify('Error updating calculation', false);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endpush
