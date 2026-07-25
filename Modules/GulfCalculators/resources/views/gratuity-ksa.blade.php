@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('End of Service Calculator — Saudi Arabia'))
@section('meta_description', __('Calculate your Saudi end-of-service award under Labor Law Articles 84 and 85 — half a month per year for the first 5 years, a full month after, with resignation fractions applied.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How is end-of-service calculated in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Under Article 84 of the Saudi Labor Law, the award is half a month\'s wage per year for the first five years of service and one month\'s wage per year thereafter, based on the last wage, with fractions of a year pro-rated.']],
        ['@type' => 'Question', 'name' => 'What happens to end-of-service if I resign in Saudi Arabia?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Article 85 applies fractions on resignation: under 2 years of service — no award; 2 to 5 years — one third; 5 to 10 years — two thirds; 10 or more years — the full award.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('End of Service Calculator — Saudi Arabia') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div>
                <label for="monthly_wage" class="field-label">{{ __('Monthly wage') }} (SAR)</label>
                <input type="number" step="0.01" min="1" id="monthly_wage" name="monthly_wage"
                       value="{{ old('monthly_wage') }}" required class="field-input" dir="ltr">
                <p class="field-help">{{ __('The last wage — for most employees this is the full monthly wage.') }}</p>
                @error('monthly_wage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="start_date" class="field-label">{{ __('Start date') }}</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required class="field-input" dir="ltr">
                    @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_date" class="field-label">{{ __('End date') }}</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required class="field-input" dir="ltr">
                    @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <fieldset>
                <legend class="field-label">{{ __('How did the employment end?') }}</legend>
                <div class="mt-1 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="end_type" value="termination" class="text-brand focus:ring-brand" @checked(old('end_type', 'termination') === 'termination')>
                        {{ __('Termination by employer') }}
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm has-[:checked]:border-brand has-[:checked]:bg-brand-light">
                        <input type="radio" name="end_type" value="resignation" class="text-brand focus:ring-brand" @checked(old('end_type') === 'resignation')>
                        {{ __('Resignation') }}
                    </label>
                </div>
                @error('end_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </fieldset>

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            @if(!$result['eligible'])
                <div class="rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    {{ __('Resignation with under 2 years of service: no award (Art. 85)') }}
                </div>
            @else
                <p class="text-sm font-semibold uppercase tracking-wide text-ink-faint">{{ __('Total gratuity') }}</p>
                <p class="tabular mt-1 text-4xl font-bold text-brand" dir="ltr">SAR {{ number_format($result['gratuity'], 2) }}</p>
            @endif
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How the Saudi end-of-service award works') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('Article 84 of the Saudi Labor Law grants half a month’s wage per year for the first five years and a full month’s wage per year beyond five, based on the last wage. On resignation, Article 85 reduces the award: nothing under 2 years, one third between 2 and 5 years, two thirds between 5 and 10 years, and the full award from 10 years of service.') }}</p>
    </div>
</div>
@endsection
