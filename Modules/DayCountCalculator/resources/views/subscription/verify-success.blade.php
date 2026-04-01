@extends('daycountcalculator::layouts.master')

@section('title', 'Email Verified')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="h4 fw-bold mb-3">Email Verified Successfully!</h2>

                    <p class="text-muted mb-4">
                        Thank you for verifying your email address <strong>{{ $subscriber->email }}</strong>.
                        You're now subscribed to our updates.
                    </p>

                    <div class="alert alert-info text-start">
                        <h6 class="fw-semibold mb-2">What's Next?</h6>
                        <ul class="mb-0 small">
                            <li>You'll receive updates about new calculators and features</li>
                            <li>Educational content about day count conventions</li>
                            <li>Tips and tricks for financial calculations</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('calculator.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-calculator"></i> Start Calculating
                        </a>
                        <a href="{{ route('comparison.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-bar-chart"></i> Try Comparison Tool
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
