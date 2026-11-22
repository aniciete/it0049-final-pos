<section>
    <div class="form-card-header">
        <h2 class="form-section-title">Profile Information</h2>
    </div>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}" novalidate>
        @csrf
        @method('PATCH')

        <div class="form-row-two">
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                @error('name')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email')
                    <p class="form-field-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="form-meta-note">
                Your email address is unverified.
                <button form="send-verification" class="table-link" style="background:none;border:0;cursor:pointer;padding:0;">
                    Click here to re-send the verification email.
                </button>
            </p>
        @endif

        <div class="form-actions">
            @if (session('status') === 'profile-updated')
                <span class="status-pill status-ok">Saved</span>
            @endif
            <button type="submit" class="button button-primary">Save Profile</button>
        </div>
    </form>
</section>
