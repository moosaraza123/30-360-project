<x-guest-layout>

    {{-- Lock icon --}}
    <div style="width:52px; height:52px; background:linear-gradient(135deg,#c9a227,#a07d18); border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1.25rem;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect x="5" y="11" width="14" height="10" rx="2" stroke="#fff" stroke-width="1.8"/>
            <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="16" r="1.5" fill="#fff"/>
        </svg>
    </div>

    <h2 class="auth-page-heading">Choose a new password</h2>
    <p class="auth-page-sub">Must be at least 8 characters and contain a mix of letters and numbers.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label class="auth-label" for="email">Email address</label>
            <input
                id="email"
                class="auth-input"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
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
            <label class="auth-label" for="password">New password</label>
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
            <label class="auth-label" for="password_confirmation">Confirm new password</label>
            <input
                id="password_confirmation"
                class="auth-input"
                type="password"
                name="password_confirmation"
                placeholder="Repeat new password"
                required
                autocomplete="new-password"
            >
            @error('password_confirmation')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="auth-btn" style="margin-top:0.25rem;">Reset Password</button>
    </form>

</x-guest-layout>
