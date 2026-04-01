@extends('daycountcalculator::layouts.master')

@section('title', 'Verification Failed')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="h4 fw-bold mb-3">Verification Failed</h2>

                    <p class="text-muted mb-4">
                        {{ $message }}
                    </p>

                    <div class="alert alert-warning text-start">
                        <h6 class="fw-semibold mb-2">Possible Reasons:</h6>
                        <ul class="mb-0 small">
                            <li>The verification link has expired</li>
                            <li>The link has already been used</li>
                            <li>The link is invalid or corrupted</li>
                        </ul>
                    </div>

                    <div class="card bg-light border-0 mt-4">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Need Help?</h6>
                            <p class="small mb-3">If you believe this is an error, please try subscribing again.</p>

                            <form action="{{ route('subscribe.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="source" value="verify_failed">
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control" placeholder="Your email address" required>
                                    <button type="submit" class="btn btn-primary">Resubscribe</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <a href="{{ route('calculator.index') }}" class="btn btn-outline-secondary mt-3">
                        <i class="bi bi-arrow-left"></i> Return to Calculator
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
