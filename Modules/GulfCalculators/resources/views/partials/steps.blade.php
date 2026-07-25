{{-- Step-by-step breakdown table ($steps = [['label','value','detail'], ...]) --}}
<div class="mt-6">
    <h3 class="text-sm font-bold uppercase tracking-wide text-ink-faint">{{ __('How it was calculated') }}</h3>
    <dl class="mt-3 divide-y divide-line rounded-lg border border-line">
        @foreach($steps as $step)
            <div class="flex items-center justify-between gap-4 px-4 py-3">
                <dt class="text-sm text-ink-soft">
                    {{ $step['label'] }}
                    @if(!empty($step['detail']))
                        <span class="block text-xs text-ink-faint" dir="ltr">{{ $step['detail'] }}</span>
                    @endif
                </dt>
                <dd class="tabular text-sm font-semibold text-ink" dir="ltr">{{ $step['value'] }}</dd>
            </div>
        @endforeach
    </dl>
</div>
