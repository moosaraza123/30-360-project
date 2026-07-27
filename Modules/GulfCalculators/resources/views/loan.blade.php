@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Loan & EMI Calculator — Flat vs Reducing Rate'))
@section('meta_description', __('Calculate your monthly installment in AED or SAR — flat rate or reducing balance, with the true APR equivalent of flat-rate offers revealed.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'What is the difference between flat rate and reducing rate?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'A flat rate charges interest on the original principal for the whole tenure, while a reducing (amortizing) rate charges interest only on the outstanding balance. A flat rate is roughly 1.8x more expensive than the same reducing rate — banks in the Gulf often quote flat rates because they look smaller.']],
        ['@type' => 'Question', 'name' => 'How is EMI calculated?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'EMI = P × r × (1+r)^n / ((1+r)^n − 1), where P is the principal, r the monthly rate and n the number of months.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Loan & EMI Calculator — Flat vs Reducing Rate') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="currency" class="field-label">{{ __('Currency') }}</label>
                    <select id="currency" name="currency" class="field-input">
                        <option value="AED" @selected(old('currency', 'AED') === 'AED')>AED — {{ __('UAE Dirham') }}</option>
                        <option value="SAR" @selected(old('currency') === 'SAR')>SAR — {{ __('Saudi Riyal') }}</option>
                    </select>
                </div>
                <div>
                    <label for="principal" class="field-label">{{ __('Financing amount') }}</label>
                    <input type="number" step="0.01" min="100" id="principal" name="principal"
                           value="{{ old('principal') }}" required class="field-input" dir="ltr">
                    @error('principal')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="annual_rate" class="field-label">{{ __('Annual rate') }} (%)</label>
                    <input type="number" step="0.01" min="0" max="100" id="annual_rate" name="annual_rate"
                           value="{{ old('annual_rate') }}" required class="field-input" dir="ltr">
                    @error('annual_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="months" class="field-label">{{ __('Tenure (months)') }}</label>
                    <input type="number" step="1" min="1" max="480" id="months" name="months"
                           value="{{ old('months') }}" required class="field-input" dir="ltr">
                    @error('months')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <fieldset>
                <legend class="field-label">{{ __('Rate type') }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="method" value="reducing" class="text-brand focus:ring-brand" @checked(old('method', 'reducing') === 'reducing')>
                        {{ __('Reducing balance') }} <span class="text-ink-faint">({{ __('true amortization') }})</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="method" value="flat" class="text-brand focus:ring-brand" @checked(old('method') === 'flat')>
                        {{ __('Flat rate') }} <span class="text-ink-faint">({{ __('as quoted by many banks') }})</span>
                    </label>
                </div>
                @error('method')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Monthly installment') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">{{ $result['currency'] }} {{ number_format($result['emi'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Total financing cost') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">{{ $result['currency'] }} {{ number_format($result['total_interest'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Total payment') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">{{ $result['currency'] }} {{ number_format($result['total_payment'], 2) }}</p>
                </div>
            </div>
            @if($result['method'] === 'flat' && $result['apr_equivalent'] !== null)
                <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    {{ __('This flat rate is equivalent to a reducing-balance rate of about') }}
                    <strong dir="ltr">{{ number_format($result['apr_equivalent'], 2) }}%</strong> —
                    {{ __('use this number when comparing offers.') }}
                </div>
            @endif
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('Flat vs reducing rate — why it matters') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('Many Gulf banks quote personal financing with a flat rate, where the charge is computed on the original amount for the entire tenure. A reducing-balance rate charges only on what you still owe, so a 5% flat rate actually costs about the same as a 9–9.5% reducing rate. This calculator shows the equivalent APR for every flat-rate quote so you can compare offers honestly.') }}</p>
    </div>
</div>
@endsection
