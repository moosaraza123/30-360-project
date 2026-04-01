<nav x-data="{ open: false }" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); box-shadow: 0 2px 16px rgba(15,23,42,.35);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('calculator.index') }}" class="flex items-center gap-2" style="text-decoration: none;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:linear-gradient(135deg,#c9a227,#a07d18);color:#0f172a;border-radius:8px;font-weight:900;font-size:.8rem;">30</span>
                    <span style="color:#fff;font-weight:800;font-size:1rem;letter-spacing:-.02em;">
                        <span style="color:rgba(255,255,255,.4);font-weight:300;">/</span><span style="color:rgba(255,255,255,.85);font-weight:600;">360</span>
                        <span style="color:rgba(255,255,255,.55);font-weight:400;font-size:.85rem;margin-left:.25rem;">Calculator</span>
                    </span>
                </a>

                <div class="hidden sm:flex sm:items-center sm:ms-10 sm:space-x-2">
                    <a href="{{ route('calculator.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition" style="color:{{ request()->routeIs('calculator.*') ? '#c9a227' : 'rgba(255,255,255,.75)' }};background:{{ request()->routeIs('calculator.*') ? 'rgba(201,162,39,.1)' : 'transparent' }};">
                        Calculator
                    </a>
                    <a href="{{ route('comparison.index') }}" class="px-3 py-2 rounded-md text-sm font-medium transition" style="color:{{ request()->routeIs('comparison.*') ? '#c9a227' : 'rgba(255,255,255,.75)' }};background:{{ request()->routeIs('comparison.*') ? 'rgba(201,162,39,.1)' : 'transparent' }};">
                        Compare
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border-0 text-sm leading-4 font-medium rounded-md transition" style="background:transparent;color:rgba(255,255,255,.85);font-weight:600;">
                            <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:linear-gradient(135deg,#c9a227,#a07d18);color:#0f172a;border-radius:50%;font-size:.7rem;font-weight:800;margin-right:.5rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" style="color:rgba(255,255,255,.6);">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md transition" style="color:rgba(255,255,255,.75);">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" style="background:rgba(0,0,0,.1);">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('calculator.index')" :active="request()->routeIs('calculator.*')" style="color:rgba(255,255,255,.85);">
                Calculator
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('comparison.index')" :active="request()->routeIs('comparison.*')" style="color:rgba(255,255,255,.85);">
                Compare
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t" style="border-color:rgba(255,255,255,.1);">
            <div class="px-4">
                <div class="font-medium text-base" style="color:#fff;">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm" style="color:rgba(255,255,255,.5);">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" style="color:rgba(255,255,255,.85);">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            style="color:rgba(255,255,255,.85);">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
