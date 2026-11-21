<section>
    <div class="form-card-header">
        <h2 class="form-section-title">Delete Account</h2>
    </div>

    <p class="page-lead" style="font-size: var(--text-sm); margin-bottom: var(--space-4);">
        Enter your current password to permanently delete your own account. Accounts with recorded sales cannot be removed.
    </p>

    <form
        method="POST"
        action="{{ route('profile.destroy') }}"
        data-confirm-title="Delete Your Account"
        data-confirm-message="Permanently delete your operator account? This action cannot be undone."
        data-confirm-action="Delete Account"
    >
        @csrf
        @method('DELETE')

        <div class="form-group">
            <label for="delete_account_password" class="form-label">Confirm Password</label>
            <input id="delete_account_password" name="password" type="password" class="form-control" placeholder="Enter your password" required>
            @if ($errors->userDeletion->has('password'))
                <p class="form-field-error">{{ $errors->userDeletion->first('password') }}</p>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-danger">Delete Account</button>
        </div>
    </form>
</section>
