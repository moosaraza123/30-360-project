@extends('daycountcalculator::layouts.master')

@section('title', 'Already Verified')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-info-circle-fill text-info" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="h4 fw-bold mb-3">Email Already Verified</h2>

                    <p class="text-muted mb-4">
                        The email address <strong>{{ $subscriber->email }}</strong> has already been verified.
                    </p>

                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        Your subscription is active and you'll receive our updates.
                    </div>

                    <p class="small text-muted">
                        Verified on {{ $subscriber->email_verified_at->format('F j, Y \a\t g:i A') }}
                    </p>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('calculator.index') }}" class="btn btn-primary">
                            <i class="bi bi-calculator"></i> Go to Calculator
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
