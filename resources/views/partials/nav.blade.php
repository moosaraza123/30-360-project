{{-- Shared platform navigation --}}
    <nav class="border-b border-line bg-white" x-data="{ open: false }">
        <div class="mx-auto flex h-16 max-w-content items-center justify-between px-4 sm:px-6">
            <a href="{{ app()->getLocale() === 'ar' ? url('/ar') : url('/') }}" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand text-lg font-bold text-white">ح</span>
                <span class="text-lg font-bold text-ink">{{ __('Hisabi') }}</span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @php $ar = app()->getLocale() === 'ar' ? 'ar/' : ''; @endphp
                <a href="{{ url($ar.'gratuity-calculator-uae') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Gratuity UAE') }}</a>
                <a href="{{ url($ar.'end-of-service-calculator-saudi-arabia') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Gratuity KSA') }}</a>
                <a href="{{ url($ar.'vat-calculator-uae') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('VAT') }}</a>
                <a href="{{ url($ar.'zakat-calculator') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Zakat') }}</a>
                <a href="{{ route('calculator.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Professional') }}</a>
            </div>

            <div class="flex items-center gap-2">
                @hasSection('alternate')
                    <a href="@yield('alternate')" class="rounded-lg border border-line px-3 py-1.5 text-sm font-semibold text-ink-soft hover:border-brand hover:text-brand">
                        {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </a>
                @endif

                @auth
                    {{-- User dropdown --}}
                    <div class="relative hidden sm:block" x-data="{ userMenu: false }" @click.outside="userMenu = false" @keydown.escape.window="userMenu = false">
                        <button class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-ink-soft hover:bg-surface-muted hover:text-ink"
                                @click="userMenu = !userMenu" aria-haspopup="true" :aria-expanded="userMenu">
                            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="max-w-[10rem] truncate">{{ auth()->user()->name }}</span>
                            <svg class="h-4 w-4 text-ink-faint transition" :class="userMenu && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <div class="absolute end-0 z-20 mt-2 w-56 overflow-hidden rounded-xl border border-line bg-white shadow-card-hover"
                             x-show="userMenu" x-transition.opacity x-cloak>
                            <div class="border-b border-line px-4 py-3">
                                <p class="truncate text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-ink-faint">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1.5">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Dashboard') }}</a>
                                <a href="{{ route('calculator.saved') }}" class="block px-4 py-2 text-sm text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Saved Calculations') }}</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Profile') }}</a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-ink-soft hover:bg-surface-muted hover:text-ink">{{ __('Admin Panel') }}</a>
                                @endif
                            </div>
                            <div class="border-t border-line py-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm font-medium text-red-600 hover:bg-red-50">{{ __('Sign Out') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:text-ink sm:block">{{ __('Sign In') }}</a>
                    <a href="{{ route('register') }}" class="btn-primary hidden !px-4 !py-2 sm:inline-flex">{{ __('Get Started') }}</a>
                @endauth

                <button class="rounded-lg p-2 text-ink-soft hover:bg-surface-muted md:hidden" @click="open = !open" aria-label="Menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div class="border-t border-line px-4 py-3 md:hidden" x-show="open" x-cloak>
            <div class="flex flex-col gap-1">
                <a href="{{ url($ar.'gratuity-calculator-uae') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('Gratuity UAE') }}</a>
                <a href="{{ url($ar.'end-of-service-calculator-saudi-arabia') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('Gratuity KSA') }}</a>
                <a href="{{ url($ar.'vat-calculator-uae') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('VAT') }}</a>
                <a href="{{ url($ar.'zakat-calculator') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('Zakat') }}</a>
                <a href="{{ route('calculator.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('Professional') }}</a>

                <div class="mt-2 border-t border-line pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('Dashboard') }}</a>
                        <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('Profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-lg px-3 py-2 text-start text-sm font-medium text-red-600 hover:bg-red-50">{{ __('Sign Out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink-soft hover:bg-surface-muted">{{ __('Sign In') }}</a>
                        <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-brand hover:bg-brand-light">{{ __('Get Started') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
