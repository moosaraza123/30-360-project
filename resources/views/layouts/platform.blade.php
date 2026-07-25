<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Hisabi')) — {{ __('Hisabi') }}</title>

    <meta name="description" content="@yield('meta_description', __('Accurate, source-cited calculators for salaries, taxes, zakat and fixed income — in English and Arabic.'))">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- hreflang alternates: pages with an Arabic twin set the 'alternate' section --}}
    @hasSection('alternate')
        @if(app()->getLocale() === 'ar')
            <link rel="alternate" hreflang="en" href="@yield('alternate')">
            <link rel="alternate" hreflang="ar" href="{{ url()->current() }}">
        @else
            <link rel="alternate" hreflang="ar" href="@yield('alternate')">
            <link rel="alternate" hreflang="en" href="{{ url()->current() }}">
        @endif
        <link rel="alternate" hreflang="x-default" href="@if(app()->getLocale() === 'ar')@yield('alternate')@else{{ url()->current() }}@endif">
    @endif

    <meta property="og:title" content="@yield('title', __('Hisabi')) — {{ __('Hisabi') }}">
    <meta property="og:description" content="@yield('meta_description', __('Trusted financial calculators for the Gulf'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ __('Hisabi') }}">

    @yield('structured_data')

    @if(config('daycountcalculator.adsense_client'))
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('daycountcalculator.adsense_client') }}" crossorigin="anonymous"></script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex min-h-screen flex-col">

    @include('partials.nav')

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mx-auto mt-4 w-full max-w-content px-4 sm:px-6">
            <div class="rounded-lg border border-brand/30 bg-brand-light px-4 py-3 text-sm text-brand-dark">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto mt-4 w-full max-w-content px-4 sm:px-6">
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        </div>
    @endif

    {{-- Main --}}
    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')

    @if(app()->environment('production') && config('services.google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.google_analytics_id') }}');
        </script>
    @endif
</body>
</html>
