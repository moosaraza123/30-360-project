@if($calculations->isEmpty())
    <div class="card shadow-sm">
        <div class="card-body text-center py-4">
            <p class="text-muted mb-0">No calculations in this category.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($calculations as $saved)
            <div class="col-md-6 col-lg-4">
                <div class="card saved-calc-card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0" style="color:#0f172a;font-size:.9rem;">{{ $saved->name }}</h6>
                            <button type="button"
                                    class="btn btn-sm btn-link p-0 favorite-star {{ $saved->is_favorite ? 'is-favorite' : '' }}"
                                    onclick="toggleFavorite({{ $saved->id }})"
                                    data-bs-toggle="tooltip"
                                    title="{{ $saved->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}">
                                <i class="bi bi-star{{ $saved->is_favorite ? '-fill' : '' }}"></i>
                            </button>
                        </div>

                        @if($saved->notes)
                            <p class="text-muted mb-3" style="font-size:.8rem;line-height:1.5;">{{ Str::limit($saved->notes, 80) }}</p>
                        @endif

                        <div class="mb-2">
                            <span class="saved-card-convention">{{ $saved->calculation->convention_type }}</span>
                        </div>

                        <div class="mb-2" style="font-size:.8rem;color:#64748b;">
                            <i class="bi bi-calendar3"></i>
                            {{ $saved->calculation->start_date->format('M d, Y') }} —
                            {{ $saved->calculation->end_date->format('M d, Y') }}
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="metric-mini">
                                    <div class="mini-value">{{ $saved->calculation->days_calculated }}</div>
                                    <div class="mini-label">Days</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-mini">
                                    <div class="mini-value font-monospace" style="font-size:.85rem;">{{ number_format($saved->calculation->day_count_factor, 4) }}</div>
                                    <div class="mini-label">Factor</div>
                                </div>
                            </div>
                        </div>

                        @if($saved->calculation->interest_amount)
                            <div class="rounded-3 py-2 px-2 mb-3" style="background:rgba(201,162,39,.06);border:1px solid rgba(201,162,39,.2);">
                                <small class="d-block" style="color:#7c5e0a;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Interest Amount</small>
                                <strong class="font-monospace" style="color:#a07d18;">${{ number_format($saved->calculation->interest_amount, 2) }}</strong>
                            </div>
                        @endif

                        <div class="text-muted mb-3" style="font-size:.75rem;">
                            <i class="bi bi-clock"></i> Saved {{ $saved->created_at->diffForHumans() }}
                        </div>

                        <div class="btn-group w-100" role="group">
                            <button type="button"
                                    class="btn btn-sm btn-outline-gold"
                                    onclick="viewCalculation({{ $saved->calculation->id }})"
                                    data-bs-toggle="tooltip"
                                    title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-gold"
                                    onclick="editSavedCalculation({{ $saved->id }}, '{{ addslashes($saved->name) }}', '{{ addslashes($saved->notes ?? '') }}', {{ $saved->is_favorite ? 'true' : 'false' }})"
                                    data-bs-toggle="tooltip"
                                    title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="deleteSavedCalculation({{ $saved->id }}, '{{ addslashes($saved->name) }}')"
                                    data-bs-toggle="tooltip"
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
