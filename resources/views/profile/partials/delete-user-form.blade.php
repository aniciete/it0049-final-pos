<section>
    <div class="form-card-header">
        <h2 class="form-section-title">Delete Account</h2>
    </div>

    @if (($salesCount ?? 0) > 0)
        <div class="locked-rule-notice" role="status">
            <x-icon name="lock" :size="18" />
            <div>
                <p class="locked-rule-title">Account deletion is locked</p>
                <p class="locked-rule-desc">
                    This account has <strong class="mono-num">{{ $salesCount }}</strong> recorded
                    {{ \Illuminate\Support\Str::plural('sale', $salesCount) }} in the ledger and cannot be removed.
                </p>
            </div>
        </div>
    @else
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

            <x-password-input
                id="delete_account_password"
                name="password"
                label="Confirm Password"
                autocomplete="current-password"
                :required="true"
                error-bag="userDeletion"
                error-id="delete_account_password_error"
            />

            <div class="form-actions">
                <button type="submit" class="button button-danger">
                    <x-icon name="trash" :size="15" />
                    <span>Delete Account</span>
                </button>
            </div>
        </form>
    @endif
</section>
