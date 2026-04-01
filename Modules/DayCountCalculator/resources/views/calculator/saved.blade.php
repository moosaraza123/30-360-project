@extends('daycountcalculator::layouts.master')

@section('title', 'Saved Calculations')

@section('content')

{{-- Page Hero --}}
<div class="page-hero">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="hero-badge">Your Collection</div>
                <h1 class="mb-1">Saved <span class="gold-accent">Calculations</span></h1>
                <p class="mb-0">Your saved calculations — {{ $savedCalculations->count() }} total</p>
            </div>
            <a href="{{ route('calculator.index') }}" class="btn btn-gold d-none d-md-inline-flex align-items-center gap-2">
                <i class="bi bi-calculator"></i> New Calculation
            </a>
        </div>
    </div>
</div>

<div class="container pb-5">
    @if($savedCalculations->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-bookmark" style="font-size:3rem;color:#94a3b8;"></i>
                <h5 class="mt-3 fw-bold" style="color:#0f172a;">No Saved Calculations</h5>
                <p class="text-muted">Save your important calculations to access them later.</p>
                <a href="{{ route('calculator.index') }}" class="btn btn-gold mt-2">
                    <i class="bi bi-calculator me-2"></i>Create a Calculation
                </a>
            </div>
        </div>
    @else
        {{-- Filter Tabs --}}
        <ul class="nav nav-tabs mb-4 border-0" id="savedTabs" role="tablist" style="gap:.5rem;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-3 border-0 fw-semibold" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button"
                        style="background:#f8fafc;color:#475569;"
                        onmouseover="if(!this.classList.contains('active')) this.style.background='rgba(201,162,39,.08)'"
                        onmouseout="if(!this.classList.contains('active')) this.style.background='#f8fafc'">
                    All <span style="background:var(--gold);color:var(--navy-dark);padding:.125rem .5rem;border-radius:100px;font-size:.75rem;font-weight:700;margin-left:.25rem;">{{ $savedCalculations->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-3 border-0 fw-semibold" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites" type="button"
                        style="background:#f8fafc;color:#475569;"
                        onmouseover="if(!this.classList.contains('active')) this.style.background='rgba(201,162,39,.08)'"
                        onmouseout="if(!this.classList.contains('active')) this.style.background='#f8fafc'">
                    <i class="bi bi-star-fill" style="color:var(--gold);"></i> Favorites <span style="background:var(--gold);color:var(--navy-dark);padding:.125rem .5rem;border-radius:100px;font-size:.75rem;font-weight:700;margin-left:.25rem;">{{ $savedCalculations->where('is_favorite', true)->count() }}</span>
                </button>
            </li>
        </ul>

        <style>
            .nav-tabs .nav-link.active {
                background: linear-gradient(135deg, var(--navy-dark), var(--navy-mid)) !important;
                color: #fff !important;
            }
        </style>

        <div class="tab-content" id="savedTabsContent">
            {{-- All Calculations Tab --}}
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                @include('daycountcalculator::calculator.partials.saved-list', ['calculations' => $savedCalculations])
            </div>

            {{-- Favorites Tab --}}
            <div class="tab-pane fade" id="favorites" role="tabpanel">
                @include('daycountcalculator::calculator.partials.saved-list', ['calculations' => $savedCalculations->where('is_favorite', true)])
            </div>
        </div>
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

{{-- Edit Saved Calculation Modal --}}
<div class="modal fade" id="editSavedCalculationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:.875rem;border:none;">
            <div class="card-header-navy p-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">Edit Saved Calculation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editSavedCalculationForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_saved_id" name="saved_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label fw-semibold" style="font-size:.875rem;">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required
                               style="border-color:#e2e8f0;border-radius:.5rem;">
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label fw-semibold" style="font-size:.875rem;">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"
                                  style="border-color:#e2e8f0;border-radius:.5rem;"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_favorite" name="is_favorite"
                               style="accent-color:var(--gold);">
                        <label class="form-check-label" for="edit_is_favorite" style="font-size:.875rem;">
                            <i class="bi bi-star-fill" style="color:var(--gold);"></i> Mark as favourite
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function viewCalculation(calculationId) {
        fetch(`/api/calculations/${calculationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
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
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading calculation details');
        });
    }

    function editSavedCalculation(savedId, name, notes, isFavorite) {
        document.getElementById('edit_saved_id').value = savedId;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_notes').value = notes || '';
        document.getElementById('edit_is_favorite').checked = isFavorite;

        const modal = new bootstrap.Modal(document.getElementById('editSavedCalculationModal'));
        modal.show();
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
                location.reload();
            } else {
                alert('Error toggling favorite');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteSavedCalculation(savedId, name) {
        if (!confirm(`Are you sure you want to delete "${name}"?`)) {
            return;
        }

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
                location.reload();
            } else {
                alert('Error deleting calculation');
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
                    location.reload();
                } else {
                    alert('Error updating calculation');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
@endpush
