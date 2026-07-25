@extends('layouts.platform')

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
<div class="mx-auto max-w-content px-4 py-10 sm:px-6">
    <div class="grid gap-8 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Header -->
            <div class="mb-6">
                <nav aria-label="breadcrumb" class="mb-3 text-sm">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a href="{{ route('calculator.index') }}" class="text-brand hover:underline">Calculator</a></li>
                        <li class="text-ink-faint" aria-hidden="true">/</li>
                        <li class="text-ink-faint">{{ $convention['name'] }}</li>
                    </ol>
                </nav>

                <h1 class="text-2xl font-bold text-ink sm:text-3xl">{{ $convention['name'] }}</h1>

                {{-- Trust / citation block --}}
                <div class="trust-source">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <div>
                        <span class="font-semibold text-ink">Based on:</span>
                        <a href="https://www.isda.org/" target="_blank" rel="noopener" class="underline decoration-brand/40 hover:text-brand">ISDA 2006 Definitions</a>
                        &amp; standard market practice for {{ $convention['name'] }}@if($convention['alias']) ({{ $convention['alias'] }})@endif
                        <span class="mx-1 text-ink-faint">·</span>
                        <span class="text-ink-faint" dir="ltr">{{ $convention['formula'] }}</span>
                    </div>
                </div>

                <p class="mt-4 text-lg text-ink-faint">{{ $convention['description'] }}</p>

                @if($convention['alias'])
                    <div class="mt-4 rounded-lg border border-brand/30 bg-brand-light px-4 py-3 text-sm text-ink-soft">
                        <strong class="text-ink">Also Known As:</strong> {{ $convention['alias'] }}
                    </div>
                @endif
            </div>

            <!-- Formula -->
            <div class="card mb-6 p-6">
                <h2 class="mb-3 text-lg font-bold text-ink">Formula</h2>
                <div class="rounded-lg border-l-4 border-gold bg-ink px-4 py-3">
                    <code class="font-mono text-sm text-gold" dir="ltr">{{ $convention['formula'] }}</code>
                </div>
            </div>

            <!-- Common Use Cases -->
            <div class="card mb-6 p-6">
                <h2 class="mb-3 text-lg font-bold text-ink">Common Use Cases</h2>
                <ul class="list-disc space-y-2 pl-5 text-sm text-ink-soft">
                    @foreach($convention['use_cases'] as $useCase)
                        <li>{{ $useCase }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Detailed Explanation -->
            <div class="card mb-6 p-6">
                <h2 class="mb-3 text-lg font-bold text-ink">How It Works</h2>

                @if(Str::contains($convention['type'], '30/360'))
                    <p class="text-sm text-ink-soft">The {{ $convention['name'] }} convention assumes:</p>
                    <ul class="mt-2 list-disc space-y-1.5 pl-5 text-sm text-ink-soft">
                        <li>Each month has 30 days</li>
                        <li>Each year has 360 days (12 months × 30 days)</li>
                        <li>Specific adjustment rules apply when dates fall on the 31st or last day of February</li>
                    </ul>

                    <h3 class="mb-3 mt-6 text-sm font-bold uppercase tracking-wide text-ink">Adjustment Rules</h3>
                    @if(Str::contains($convention['type'], 'US') || Str::contains($convention['type'], 'Bond Basis'))
                        <ol class="list-decimal space-y-2 pl-5 text-sm text-ink-soft">
                            <li>If the start day (D1) is 31, change it to 30</li>
                            <li>If the end day (D2) is 31 and D1 is 30 or 31, change D2 to 30</li>
                        </ol>
                    @elseif(Str::contains($convention['type'], '30E/360 ISDA'))
                        <ol class="list-decimal space-y-2 pl-5 text-sm text-ink-soft">
                            <li>If D1 is the last day of February, change it to 30</li>
                            <li>If D2 is the last day of February, change it to 30</li>
                            <li>If D1 is 31, change it to 30</li>
                            <li>If D2 is 31, change it to 30</li>
                        </ol>
                    @elseif(Str::contains($convention['type'], '30E/360'))
                        <ol class="list-decimal space-y-2 pl-5 text-sm text-ink-soft">
                            <li>If D1 is the last day of the month, change it to 30</li>
                            <li>If D2 is the last day of the month (unless it's the maturity date and in February), change it to 30</li>
                        </ol>
                    @endif

                @elseif(Str::contains($convention['type'], 'Actual'))
                    <p class="text-sm text-ink-soft">The {{ $convention['name'] }} convention uses:</p>
                    <ul class="mt-2 list-disc space-y-1.5 pl-5 text-sm text-ink-soft">
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

                    <div class="mt-4 rounded-lg border border-gold/30 bg-gold/5 px-4 py-3 text-sm text-ink-soft">
                        <strong class="text-ink">Note:</strong> This convention counts the actual calendar days, making it more precise for certain financial instruments.
                    </div>
                @endif
            </div>

            <!-- Example Calculation -->
            <div class="card mb-6 overflow-hidden border-brand/40">
                <div class="border-b border-line bg-brand px-6 py-4">
                    <h2 class="text-base font-bold text-white">Example Calculation</h2>
                </div>
                <div class="p-6">
                    <p class="mb-4 text-sm text-ink-soft">Let's calculate the day count factor from <strong class="text-ink">January 15, 2024</strong> to <strong class="text-ink">July 15, 2024</strong>:</p>

                    @if(Str::contains($convention['type'], '30/360'))
                        <div class="space-y-4 text-sm text-ink-soft">
                            <p><strong class="text-ink">Step 1:</strong> Identify the components</p>
                            <ul class="list-disc pl-5" dir="ltr">
                                <li>Y1 = 2024, M1 = 1, D1 = 15</li>
                                <li>Y2 = 2024, M2 = 7, D2 = 15</li>
                            </ul>

                            <p><strong class="text-ink">Step 2:</strong> Check adjustment rules</p>
                            <p class="text-ink-faint">No adjustments needed (neither date is 31st or last day of February)</p>

                            <p><strong class="text-ink">Step 3:</strong> Apply formula</p>
                            <div class="rounded-lg bg-surface-muted p-4 font-mono text-xs leading-relaxed text-ink" dir="ltr">
                                Days = 360×(2024-2024) + 30×(7-1) + (15-15)<br>
                                Days = 360×0 + 30×6 + 0<br>
                                Days = 180
                            </div>

                            <p><strong class="text-ink">Step 4:</strong> Calculate factor</p>
                            <div class="rounded-lg bg-surface-muted p-4 font-mono text-xs text-ink" dir="ltr">
                                Factor = 180 / 360 = 0.5000000000
                            </div>
                        </div>
                    @else
                        <div class="space-y-4 text-sm text-ink-soft">
                            <p><strong class="text-ink">Step 1:</strong> Count actual days</p>
                            <p class="text-ink-faint">From Jan 15 to Jul 15, 2024 = 182 actual days</p>

                            <p><strong class="text-ink">Step 2:</strong> Apply formula</p>
                            <div class="rounded-lg bg-surface-muted p-4 font-mono text-xs text-ink" dir="ltr">
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

                    <div class="mt-6 rounded-lg border border-brand/30 bg-brand-light px-4 py-3 text-sm text-ink-soft">
                        <strong class="text-ink">Try it yourself:</strong>
                        <a href="{{ route('calculator.index') }}" class="font-semibold text-brand underline decoration-brand/40 hover:text-brand-dark">Use the calculator</a>
                        to verify this example or try your own dates.
                    </div>
                </div>
            </div>

            <!-- Related Conventions -->
            <div class="card mb-6 p-6">
                <h2 class="mb-2 text-lg font-bold text-ink">Related Conventions</h2>
                <p class="mb-4 text-sm text-ink-faint">You might also be interested in these conventions:</p>
                <div class="divide-y divide-line overflow-hidden rounded-lg border border-line">
                    @php
                        $related = collect(config('daycountcalculator.conventions'))
                            ->whereIn('type', $convention['related'] ?? [])
                            ->values();
                    @endphp

                    @foreach($related as $relatedConvention)
                        <a href="{{ route('calculator.educate', $relatedConvention['slug']) }}"
                           class="flex items-center justify-between px-4 py-3 text-sm font-medium text-ink transition hover:bg-brand-light hover:text-brand">
                            {{ $relatedConvention['name'] }}
                            <svg class="h-4 w-4 shrink-0 text-ink-faint" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Action -->
            <div class="card border-brand/40 p-6 text-center">
                <h2 class="mb-4 text-sm font-bold text-ink">Try This Convention</h2>
                <a href="{{ route('calculator.index') }}" class="btn-primary mb-2 w-full">Use Calculator</a>
                <a href="{{ route('comparison.index') }}" class="btn-secondary w-full">Compare Conventions</a>
            </div>

            <!-- Key Points -->
            <div class="card p-6">
                <h2 class="mb-3 text-sm font-bold text-ink">Key Points</h2>
                <ul class="list-disc space-y-2 pl-5 text-sm text-ink-soft">
                    @if(Str::contains($convention['type'], '30/360'))
                        <li>Uses standardized 30-day months</li>
                        <li>Year always has 360 days</li>
                        <li>Simplifies calculations</li>
                        <li>Common in bond markets</li>
                    @else
                        <li>Uses actual calendar days</li>
                        <li>More precise calculation</li>
                        <li>Accounts for leap years</li>
                        <li>Common in government bonds</li>
                    @endif
                </ul>
            </div>

            <!-- Resources -->
            <div class="card p-6">
                <h2 class="mb-3 text-sm font-bold text-ink">Additional Resources</h2>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="https://en.wikipedia.org/wiki/Day_count_convention" target="_blank" rel="noopener" class="text-ink-soft hover:text-brand">
                            Wikipedia — Day Count Conventions
                        </a>
                    </li>
                    <li>
                        <a href="https://www.isda.org/" target="_blank" rel="noopener" class="text-ink-soft hover:text-brand">
                            ISDA Documentation
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('calculator.history') }}" class="text-ink-soft hover:text-brand">
                            Your Calculation History
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
