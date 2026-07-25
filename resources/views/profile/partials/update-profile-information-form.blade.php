<section>
    <header class="flex items-start gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-light text-brand">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 0 1 15 0"/></svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-ink">{{ __('Profile Information') }}</h2>
            <p class="mt-0.5 text-sm text-ink-faint">{{ __("Your account details and how we can reach you.") }}</p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" name="phone" type="text" :value="old('phone', $user->phone)" autocomplete="tel" placeholder="+971 ..." />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            <div>
                <x-input-label for="company" :value="__('Company')" />
                <x-text-input id="company" name="company" type="text" :value="old('company', $user->company)" autocomplete="organization" />
                <x-input-error class="mt-2" :messages="$errors->get('company')" />
            </div>
            <div>
                <x-input-label for="job_title" :value="__('Job title')" />
                <x-text-input id="job_title" name="job_title" type="text" :value="old('job_title', $user->job_title)" autocomplete="organization-title" />
                <x-input-error class="mt-2" :messages="$errors->get('job_title')" />
            </div>
            <div>
                <x-input-label for="country" :value="__('Country')" />
                <x-text-input id="country" name="country" type="text" :value="old('country', $user->country)" autocomplete="country-name" />
                <x-input-error class="mt-2" :messages="$errors->get('country')" />
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ __('Your email address is unverified.') }}
                <button form="send-verification" class="font-semibold underline hover:text-amber-900">
                    {{ __('Re-send the verification email') }}
                </button>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 font-medium text-brand-dark">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-brand">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
