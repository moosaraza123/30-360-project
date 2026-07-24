// Import Bootstrap and expose globally
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Bootstrap Icons font (every view uses <i class="bi bi-...">)
import 'bootstrap-icons/font/bootstrap-icons.css';

// Import Flatpickr for date picking
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// Import Chart.js for comparison visualization
import Chart from 'chart.js/auto';

// Calculator functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    initializeDatePickers();

    // Initialize convention selector
    initializeConventionSelector();

    // Initialize calculator form
    initializeCalculatorForm();

    // Initialize tooltips
    initializeTooltips();
});

function initializeDatePickers() {
    const dateInputs = document.querySelectorAll('.date-picker');

    dateInputs.forEach(input => {
        flatpickr(input, {
            dateFormat: 'Y-m-d',
            allowInput: true,
            defaultDate: input.value || null
        });
    });
}

function initializeConventionSelector() {
    const conventionCards = document.querySelectorAll('.convention-card');
    const conventionInput = document.getElementById('convention_type');

    conventionCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove active class from all cards
            conventionCards.forEach(c => c.classList.remove('active'));

            // Add active class to clicked card
            this.classList.add('active');

            // Update hidden input
            const convention = this.dataset.convention;
            if (conventionInput) {
                conventionInput.value = convention;
            }

            // Update educational sidebar
            updateEducationalContent(convention);
        });
    });
}

function initializeCalculatorForm() {
    const form = document.getElementById('calculator-form');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = form.querySelector('.calculate-btn');
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.spinner-border');

            // Show loading state
            btn.disabled = true;
            btnText.textContent = 'Calculating...';
            btnSpinner.classList.remove('d-none');

            // Submit form via AJAX
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                // Hide loading state
                btn.disabled = false;
                btnText.textContent = 'Calculate';
                btnSpinner.classList.add('d-none');

                // Display results
                displayResults(data);

                // Scroll to results
                document.getElementById('results-container').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btnText.textContent = 'Calculate';
                btnSpinner.classList.add('d-none');

                alert('An error occurred. Please try again.');
            });
        });
    }
}

function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function updateEducationalContent(convention) {
    // Update sidebar content based on selected convention
    const infoContainer = document.getElementById('convention-info');

    if (!infoContainer) return;

    const conventionData = {
        '30/360 US': {
            description: 'Also known as Bond Basis or 30/360. Assumes 30 days in each month and 360 days in a year.',
            formula: 'Days = 360×(Y₂-Y₁) + 30×(M₂-M₁) + (D₂-D₁)',
            useCase: 'Commonly used for US corporate bonds and many other financial instruments.'
        },
        '30E/360': {
            description: 'European method (Eurobond Basis). Similar to 30/360 but with different end-of-month handling.',
            formula: 'Days = 360×(Y₂-Y₁) + 30×(M₂-M₁) + (D₂-D₁)',
            useCase: 'Used for Eurobonds and many European financial products.'
        },
        'Actual/365': {
            description: 'Counts actual days between dates and divides by 365.',
            formula: 'Factor = (Actual Days) / 365',
            useCase: 'Used for various fixed income securities and some government bonds.'
        },
        'Actual/360': {
            description: 'Counts actual days between dates and divides by 360 (Money Market basis).',
            formula: 'Factor = (Actual Days) / 360',
            useCase: 'Common in money market instruments, repos, and commercial paper.'
        }
        // Add more conventions as needed
    };

    const data = conventionData[convention] || {};

    infoContainer.innerHTML = `
        <div class="info-section">
            <div class="info-label">Description</div>
            <div class="info-content">${data.description || 'Select a convention to see details.'}</div>
        </div>
        <div class="info-section">
            <div class="info-label">Formula</div>
            <div class="formula-display">${data.formula || 'N/A'}</div>
        </div>
        <div class="info-section">
            <div class="info-label">Common Use Case</div>
            <div class="info-content">${data.useCase || 'N/A'}</div>
        </div>
    `;
}

function displayResults(data) {
    const resultsContainer = document.getElementById('results-container');

    if (!resultsContainer) return;

    resultsContainer.innerHTML = `
        <div class="result-card">
            <div class="result-summary">
                <div class="result-item">
                    <div class="result-label">Days</div>
                    <div class="result-value">${data.days}</div>
                </div>
                <div class="result-item">
                    <div class="result-label">Day Count Factor</div>
                    <div class="result-value">${data.day_count_factor.toFixed(10)}</div>
                </div>
                ${data.interest_amount ? `
                <div class="result-item">
                    <div class="result-label">Interest Amount</div>
                    <div class="result-value">$${data.interest_amount.toFixed(2)}</div>
                </div>
                ` : ''}
            </div>

            <div class="calculation-steps">
                <h5>Calculation Steps</h5>
                ${renderSteps(data.steps)}
            </div>

            <div class="action-buttons">
                <button class="btn btn-primary" onclick="downloadPDF()">
                    <i class="bi bi-file-pdf"></i> Download PDF
                </button>
                <button class="btn btn-success" onclick="exportExcel()">
                    <i class="bi bi-file-excel"></i> Export Excel
                </button>
                <button class="btn btn-info" onclick="saveCalculation()">
                    <i class="bi bi-save"></i> Save Calculation
                </button>
            </div>
        </div>
    `;

    resultsContainer.classList.remove('d-none');
}

function renderSteps(steps) {
    if (!steps || steps.length === 0) {
        return '<p>No detailed steps available.</p>';
    }

    return steps.map(step => `
        <div class="step-item ${step.applied ? 'applied' : 'not-applied'}">
            <div class="step-title">${step.title}</div>
            <div class="step-description">${step.description}</div>
            ${step.formula ? `<div class="step-formula">${step.formula}</div>` : ''}
        </div>
    `).join('');
}

// Export functions
window.downloadPDF = function() {
    // Implementation will be added
    console.log('Download PDF');
};

window.exportExcel = function() {
    // Implementation will be added
    console.log('Export Excel');
};

// Make Chart available globally for comparison tool
window.Chart = Chart;
