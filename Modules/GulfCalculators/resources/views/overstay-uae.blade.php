@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('Visa Overstay Fine Calculator — UAE'))
@section('meta_description', __('Calculate your UAE visa overstay fine — AED 50 per day for all visa types, with grace periods for residence visas taken into account.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How much is the overstay fine in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'AED 50 for every day of overstay, for all visa types — tourist, visit and residence. The ICP unified the daily rate across all emirates.']],
        ['@type' => 'Question', 'name' => 'Is there a grace period after my UAE visa expires?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Tourist and visit visas have no grace period — fines start the day after expiry. A cancelled or expired residence visa carries a 30-day grace period by default, extended up to 180 days for Golden Visa holders.']],
        ['@type' => 'Question', 'name' => 'How do I pay a UAE overstay fine?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Fines can be paid online through the ICP Smart Services portal, the GDRFA Dubai website, the UAE Pass app, or in person at an Amer centre (Dubai) or immigration office when exiting.']],
        ['@type' => 'Question', 'name' => 'Can UAE overstay fines be waived or reduced?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The UAE has periodically run amnesty programmes that waive overstay fines for those who regularise their status or exit. Outside amnesties, fines generally must be settled before exit or status change.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('Visa Overstay Fine Calculator — UAE') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <fieldset>
                <legend class="field-label">{{ __('Visa type') }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-3">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="visa_type" value="tourist" class="text-brand focus:ring-brand" @checked(old('visa_type', 'tourist') === 'tourist')>
                        {{ __('Tourist / visit') }} <span class="text-ink-faint">({{ __('no grace') }})</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="visa_type" value="residence" class="text-brand focus:ring-brand" @checked(old('visa_type') === 'residence')>
                        {{ __('Residence (cancelled/expired)') }} <span class="text-ink-faint">(30 {{ __('days') }})</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="visa_type" value="golden" class="text-brand focus:ring-brand" @checked(old('visa_type') === 'golden')>
                        {{ __('Golden Visa') }} <span class="text-ink-faint">(180 {{ __('days') }})</span>
                    </label>
                </div>
                @error('visa_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="expiry_date" class="field-label">{{ __('Visa expiry / cancellation date') }}</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}"
                           required class="field-input" dir="ltr">
                    @error('expiry_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="settlement_date" class="field-label">{{ __('Exit / settlement date') }}</label>
                    <input type="date" id="settlement_date" name="settlement_date"
                           value="{{ old('settlement_date', now()->toDateString()) }}" required class="field-input" dir="ltr">
                    <p class="field-help">{{ __('Defaults to today') }}</p>
                    @error('settlement_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Overstay fine') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-brand" dir="ltr">AED {{ number_format($result['fine'], 2) }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Chargeable days') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">{{ $result['overstay_days'] }}</p>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Grace period ends') }}</p>
                    <p class="tabular mt-1 text-2xl font-bold text-ink" dir="ltr">{{ $result['grace_end'] }}</p>
                </div>
            </div>
            @if($result['overstay_days'] === 0)
                <p class="mt-3 text-sm font-semibold text-brand">{{ __('You are within the grace period — no fine is due if you exit or regularise by this date.') }}</p>
            @endif
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
            <p class="mt-4 text-xs text-ink-faint">{{ __('Administrative and exit-permit service fees may apply on top. Check your exact fine on ICP Smart Services or GDRFA before paying.') }}</p>
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How UAE overstay fines work') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('The UAE charges a unified AED 50 for every day you remain in the country beyond your authorised stay, regardless of visa type or emirate. Tourist and visit visas have no grace period, so the fine starts accruing the day after expiry. If your residence visa is cancelled or expires you get a 30-day grace period to exit, change status or renew — extended to as much as 180 days for Golden Visa holders. Fines are payable online via ICP Smart Services, GDRFA Dubai or UAE Pass, or at the border when exiting.') }}</p>
    </div>
</div>
@endsection
