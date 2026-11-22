@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('users.index') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Staff</span>
        </a>
    </div>

    <header class="directory-header">
        <div class="cell-product-lockup">
            <img
                src="{{ $user->avatarUrl() }}"
                alt="{{ $user->full_name }} photo"
                class="detail-identity-avatar"
                width="56"
                height="56"
            >
            <div>
                <h1 class="page-title">{{ $user->full_name }}</h1>
                <p class="page-lead">
                    <span class="status-pill status-neutral">{{ $user->role }}</span>
                </p>
            </div>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('users.edit', $user) }}" class="button button-primary">
                <x-icon name="pencil-simple" :size="15" />
                <span>Edit Staff</span>
            </a>
        </div>
    </header>

    <div class="workspace-split">
        <section class="details-card" aria-label="Staff profile details">
            <div class="detail-attr-grid">
                <div class="detail-attr-item">
                    <span class="detail-attr-label">Username</span>
                    <span class="detail-attr-value mono-num">{{ $user->username }}</span>
                </div>
                <div class="detail-attr-item">
                    <span class="detail-attr-label">Email Address</span>
                    <span class="detail-attr-value">{{ $user->email ?: 'Not set' }}</span>
                </div>
                <div class="detail-attr-item">
                    <span class="detail-attr-label">Credential Status</span>
                    <span class="detail-attr-value">Password set</span>
                </div>
                <div class="detail-attr-item">
                    <span class="detail-attr-label">Recorded Sales</span>
                    <span class="detail-attr-value mono-num">
                        <x-sales-count-link :count="$user->sales_count" :url="route('sales.index', ['staff' => $user->id])" />
                    </span>
                </div>
                <div class="detail-attr-item" style="border-bottom: 0; padding-bottom: 0;">
                    <span class="detail-attr-label">Member Since</span>
                    <span class="detail-attr-value mono-num">{{ $user->created_at?->format('M j, Y') }}</span>
                </div>
                <div class="detail-attr-item" style="border-bottom: 0; padding-bottom: 0;">
                    <span class="detail-attr-label">Session Status</span>
                    <span class="detail-attr-value">{{ $user->is(Auth::user()) ? 'Current Session' : 'Staff operator' }}</span>
                </div>
            </div>
        </section>

        <x-recent-sales-panel
            title="Recent Sales"
            :sales="$recentSales"
            empty-text="No sales recorded by this staff member yet."
            :show-seller="false"
            :filter-url="(int) $user->sales_count > 0 ? route('sales.index', ['staff' => $user->id]) : null"
        />
    </div>
</div>
@endsection
