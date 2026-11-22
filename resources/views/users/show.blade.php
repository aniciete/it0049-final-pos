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
                alt="{{ $user->full_name }} avatar"
                class="detail-identity-avatar"
                width="56"
                height="56"
            >
            <div>
                <h1 class="page-title">{{ $user->full_name }}</h1>
                <p class="page-lead">{{ $user->role }} &middot; Account <span class="mono-num">#{{ $user->id }}</span></p>
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
            <div class="detail-kv-row">
                <span class="detail-kv-label">Role</span>
                <span class="detail-kv-value">
                    <span class="status-pill status-neutral">{{ $user->role }}</span>
                </span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Username</span>
                <span class="detail-kv-value mono-num">{{ $user->username }}</span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Email Address</span>
                <span class="detail-kv-value">{{ $user->email ?: 'Not set' }}</span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Credential Status</span>
                <span class="detail-kv-value">Password set</span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Recorded Sales</span>
                <span class="detail-kv-value mono-num">
                    <x-sales-count-link :count="$user->sales_count" :url="route('sales.index', ['staff' => $user->id])" />
                </span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Member Since</span>
                <span class="detail-kv-value mono-num">{{ $user->created_at?->format('M j, Y') }}</span>
            </div>
        </section>

        <x-recent-sales-panel
            title="Recent Sales"
            subtitle="Latest receipts processed by this staff member."
            :sales="$recentSales"
            empty-text="No sales recorded by this staff member yet."
            :show-seller="false"
            :filter-url="(int) $user->sales_count > 0 ? route('sales.index', ['staff' => $user->id]) : null"
        />
    </div>
</div>
@endsection
