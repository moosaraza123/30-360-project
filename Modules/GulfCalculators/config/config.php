<?php

return [
    'name' => 'GulfCalculators',

    /*
    | Default precious-metal prices per gram, used for the zakat nisab.
    | Editable on the page by the user; update these defaults periodically
    | (roadmap: replace with a metals price API + daily cache).
    */
    'zakat' => [
        // Live prices via metalpriceapi.com when GOLD_API_KEY is set;
        // these static values are the fallback when the API is unavailable.
        'api_key' => env('GOLD_API_KEY'),
        'gold_price_per_gram' => [
            'AED' => 390.0,
            'SAR' => 400.0,
        ],
        'prices_updated_at' => '2026-07-24',
    ],

    'vat_rates' => [
        'uae' => 0.05,
        'ksa' => 0.15,
    ],

    /*
    | Page registry: slug => metadata. Single source for routes, sitemap and
    | hreflang generation. 'source' feeds the on-page trust citation block.
    */
    'pages' => [
        'gratuity-calculator-uae' => [
            'title' => 'End of Service Gratuity Calculator — UAE',
            'source' => 'UAE Federal Decree-Law No. 33 of 2021, Article 51',
            'source_url' => 'https://www.mohre.gov.ae/',
            'reviewed' => '2026-07-24',
        ],
        'end-of-service-calculator-saudi-arabia' => [
            'title' => 'End of Service Calculator — Saudi Arabia',
            'source' => 'Saudi Labor Law, Articles 84–85',
            'source_url' => 'https://hrsd.gov.sa/',
            'reviewed' => '2026-07-24',
        ],
        'vat-calculator-uae' => [
            'title' => 'VAT Calculator — UAE (5%)',
            'source' => 'UAE Federal Decree-Law No. 8 of 2017 (5% standard rate)',
            'source_url' => 'https://tax.gov.ae/',
            'reviewed' => '2026-07-24',
        ],
        'vat-calculator-saudi-arabia' => [
            'title' => 'VAT Calculator — Saudi Arabia (15%)',
            'source' => 'ZATCA — 15% standard rate effective 1 July 2020',
            'source_url' => 'https://zatca.gov.sa/',
            'reviewed' => '2026-07-24',
        ],
        'zakat-calculator' => [
            'title' => 'Zakat Calculator',
            'source' => 'Nisab: 85g of gold; rate 2.5% (classical fiqh consensus)',
            'source_url' => null,
            'reviewed' => '2026-07-24',
        ],
    ],
];
