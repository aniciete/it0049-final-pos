@php
    $isEdit = $user->exists;
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
            <label for="user-email" class="form-label">
                Email Address <span class="form-label-optional">(Optional)</span>
            </label>
            <input
                type="email"
                id="user-email"
                name="email"
                class="form-control"
                value="{{ old('email', $user->email) }}"
                maxlength="150"
                placeholder="e.g. staff@brightmarket.test"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @error('email') aria-describedby="user_email_error" @enderror
            >
            @error('email')
                <p id="user_email_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user-role" class="form-label">Role</label>
            <select
                id="user-role"
                name="role"
                class="form-control"
                aria-invalid="{{ $errors->has('role') ? 'true' : 'false' }}"
                @error('role') aria-describedby="user_role_error" @enderror
            >
                @foreach ($roles as $role)
                    <option value="{{ $role }}" {{ old('role', $user->role ?: 'Staff Member') === $role ? 'selected' : '' }}>
                        {{ $role }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p id="user_role_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="form-row-two">
        <div class="form-group">
            <label for="user-password" class="form-label">
                {{ $isEdit ? 'New Password' : 'Password' }}
                @if ($isEdit)
                    <span class="form-label-optional">(Leave blank to keep current)</span>
                @endif
            </label>
            <div class="input-with-toggle">
                <input
                    type="password"
                    id="user-password"
                    name="password"
                    class="form-control"
                    autocomplete="new-password"
                    {{ $isEdit ? '' : 'required' }}
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    @error('password') aria-describedby="user_password_error" @enderror
                >
                <button
                    type="button"
                    class="password-toggle-btn"
                    data-password-toggle="user-password"
                    aria-controls="user-password"
                    aria-pressed="false"
                    aria-label="Show password"
                >Show</button>
            </div>
            @error('password')
                <p id="user_password_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user-password-confirmation" class="form-label">Confirm Password</label>
            <input
                type="password"
                id="user-password-confirmation"
                name="password_confirmation"
                class="form-control"
                autocomplete="new-password"
                {{ $isEdit ? '' : 'required' }}
            >
        </div>
    </div>

    <div class="form-group">
        <label for="user-avatar" class="form-label">
            Avatar Image <span class="form-label-optional">(Optional — JPG, PNG, WebP up to 2 MB)</span>
        </label>

        <div class="media-preview-row">
            <img
                id="user-avatar-preview"
                src="{{ $user->avatarUrl() }}"
                data-default-src="{{ $user->avatarUrl() }}"
                alt="{{ $user->full_name ?: 'Staff' }} avatar preview"
                class="media-preview-thumb"
                width="64"
                height="64"
            >
            <div>
                <p id="user-avatar-status" class="form-hint" style="font-weight: 700; color: var(--basil);">
                    {{ $user->hasAvatar() ? 'Current avatar stored on public disk' : 'No custom avatar uploaded (using placeholder)' }}
                </p>
                <p class="form-hint">Avatars are cropped cleanly to 256&times;256 on save.</p>
            </div>
        </div>

        <input
            type="file"
            id="user-avatar"
            name="avatar"
            class="form-control"
            accept="image/jpeg,image/png,image/webp"
            data-preview-img="user-avatar-preview"
            data-preview-status="user-avatar-status"
            data-remove-checkbox="remove_avatar"
            aria-invalid="{{ $errors->has('avatar') ? 'true' : 'false' }}"
            @error('avatar') aria-describedby="user_avatar_error" @enderror
        >
        @error('avatar')
            <p id="user_avatar_error" class="form-field-error">{{ $message }}</p>
        @enderror

        @if ($isEdit && $user->hasAvatar())
            <label for="remove_avatar" class="checkbox-inline" style="margin-top: var(--space-1);">
                <input id="remove_avatar" type="checkbox" name="remove_avatar" value="1" class="checkbox-input" {{ old('remove_avatar') ? 'checked' : '' }}>
                <span>Remove existing avatar on save</span>
            </label>
        @endif
    </div>
</div>
