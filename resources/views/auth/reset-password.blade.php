<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Set New Password</h1>
            <p class="auth-subtitle">Choose a new password for your staff account.</p>
        </header>

        @include('partials.error_summary')

        <form method="POST" action="{{ route('password.store') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required autofocus>
                @error('email')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <x-password-input
                id="password"
                name="password"
                label="New Password"
                autocomplete="new-password"
                :required="true"
                error-id="reset_password_error"
                hint="Minimum 8 characters."
            />

            <x-password-input
                id="password_confirmation"
                name="password_confirmation"
                label="Confirm Password"
                autocomplete="new-password"
                :required="true"
                error-id="reset_password_confirmation_error"
                toggle-label="Show confirm password"
            />

            <div class="form-actions">
                <button type="submit" class="button button-primary">Reset Password</button>
            </div>
        </form>
    </div>
</x-guest-layout>
