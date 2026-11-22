<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Create Staff Account</h1>
            <p class="auth-subtitle">Register a new operator account for the POS register.</p>
        </header>

        @include('partials.error_summary')

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    required
                    autofocus
                    aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                    @error('name') aria-describedby="name_error" @enderror
                >
                @error('name')
                    <p id="name_error" class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    @error('email') aria-describedby="email_error" @enderror
                >
                @error('email')
                    <p id="email_error" class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <x-password-input
                id="password"
                name="password"
                label="Password"
                autocomplete="new-password"
                :required="true"
                error-id="password_error"
                hint="Minimum 8 characters."
            />

            <x-password-input
                id="password_confirmation"
                name="password_confirmation"
                label="Confirm Password"
                autocomplete="new-password"
                :required="true"
                error-id="password_confirmation_error"
                toggle-label="Show confirm password"
            />

            <div class="form-actions">
                <a href="{{ route('login') }}" class="button button-secondary">Sign In</a>
                <button type="submit" class="button button-primary" data-busy-text="Creating...">Create Account</button>
            </div>
        </form>
    </div>
</x-guest-layout>
