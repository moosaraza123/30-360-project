@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Zakat Calculator'))
@section('meta_description', __('Calculate your zakat: 2.5% of zakatable wealth above the nisab (85g of gold). Cash, gold, silver, business assets and debts — with a clear breakdown.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How much zakat do I pay?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Zakat is 2.5% of your zakatable wealth (cash, gold, silver, business assets and expected receivables, minus short-term debts) if it meets or exceeds the nisab and has been held for one lunar year.']],
        ['@type' => 'Question', 'name' => 'What is the nisab?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The nisab is the minimum wealth at which zakat becomes due — the value of 85 grams of gold (or 595 grams of silver). This calculator uses the gold nisab by default.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Zakat Calculator') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5" x-data="{ currency: '{{ old('currency', 'AED') }}', prices: {{ json_encode($defaults['gold_price_per_gram']) }} }">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="currency" class="field-label">{{ __('Currency') }}</label>
                    <select id="currency" name="currency" class="field-input" x-model="currency"
                            @change="$refs.goldprice.value = prices[currency]">
                        <option value="AED">AED — {{ __('UAE Dirham') }}</option>
                        <option value="SAR">SAR — {{ __('Saudi Riyal') }}</option>
                    </select>
                </div>
                <div>
                    <label for="gold_price_per_gram" class="field-label">{{ __('Gold price per gram') }}</label>
                    <input type="number" step="0.01" min="0.01" id="gold_price_per_gram" name="gold_price_per_gram"
                           x-ref="goldprice" value="{{ old('gold_price_per_gram', $defaults['gold_price_per_gram']['AED']) }}"
                           required class="field-input" dir="ltr">
                    @if(!empty($defaults['live']))
                        <p class="field-help inline-flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-brand"></span>
                            {{ __('Live market price') }} · {{ $defaults['prices_updated_at'] }}
                        </p>
                    @else
                        <p class="field-help">{{ __('Default from') }} {{ $defaults['prices_updated_at'] }} — {{ __('adjust to today’s price for accuracy.') }}</p>
                    @endif
                </div>
            </div>

            @foreach([
                'cash' => __('Cash and bank balances'),
                'gold' => __('Gold value'),
                'silver' => __('Silver value'),
                'business' => __('Business assets and inventory'),
                'receivables' => __('Money owed to you (expected)'),
                'debts' => __('Short-term debts you owe'),
            ] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="field-label">{{ $label }}</label>
                    <input type="number" step="0.01" min="0" id="{{ $field }}" name="{{ $field }}"
                           value="{{ old($field) }}" placeholder="0" class="field-input" dir="ltr">
                    @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            @if($result['due'])
                <p class="text-sm font-semibold uppercase tracking-wide text-ink-faint">{{ __('Zakat due') }}</p>
                <p class="tabular mt-1 text-4xl font-bold text-brand" dir="ltr">{{ $result['currency'] }} {{ number_format($result['zakat'], 2) }}</p>
            @else
                <div class="rounded-lg bg-brand-light px-4 py-3 text-sm text-brand-dark">
                    {{ __('Your wealth is below the nisab threshold — zakat is not due.') }}
                </div>
            @endif

            <dl class="mt-6 divide-y divide-line rounded-lg border border-line">
                <div class="flex items-center justify-between px-4 py-3">
                    <dt class="text-sm text-ink-soft">{{ __('Total zakatable wealth') }}</dt>
                    <dd class="tabular text-sm font-semibold text-ink" dir="ltr">{{ $result['currency'] }} {{ number_format($result['zakatable'], 2) }}</dd>
                </div>
                <div class="flex items-center justify-between px-4 py-3">
                    <dt class="text-sm text-ink-soft">{{ __('Nisab threshold') }} ({{ $result['nisab_grams'] }}g × {{ number_format($result['gold_price_per_gram'], 2) }})</dt>
                    <dd class="tabular text-sm font-semibold text-ink" dir="ltr">{{ $result['currency'] }} {{ number_format($result['nisab'], 2) }}</dd>
                </div>
                <div class="flex items-center justify-between px-4 py-3">
                    <dt class="text-sm text-ink-soft">{{ __('Rate') }}</dt>
                    <dd class="tabular text-sm font-semibold text-ink" dir="ltr">2.5%</dd>
                </div>
            </dl>
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How zakat is calculated') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('Zakat is due at 2.5% on wealth held for a full lunar year (hawl) when it meets the nisab — the value of 85 grams of gold. Add your cash, gold, silver, business assets and receivables you expect to collect, subtract short-term debts, and if the result reaches the nisab, 2.5% of it is due. Consult a scholar for complex cases such as investment properties or pension funds.') }}</p>
    </div>
</div>
@endsection
