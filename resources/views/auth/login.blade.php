<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Sign In</h1>
        </header>

        @include('partials.error_summary')

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="login" class="form-label">Username or Email</label>
                <input
                    id="login"
                    type="text"
                    name="login"
                    class="form-control"
                    value="{{ old('login', old('email')) }}"
                    autocomplete="username"
                    autocorrect="off"
                    autocapitalize="none"
                    spellcheck="false"
                    required
                    autofocus
                    aria-invalid="{{ ($errors->has('login') || $errors->has('email')) ? 'true' : 'false' }}"
                    @if ($errors->has('login') || $errors->has('email')) aria-describedby="login_error" @endif
                >
                @if ($errors->has('login') || $errors->has('email'))
                    <p id="login_error" class="form-field-error">{{ $errors->first('login') ?: $errors->first('email') }}</p>
                @endif
            </div>

            <x-password-input
                id="password"
                name="password"
                label="Password"
                autocomplete="current-password"
                :required="true"
                error-id="password_error"
            />

            <div class="auth-meta-row">
                <label for="remember_me" class="checkbox-inline">
                    <input id="remember_me" type="checkbox" name="remember" class="checkbox-input" {{ old('remember') ? 'checked' : '' }}>
                    <span>Keep me signed in</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="table-link">Forgot password?</a>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary button-block" data-busy-text="Signing in...">
                    <x-icon name="sign-in" :size="15" />
                    <span>Sign In</span>
                </button>
            </div>
        </form>

        <p class="auth-secondary-link-row">
            Need an account? <a href="{{ route('register') }}" class="table-link">Register</a>
        </p>

        @if (config('app.demo_hint'))
            <div class="auth-demo-footer">
                <button
                    type="button"
                    class="button button-secondary button-sm"
                    data-fill-demo-login
                    data-demo-user="admin.reyes"
                    data-demo-pass="POSDemo!2026"
                >
                    Fill Demo Credentials
                </button>
            </div>
        @endif
    </div>
</x-guest-layout>
