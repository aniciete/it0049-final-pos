@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('users.index') }}" class="back-link">&larr; Back to Staff</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Staff Account #{{ $user->id }}</p>
            <h1 class="page-title">{{ $user->full_name }}</h1>
            <p class="page-lead">Authenticated POS staff profile and security summary.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('users.edit', $user) }}" class="button button-primary">Edit Staff User</a>
        </div>
    </header>

    <div class="details-card" style="max-width: 680px;">
        <div class="media-preview-row" style="margin-bottom: var(--space-5);">
            <img
                src="{{ $user->avatarUrl() }}"
                alt="{{ $user->full_name }} avatar"
                class="media-preview-thumb"
                width="64"
                height="64"
            >
            <div>
                <h2 class="section-title">{{ $user->full_name }}</h2>
                <p class="section-meta">{{ $user->role }}</p>
            </div>
        </div>

        <ul class="details-list">
            <li>
                <strong>Username</strong>
                <span class="mono-num">{{ $user->username }}</span>
            </li>
            <li>
                <strong>Email Address</strong>
                <span>{{ $user->email ?: 'Not set' }}</span>
            </li>
            <li>
                <strong>Password Storage</strong>
                <span class="status-pill status-ok">Bcrypt Hashed</span>
            </li>
            <li>
                <strong>Recorded Sales</strong>
                <span class="mono-num">{{ $user->sales_count }}</span>
            </li>
            <li>
                <strong>Member Since</strong>
                <span class="mono-num">{{ $user->created_at?->format('M j, Y H:i') }}</span>
            </li>
        </ul>
    </div>
</div>
@endsection
