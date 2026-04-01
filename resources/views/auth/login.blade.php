<x-guest-layout>

    <h2 class="auth-page-heading">Welcome back</h2>
    <p class="auth-page-sub">Sign in to your account to continue</p>

    @if (session('status'))
        <div class="auth-status-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                autofocus
                autocomplete="username"
            >
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.4rem;">
                <label class="auth-label" for="password" style="margin-bottom:0;">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link" style="font-size:0.8rem; text-transform:none; font-weight:500;">Forgot password?</a>
                @endif
            </div>
            <input
                id="password"
                class="auth-input"
                type="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field" style="margin-bottom:1.5rem;">
            <div class="auth-checkbox-row">
                <input id="remember_me" class="auth-checkbox" type="checkbox" name="remember">
                <label for="remember_me" class="auth-checkbox-label">Keep me signed in</label>
            </div>
        </div>

        <button type="submit" class="auth-btn">Sign In</button>

        <p style="text-align:center; margin-top:1.25rem; font-size:0.875rem; color:#64748b;">
            Don't have an account?&nbsp;
            <a href="{{ route('register') }}" class="auth-link-gold">Create one free</a>
        </p>
    </form>

</x-guest-layout>
