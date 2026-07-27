@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Take-Home Salary Calculator — UAE'))
@section('meta_description', __('Calculate your UAE take-home salary. No income tax; GPSSA pension rates for UAE nationals (5% or 11%) applied with a clear breakdown.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'Is salary taxed in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'No. The UAE has no personal income tax, so for expatriates take-home salary equals gross salary unless the employer applies other deductions.']],
        ['@type' => 'Question', 'name' => 'What is deducted from UAE nationals\' salaries?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'UAE nationals contribute to the GPSSA pension: 5% of the contribution salary for those who joined before October 2023 (Law 7/1999), and 11% for those who joined after (Law 57/2023).']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Take-Home Salary Calculator — UAE') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div>
                <label for="gross_salary" class="field-label">{{ __('Gross monthly salary') }} (AED)</label>
                <input type="number" step="0.01" min="1" id="gross_salary" name="gross_salary"
                       value="{{ old('gross_salary') }}" required class="field-input" dir="ltr">
                @error('gross_salary')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="field-label">{{ __('Employee category') }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-3">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="category" value="expat" class="text-brand focus:ring-brand" @checked(old('category', 'expat') === 'expat')>
                        {{ __('Expatriate') }}
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="category" value="national_pre2023" class="text-brand focus:ring-brand" @checked(old('category') === 'national_pre2023')>
                        {{ __('UAE national — joined before Oct 2023') }} <span class="text-ink-faint">(5%)</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="category" value="national_post2023" class="text-brand focus:ring-brand" @checked(old('category') === 'national_post2023')>
                        {{ __('UAE national — joined after Oct 2023') }} <span class="text-ink-faint">(11%)</span>
                    </label>
                </div>
                @error('category')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Net monthly salary') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">AED {{ number_format($result['net'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Pension contribution') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">AED {{ number_format($result['pension'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Annual net salary') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">AED {{ number_format($result['annual_net'], 2) }}</p>
                </div>
            </div>
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How UAE salaries are calculated') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('The UAE levies no personal income tax, so an expatriate’s take-home pay equals the gross salary unless the employer applies specific deductions. UAE nationals contribute to the GPSSA pension: 5% of the contribution salary under Law 7/1999 (joined before October 2023) or 11% under Law 57/2023 (joined after). This calculator applies the rate to the gross salary as a simplification — GPSSA formally defines a contribution salary that may differ from total gross.') }}</p>
    </div>
</div>
@endsection
