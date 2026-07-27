@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('GOSI & Net Salary Calculator — Saudi Arabia'))
@section('meta_description', __('Calculate your GOSI contribution and net salary in Saudi Arabia — 9.75% employee rate for Saudis, SAR 45,000 cap, with the full breakdown.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How is GOSI calculated in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'GOSI is calculated on the basic salary plus housing allowance, capped at SAR 45,000. Saudi employees contribute 9.75% (9% annuities + 0.75% SANED) and employers 11.75%. For expatriates only the employer pays: 2% for occupational hazards.']],
        ['@type' => 'Question', 'name' => 'Is GOSI deducted from allowances?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Only the housing allowance is included in the GOSI base. Transport, phone and other allowances are excluded.']],
        ['@type' => 'Question', 'name' => 'Do expats pay GOSI in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'No deduction is taken from expatriate salaries. The employer pays 2% of the capped base for occupational hazards insurance.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('GOSI & Net Salary Calculator — Saudi Arabia') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="basic_salary" class="field-label">{{ __('Basic monthly salary') }} (SAR)</label>
                    <input type="number" step="0.01" min="1" id="basic_salary" name="basic_salary"
                           value="{{ old('basic_salary') }}" required class="field-input" dir="ltr">
                    @error('basic_salary')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="housing_allowance" class="field-label">{{ __('Housing allowance') }}</label>
                    <input type="number" step="0.01" min="0" id="housing_allowance" name="housing_allowance"
                           value="{{ old('housing_allowance') }}" placeholder="0" class="field-input" dir="ltr">
                    @error('housing_allowance')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="other_allowances" class="field-label">{{ __('Other allowances') }}</label>
                    <input type="number" step="0.01" min="0" id="other_allowances" name="other_allowances"
                           value="{{ old('other_allowances') }}" placeholder="0" class="field-input" dir="ltr">
                    <p class="field-help">{{ __('Not included in the GOSI base') }}</p>
                    @error('other_allowances')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <fieldset>
                <legend class="field-label">{{ __('Nationality') }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="nationality" value="saudi" class="text-brand focus:ring-brand" @checked(old('nationality', 'saudi') === 'saudi')>
                        {{ __('Saudi national') }} <span class="text-ink-faint">(9.75%)</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="nationality" value="expat" class="text-brand focus:ring-brand" @checked(old('nationality') === 'expat')>
                        {{ __('Expatriate') }} <span class="text-ink-faint">({{ __('no deduction') }})</span>
                    </label>
                </div>
                @error('nationality')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Net monthly salary') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">SAR {{ number_format($result['net'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Your GOSI deduction') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">SAR {{ number_format($result['employee_contribution'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Annual net salary') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">SAR {{ number_format($result['annual_net'], 2) }}</p>
                </div>
            </div>
            @if($result['base_capped'])
                <p class="mt-3 text-xs text-amber-700">{{ __('The GOSI base was capped at SAR 45,000.') }}</p>
            @endif
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How GOSI works') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('GOSI contributions are calculated on the basic salary plus housing allowance, capped at SAR 45,000. Saudi employees contribute 9.75% (9% annuities and 0.75% SANED unemployment insurance) while employers pay 11.75%. For expatriate employees nothing is deducted from the salary; the employer pays 2% for occupational hazards insurance. Note: the 2024 pension reform introduces gradually increasing rates for employees who joined the system after July 2024.') }}</p>
    </div>
</div>
@endsection
