<x-guest-layout>

    {{-- Envelope icon --}}
    <div style="width:52px; height:52px; background:linear-gradient(135deg,#c9a227,#a07d18); border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1.25rem;">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="5" width="20" height="14" rx="2" stroke="#fff" stroke-width="1.8"/>
            <path d="M2 7l10 7 10-7" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </div>

    <h2 class="auth-page-heading">Reset your password</h2>
    <p class="auth-page-sub">Enter your email and we'll send you a link to choose a new password.</p>

    @if (session('status'))
        <div class="auth-status-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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
            >
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn" style="margin-top:0.25rem;">Send Reset Link</button>

        <p style="text-align:center; margin-top:1.25rem; font-size:0.875rem; color:#64748b;">
            Remembered it?&nbsp;
            <a href="{{ route('login') }}" class="auth-link-gold">Back to sign in</a>
        </p>
    </form>

</x-guest-layout>
