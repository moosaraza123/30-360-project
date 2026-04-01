<x-guest-layout>

    {{-- Mail check icon --}}
    <div style="width:52px; height:52px; background:linear-gradient(135deg,#c9a227,#a07d18); border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1.25rem;">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
            <rect x="2" y="5" width="20" height="14" rx="2" stroke="#fff" stroke-width="1.8"/>
            <path d="M2 7l10 7 10-7" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M14 14l2 2 4-4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <h2 class="auth-page-heading">Verify your email</h2>
    <p class="auth-page-sub">
        Thanks for signing up! We sent a verification link to your email address.
        Click it to activate your account.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-status-success">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="auth-btn">Resend Verification Email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top:1rem; text-align:center;">
        @csrf
        <button type="submit" class="auth-link" style="background:none; border:none; cursor:pointer; padding:0;">
            Sign out and use a different account
        </button>
    </form>

</x-guest-layout>
