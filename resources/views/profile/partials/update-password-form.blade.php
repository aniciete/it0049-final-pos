<section>
    <div class="form-card-header">
        <h2 class="form-section-title">Update Password</h2>
    </div>

    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        @method('PUT')

        <div class="form-grid">
            <x-password-input
                id="update_password_current_password"
                name="current_password"
                label="Current Password"
                autocomplete="current-password"
                error-bag="updatePassword"
                error-id="update_current_password_error"
            />

            <div class="form-row-two">
                <x-password-input
                    id="update_password_password"
                    name="password"
                    label="New Password"
                    autocomplete="new-password"
                    error-bag="updatePassword"
                    error-id="update_new_password_error"
                    hint="Minimum 8 characters."
                />

                <x-password-input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    label="Confirm Password"
                    autocomplete="new-password"
                    error-bag="updatePassword"
                    error-id="update_confirm_password_error"
                    toggle-label="Show confirm password"
                />
            </div>
        </div>

        <div class="form-actions">
            @if (session('status') === 'password-updated')
                <span class="status-pill status-ok">Password Updated</span>
            @endif
            <button type="submit" class="button button-primary">Update Password</button>
        </div>
    </form>
</section>
