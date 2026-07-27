@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Real Estate Transaction Tax (RETT) Calculator — Saudi Arabia'))
@section('meta_description', __('Calculate the 5% Real Estate Transaction Tax on property purchases in Saudi Arabia, including the first-home relief of up to SAR 1 million for citizens.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'What is the RETT rate in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => '5% of the real estate transaction value, payable to ZATCA before or at deed transfer. The RETT Law and its executive regulations took effect in April 2025.']],
        ['@type' => 'Question', 'name' => 'Who pays RETT — buyer or seller?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The seller (transferor) is legally liable for RETT, though in practice the cost is often reflected in the agreed price. The transaction must be registered on the ZATCA RETT portal before deed transfer.']],
        ['@type' => 'Question', 'name' => 'Is a first home exempt from RETT in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'For a Saudi citizen\'s first home, the state bears the RETT on the first SAR 1,000,000 of the purchase price. Only the amount above SAR 1 million is taxed at 5%.']],
        ['@type' => 'Question', 'name' => 'Are any transactions exempt from RETT?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes — the law exempts specific cases such as inheritance divisions, gifts to close relatives, transfers to licensed real estate funds and certain corporate restructurings. Check the ZATCA regulations for the full list.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Real Estate Transaction Tax (RETT) Calculator — Saudi Arabia') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div>
                <label for="property_value" class="field-label">{{ __('Property / transaction value') }} (SAR)</label>
                <input type="number" step="0.01" min="1" id="property_value" name="property_value"
                       value="{{ old('property_value') }}" required class="field-input" dir="ltr">
                @error('property_value')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-start gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light" for="first_home">
                <input type="checkbox" id="first_home" name="first_home" value="1" @checked(old('first_home'))
                       class="mt-0.5 h-4 w-4 rounded border-line text-brand focus:ring-brand">
                <span>
                    {{ __("First home of a Saudi citizen") }}
                    <span class="block text-xs text-ink-faint">{{ __('State bears the RETT on the first SAR 1,000,000') }}</span>
                </span>
            </label>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('RETT due') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">SAR {{ number_format($result['tax'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Taxable amount') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">SAR {{ number_format($result['taxable'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Total cost including RETT') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">SAR {{ number_format($result['total_cost'], 2) }}</p>
                </div>
            </div>
            @if($result['exempt'] > 0)
                <p class="mt-3 text-sm font-semibold text-brand">{{ __('First-home relief applied:') }} SAR {{ number_format($result['exempt'], 2) }} {{ __('of the price is borne by the state.') }}</p>
            @endif
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How RETT works in Saudi Arabia') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('Saudi Arabia levies a 5% Real Estate Transaction Tax on the value of property disposals — sales, transfers, long-term usufructs and certain financing structures — under the RETT Law in force since April 2025, administered by ZATCA. Every transaction must be registered on the ZATCA portal before the deed is transferred. For a Saudi citizen buying a first home, the state bears the tax on the first SAR 1,000,000 of the price; several other transaction types, such as inheritance divisions and gifts to close relatives, are exempt entirely.') }}</p>
    </div>
</div>
@endsection
