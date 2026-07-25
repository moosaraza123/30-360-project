@if($calculations->isEmpty())
    <div class="card p-8 text-center">
        <p class="text-sm text-ink-faint">No calculations in this category.</p>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($calculations as $saved)
            <div class="card flex h-full flex-col p-4 transition hover:shadow-card-hover"
                 data-saved-id="{{ $saved->id }}" data-favorite="{{ $saved->is_favorite ? '1' : '0' }}">
                <div class="mb-2 flex items-start justify-between gap-2">
                    <h3 class="text-sm font-bold text-ink">{{ $saved->name }}</h3>
                    <button type="button"
                            class="shrink-0 rounded p-0.5 text-lg leading-none {{ $saved->is_favorite ? 'text-gold' : 'text-ink-faint hover:text-gold' }}"
                            onclick="toggleFavorite({{ $saved->id }})"
                            title="{{ $saved->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                        {{ $saved->is_favorite ? '★' : '☆' }}
                    </button>
                </div>

                @if($saved->notes)
                    <p class="mb-3 text-xs leading-relaxed text-ink-faint">{{ Str::limit($saved->notes, 80) }}</p>
                @endif

                <div class="mb-2">
                    <span class="inline-block rounded-full bg-brand-light px-2.5 py-0.5 text-xs font-semibold text-brand">{{ $saved->calculation->convention_type }}</span>
                </div>

                <div class="mb-3 text-xs text-ink-faint" dir="ltr">
                    {{ $saved->calculation->start_date->format('M d, Y') }} —
                    {{ $saved->calculation->end_date->format('M d, Y') }}
                </div>

                <div class="mb-3 grid grid-cols-2 gap-2">
                    <div class="rounded-lg border border-line bg-surface-muted p-2.5 text-center">
                        <div class="tabular font-mono text-base font-bold text-ink" dir="ltr">{{ $saved->calculation->days_calculated }}</div>
                        <div class="text-[0.7rem] font-semibold uppercase tracking-wide text-ink-faint">Days</div>
                    </div>
                    <div class="rounded-lg border border-line bg-surface-muted p-2.5 text-center">
                        <div class="tabular font-mono text-sm font-bold text-ink" dir="ltr">{{ number_format($saved->calculation->day_count_factor, 4) }}</div>
                        <div class="text-[0.7rem] font-semibold uppercase tracking-wide text-ink-faint">Factor</div>
                    </div>
                </div>

                @if($saved->calculation->interest_amount)
                    <div class="mb-3 rounded-lg border border-gold/25 bg-gold/5 px-3 py-2">
                        <div class="text-[0.7rem] font-semibold uppercase tracking-wide text-gold-dark">Interest Amount</div>
                        <div class="tabular font-mono text-sm font-bold text-gold-dark" dir="ltr">${{ number_format($saved->calculation->interest_amount, 2) }}</div>
                    </div>
                @endif

                <div class="mb-3 text-xs text-ink-faint">Saved {{ $saved->created_at->diffForHumans() }}</div>

                <div class="mt-auto grid grid-cols-3 gap-1.5">
                    <button type="button"
                            class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:border-brand hover:text-brand"
                            onclick="viewCalculation({{ $saved->calculation->id }})"
                            title="View Details">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.4 12S5.9 5.25 12 5.25 21.6 12 21.6 12 18.1 18.75 12 18.75 2.4 12 2.4 12Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    <button type="button"
                            class="inline-flex items-center justify-center rounded-lg border border-line p-2 text-ink-soft transition hover:border-brand hover:text-brand"
                            onclick="editSavedCalculation({{ $saved->id }}, '{{ addslashes($saved->name) }}', '{{ addslashes($saved->notes ?? '') }}', {{ $saved->is_favorite ? 'true' : 'false' }})"
                            title="Edit">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.9 4.3 2.8 2.8m-1.6-4a2 2 0 0 1 2.8 2.8L7.5 19.3 3 21l1.7-4.5L18.1 3.1Z"/>
                        </svg>
                    </button>
                    <button type="button"
                            class="inline-flex items-center justify-center rounded-lg border border-red-200 p-2 text-red-500 transition hover:border-red-400 hover:bg-red-50"
                            onclick="deleteSavedCalculation({{ $saved->id }}, '{{ addslashes($saved->name) }}')"
                            title="Delete">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2m3 0-1 13a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1L6 7m4 4v6m4-6v6"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif
