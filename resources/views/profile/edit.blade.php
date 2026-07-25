<x-app-layout>
    {{-- Header band --}}
    <div class="border-b border-line bg-ink py-10">
        <div class="mx-auto flex max-w-content items-center gap-5 px-4 sm:px-6">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-gold to-gold-dark text-2xl font-bold text-ink">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <h1 class="truncate text-2xl font-bold text-white">{{ auth()->user()->name }}</h1>
                <p class="truncate text-sm text-white/60">{{ auth()->user()->email }}</p>
            </div>
            @if(auth()->user()->isAdmin())
                <span class="ms-auto hidden rounded-full bg-gold/20 px-3 py-1 text-xs font-semibold text-gold sm:block">
                    {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                </span>
            @endif
        </div>
    </div>

    <div class="mx-auto max-w-content px-4 py-10 sm:px-6">
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Summary sidebar --}}
            <aside class="space-y-4 lg:sticky lg:top-6 lg:self-start">
                <div class="card p-6">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-ink-faint">Account</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-ink-faint">Member since</dt>
                            <dd class="font-semibold text-ink">{{ auth()->user()->created_at->format('M Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-ink-faint">Email status</dt>
                            <dd>
                                @if(auth()->user()->hasVerifiedEmail())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-brand-light px-2.5 py-0.5 text-xs font-semibold text-brand-dark">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Unverified</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-ink-faint">Saved calculations</dt>
                            <dd class="tabular font-semibold text-ink">{{ auth()->user()->savedCalculations()->count() }}</dd>
                        </div>
                    </dl>
                </div>
                <a href="{{ route('calculator.saved') }}" class="btn-secondary w-full">View saved calculations</a>
            </aside>

            {{-- Settings sections --}}
            <div class="space-y-6 lg:col-span-2">
                <div class="card p-6 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="card p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="rounded-card border border-red-200 bg-white p-6 shadow-card sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
