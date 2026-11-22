@props([
    'idPrefix' => 'login',
    'autofocus' => false,
    'showSummary' => true,
])

@if ($showSummary)
    @include('partials.error_summary')
@endif

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <div class="form-group">
        <label for="{{ $idPrefix }}-login" class="form-label">Username or Email</label>
        <input
            id="{{ $idPrefix }}-login"
            type="text"
            name="login"
            class="form-control"
            value="{{ old('login', old('email')) }}"
            autocomplete="username"
            autocorrect="off"
            autocapitalize="none"
            spellcheck="false"
            required
            @if ($autofocus) autofocus @endif
            aria-invalid="{{ ($errors->has('login') || $errors->has('email')) ? 'true' : 'false' }}"
            @if ($errors->has('login') || $errors->has('email')) aria-describedby="{{ $idPrefix }}_login_error" @endif
        >
        @if ($errors->has('login') || $errors->has('email'))
            <p id="{{ $idPrefix }}_login_error" class="form-field-error">{{ $errors->first('login') ?: $errors->first('email') }}</p>
        @endif
    </div>

    <x-password-input
        :id="$idPrefix.'-password'"
        name="password"
        label="Password"
        autocomplete="current-password"
        :required="true"
        :error-id="$idPrefix.'_password_error'"
    />

    <div class="auth-meta-row">
        <label for="{{ $idPrefix }}-remember" class="checkbox-inline">
            <input id="{{ $idPrefix }}-remember" type="checkbox" name="remember" class="checkbox-input" {{ old('remember') ? 'checked' : '' }}>
            <span>Keep me signed in</span>
        </label>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="table-link">Forgot password?</a>
        @endif
    </div>

    <div class="form-actions" style="margin-top: var(--space-4);">
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
