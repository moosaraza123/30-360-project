@extends('layouts.platform')

@php $isAr = app()->getLocale() === 'ar'; $p = $isAr ? 'ar/' : ''; @endphp

@section('title', __('Trusted financial calculators for the Gulf'))
@section('meta_description', __('Accurate, source-cited calculators for salaries, taxes, zakat and fixed income — in English and Arabic.'))
@section('alternate', $isAr ? url('/') : url('/ar'))
@section('canonical', url(request()->path() === '/' ? '' : request()->path()))

@section('structured_data')
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => __('Hisabi'),
    'url' => url('/'),
    'description' => __('Trusted financial calculators for the Gulf'),
    'inLanguage' => [app()->getLocale()],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')
{{-- Hero --}}
<section class="border-b border-line bg-white">
    <div class="mx-auto max-w-content px-4 py-16 text-center sm:px-6 sm:py-20">
        <h1 class="mx-auto max-w-2xl text-3xl font-bold leading-tight text-ink sm:text-5xl">
            {{ __('Trusted financial calculators for the Gulf') }}
        </h1>
        <p class="mx-auto mt-4 max-w-xl text-base text-ink-faint sm:text-lg">
            {{ __('Accurate, source-cited calculators for salaries, taxes, zakat and fixed income — in English and Arabic.') }}
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ url($p.'gratuity-calculator-uae') }}" class="btn-primary">{{ __('Gratuity UAE') }}</a>
            <a href="{{ url($p.'zakat-calculator') }}" class="btn-secondary">{{ __('Zakat') }}</a>
        </div>
    </div>
</section>

{{-- Calculator grid --}}
<section class="mx-auto max-w-content px-4 py-14 sm:px-6">
    <h2 class="text-xl font-bold text-ink">{{ __('Explore all calculators') }}</h2>
    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([
            ['url' => $p.'gratuity-calculator-uae', 'title' => __('End of Service Gratuity Calculator — UAE'), 'desc' => __('21 days per year for the first 5 years, 30 after — per the 2021 labor law.'), 'tag' => 'UAE'],
            ['url' => $p.'end-of-service-calculator-saudi-arabia', 'title' => __('End of Service Calculator — Saudi Arabia'), 'desc' => __('Labor Law Articles 84–85, including resignation fractions.'), 'tag' => 'KSA'],
            ['url' => $p.'vat-calculator-uae', 'title' => __('VAT Calculator — UAE (5%)'), 'desc' => __('Add or remove 5% VAT instantly.'), 'tag' => 'UAE'],
            ['url' => $p.'vat-calculator-saudi-arabia', 'title' => __('VAT Calculator — Saudi Arabia (15%)'), 'desc' => __('Add or remove 15% VAT instantly.'), 'tag' => 'KSA'],
            ['url' => $p.'zakat-calculator', 'title' => __('Zakat Calculator'), 'desc' => __('2.5% above the nisab — with a clear breakdown.'), 'tag' => __('Zakat')],
        ] as $calc)
            <a href="{{ url($calc['url']) }}" class="card group p-6 transition hover:shadow-card-hover">
                <span class="inline-block rounded-full bg-brand-light px-2.5 py-0.5 text-xs font-semibold text-brand-dark">{{ $calc['tag'] }}</span>
                <h3 class="mt-3 font-bold text-ink group-hover:text-brand">{{ $calc['title'] }}</h3>
                <p class="mt-1.5 text-sm text-ink-faint">{{ $calc['desc'] }}</p>
            </a>
        @endforeach

        <a href="{{ route('calculator.index') }}" class="card group p-6 transition hover:shadow-card-hover">
            <span class="inline-block rounded-full bg-gold/15 px-2.5 py-0.5 text-xs font-semibold text-gold-dark">{{ __('Professional') }}</span>
            <h3 class="mt-3 font-bold text-ink group-hover:text-brand">{{ __('Day Count') }} — 30/360, Act/360, Act/Act</h3>
            <p class="mt-1.5 text-sm text-ink-faint">{{ __('ISDA-verified day count conventions for bonds, sukuk and derivatives.') }}</p>
        </a>
    </div>
</section>

{{-- Trust strip --}}
<section class="border-t border-line bg-white">
    <div class="mx-auto max-w-content px-4 py-14 sm:px-6">
        <h2 class="text-center text-xl font-bold text-ink">{{ __('Why Hisabi?') }}</h2>
        <div class="mt-8 grid gap-8 text-center sm:grid-cols-3">
            <div>
                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-brand-light text-brand">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <h3 class="mt-3 font-bold text-ink">{{ __('Source-cited') }}</h3>
                <p class="mt-1 text-sm text-ink-faint">{{ __('Every calculator cites the official law or standard it implements.') }}</p>
            </div>
            <div>
                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-brand-light text-brand">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                </div>
                <h3 class="mt-3 font-bold text-ink">{{ __('Step-by-step') }}</h3>
                <p class="mt-1 text-sm text-ink-faint">{{ __('See exactly how every number was calculated — no black boxes.') }}</p>
            </div>
            <div>
                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-brand-light text-brand">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a9.004 9.004 0 0 1 8.716 6.747M12 3a9.004 9.004 0 0 0-8.716 6.747M21.75 12H2.25"/></svg>
                </div>
                <h3 class="mt-3 font-bold text-ink">{{ __('Bilingual') }}</h3>
                <p class="mt-1 text-sm text-ink-faint">{{ __('Full Arabic and English, built for the Gulf.') }}</p>
            </div>
        </div>
    </div>
</section>
@endsection
