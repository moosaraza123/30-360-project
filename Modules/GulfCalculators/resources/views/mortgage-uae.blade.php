@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Mortgage Affordability Calculator — UAE'))
@section('meta_description', __('How much mortgage can you afford in the UAE? Calculate your maximum loan, property price and down payment under Central Bank rules — 50% DBR and LTV caps.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How much mortgage can I afford in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Two Central Bank rules cap your borrowing: your total monthly debt repayments cannot exceed 50% of your income (Debt Burden Ratio), and the loan amount cannot exceed 7 years of annual income for expats (8 for UAE nationals). The lower of the two limits applies.']],
        ['@type' => 'Question', 'name' => 'What deposit do I need to buy property in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'For a first home under AED 5 million: at least 20% for expats (80% LTV) and 15% for UAE nationals (85% LTV). Above AED 5 million the deposit rises to 30%/25%. For a second or investment property it is 40% for expats and 35% for nationals.']],
        ['@type' => 'Question', 'name' => 'What is the maximum mortgage term in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => '25 years, subject to a maximum age at final payment of 65 for salaried borrowers (70 for the self-employed).']],
        ['@type' => 'Question', 'name' => 'What is the Debt Burden Ratio (DBR)?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The share of your monthly income going to debt repayments — the proposed mortgage plus car loans, personal loans and 5% of credit card limits. The Central Bank caps it at 50% of income.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Mortgage Affordability Calculator — UAE') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="monthly_income" class="field-label">{{ __('Monthly income') }} (AED)</label>
                    <input type="number" step="0.01" min="1" id="monthly_income" name="monthly_income"
                           value="{{ old('monthly_income') }}" required class="field-input" dir="ltr">
                    @error('monthly_income')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="obligations" class="field-label">{{ __('Existing monthly repayments') }} (AED)</label>
                    <input type="number" step="0.01" min="0" id="obligations" name="obligations"
                           value="{{ old('obligations') }}" placeholder="0" class="field-input" dir="ltr">
                    <p class="field-help">{{ __('Car loans, personal loans, 5% of credit card limits') }}</p>
                    @error('obligations')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="annual_rate" class="field-label">{{ __('Interest / profit rate') }} (% {{ __('per year') }})</label>
                    <input type="number" step="0.01" min="0" max="30" id="annual_rate" name="annual_rate"
                           value="{{ old('annual_rate', 4.5) }}" required class="field-input" dir="ltr">
                    @error('annual_rate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="years" class="field-label">{{ __('Term') }} ({{ __('years') }})</label>
                    <input type="number" step="1" min="1" max="25" id="years" name="years"
                           value="{{ old('years', 25) }}" required class="field-input" dir="ltr">
                    <p class="field-help">{{ __('Maximum 25 years') }}</p>
                    @error('years')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <fieldset>
                    <legend class="field-label">{{ __('Buyer') }}</legend>
                    <div class="mt-1 grid gap-3">
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                            <input type="radio" name="buyer" value="expat" class="text-brand focus:ring-brand" @checked(old('buyer', 'expat') === 'expat')>
                            {{ __('Expatriate') }}
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                            <input type="radio" name="buyer" value="national" class="text-brand focus:ring-brand" @checked(old('buyer') === 'national')>
                            {{ __('UAE national') }}
                        </label>
                    </div>
                    @error('buyer')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </fieldset>
                <fieldset>
                    <legend class="field-label">{{ __('Property') }}</legend>
                    <div class="mt-1 grid gap-3">
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                            <input type="radio" name="property_type" value="first" class="text-brand focus:ring-brand" @checked(old('property_type', 'first') === 'first')>
                            {{ __('First home (own use)') }}
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                            <input type="radio" name="property_type" value="second" class="text-brand focus:ring-brand" @checked(old('property_type') === 'second')>
                            {{ __('Second / investment property') }}
                        </label>
                    </div>
                    @error('property_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </fieldset>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            @if(! $result['eligible'])
                <p class="text-sm font-semibold text-red-600">{{ __('Your existing repayments already use the full 50% debt capacity — a bank cannot offer a mortgage at this income. Reduce obligations first.') }}</p>
                @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
            @else
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-brand-light p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Maximum property price') }}</p>
                        <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">AED {{ number_format($result['max_property'], 0) }}</p>
                    </div>
                    <div class="rounded-lg bg-surface-muted p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Maximum loan') }}</p>
                        <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">AED {{ number_format($result['max_loan'], 0) }}</p>
                    </div>
                    <div class="rounded-lg bg-surface-muted p-4 text-center">
                        <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Down payment needed') }}</p>
                        <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">AED {{ number_format($result['down_payment'], 0) }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-ink-faint">
                    {{ $result['binding_constraint'] === 'dbr'
                        ? __('Your limit is set by the 50% Debt Burden Ratio.')
                        : __('Your limit is set by the income-multiple cap (7× annual income for expats, 8× for nationals).') }}
                </p>
                @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
                <p class="mt-4 text-xs text-ink-faint">{{ __('Excludes purchase costs (typically ~7-8%: DLD fee, agency, bank fees), which may not be financed. Banks also apply stress-tested rates.') }}</p>
            @endif
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How mortgage affordability works in the UAE') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('The Central Bank of the UAE caps mortgages three ways. Your Debt Burden Ratio — all monthly repayments including the new mortgage — cannot exceed 50% of income. The loan cannot exceed 7 years of annual income for expats (8 for nationals). And the loan-to-value is capped: for a first home under AED 5 million, 80% for expats and 85% for nationals (70%/75% above AED 5M); for a second or investment property, 60%/65%. This calculator applies all three and shows which one limits you.') }}</p>
    </div>
</div>
@endsection
