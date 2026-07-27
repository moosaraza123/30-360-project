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
        'gosi-calculator-saudi-arabia' => [
            'title' => 'GOSI & Net Salary Calculator — Saudi Arabia',
            'source' => 'GOSI Social Insurance Law — 9.75%/11.75% (Saudi), 2% (expat), SAR 45,000 cap',
            'source_url' => 'https://www.gosi.gov.sa/',
            'reviewed' => '2026-07-24',
        ],
        'salary-calculator-uae' => [
            'title' => 'Take-Home Salary Calculator — UAE',
            'source' => 'GPSSA pension law (Law 7/1999 & Law 57/2023); no personal income tax',
            'source_url' => 'https://gpssa.gov.ae/',
            'reviewed' => '2026-07-24',
        ],
        'loan-calculator' => [
            'title' => 'Loan & EMI Calculator — Flat vs Reducing Rate',
            'source' => 'Standard amortization formula; flat-rate APR equivalence shown',
            'source_url' => null,
            'reviewed' => '2026-07-24',
        ],
        'iqama-fees-calculator-saudi-arabia' => [
            'title' => 'Iqama Renewal Fees Calculator — Saudi Arabia',
            'source' => 'MHRSD work-permit levy (SAR 700–800/month) + dependent levy (SAR 400/month); iqama fee SAR 650/year',
            'source_url' => 'https://hrsd.gov.sa/',
            'reviewed' => '2026-07-27',
        ],
        'overstay-fine-calculator-uae' => [
            'title' => 'Visa Overstay Fine Calculator — UAE',
            'source' => 'ICP unified overstay fine: AED 50/day for all visa types',
            'source_url' => 'https://icp.gov.ae/',
            'reviewed' => '2026-07-27',
        ],
        'corporate-tax-calculator-uae' => [
            'title' => 'Corporate Tax Calculator — UAE (9%)',
            'source' => 'Federal Decree-Law No. 47 of 2022 — 0% up to AED 375,000, 9% above; Small Business Relief ≤ AED 3M revenue (periods ending by 31 Dec 2026)',
            'source_url' => 'https://tax.gov.ae/',
            'reviewed' => '2026-07-27',
        ],
        'mortgage-affordability-calculator-uae' => [
            'title' => 'Mortgage Affordability Calculator — UAE',
            'source' => 'CBUAE mortgage regulations — 50% DBR, LTV 80%/85% first home, max 25 years',
            'source_url' => 'https://rulebook.centralbank.ae/en/rulebook/regulations-regarding-mortgage-loans',
            'reviewed' => '2026-07-27',
        ],
        'personal-loan-eligibility-calculator-uae' => [
            'title' => 'Personal Loan Eligibility Calculator — UAE',
            'source' => 'CBUAE personal loan regulations — 20× monthly salary cap, 48-month max term, 50% DBR',
            'source_url' => 'https://rulebook.centralbank.ae/',
            'reviewed' => '2026-07-27',
        ],
        'rett-calculator-saudi-arabia' => [
            'title' => 'Real Estate Transaction Tax (RETT) Calculator — Saudi Arabia',
            'source' => 'ZATCA RETT Law — 5% of transaction value; first-home relief up to SAR 1M for citizens',
            'source_url' => 'https://zatca.gov.sa/en/RulesRegulations/Taxes/Pages/RETTRegulation.aspx',
            'reviewed' => '2026-07-27',
        ],
    ],
];
