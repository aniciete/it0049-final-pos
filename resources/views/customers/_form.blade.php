<div class="form-grid">
    <div class="form-group">
        <label for="customer-full-name" class="form-label">Full Name</label>
        <input
            type="text"
            id="customer-full-name"
            name="full_name"
            class="form-control"
            value="{{ old('full_name', $customer->full_name) }}"
            maxlength="100"
            required
            aria-invalid="{{ $errors->has('full_name') ? 'true' : 'false' }}"
            @error('full_name') aria-describedby="customer_name_error" @enderror
        >
        @error('full_name')
            <p id="customer_name_error" class="form-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-row-two">
        <div class="form-group">
            <label for="customer-email" class="form-label">Email Address</label>
            <input
                type="email"
                id="customer-email"
                name="email"
                class="form-control"
                value="{{ old('email', $customer->email) }}"
                maxlength="100"
                required
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @error('email') aria-describedby="customer_email_error" @enderror
            >
            @error('email')
                <p id="customer_email_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="customer-phone" class="form-label">Phone Number</label>
            <input
                type="text"
                id="customer-phone"
                name="phone"
                class="form-control mono-num"
                value="{{ old('phone', $customer->phone) }}"
                maxlength="20"
                aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                @error('phone') aria-describedby="customer_phone_error" @enderror
            >
            <p class="form-hint">Optional. Format example: +63 917 555 0199</p>
            @error('phone')
                <p id="customer_phone_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
