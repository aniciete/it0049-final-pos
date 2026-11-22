<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Confirm Password</h1>
            <p class="auth-subtitle">Please confirm your password before continuing to this protected action.</p>
        </header>

        @include('partials.error_summary')

        <form method="POST" action="{{ route('password.confirm') }}" novalidate>
            @csrf

            <x-password-input
                id="password"
                name="password"
                label="Password"
                autocomplete="current-password"
                :required="true"
                error-id="confirm_password_error"
            />

            <div class="form-actions">
                <button type="submit" class="button button-primary">Confirm</button>
            </div>
        </form>
    </div>
</x-guest-layout>
