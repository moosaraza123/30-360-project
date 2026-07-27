{{-- Shared platform footer --}}
@php $ar = app()->getLocale() === "ar" ? "ar/" : ""; @endphp
    <footer class="mt-16 border-t border-line bg-white">
        <div class="mx-auto max-w-content px-4 py-10 sm:px-6">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand text-base font-bold text-white">ح</span>
                        <span class="font-bold text-ink">{{ __('Hisabi') }}</span>
                    </div>
                    <p class="mt-3 text-sm text-ink-faint">{{ __('Trusted financial calculators for the Gulf') }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ink-faint">{{ __('Calculators') }}</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="{{ url($ar.'gratuity-calculator-uae') }}" class="text-ink-soft hover:text-brand">{{ __('Gratuity UAE') }}</a></li>
                        <li><a href="{{ url($ar.'end-of-service-calculator-saudi-arabia') }}" class="text-ink-soft hover:text-brand">{{ __('Gratuity KSA') }}</a></li>
                        <li><a href="{{ url($ar.'gosi-calculator-saudi-arabia') }}" class="text-ink-soft hover:text-brand">{{ __('GOSI & Net Salary — KSA') }}</a></li>
                        <li><a href="{{ url($ar.'zakat-calculator') }}" class="text-ink-soft hover:text-brand">{{ __('Zakat') }}</a></li>
                        <li><a href="{{ url($ar.'loan-calculator') }}" class="text-ink-soft hover:text-brand">{{ __('Loan') }}</a></li>
                        <li><a href="{{ url($ar.'iqama-fees-calculator-saudi-arabia') }}" class="text-ink-soft hover:text-brand">{{ __('Iqama Fees — KSA') }}</a></li>
                        <li><a href="{{ url($ar.'overstay-fine-calculator-uae') }}" class="text-ink-soft hover:text-brand">{{ __('Overstay Fine — UAE') }}</a></li>
                        <li><a href="{{ url($ar.'corporate-tax-calculator-uae') }}" class="text-ink-soft hover:text-brand">{{ __('Corporate Tax — UAE (9%)') }}</a></li>
                        <li><a href="{{ url($ar.'mortgage-affordability-calculator-uae') }}" class="text-ink-soft hover:text-brand">{{ __('Mortgage Affordability — UAE') }}</a></li>
                        <li><a href="{{ route('calculator.index') }}" class="text-ink-soft hover:text-brand">{{ __('Day Count') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-ink-faint">Newsletter</h3>
                    <form action="{{ route('subscribe.subscribe') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="source" value="footer">
                        <div class="flex gap-2">
                            <input type="email" name="email" required placeholder="you@email.com" class="field-input text-sm">
                            <button type="submit" class="btn-primary shrink-0">OK</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-8 border-t border-line pt-6 text-center text-xs text-ink-faint">
                <p>{{ __('All calculations are for guidance only and do not constitute legal or financial advice.') }}</p>
                <p class="mt-2">
                    &copy; {{ date('Y') }} {{ __('Hisabi') }}
                    <span class="mx-1 opacity-50">·</span>
                    <a href="{{ route('legal.privacy') }}" class="hover:text-brand">{{ __('Privacy Policy') }}</a>
                    <span class="mx-1 opacity-50">·</span>
                    <a href="{{ route('legal.terms') }}" class="hover:text-brand">{{ __('Terms of Use') }}</a>
                </p>
            </div>
        </div>
    </footer>
