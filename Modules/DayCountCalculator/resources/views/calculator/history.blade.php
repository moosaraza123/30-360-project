@extends('daycountcalculator::layouts.master')

@section('title', 'Calculation History')

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="hero-badge">Your Records</div>
                <h1 class="mb-1">Calculation <span class="gold-accent">History</span></h1>
                <p class="mb-0">
                    @auth
                        Your calculation history — {{ $calculations->count() }} total
                    @else
                        Recent calculations from this session — {{ $calculations->count() }} total
                    @endauth
                </p>
            </div>
            <a href="{{ route('calculator.index') }}" class="btn btn-gold d-none d-md-inline-flex align-items-center gap-2">
                <i class="bi bi-calculator"></i> New Calculation
            </a>
        </div>
    </div>
</div>

<div class="container pb-5">
    @if($calculations->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-clock-history" style="font-size:3rem;color:#94a3b8;"></i>
                <h5 class="mt-3 fw-bold" style="color:#0f172a;">No Calculations Yet</h5>
                <p class="text-muted">Your calculation history will appear here once you perform calculations.</p>
                <a href="{{ route('calculator.index') }}" class="btn btn-gold mt-2">
                    <i class="bi bi-calculator me-2"></i>Get Started
                </a>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-navy table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Convention</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th class="text-end">Days</th>
                                <th class="text-end">Factor</th>
                                <th class="text-end">Interest</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calculations as $calculation)
                                <tr>
                                    <td><strong style="color:#0f172a;">{{ $calculation->convention_type }}</strong></td>
                                    <td><span class="text-nowrap">{{ $calculation->start_date->format('M d, Y') }}</span></td>
                                    <td><span class="text-nowrap">{{ $calculation->end_date->format('M d, Y') }}</span></td>
                                    <td class="text-end font-monospace">
                                        <span class="badge badge-gold">{{ $calculation->days_calculated }}</span>
                                    </td>
                                    <td class="text-end font-monospace">{{ number_format($calculation->day_count_factor, 6) }}</td>
                                    <td class="text-end font-monospace">
                                        @if($calculation->interest_amount)
                                            ${{ number_format($calculation->interest_amount, 2) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $calculation->created_at->diffForHumans() }}</small></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-gold btn-sm"
                                                    onclick='viewCalculation(@json($calculation))'
                                                    data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @auth
                                                @if(!$calculation->savedCalculations()->where('user_id', auth()->id())->exists())
                                                    <button type="button" class="btn btn-outline-gold btn-sm"
                                                            onclick="saveCalculation({{ $calculation->id }})"
                                                            data-bs-toggle="tooltip" title="Save">
                                                        <i class="bi bi-bookmark"></i>
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
        </div>

        @guest
            <div class="alert mt-4 border-0 rounded-3" style="background:rgba(201,162,39,.08);border-left:4px solid #c9a227!important;color:#7c5e0a;">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Tip:</strong> <a href="{{ route('register') }}" style="color:#a07d18;font-weight:600;text-decoration:none;">Create an account</a> to save your calculations permanently and access them from any device.
            </div>
        @endguest
    @endif
</div>

{{-- Calculation Details Modal --}}
<div class="modal fade" id="calculationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:.875rem;border:none;">
            <div class="card-header-navy p-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">Calculation Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="calculationDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border" style="color:var(--gold);" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Save Calculation Modal --}}
@auth
<div class="modal fade" id="saveCalculationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:.875rem;overflow:hidden;border:none;">
            <div class="card-header-navy p-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold" style="font-size:1rem;">Save Calculation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="saveCalculationForm">
                <input type="hidden" id="save_calculation_id" name="calculation_id">
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    });

    function viewCalculation(data) {
        const modal = new bootstrap.Modal(document.getElementById('calculationDetailsModal'));
        const content = document.getElementById('calculationDetailsContent');

        let html = `
            <div class="mb-3">
                <h6 class="fw-bold" style="color:#0f172a;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">Convention Type</h6>
                <p class="mb-0" style="color:#475569;">${data.convention_type}</p>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="fw-bold" style="color:#0f172a;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">Start Date</h6>
                    <p class="mb-0" style="color:#475569;">${data.start_date}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold" style="color:#0f172a;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">End Date</h6>
                    <p class="mb-0" style="color:#475569;">${data.end_date}</p>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="metric-box">
                        <div class="metric-value font-monospace">${data.days_calculated}</div>
                        <div class="metric-label">Days</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-box">
                        <div class="metric-value font-monospace" style="font-size:1.2rem;">${parseFloat(data.day_count_factor).toFixed(10)}</div>
                        <div class="metric-label">Day Count Factor</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-box">
                        <div class="metric-value font-monospace">${data.interest_amount ? '$' + parseFloat(data.interest_amount).toFixed(2) : 'N/A'}</div>
                        <div class="metric-label">Interest Amount</div>
                    </div>
                </div>
            </div>
        `;

        if (data.calculation_steps && data.calculation_steps.length > 0) {
            html += `<h6 class="fw-bold mb-3" style="color:#0f172a;">Calculation Steps</h6><div class="accordion" id="stepsAccordion">`;
            data.calculation_steps.forEach((step, i) => {
                html += `
                    <div class="accordion-item border mb-2" style="border-radius:.5rem;overflow:hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button ${i === 0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#step${i}" style="font-size:.875rem;">
                                <span class="step-number me-2">${i + 1}</span>${step.title}
                                ${step.applied ? '<span class="badge badge-gold ms-2">Applied</span>' : ''}
                            </button>
                        </h2>
                        <div id="step${i}" class="accordion-collapse collapse ${i === 0 ? 'show' : ''}">
                            <div class="accordion-body">
                                <p style="font-size:.85rem;color:#475569;">${step.description}</p>
                                <div class="formula-block">${step.formula}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        content.innerHTML = html;
        modal.show();
    }

    @auth
    function saveCalculation(calculationId) {
        document.getElementById('save_calculation_id').value = calculationId;
        document.getElementById('calculation_name').value = '';
        document.getElementById('calculation_notes').value = '';
        document.getElementById('is_favorite').checked = false;

        const modal = new bootstrap.Modal(document.getElementById('saveCalculationModal'));
        modal.show();
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
                    bootstrap.Modal.getInstance(document.getElementById('saveCalculationModal')).hide();
                    alert('Calculation saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(() => alert('An error occurred while saving the calculation.'));
        });
    });
    @endauth
</script>
@endpush
