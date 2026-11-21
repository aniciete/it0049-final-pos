<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Verify Email Address</h1>
            <p class="auth-subtitle">
                Thanks for registering! Please verify your email address by clicking the link we sent to your inbox.
            </p>
        </header>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success" role="status">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="form-actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="button button-primary">Resend Verification Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button button-secondary">Log Out</button>
            </form>
        </div>
    </div>
</x-guest-layout>
