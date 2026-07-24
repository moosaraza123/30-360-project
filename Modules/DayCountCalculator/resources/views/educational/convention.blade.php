@extends('daycountcalculator::layouts.master')

@section('title', $convention['name'] . ' - Day Count Convention')

@section('meta_description', 'Learn about ' . $convention['name'] . ' day count convention. ' . $convention['description'])

@section('canonical', route('calculator.educate', $convention['slug']))

@section('structured_data')
@php
    $structuredData = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => "What is the {$convention['name']} day count convention?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $convention['description'] . ($convention['alias'] ? " Also known as: {$convention['alias']}." : ''),
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "What is the formula for {$convention['name']}?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $convention['formula'],
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "When is {$convention['name']} used?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Common uses include: ' . implode(', ', $convention['use_cases']) . '.',
                    ],
                ],
            ],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Calculator', 'item' => route('calculator.index')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $convention['name'], 'item' => route('calculator.educate', $convention['slug'])],
            ],
        ],
    ];
@endphp
@foreach($structuredData as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
@endsection

@section('content')
<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('calculator.index') }}">Calculator</a></li>
                        <li class="breadcrumb-item active">{{ $convention['name'] }}</li>
                    </ol>
                </nav>

                <h1 class="h2 fw-bold mb-2">{{ $convention['name'] }}</h1>
                <p class="lead text-muted">{{ $convention['description'] }}</p>

                @if($convention['alias'])
                    <div class="alert alert-info">
                        <strong>Also Known As:</strong> {{ $convention['alias'] }}
                    </div>
                @endif
            </div>

            <!-- Formula -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Formula</h5>
                    <div class="bg-light p-3 rounded">
                        <code class="h6 text-dark">{{ $convention['formula'] }}</code>
                    </div>
                </div>
            </div>

            <!-- Common Use Cases -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Common Use Cases</h5>
                    <ul class="mb-0">
                        @foreach($convention['use_cases'] as $useCase)
                            <li class="mb-2">{{ $useCase }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Detailed Explanation -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">How It Works</h5>

                    @if(Str::contains($convention['type'], '30/360'))
                        <p>The {{ $convention['name'] }} convention assumes:</p>
                        <ul>
                            <li>Each month has 30 days</li>
                            <li>Each year has 360 days (12 months × 30 days)</li>
                            <li>Specific adjustment rules apply when dates fall on the 31st or last day of February</li>
                        </ul>

                        <h6 class="fw-semibold mt-4 mb-3">Adjustment Rules</h6>
                        @if(Str::contains($convention['type'], 'US') || Str::contains($convention['type'], 'Bond Basis'))
                            <ol>
                                <li class="mb-2">If the start day (D1) is 31, change it to 30</li>
                                <li class="mb-2">If the end day (D2) is 31 and D1 is 30 or 31, change D2 to 30</li>
                            </ol>
                        @elseif(Str::contains($convention['type'], '30E/360 ISDA'))
                            <ol>
                                <li class="mb-2">If D1 is the last day of February, change it to 30</li>
                                <li class="mb-2">If D2 is the last day of February, change it to 30</li>
                                <li class="mb-2">If D1 is 31, change it to 30</li>
                                <li class="mb-2">If D2 is 31, change it to 30</li>
                            </ol>
                        @elseif(Str::contains($convention['type'], '30E/360'))
                            <ol>
                                <li class="mb-2">If D1 is the last day of the month, change it to 30</li>
                                <li class="mb-2">If D2 is the last day of the month (unless it's the maturity date and in February), change it to 30</li>
                            </ol>
                        @endif

                    @elseif(Str::contains($convention['type'], 'Actual'))
                        <p>The {{ $convention['name'] }} convention uses:</p>
                        <ul>
                            <li>The actual number of days between two dates</li>
                            @if(Str::contains($convention['type'], 'Actual/Actual'))
                                <li>The actual number of days in the year (365 or 366 for leap years)</li>
                                @if(Str::contains($convention['type'], 'ISDA'))
                                    <li>Days are split between leap years (÷366) and non-leap years (÷365)</li>
                                @else
                                    <li>Weighted calculation when period spans multiple years</li>
                                @endif
                            @elseif(Str::contains($convention['type'], '365'))
                                <li>A fixed denominator of 365 days</li>
                            @elseif(Str::contains($convention['type'], '360'))
                                <li>A fixed denominator of 360 days (money market basis)</li>
                            @elseif(Str::contains($convention['type'], '364'))
                                <li>A fixed denominator of 364 days</li>
                            @endif
                        </ul>

                        <div class="alert alert-warning">
                            <strong>Note:</strong> This convention counts the actual calendar days, making it more precise for certain financial instruments.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Example Calculation -->
            <div class="card shadow-sm mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 fw-bold">Example Calculation</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Let's calculate the day count factor from <strong>January 15, 2024</strong> to <strong>July 15, 2024</strong>:</p>

                    @if(Str::contains($convention['type'], '30/360'))
                        <div class="calculation-example">
                            <p><strong>Step 1:</strong> Identify the components</p>
                            <ul>
                                <li>Y1 = 2024, M1 = 1, D1 = 15</li>
                                <li>Y2 = 2024, M2 = 7, D2 = 15</li>
                            </ul>

                            <p><strong>Step 2:</strong> Check adjustment rules</p>
                            <p class="text-muted">No adjustments needed (neither date is 31st or last day of February)</p>

                            <p><strong>Step 3:</strong> Apply formula</p>
                            <div class="bg-light p-3 rounded font-monospace">
                                Days = 360×(2024-2024) + 30×(7-1) + (15-15)<br>
                                Days = 360×0 + 30×6 + 0<br>
                                Days = 180
                            </div>

                            <p><strong>Step 4:</strong> Calculate factor</p>
                            <div class="bg-light p-3 rounded font-monospace">
                                Factor = 180 / 360 = 0.5000000000
                            </div>
                        </div>
                    @else
                        <div class="calculation-example">
                            <p><strong>Step 1:</strong> Count actual days</p>
                            <p class="text-muted">From Jan 15 to Jul 15, 2024 = 182 actual days</p>

                            <p><strong>Step 2:</strong> Apply formula</p>
                            <div class="bg-light p-3 rounded font-monospace">
                                @if(Str::contains($convention['type'], '365'))
                                    Factor = 182 / 365 = 0.4986301370
                                @elseif(Str::contains($convention['type'], '360'))
                                    Factor = 182 / 360 = 0.5055555556
                                @elseif(Str::contains($convention['type'], '364'))
                                    Factor = 182 / 364 = 0.5000000000
                                @elseif(Str::contains($convention['type'], 'Actual/Actual'))
                                    Factor = 182 / 366 = 0.4972677596 (2024 is a leap year)
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="alert alert-info mt-4">
                        <strong>Try it yourself:</strong>
                        <a href="{{ route('calculator.index') }}" class="alert-link">Use the calculator</a>
                        to verify this example or try your own dates.
                    </div>
                </div>
            </div>

            <!-- Related Conventions -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Related Conventions</h5>
                    <p class="text-muted">You might also be interested in these conventions:</p>
                    <div class="list-group">
                        @php
                            $related = collect(config('daycountcalculator.conventions'))
                                ->whereIn('type', $convention['related'] ?? [])
                                ->values();
                        @endphp

                        @foreach($related as $relatedConvention)
                            <a href="{{ route('calculator.educate', $relatedConvention['slug']) }}" class="list-group-item list-group-item-action">
                                {{ $relatedConvention['name'] }}
                                <i class="bi bi-arrow-right float-end"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Action -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-3">Try This Convention</h6>
                    <a href="{{ route('calculator.index') }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-calculator"></i> Use Calculator
                    </a>
                    <a href="{{ route('comparison.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-bar-chart"></i> Compare Conventions
                    </a>
                </div>
            </div>

            <!-- Key Points -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Key Points</h6>
                    <ul class="small mb-0">
                        @if(Str::contains($convention['type'], '30/360'))
                            <li class="mb-2">Uses standardized 30-day months</li>
                            <li class="mb-2">Year always has 360 days</li>
                            <li class="mb-2">Simplifies calculations</li>
                            <li class="mb-2">Common in bond markets</li>
                        @else
                            <li class="mb-2">Uses actual calendar days</li>
                            <li class="mb-2">More precise calculation</li>
                            <li class="mb-2">Accounts for leap years</li>
                            <li class="mb-2">Common in government bonds</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Resources -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Additional Resources</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <a href="https://en.wikipedia.org/wiki/Day_count_convention" target="_blank" class="text-decoration-none">
                                <i class="bi bi-wikipedia"></i> Wikipedia - Day Count Conventions
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="https://www.isda.org/" target="_blank" class="text-decoration-none">
                                <i class="bi bi-globe"></i> ISDA Documentation
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('calculator.history') }}" class="text-decoration-none">
                                <i class="bi bi-clock-history"></i> Your Calculation History
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
