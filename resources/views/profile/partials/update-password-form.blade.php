<section>
    <div class="form-card-header">
        <h2 class="form-section-title">Update Password</h2>
    </div>

    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="update_password_current_password" class="form-label">Current Password</label>
                <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
                @if ($errors->updatePassword->has('current_password'))
                    <p class="form-field-error">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <div class="form-row-two">
                <div class="form-group">
                    <label for="update_password_password" class="form-label">New Password</label>
                    <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
                    @if ($errors->updatePassword->has('password'))
                        <p class="form-field-error">{{ $errors->updatePassword->first('password') }}</p>
                    @endif
                </div>

                <div class="form-group">
                    <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                    @if ($errors->updatePassword->has('password_confirmation'))
                        <p class="form-field-error">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                    @endif
                </div>
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
