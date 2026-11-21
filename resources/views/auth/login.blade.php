<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Staff Sign In</h1>
            <p class="auth-subtitle">
                Enter your staff username or email address and password to access POS management and sales checkout.
            </p>
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
                    placeholder="e.g. admin.reyes or admin.reyes@brightmarket.test"
                    required
                    autofocus
                    aria-invalid="{{ ($errors->has('login') || $errors->has('email')) ? 'true' : 'false' }}"
                    @if ($errors->has('login') || $errors->has('email')) aria-describedby="login_error" @endif
                >
                @if ($errors->has('login') || $errors->has('email'))
                    <p id="login_error" class="form-field-error">{{ $errors->first('login') ?: $errors->first('email') }}</p>
                @endif
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-with-toggle">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control"
                        autocomplete="current-password"
                        required
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        @error('password') aria-describedby="password_error" @enderror
                    >
                    <button
                        type="button"
                        class="password-toggle-btn"
                        data-password-toggle="password"
                        aria-controls="password"
                        aria-pressed="false"
                        aria-label="Show password"
                    >Show</button>
                </div>
                @error('password')
                    <p id="password_error" class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

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
                <a href="{{ route('register') }}" class="button button-secondary">Register Account</a>
                <button type="submit" class="button button-primary" data-busy-text="Signing in...">Sign In</button>
            </div>
        </form>

        <aside class="auth-demo-note" aria-label="Seeded demo staff credentials">
            <strong>Demo Staff Credentials:</strong><br>
            Username: <code>admin.reyes</code> (or <code>admin.reyes@brightmarket.test</code>)<br>
            Password: <code>POSDemo!2026</code>
        </aside>
    </div>
</x-guest-layout>
