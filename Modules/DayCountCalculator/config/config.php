<?php

return [
    'name' => 'DayCountCalculator',

    /*
    |--------------------------------------------------------------------------
    | Google AdSense
    |--------------------------------------------------------------------------
    | Set ADSENSE_CLIENT (e.g. "ca-pub-1234567890123456") to enable the
    | AdSense Auto Ads script and the /ads.txt route. Leave empty to keep
    | the site entirely ad-free (local/dev default).
    */
    'adsense_client' => env('ADSENSE_CLIENT'),

    /*
    |--------------------------------------------------------------------------
    | Day Count Conventions — single source of truth
    |--------------------------------------------------------------------------
    | Consumed by the calculator UI, validation rules, comparison tool,
    | educational pages, and the sitemap. `type` is the canonical identifier
    | stored in the database; `slug` is the SEO-friendly URL segment.
    */
    'conventions' => [
        [
            'type' => '30/360 US',
            'slug' => '30-360-us',
            'name' => '30/360 US',
            'alias' => 'Bond Basis',
            'description' => 'Standard convention for US corporate bonds',
            'icon' => 'building',
            'use_cases' => ['US corporate bonds', 'Municipal bonds', 'Agency bonds'],
            'formula' => 'Days = 360×(Y2-Y1) + 30×(M2-M1) + (D2-D1)',
            'related' => ['30/360 Bond Basis', '30E/360', '30E/360 ISDA'],
        ],
        [
            'type' => '30/360 Bond Basis',
            'slug' => '30-360-bond-basis',
            'name' => '30/360 Bond Basis',
            'alias' => 'Same as 30/360 US',
            'description' => 'Alias for 30/360 US convention',
            'icon' => 'building',
            'use_cases' => ['US corporate bonds', 'Municipal bonds'],
            'formula' => 'Days = 360×(Y2-Y1) + 30×(M2-M1) + (D2-D1)',
            'related' => ['30/360 US', '30E/360'],
        ],
        [
            'type' => '30E/360',
            'slug' => '30e-360',
            'name' => '30E/360',
            'alias' => 'Eurobond Basis',
            'description' => 'European convention for international bonds',
            'icon' => 'globe',
            'use_cases' => ['Eurobonds', 'International bonds'],
            'formula' => 'Days = 360×(Y2-Y1) + 30×(M2-M1) + (D2-D1)',
            'related' => ['30/360 US', '30E/360 ISDA'],
        ],
        [
            'type' => '30E/360 ISDA',
            'slug' => '30e-360-isda',
            'name' => '30E/360 ISDA',
            'alias' => 'German',
            'description' => 'ISDA variant with different end-of-month handling',
            'icon' => 'file-text',
            'use_cases' => ['Interest rate swaps', 'German bonds'],
            'formula' => 'Days = 360×(Y2-Y1) + 30×(M2-M1) + (D2-D1)',
            'related' => ['30E/360', 'Actual/Actual ISDA'],
        ],
        [
            'type' => 'Actual/365 Fixed',
            'slug' => 'actual-365-fixed',
            'name' => 'Actual/365 Fixed',
            'alias' => 'Act/365',
            'description' => 'Actual days divided by 365',
            'icon' => 'calendar',
            'use_cases' => ['UK Gilts', 'Japanese bonds', 'Euro-Sterling bonds'],
            'formula' => 'Factor = Actual Days / 365',
            'related' => ['Actual/360', 'Actual/Actual'],
        ],
        [
            'type' => 'Actual/360',
            'slug' => 'actual-360',
            'name' => 'Actual/360',
            'alias' => 'Money Market Basis',
            'description' => 'Actual days divided by 360',
            'icon' => 'dollar-sign',
            'use_cases' => ['Money market instruments', 'Short-term loans', 'Commercial paper'],
            'formula' => 'Factor = Actual Days / 360',
            'related' => ['Actual/365 Fixed', 'Actual/364'],
        ],
        [
            'type' => 'Actual/364',
            'slug' => 'actual-364',
            'name' => 'Actual/364',
            'alias' => 'Act/364',
            'description' => 'Actual days divided by 364',
            'icon' => 'calendar-check',
            'use_cases' => ['Some floating rate notes'],
            'formula' => 'Factor = Actual Days / 364',
            'related' => ['Actual/360', 'Actual/365 Fixed'],
        ],
        [
            'type' => 'Actual/Actual',
            'slug' => 'actual-actual',
            'name' => 'Actual/Actual',
            'alias' => 'Calendar-Year Split',
            'description' => 'Actual days weighted by the length of each calendar year (365/366). Not ICMA — true Act/Act ICMA requires a coupon schedule.',
            'icon' => 'clock',
            'use_cases' => ['Act/Act estimates without a coupon schedule'],
            'formula' => 'Factor = Σ Days in Year i / (365 or 366)',
            'related' => ['Actual/Actual ISDA', 'Actual/365 Fixed'],
        ],
        [
            'type' => 'Actual/Actual ISDA',
            'slug' => 'actual-actual-isda',
            'name' => 'Actual/Actual ISDA',
            'alias' => 'Act/Act ISDA',
            'description' => 'ISDA variant with separate leap/non-leap year calculation',
            'icon' => 'file-text',
            'use_cases' => ['Interest rate swaps', 'Many derivatives'],
            'formula' => 'Factor = (Leap Days/366) + (Non-Leap Days/365)',
            'related' => ['Actual/Actual', '30E/360 ISDA'],
        ],
    ],
];
