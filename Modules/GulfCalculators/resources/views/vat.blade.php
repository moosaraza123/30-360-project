@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
    $titleKey = $country === 'uae' ? 'VAT Calculator — UAE (5%)' : 'VAT Calculator — Saudi Arabia (15%)';
@endphp

@section('title', __($titleKey))
@section('meta_description', __('Add or remove :rate% VAT instantly. Free bilingual VAT calculator with the official standard rate.', ['rate' => $rate * 100]))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __($titleKey) }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div>
                <label for="amount" class="field-label">{{ __('Amount') }} ({{ $currency }})</label>
                <input type="number" step="0.01" min="0.01" id="amount" name="amount"
                       value="{{ old('amount') }}" required class="field-input" dir="ltr">
                @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="field-label">{{ __('What do you want to do?') }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="mode" value="add" class="text-brand focus:ring-brand" @checked(old('mode', 'add') === 'add')>
                        {{ __('Add VAT') }} <span class="text-ink-faint">({{ __('amount is before tax') }})</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="mode" value="remove" class="text-brand focus:ring-brand" @checked(old('mode') === 'remove')>
                        {{ __('Remove VAT') }} <span class="text-ink-faint">({{ __('amount includes tax') }})</span>
                    </label>
                </div>
                @error('mode')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            <dl class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Net amount') }}</dt>
                    <dd class="tabular mt-1 text-xl font-bold text-ink" dir="ltr">{{ $currency }} {{ number_format($result['net'], 2) }}</dd>
                </div>
                <div class="rounded-lg bg-brand-light p-4 text-center">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('VAT amount') }} ({{ $rate * 100 }}%)</dt>
                    <dd class="tabular mt-1 text-xl font-bold text-brand" dir="ltr">{{ $currency }} {{ number_format($result['vat'], 2) }}</dd>
                </div>
                <div class="rounded-lg bg-surface-muted p-4 text-center">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-ink-faint">{{ __('Gross amount') }}</dt>
                    <dd class="tabular mt-1 text-xl font-bold text-ink" dir="ltr">{{ $currency }} {{ number_format($result['gross'], 2) }}</dd>
                </div>
            </dl>
        </div>
    @endif
</div>
@endsection
