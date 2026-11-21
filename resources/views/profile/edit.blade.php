<x-app-layout :title="'My Profile | Bright-Market POS'" :activePage="'profile'">
    <div class="back-link-row">
        <a href="{{ route('dashboard') }}" class="back-link">&larr; Back to Dashboard</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Operator Settings</p>
            <h1 class="page-title">Account Profile</h1>
            <p class="page-lead">Update your staff profile details, email address, or password.</p>
        </div>
    </header>

    <div class="profile-stack">
        <div class="form-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="form-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="form-card">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
