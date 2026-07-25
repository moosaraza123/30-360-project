<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hisabi') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body { font-family: 'Inter', sans-serif; min-height: 100vh; background: #f1f5f9; }

        /* Wrapper */
        .auth-wrapper { display: flex; min-height: 100vh; }

        /* Left brand panel */
        .auth-brand {
            width: 42%;
            flex-shrink: 0;
            background: linear-gradient(160deg, #0B1526 0%, #0A5F4E 55%, #0B1526 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3.5rem;
            position: relative;
            overflow: hidden;
        }

        .auth-brand::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(201,162,39,0.07);
            pointer-events: none;
        }

        .auth-brand::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(201,162,39,0.05);
            pointer-events: none;
        }

        .auth-brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 3rem;
        }

        .auth-brand-logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #0E7C66, #0A5F4E);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .auth-brand-logo-text {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.25;
        }

        .auth-brand-logo-text span {
            display: block;
            font-size: 0.68rem;
            font-weight: 400;
            color: rgba(255,255,255,0.45);
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        .auth-brand-divider {
            width: 48px; height: 3px;
            background: linear-gradient(90deg, #c9a227, transparent);
            border-radius: 2px;
            margin-bottom: 2rem;
        }

        .auth-brand-headline {
            font-size: 1.9rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            margin-bottom: 1rem;
        }

        .auth-brand-headline em { font-style: normal; color: #c9a227; }

        .auth-brand-tagline {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.55);
            line-height: 1.75;
            margin-bottom: 2.5rem;
        }

        .auth-brand-features { list-style: none; display: flex; flex-direction: column; gap: 1rem; }

        .auth-brand-features li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: rgba(255,255,255,0.72);
            font-size: 0.85rem;
            line-height: 1.55;
        }

        .auth-feature-icon {
            width: 20px; height: 20px; min-width: 20px;
            background: rgba(201,162,39,0.15);
            border: 1px solid rgba(201,162,39,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-top: 1px;
        }

        /* Right form panel */
        .auth-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2rem;
            background: #fff;
            overflow-y: auto;
        }

        .auth-form-inner {
            width: 100%;
            max-width: 440px;
            animation: authFadeUp 0.45s ease both;
        }

        @keyframes authFadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Shared input / label / button styles used by auth views */
        .auth-input {
            width: 100%;
            padding: 0.65rem 0.95rem;
            font-size: 0.915rem;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none;
        }

        .auth-input:focus {
            background: #fff;
            border-color: #c9a227;
            box-shadow: 0 0 0 3px rgba(201,162,39,0.13);
        }

        .auth-input::placeholder { color: #94a3b8; }

        .auth-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.4rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .auth-label-optional {
            font-weight: 400;
            color: #94a3b8;
            font-size: 0.72rem;
            text-transform: none;
            letter-spacing: 0;
            margin-left: 0.25rem;
        }

        .auth-field { margin-bottom: 1rem; }

        .auth-error {
            margin-top: 0.35rem;
            font-size: 0.78rem;
            color: #dc2626;
        }

        .auth-btn {
            width: 100%;
            padding: 0.78rem 1rem;
            font-size: 0.925rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #0E7C66 0%, #0A5F4E 100%);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            letter-spacing: 0.01em;
            transition: opacity 0.18s, transform 0.15s, box-shadow 0.18s;
            box-shadow: 0 2px 10px rgba(201,162,39,0.35);
        }

        .auth-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(201,162,39,0.42);
        }

        .auth-btn:active { transform: translateY(0); opacity: 1; }

        .auth-link {
            color: #0A5F4E;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.15s;
        }

        .auth-link:hover { color: #c9a227; }

        .auth-link-gold {
            color: #a07d18;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link-gold:hover { text-decoration: underline; }

        .auth-status-success {
            padding: 0.65rem 0.9rem;
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            color: #166534;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .auth-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 1rem;
        }

        .auth-section-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 1.2rem 0 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .auth-section-label::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

        .auth-checkbox-row { display: flex; align-items: center; gap: 0.5rem; }

        .auth-checkbox {
            width: 15px; height: 15px;
            accent-color: #0E7C66;
            cursor: pointer;
            flex-shrink: 0;
        }

        .auth-checkbox-label { font-size: 0.875rem; color: #475569; cursor: pointer; }

        .auth-page-heading {
            font-size: 1.55rem;
            font-weight: 700;
            color: #0B1526;
            margin-bottom: 0.35rem;
        }

        .auth-page-sub {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 1.75rem;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .auth-brand { display: none; }
            .auth-form-panel { background: #f1f5f9; }
            .auth-form-inner {
                background: #fff;
                border-radius: 16px;
                padding: 2rem 1.75rem;
                box-shadow: 0 4px 24px rgba(15,23,42,0.08);
            }
        }

        @media (max-width: 480px) {
            .auth-form-inner { padding: 1.5rem 1.25rem; }
            .auth-grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="auth-wrapper">

    {{-- Left Brand Panel --}}
    <aside class="auth-brand">
        <div class="auth-brand-logo">
            <div class="auth-brand-logo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
                    <circle cx="7.5" cy="6" r="1.8" fill="#fff"/>
                    <circle cx="12" cy="12" r="1.8" fill="#fff"/>
                    <circle cx="16.5" cy="18" r="1.8" fill="#fff"/>
                </svg>
            </div>
            <div class="auth-brand-logo-text">
                Hisabi
                <span>Trusted Gulf Finance Calculators</span>
            </div>
        </div>

        <div class="auth-brand-divider"></div>

        <h1 class="auth-brand-headline">
            Trusted financial calculators<br>for the <em>Gulf</em>
        </h1>

        <p class="auth-brand-tagline">
            Gratuity, VAT, zakat and professional day count calculators —
            source-cited, bilingual, and computed with full step-by-step transparency.
        </p>

        <ul class="auth-brand-features">
            <li>
                <div class="auth-feature-icon">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <path d="M2 5l2.2 2.2L8 3" stroke="#c9a227" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                Gratuity, VAT &amp; zakat calculators for UAE and KSA — in English &amp; Arabic
            </li>
            <li>
                <div class="auth-feature-icon">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <path d="M2 5l2.2 2.2L8 3" stroke="#c9a227" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                Every result cites the official law and shows its full calculation
            </li>
            <li>
                <div class="auth-feature-icon">
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <path d="M2 5l2.2 2.2L8 3" stroke="#c9a227" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                Save calculations, export to PDF &amp; CSV, compare conventions
            </li>
        </ul>
    </aside>

    {{-- Right Form Panel --}}
    <div class="auth-form-panel">
        <div class="auth-form-inner">
            {{ $slot }}
        </div>
    </div>

</div>
</body>
</html>
