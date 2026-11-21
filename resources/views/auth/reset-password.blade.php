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

            <div class="form-group">
                <label for="password" class="form-label">New Password</label>
                <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
                @error('password')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                @error('password_confirmation')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Reset Password</button>
            </div>
        </form>
    </div>
</x-guest-layout>
