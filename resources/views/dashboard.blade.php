<x-app-layout>
    <div class="border-b border-line bg-ink py-12">
        <div class="mx-auto max-w-content px-4 sm:px-6">
            <h1 class="text-2xl font-bold text-white sm:text-3xl">
                Welcome back, <span class="text-gold">{{ Auth::user()->name }}</span>
            </h1>
            <p class="mt-1 text-white/60">Your calculators, history and saved work</p>
        </div>
    </div>

    <div class="mx-auto max-w-content px-4 py-10 sm:px-6">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach([
                ['route' => route('calculator.index'), 'title' => 'Start Calculating', 'desc' => 'Day count factors and accrued interest for any convention', 'cta' => 'Open Calculator', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                ['route' => route('comparison.index'), 'title' => 'Compare Conventions', 'desc' => 'Side-by-side comparison of multiple conventions', 'cta' => 'Open Comparison', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ['route' => route('calculator.saved'), 'title' => 'Saved Calculations', 'desc' => 'Your named and favorited calculations', 'cta' => 'View Saved', 'icon' => 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z'],
            ] as $card)
                <a href="{{ $card['route'] }}" class="card group p-6 transition hover:shadow-card-hover">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-light text-brand">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </div>
                    <h2 class="mt-4 font-bold text-ink group-hover:text-brand">{{ $card['title'] }}</h2>
                    <p class="mt-1 text-sm text-ink-faint">{{ $card['desc'] }}</p>
                    <span class="mt-3 inline-block text-sm font-semibold text-brand">{{ $card['cta'] }} &rarr;</span>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
