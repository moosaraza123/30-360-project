@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Corporate Tax Calculator — UAE (9%)'))
@section('meta_description', __('Calculate UAE corporate tax — 0% up to AED 375,000 and 9% above, including the Small Business Relief check for revenue up to AED 3 million.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'What is the corporate tax rate in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => '0% on taxable income up to AED 375,000 and 9% on the amount above it, under Federal Decree-Law No. 47 of 2022. Qualifying Free Zone Persons can keep a 0% rate on qualifying income.']],
        ['@type' => 'Question', 'name' => 'What is Small Business Relief?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'A resident business with revenue of AED 3 million or less in the tax period (and all previous periods) can elect to be treated as having no taxable income — paying zero corporate tax. The relief applies to tax periods ending on or before 31 December 2026.']],
        ['@type' => 'Question', 'name' => 'Do free zone companies pay UAE corporate tax?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'A Qualifying Free Zone Person pays 0% on qualifying income and 9% on non-qualifying income. The conditions are strict — substance requirements, audited accounts and de minimis limits on non-qualifying revenue.']],
        ['@type' => 'Question', 'name' => 'Is taxable income the same as profit?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Taxable income starts from accounting net profit and is adjusted for exempt income, disallowed expenses and other corrections defined in the law. For a quick estimate, accounting profit is a reasonable starting point — confirm the exact figure with your accountant.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Corporate Tax Calculator — UAE (9%)') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="revenue" class="field-label">{{ __('Annual revenue') }} (AED)</label>
                    <input type="number" step="0.01" min="0" id="revenue" name="revenue"
                           value="{{ old('revenue') }}" required class="field-input" dir="ltr">
                    <p class="field-help">{{ __('Total turnover — used for the Small Business Relief check') }}</p>
                    @error('revenue')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="taxable_income" class="field-label">{{ __('Taxable income (profit)') }} (AED)</label>
                    <input type="number" step="0.01" min="0" id="taxable_income" name="taxable_income"
                           value="{{ old('taxable_income') }}" required class="field-input" dir="ltr">
                    @error('taxable_income')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <label class="flex items-start gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light" for="sbr_elected">
                <input type="checkbox" id="sbr_elected" name="sbr_elected" value="1" @checked(old('sbr_elected'))
                       class="mt-0.5 h-4 w-4 rounded border-line text-brand focus:ring-brand">
                <span>
                    {{ __('Elect Small Business Relief') }}
                    <span class="block text-xs text-ink-faint">{{ __('Revenue ≤ AED 3M; tax periods ending on or before 31 December 2026') }}</span>
                </span>
            </label>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Corporate tax due') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">AED {{ number_format($result['tax'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Effective tax rate') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">{{ number_format($result['effective_rate'] * 100, 2) }}%</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Profit after tax') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">AED {{ number_format($result['net_income'], 2) }}</p>
                </div>
            </div>
            @if($result['sbr_applied'])
                <p class="mt-3 text-sm font-semibold text-brand">{{ __('Small Business Relief applied — no corporate tax is due for this period.') }}</p>
            @elseif(! $result['sbr_eligible'])
                <p class="mt-3 text-xs text-amber-700">{{ __('Small Business Relief was not applied: revenue exceeds AED 3,000,000.') }}</p>
            @endif
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
            <p class="mt-4 text-xs text-ink-faint">{{ __('Estimate only — taxable income involves adjustments to accounting profit. Confirm your filing position with a registered tax agent.') }}</p>
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How UAE corporate tax works') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('Since June 2023 the UAE levies corporate tax under Federal Decree-Law No. 47 of 2022: 0% on the first AED 375,000 of taxable income and 9% on everything above. Small businesses with revenue of AED 3 million or less can elect Small Business Relief and pay nothing — but only for tax periods ending on or before 31 December 2026, so the relief is running out. Qualifying Free Zone Persons keep 0% on qualifying income subject to strict conditions. All taxable persons must register with the Federal Tax Authority and file a return within nine months of their period end.') }}</p>
    </div>
</div>
@endsection
