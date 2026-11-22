<x-app-layout :title="'My Profile | Bright-Market POS'" :activePage="'profile'">
    <div class="back-link-row">
        <a href="{{ route('dashboard') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Dashboard</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">Account Profile</h1>
            <p class="page-lead">Update your personal details, email address, or password.</p>
        </div>
    </header>

    <div class="workspace-split">
        <div class="profile-column">
            <div class="form-card">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="form-card">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="profile-column">
            <section class="context-panel" aria-label="Account Overview">
                <div class="detail-identity-row">
                    <img
                        src="{{ $user->avatarUrl() }}"
                        alt="{{ $user->full_name }} photo"
                        class="detail-identity-avatar"
                        width="56"
                        height="56"
                    >
                    <div>
                        <h2 class="section-title">{{ $user->full_name }}</h2>
                        <p class="section-meta" style="margin-top: 0.25rem;">
                            <span class="status-pill status-neutral">{{ $user->role }}</span>
                        </p>
                    </div>
                </div>

                <div class="detail-attr-grid">
                    <div class="detail-attr-item" style="border-bottom: 0; padding-bottom: 0;">
                        <span class="detail-attr-label">Username</span>
                        <span class="detail-attr-value mono-num">{{ $user->username }}</span>
                    </div>
                    <div class="detail-attr-item" style="border-bottom: 0; padding-bottom: 0;">
                        <span class="detail-attr-label">Recorded Sales</span>
                        <span class="detail-attr-value mono-num">
                            <x-sales-count-link :count="$salesCount" :url="route('sales.index', ['staff' => $user->id])" />
                        </span>
                    </div>
                </div>
            </section>

            <div class="form-card">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
