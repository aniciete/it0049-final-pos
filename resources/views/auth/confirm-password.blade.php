<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Confirm Password</h1>
            <p class="auth-subtitle">Please confirm your password before continuing to this protected action.</p>
        </header>

        @include('partials.error_summary')

        <form method="POST" action="{{ route('password.confirm') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
                @error('password')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Confirm</button>
            </div>
        </form>
    </div>
</x-guest-layout>
