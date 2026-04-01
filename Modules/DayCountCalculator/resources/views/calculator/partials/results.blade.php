<div class="results-panel" id="resultsCard">
    {{-- Header --}}
    <div class="results-header">
        <div class="results-icon">✦</div>
        <div>
            <h5>Calculation Results</h5>
            <small>{{ $result->conventionType }}</small>
        </div>
        <div class="ms-auto">
            <span class="badge badge-gold px-3 py-2" style="font-size:.8rem;">{{ $result->days }} days</span>
        </div>
    </div>

    {{-- Metric Boxes --}}
    <div class="p-4">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="metric-box">
                    <div class="metric-value font-monospace">{{ $result->days }}</div>
                    <div class="metric-label">Days Calculated</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-box">
                    <div class="metric-value font-monospace" style="font-size:1.4rem;">{{ $result->getFormattedFactor(6) }}</div>
                    <div class="metric-label">Day Count Factor</div>
                </div>
            </div>
            @if($result->interestAmount !== null)
                <div class="col-md-4">
                    <div class="metric-box" style="border-color:rgba(201,162,39,.3);background:rgba(201,162,39,.04);">
                        <div class="metric-value font-monospace">${{ number_format($result->interestAmount, 2) }}</div>
                        <div class="metric-label">Interest Amount</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Calculation Steps Accordion --}}
        <div class="steps-accordion accordion" id="calculationStepsAccordion">
            <div class="accordion-item border-0" style="border-radius:.625rem;overflow:hidden;border:1px solid #e2e8f0;">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button"
                            data-bs-toggle="collapse" data-bs-target="#stepsCollapse"
                            style="font-size:.875rem;font-weight:600;">
                        <i class="bi bi-list-check me-2" style="color:var(--gold);"></i>
                        View Step-by-Step Breakdown
                    </button>
                </h2>
                <div id="stepsCollapse" class="accordion-collapse collapse show">
                    <div class="accordion-body pt-2">
                        @foreach($result->steps as $index => $step)
                            <div class="d-flex align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}"
                                 style="border-color:#f1f5f9!important;">
                                <span class="step-number flex-shrink-0" style="margin-top:1px;">{{ $index + 1 }}</span>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="fw-semibold" style="font-size:.875rem;color:#0f172a;">{{ $step['title'] }}</span>
                                        @if($step['applied'])
                                            <span class="badge" style="background:rgba(201,162,39,.12);color:#a07d18;font-size:.7rem;font-weight:700;border:1px solid rgba(201,162,39,.25);">Applied</span>
                                        @else
                                            <span class="badge bg-light text-muted" style="font-size:.7rem;">Skipped</span>
                                        @endif
                                    </div>
                                    <p class="text-muted mb-2" style="font-size:.825rem;line-height:1.55;">{{ $step['description'] }}</p>
                                    <div class="font-monospace" style="background:#0f172a;color:#c9a227;padding:.625rem .875rem;border-radius:.375rem;font-size:.78rem;line-height:1.6;border-left:3px solid #c9a227;">
                                        {!! nl2br(e($step['formula'])) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Guest signup CTA --}}
        @guest
        <div class="alert mt-4 mb-0 d-flex align-items-center gap-3"
             style="background:rgba(201,162,39,.08);border:1px solid rgba(201,162,39,.3);border-radius:.625rem;padding:.875rem 1.25rem;">
            <i class="bi bi-bookmark-star-fill flex-shrink-0" style="color:#c9a227;font-size:1.25rem;"></i>
            <div class="flex-grow-1">
                <div class="fw-semibold" style="font-size:.875rem;color:#0f172a;">Sign up to save your calculations</div>
                <div class="text-muted" style="font-size:.8rem;">Create a free account to save, name, and revisit any calculation.</div>
            </div>
            <a href="{{ route('register') }}" class="btn btn-gold btn-sm flex-shrink-0 px-3">Sign Up Free</a>
        </div>
        @endguest

        {{-- Action Buttons --}}
        <div class="row g-2 mt-4">
            <div class="col-6 col-md-3">
                <button type="button" class="btn btn-outline-gold w-100 btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('comparison.index') }}" class="btn btn-outline-gold w-100 btn-sm">
                    <i class="bi bi-bar-chart me-1"></i>Compare
                </a>
            </div>
            @auth
                <div class="col-6 col-md-3">
                    <button type="button" class="btn btn-outline-gold w-100 btn-sm"
                            data-bs-toggle="modal" data-bs-target="#saveCalculationModal">
                        <i class="bi bi-bookmark me-1"></i>Save
                    </button>
                </div>
            @endauth
            <div class="col-6 col-md-3">
                <button type="button" class="btn btn-outline-gold w-100 btn-sm" onclick="shareCalculation()">
                    <i class="bi bi-share me-1"></i>Share
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Save Modal --}}
@auth
<div class="modal fade" id="saveCalculationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:.875rem;overflow:hidden;border:none;">
            <div class="card-header-navy p-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold" style="font-size:1rem;">Save Calculation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="saveCalculationForm" action="{{ route('calculator.save', $calculationId ?? 0) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="calculation_name" class="form-label fw-semibold" style="font-size:.875rem;">Name</label>
                        <input type="text" class="form-control" id="calculation_name" name="name" required
                               placeholder="e.g. Q1 2024 Bond Calculation"
                               style="border-color:#e2e8f0;border-radius:.5rem;">
                    </div>
                    <div class="mb-3">
                        <label for="calculation_notes" class="form-label fw-semibold" style="font-size:.875rem;">Notes <span class="fw-normal text-muted">(Optional)</span></label>
                        <textarea class="form-control" id="calculation_notes" name="notes" rows="3"
                                  placeholder="Add any notes about this calculation..."
                                  style="border-color:#e2e8f0;border-radius:.5rem;"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_favorite" name="is_favorite" value="1"
                               style="accent-color:var(--gold);">
                        <label class="form-check-label" for="is_favorite" style="font-size:.875rem;">
                            <i class="bi bi-star-fill" style="color:var(--gold);"></i> Mark as favourite
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold px-4">Save Calculation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resultsCard = document.getElementById('resultsCard');
        if (resultsCard) {
            resultsCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    function shareCalculation() {
        const url = window.location.href;
        if (navigator.share) {
            navigator.share({ title: 'Day Count Calculation', url }).catch(() => {});
        } else {
            navigator.clipboard.writeText(url).then(() => alert('Link copied to clipboard!'));
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
                bootstrap.Modal.getInstance(document.getElementById('saveCalculationModal')).hide();
                alert('Calculation saved successfully!');
                this.reset();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(() => alert('An error occurred while saving.'));
    });
    @endauth
</script>
@endpush

<style>
@media print {
    .btn, .accordion-button { display: none !important; }
    .accordion-collapse { display: block !important; }
}
</style>
