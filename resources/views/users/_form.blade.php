@php
    $isEdit = $user->exists;
    $isSelf = $isEdit && $user->is(Auth::user());
@endphp

<div class="form-grid">
    <div class="form-row-two">
        <div class="form-group">
            <label for="user-full-name" class="form-label">Full Name</label>
            <input
                type="text"
                id="user-full-name"
                name="full_name"
                class="form-control"
                value="{{ old('full_name', $user->full_name) }}"
                maxlength="100"
                required
                aria-invalid="{{ $errors->has('full_name') ? 'true' : 'false' }}"
                @error('full_name') aria-describedby="user_fullname_error" @enderror
            >
            @error('full_name')
                <p id="user_fullname_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user-username" class="form-label">Username</label>
            <input
                type="text"
                id="user-username"
                name="username"
                class="form-control mono-num"
                value="{{ old('username', $user->username) }}"
                maxlength="50"
                required
                autocomplete="off"
                aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                @error('username') aria-describedby="user_username_error" @enderror
            >
            @error('username')
                <p id="user_username_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="form-row-two">
        <div class="form-group">
            <label for="user-email" class="form-label">Email Address</label>
            <input
                type="email"
                id="user-email"
                name="email"
                class="form-control"
                value="{{ old('email', $user->email) }}"
                maxlength="150"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @error('email') aria-describedby="user_email_error" @enderror
            >
            <p class="form-hint">Optional. Used for notifications and account recovery.</p>
            @error('email')
                <p id="user_email_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user-role" class="form-label">Role</label>
            @if ($isSelf)
                <input type="hidden" name="role" value="{{ $user->role }}">
            @endif
            <select
                id="user-role"
                @unless ($isSelf) name="role" @endunless
                class="form-control"
                {{ $isSelf ? 'disabled' : '' }}
                aria-invalid="{{ $errors->has('role') ? 'true' : 'false' }}"
                @error('role') aria-describedby="user_role_error" @enderror
            >
                @foreach ($roles as $role)
                    <option value="{{ $role }}" {{ old('role', $user->role ?: 'Staff Member') === $role ? 'selected' : '' }}>
                        {{ $role }}
                    </option>
                @endforeach
            </select>
            @if ($isSelf)
                <p class="form-hint">You cannot change your own operational role.</p>
            @endif
            @error('role')
                <p id="user_role_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="form-row-two">
        <x-password-input
            id="user-password"
            name="password"
            :label="$isEdit ? 'New Password' : 'Password'"
            autocomplete="new-password"
            :required="! $isEdit"
            error-id="user_password_error"
            :hint="$isEdit ? 'Leave blank to keep the current password.' : null"
        />

        <x-password-input
            id="user-password-confirmation"
            name="password_confirmation"
            label="Confirm Password"
            autocomplete="new-password"
            :required="! $isEdit"
            toggle-label="Show confirm password"
            :show-errors="false"
        />
    </div>

    <x-media-picker
        field-id="user-avatar"
        input-name="avatar"
        label="Avatar"
        preview-id="user-avatar-preview"
        status-id="user-avatar-status"
        :default-src="$user->avatarUrl()"
        :alt="($user->full_name ?: 'Staff').' avatar preview'"
        :has-media="$user->hasAvatar()"
        active-status="Current avatar active."
        empty-status="No custom avatar uploaded."
        help-text="Optional. JPG, PNG, or WebP up to 2 MB, cropped to 256x256."
        remove-name="remove_avatar"
        remove-label="Remove existing avatar on save"
        error-id="user_avatar_error"
        :is-edit="$isEdit"
    />
</div>
