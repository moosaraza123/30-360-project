<x-guest-layout>

    <h2 class="auth-page-heading">Create your account</h2>
    <p class="auth-page-sub">Get started with professional day count calculations</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name + Email --}}
        <div class="auth-grid-2">
            <div class="auth-field">
                <label class="auth-label" for="name">Full name</label>
                <input
                    id="name"
                    class="auth-input"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Jane Smith"
                    required
                    autofocus
                    autocomplete="name"
                >
                @error('name')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="email">Email address</label>
                <input
                    id="email"
                    class="auth-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    required
                    autocomplete="username"
                >
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password + Confirm --}}
        <div class="auth-grid-2">
            <div class="auth-field">
                <label class="auth-label" for="password">Password</label>
                <input
                    id="password"
                    class="auth-input"
                    type="password"
                    name="password"
                    placeholder="Min. 8 characters"
                    required
                    autocomplete="new-password"
                >
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="password_confirmation">Confirm password</label>
                <input
                    id="password_confirmation"
                    class="auth-input"
                    type="password"
                    name="password_confirmation"
                    placeholder="Repeat password"
                    required
                    autocomplete="new-password"
                >
                @error('password_confirmation')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Optional professional details --}}
        <div class="auth-section-label">Professional details <span class="auth-label-optional">(optional)</span></div>

        <div class="auth-grid-2">
            <div class="auth-field">
                <label class="auth-label" for="company">
                    Company <span class="auth-label-optional">optional</span>
                </label>
                <input
                    id="company"
                    class="auth-input"
                    type="text"
                    name="company"
                    value="{{ old('company') }}"
                    placeholder="e.g. BlackRock"
                    autocomplete="organization"
                >
                @error('company')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="job_title">
                    Job title <span class="auth-label-optional">optional</span>
                </label>
                <input
                    id="job_title"
                    class="auth-input"
                    type="text"
                    name="job_title"
                    value="{{ old('job_title') }}"
                    placeholder="e.g. Portfolio Manager"
                    autocomplete="organization-title"
                >
                @error('job_title')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="phone">
                    Phone <span class="auth-label-optional">optional</span>
                </label>
                <input
                    id="phone"
                    class="auth-input"
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="+1 555 000 0000"
                    autocomplete="tel"
                >
                @error('phone')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label class="auth-label" for="country">
                    Country <span class="auth-label-optional">optional</span>
                </label>
                <input
                    id="country"
                    class="auth-input"
                    type="text"
                    name="country"
                    value="{{ old('country') }}"
                    placeholder="e.g. United States"
                    autocomplete="country-name"
                >
                @error('country')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="auth-btn" style="margin-top:0.5rem;">Create Account</button>

        <p style="text-align:center; margin-top:1.25rem; font-size:0.875rem; color:#64748b;">
            Already have an account?&nbsp;
            <a href="{{ route('login') }}" class="auth-link-gold">Sign in</a>
        </p>
    </form>

</x-guest-layout>
