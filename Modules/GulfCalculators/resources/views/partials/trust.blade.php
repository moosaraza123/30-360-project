{{-- Trust/citation block: the credibility differentiator on every calculator --}}
<div class="trust-source">
    <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>
    <div>
        <span class="font-semibold text-ink">{{ __('Based on') }}:</span>
        @if(!empty($page['source_url']))
            <a href="{{ $page['source_url'] }}" target="_blank" rel="noopener" class="underline decoration-brand/40 hover:text-brand">{{ $page['source'] }}</a>
        @else
            {{ $page['source'] }}
        @endif
        <span class="mx-1 text-ink-faint">·</span>
        <span class="text-ink-faint">{{ __('Last reviewed') }}: {{ $page['reviewed'] }}</span>
    </div>
</div>
