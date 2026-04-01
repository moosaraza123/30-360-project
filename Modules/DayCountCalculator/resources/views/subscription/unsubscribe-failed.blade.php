@extends('daycountcalculator::layouts.master')

@section('title', 'Unsubscribe Failed')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="h4 fw-bold mb-3">Unsubscribe Failed</h2>

                    <p class="text-muted mb-4">
                        {{ $message }}
                    </p>

                    <div class="alert alert-warning text-start">
                        <h6 class="fw-semibold mb-2">Possible Reasons:</h6>
                        <ul class="mb-0 small">
                            <li>The unsubscribe link is invalid</li>
                            <li>The link has expired</li>
                            <li>The email address is not in our system</li>
                        </ul>
                    </div>

                    <a href="{{ route('calculator.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-arrow-left"></i> Return to Calculator
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
