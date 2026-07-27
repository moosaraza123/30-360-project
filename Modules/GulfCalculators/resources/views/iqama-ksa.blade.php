@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Iqama Renewal Fees Calculator — Saudi Arabia'))
@section('meta_description', __('Calculate the full cost of renewing your iqama in Saudi Arabia — residency fee, work-permit levy and dependent fees, with a clear breakdown of who pays what.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How much does iqama renewal cost in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The iqama (residency) fee is SAR 650 per year for company workers (SAR 600 for domestic workers), plus a work-permit levy of SAR 700–800 per month depending on the company\'s Saudization ratio, plus SAR 400 per month for each dependent.']],
        ['@type' => 'Question', 'name' => 'Who pays the iqama renewal fees — employer or employee?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'By law the employer must pay the iqama fee and the work-permit levy. The dependent levy of SAR 400 per month per dependent is borne by the employee unless the employment contract says otherwise.']],
        ['@type' => 'Question', 'name' => 'What is the dependent fee in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'SAR 400 per month for each dependent on the expat\'s iqama — SAR 4,800 per year per dependent. It must be paid for the full renewal period at renewal time.']],
        ['@type' => 'Question', 'name' => 'Can I renew my iqama for less than a year?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. Iqamas can be renewed in 3, 6, 9 or 12-month blocks, and fees are charged proportionally to the chosen period.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Iqama Renewal Fees Calculator — Saudi Arabia') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="months" class="field-label">{{ __('Renewal period') }}</label>
                    <select id="months" name="months" class="field-input">
                        @foreach([3, 6, 9, 12] as $m)
                            <option value="{{ $m }}" @selected((int) old('months', 12) === $m)>{{ $m }} {{ __('months') }}</option>
                        @endforeach
                    </select>
                    @error('months')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="dependents" class="field-label">{{ __('Number of dependents') }}</label>
                    <input type="number" min="0" max="30" step="1" id="dependents" name="dependents"
                           value="{{ old('dependents', 0) }}" class="field-input" dir="ltr">
                    <p class="field-help">{{ __('SAR 400 per month for each dependent') }}</p>
                    @error('dependents')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <fieldset>
                <legend class="field-label">{{ __('Worker type') }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="worker_type" value="company" class="text-brand focus:ring-brand" @checked(old('worker_type', 'company') === 'company')>
                        {{ __('Company employee') }}
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="worker_type" value="domestic" class="text-brand focus:ring-brand" @checked(old('worker_type') === 'domestic')>
                        {{ __('Domestic worker') }} <span class="text-ink-faint">({{ __('no levy') }})</span>
                    </label>
                </div>
                @error('worker_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <fieldset>
                <legend class="field-label">{{ __("Company's Saudization status") }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="saudization" value="compliant" class="text-brand focus:ring-brand" @checked(old('saudization', 'noncompliant') === 'compliant')>
                        {{ __('Saudis ≥ expats') }} <span class="text-ink-faint">(SAR 700/{{ __('month') }})</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="saudization" value="noncompliant" class="text-brand focus:ring-brand" @checked(old('saudization', 'noncompliant') === 'noncompliant')>
                        {{ __('Expats > Saudis') }} <span class="text-ink-faint">(SAR 800/{{ __('month') }})</span>
                    </label>
                </div>
                <p class="field-help">{{ __('Only applies to company employees; not sure? Most companies pay the higher rate.') }}</p>
                @error('saudization')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Total renewal cost') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">SAR {{ number_format($result['total'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Employer pays (by law)') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">SAR {{ number_format($result['employer_pays'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Employee pays (dependents)') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">SAR {{ number_format($result['employee_pays'], 2) }}</p>
                </div>
            </div>
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
            <p class="mt-4 text-xs text-ink-faint">{{ __('Health insurance and Absher/Muqeem processing fees vary by provider and are not included.') }}</p>
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How iqama renewal fees work') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('An iqama renewal in Saudi Arabia has three components: the residency fee (SAR 650 per year for company workers, SAR 600 for domestic workers), the work-permit levy paid to MHRSD (SAR 700 per month if the company employs at least as many Saudis as expats, SAR 800 otherwise), and the dependent levy of SAR 400 per month for each family member on your iqama. By law the employer bears the residency fee and the work-permit levy; the dependent levy falls on the employee unless the contract says otherwise. Renewals can be made in 3, 6, 9 or 12-month blocks and all fees scale with the chosen period.') }}</p>
    </div>
</div>
@endsection
