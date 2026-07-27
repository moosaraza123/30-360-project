@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Personal Loan Eligibility Calculator — UAE'))
@section('meta_description', __('Check how much personal loan you can get in the UAE — Central Bank rules: 20× monthly salary cap, 48-month maximum term and 50% Debt Burden Ratio.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'What is the maximum personal loan I can get in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Central Bank rules cap a personal loan at 20 times your monthly salary, repayable over at most 48 months, and your total monthly repayments cannot exceed 50% of your income. The lowest of these limits decides your maximum loan.']],
        ['@type' => 'Question', 'name' => 'What is the Debt Burden Ratio (DBR) in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The share of monthly income committed to debt repayments — existing loans, 5% of credit card limits, and the new loan installment. Banks cannot lend beyond a 50% DBR.']],
        ['@type' => 'Question', 'name' => 'What salary do I need for a personal loan in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'There is no regulatory minimum, but most banks set their own — commonly AED 5,000 per month, with some products starting near AED 3,000. Salary transfer to the lending bank is often required for the best rates.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Personal Loan Eligibility Calculator — UAE') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="monthly_salary" class="field-label">{{ __('Monthly salary') }} (AED)</label>
                    <input type="number" step="0.01" min="1" id="monthly_salary" name="monthly_salary"
                           value="{{ old('monthly_salary') }}" required class="field-input" dir="ltr">
                    @error('monthly_salary')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="obligations" class="field-label">{{ __('Existing monthly repayments') }} (AED)</label>
                    <input type="number" step="0.01" min="0" id="obligations" name="obligations"
                           value="{{ old('obligations') }}" placeholder="0" class="field-input" dir="ltr">
                    <p class="field-help">{{ __('Car loans, other loans, 5% of credit card limits') }}</p>
                    @error('obligations')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="annual_rate" class="field-label">{{ __('Interest / profit rate') }} (% {{ __('per year') }}, {{ __('reducing') }})</label>
                    <input type="number" step="0.01" min="0" max="60" id="annual_rate" name="annual_rate"
                           value="{{ old('annual_rate', 8) }}" required class="field-input" dir="ltr">
                    @error('annual_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="months" class="field-label">{{ __('Term') }} ({{ __('months') }})</label>
                    <input type="number" step="1" min="1" max="48" id="months" name="months"
                           value="{{ old('months', 48) }}" required class="field-input" dir="ltr">
                    <p class="field-help">{{ __('Maximum 48 months') }}</p>
                    @error('months')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            @if(! $result['eligible'])
                <p class="text-sm font-semibold text-red-600">{{ __('Your existing repayments already use the full 50% debt capacity — no additional loan is possible at this income under Central Bank rules.') }}</p>
                @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
            @else
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-brand-light p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Maximum loan') }}</p>
                        <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">AED {{ number_format($result['max_loan'], 0) }}</p>
                    </div>
                    <div class="rounded-lg bg-surface-muted p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Monthly installment') }}</p>
                        <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">AED {{ number_format($result['emi'], 2) }}</p>
                    </div>
                    <div class="rounded-lg bg-surface-muted p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Repayment headroom') }}</p>
                        <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">AED {{ number_format($result['max_emi'], 2) }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-ink-faint">
                    {{ $result['binding_constraint'] === 'dbr'
                        ? __('Your limit is set by the 50% Debt Burden Ratio.')
                        : __('Your limit is set by the 20× monthly salary cap.') }}
                </p>
                @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
                <p class="mt-4 text-xs text-ink-faint">{{ __('Banks apply their own minimum salary, employer listing and salary-transfer conditions on top of the regulatory caps.') }}</p>
            @endif
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How personal loan eligibility works in the UAE') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('Central Bank of the UAE regulations cap personal lending three ways: the loan cannot exceed 20 times your monthly salary, the repayment period cannot exceed 48 months, and your total monthly debt repayments — including the new installment — cannot exceed 50% of your income. Banks layer their own criteria on top: minimum salary (commonly AED 5,000), approved-employer lists and salary transfer. This calculator applies the regulatory caps and shows which one limits you.') }}</p>
    </div>
</div>
@endsection
