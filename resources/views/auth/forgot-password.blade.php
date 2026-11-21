<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Reset Password</h1>
            <p class="auth-subtitle">Enter your account email address to receive a password reset link.</p>
        </header>

        @include('partials.error_summary')

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('login') }}" class="button button-secondary">Back to Sign In</a>
                <button type="submit" class="button button-primary">Send Reset Link</button>
            </div>
        </form>
    </div>
</x-guest-layout>
