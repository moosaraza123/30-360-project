@extends('layouts.platform')

@php
    $altPath = app()->getLocale() === 'ar'
        ? preg_replace('#^ar/#', '', request()->path())
        : 'ar/'.request()->path();
@endphp

@section('title', __('End of Service Gratuity Calculator — UAE'))
@section('meta_description', __('Calculate your UAE end-of-service gratuity under Federal Decree-Law 33 of 2021 — 21 days per year for the first 5 years, 30 days after, with the 2-year cap applied.'))
@section('alternate', url($altPath))
@section('canonical', url(request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'How is gratuity calculated in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Under Article 51 of Federal Decree-Law No. 33 of 2021, an employee earns 21 days of basic wage per year for the first 5 years of service and 30 days per year after that. The daily wage is the basic monthly salary divided by 30, fractions of a year are pro-rated, and the total cannot exceed 2 years of wage.']],
        ['@type' => 'Question', 'name' => 'Does resignation reduce gratuity in the UAE?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'No. Under the 2021 labor law, employees who resign after completing at least one year of continuous service receive their full end-of-service gratuity.']],
        ['@type' => 'Question', 'name' => 'Is gratuity calculated on basic salary or gross salary?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'On the basic salary only. Allowances such as housing and transport are excluded from the gratuity calculation.']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ __('End of Service Gratuity Calculator — UAE') }}</h1>
    @include('gulfcalculators::partials.trust')

    <div class="card mt-6 p-6 sm:p-8">
        <form method="POST" action="{{ url(request()->path()) }}" class="space-y-5">
            @csrf
            <div>
                <label for="basic_salary" class="field-label">{{ __('Basic monthly salary') }} (AED)</label>
                <input type="number" step="0.01" min="1" id="basic_salary" name="basic_salary"
                       value="{{ old('basic_salary') }}" required class="field-input" dir="ltr">
                <p class="field-help">{{ __('Basic salary only — housing and other allowances are excluded by law.') }}</p>
                @error('basic_salary')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
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

            <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('Calculate') }}</button>
        </form>
    </div>

    @if($result !== null)
        <div class="card mt-6 border-brand/30 p-6 sm:p-8" id="result">
            @if(!$result['eligible'])
                <div class="rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    {{ __('Less than 1 year of continuous service — no gratuity entitlement under Article 51.') }}
                </div>
            @else
                <p class="text-sm font-semibold uppercase tracking-wide text-ink-faint">{{ __('Total gratuity') }}</p>
                <p class="tabular mt-1 text-4xl font-bold text-brand" dir="ltr">AED {{ number_format($result['gratuity'], 2) }}</p>
                @if($result['capped'])
                    <p class="mt-2 text-xs text-amber-700">{{ __('The legal cap of two years’ wage was applied.') }}</p>
                @endif
            @endif
            @include('gulfcalculators::partials.steps', ['steps' => $result['steps']])
        </div>
    @endif

    <div class="prose prose-sm mt-10 max-w-none text-ink-soft">
        <h2 class="text-lg font-bold text-ink">{{ __('How the UAE gratuity is calculated') }}</h2>
        <p class="mt-2 leading-relaxed">{{ __('Under Article 51 of Federal Decree-Law No. 33 of 2021, an employee who completes one year or more of continuous service is entitled to 21 days of basic wage for each of the first five years of service, and 30 days for each additional year — capped at two years’ total wage. The daily wage is the basic monthly salary divided by 30, and partial years are paid pro-rata. Under the current law, resignation does not reduce the entitlement.') }}</p>
    </div>
</div>
@endsection
