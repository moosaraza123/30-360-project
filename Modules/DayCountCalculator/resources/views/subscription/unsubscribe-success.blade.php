@extends('daycountcalculator::layouts.master')

@section('title', 'Unsubscribed')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-envelope-slash text-muted" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="h4 fw-bold mb-3">Successfully Unsubscribed</h2>

                    <p class="text-muted mb-4">
                        You have been unsubscribed from our mailing list.
                        We're sorry to see you go!
                    </p>

                    <div class="alert alert-info text-start">
                        <p class="mb-2"><strong>What this means:</strong></p>
                        <ul class="mb-0 small">
                            <li>You won't receive any more emails from us</li>
                            <li>You can still use all calculator features</li>
                            <li>You can resubscribe anytime</li>
                        </ul>
                    </div>

                    <div class="card bg-light border-0 mt-4">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Changed Your Mind?</h6>
                            <form action="{{ route('subscribe.resubscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="email" value="{{ $subscriber->email }}">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-arrow-clockwise"></i> Resubscribe
                                </button>
                            </form>
                        </div>
                    </div>

                    <a href="{{ route('calculator.index') }}" class="btn btn-outline-primary mt-3">
                        <i class="bi bi-calculator"></i> Continue to Calculator
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
