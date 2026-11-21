<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Create Staff Account</h1>
            <p class="auth-subtitle">Register a new operator account with securely hashed credentials.</p>
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
                >
                @error('name')
                    <p class="form-field-error">{{ $message }}</p>
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
                >
                @error('email')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-with-toggle">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control"
                        autocomplete="new-password"
                        required
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
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
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    autocomplete="new-password"
                    required
                    aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                >
                @error('password_confirmation')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('login') }}" class="button button-secondary">Already Registered</a>
                <button type="submit" class="button button-primary" data-busy-text="Creating...">Create Account</button>
            </div>
        </form>
    </div>
</x-guest-layout>
