<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Day Count Calculator') — Professional Financial Calculator</title>

    <meta name="description" content="@yield('meta_description', 'Professional day count convention calculator for financial instruments. Calculate 30/360, Actual/365, Actual/360, and more with step-by-step explanations.')">
    <meta name="keywords" content="@yield('meta_keywords', 'day count calculator, 30/360, actual/365, bond calculator, interest calculator, financial calculator')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:title" content="@yield('og_title', 'Day Count Calculator — Professional Financial Calculator')">
    <meta property="og:description" content="@yield('og_description', 'Professional day count convention calculator for bonds, loans, and derivatives. Calculate 30/360, Actual/365, Actual/360 and more.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="30/360 Day Count Calculator">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Day Count Calculator — Professional Financial Calculator')">
    <meta name="twitter:description" content="@yield('og_description', 'Professional day count convention calculator for bonds, loans, and derivatives.')">
    <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">

    @hasSection('structured_data')
        @yield('structured_data')
    @else
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "30/360 Day Count Calculator",
        "url": "{{ url('/calculator') }}",
        "description": "Professional day count convention calculator for financial instruments including bonds, loans, and derivatives.",
        "applicationCategory": "FinanceApplication",
        "operatingSystem": "Web",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        }
    }
    </script>
    @endif

    @if(config('daycountcalculator.adsense_client'))
        {{-- Google AdSense (Auto Ads). Enabled only when ADSENSE_CLIENT is set. --}}
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('daycountcalculator.adsense_client') }}" crossorigin="anonymous"></script>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['Modules/DayCountCalculator/resources/assets/sass/app.scss'])

    @stack('styles')
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-navy">
        <div class="container">
            <a class="navbar-brand" href="{{ route('calculator.index') }}">
                <span class="brand-mark">30</span><span class="brand-slash">/</span><span class="brand-360">360</span>&nbsp;Calculator
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('calculator.index') ? 'active' : '' }}" href="{{ route('calculator.index') }}">Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('comparison.*') ? 'active' : '' }}" href="{{ route('comparison.index') }}">Compare</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('calculator.history') ? 'active' : '' }}" href="{{ route('calculator.history') }}">History</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('calculator.saved') ? 'active' : '' }}" href="{{ route('calculator.saved') }}">Saved</a>
                        </li>
                        <li class="nav-item dropdown ms-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <span style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#c9a227,#a07d18);color:#0f172a;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.75rem;flex-shrink:0;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="nav-user-name d-block">{{ auth()->user()->email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Sign Out</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-1">
                            <a class="nav-link" href="{{ route('login') }}">Sign In</a>
                        </li>
                        <li class="nav-item ms-1">
                            <a class="btn btn-gold btn-sm px-3" href="{{ route('register') }}">Get Started</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3 mb-0 rounded-3" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3 mb-0 rounded-3" role="alert">
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show mx-3 mt-3 mb-0 rounded-3" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-navy mt-5 py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="footer-brand mb-2" style="font-size:1.1rem;">
                        <span style="color:#c9a227;font-weight:900;">30</span><span style="color:rgba(255,255,255,0.3);font-weight:300;">/</span><span style="font-weight:700;">360</span> Calculator
                    </div>
                    <p class="footer-tagline">Professional financial calculator for day count conventions used in bonds, loans, and derivatives.</p>
                </div>
                <div class="col-md-3 offset-md-1">
                    <h6 class="mb-3" style="letter-spacing:.05em;text-transform:uppercase;font-size:.72rem;color:rgba(255,255,255,.4);">Quick Links</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a href="{{ route('calculator.index') }}">Calculator</a></li>
                        <li class="mb-2"><a href="{{ route('comparison.index') }}">Comparison Tool</a></li>
                        <li class="mb-2"><a href="{{ route('calculator.history') }}">History</a></li>
                        @auth<li><a href="{{ route('calculator.saved') }}">Saved Calculations</a></li>@endauth
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-3" style="letter-spacing:.05em;text-transform:uppercase;font-size:.72rem;color:rgba(255,255,255,.4);">Stay Updated</h6>
                    <form action="{{ route('subscribe.subscribe') }}" method="POST" class="subscribe-form">
                        @csrf
                        <input type="hidden" name="source" value="footer">
                        <div class="input-group">
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="your@email.com" required>
                            <button type="submit" class="btn btn-gold btn-sm">Subscribe</button>
                        </div>
                        <small style="color:rgba(255,255,255,.35);font-size:.72rem;" class="d-block mt-1">New features &amp; calculator updates.</small>
                    </form>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center footer-copyright">
                &copy; {{ date('Y') }} 30/360 Calculator. All rights reserved.
                <span class="mx-2" style="opacity:.3;">|</span>
                Built with <a href="https://laravel.com" style="color:#c9a227;text-decoration:none;">Laravel</a>
            </div>
        </div>
    </footer>

    @vite(['Modules/DayCountCalculator/resources/assets/js/app.js'])

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
