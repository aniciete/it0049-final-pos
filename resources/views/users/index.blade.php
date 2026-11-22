@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <h1 class="page-title">Staff</h1>
            <p class="page-lead">Manage who can sign in and their operational roles.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('users.create') }}" class="button button-primary">
                <x-icon name="plus" :size="15" />
                <span>Add Staff</span>
            </a>
        </div>
    </header>

    @if ($users->isEmpty())
        <div class="empty-ledger-card">
            <div class="empty-ledger-icon"><x-icon name="users" :size="32" /></div>
            <h2 class="empty-ledger-title">No Staff Accounts Found</h2>
            <p class="empty-ledger-desc">Create an operator account to grant authenticated POS access.</p>
            <a href="{{ route('users.create') }}" class="button button-primary">
                <x-icon name="plus" :size="15" />
                <span>Add Staff</span>
            </a>
        </div>
    @else
        <div class="table-wrapper" tabindex="0" role="region" aria-label="Staff accounts directory">
            <table class="data-table">
                <caption class="visually-hidden">Staff Directory</caption>
                <thead>
                    <tr>
                        <th scope="col" class="num-col">ID</th>
                        <th scope="col">Staff Member</th>
                        <th scope="col">Username / Email</th>
                        <th scope="col">Role</th>
                        <th scope="col" class="num-col">Sales</th>
                        <th scope="col" class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $staff)
                        @php
                            $isCurrent = $staff->is(Auth::user());
                            $canDelete = ! $isCurrent && (int) $staff->sales_count === 0;
                            $deleteDisabledReason = $isCurrent
                                ? 'Current active account cannot be deleted.'
                                : 'Staff members with recorded sales cannot be deleted.';
                        @endphp
                        <tr>
                            <td class="num-col" data-label="ID">{{ $staff->id }}</td>
                            <td data-label="Staff Member">
                                <div class="cell-product-lockup">
                                    <img
                                        src="{{ $staff->avatarUrl() }}"
                                        alt="{{ $staff->full_name }} avatar"
                                        class="table-thumb"
                                        width="48"
                                        height="48"
                                        loading="lazy"
                                    >
                                    <div>
                                        <a href="{{ route('users.show', $staff) }}" class="cell-primary table-link">{{ $staff->full_name }}</a>
                                        @if ($isCurrent)
                                            <span class="cell-secondary">Current Session</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="Username / Email">
                                <div>
                                    <span class="cell-primary mono-num">{{ $staff->username }}</span>
                                    <span class="cell-secondary">{{ $staff->email ?: 'No email linked' }}</span>
                                </div>
                            </td>
                            <td data-label="Role">
                                <span class="status-pill status-neutral">{{ $staff->role }}</span>
                            </td>
                            <td class="num-col mono-num" data-label="Sales">{{ $staff->sales_count }}</td>
                            <td class="col-actions" data-label="Actions">
                                <div class="table-actions-group">
                                    <a href="{{ route('users.edit', $staff) }}" class="button button-secondary button-sm">
                                        <x-icon name="pencil-simple" :size="14" />
                                        <span>Edit</span>
                                    </a>
                                    @if ($canDelete)
                                        <form
                                            action="{{ route('users.destroy', $staff) }}"
                                            method="POST"
                                            class="inline-form"
                                            data-confirm-title="Delete Staff"
                                            data-confirm-message="Delete staff {{ $staff->full_name }} ({{ $staff->username }})? This action cannot be undone."
                                            data-confirm-action="Delete"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-ghost-danger button-sm">
                                                <x-icon name="trash" :size="14" />
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    @else
                                        <button
                                            type="button"
                                            class="button button-ghost-danger button-sm"
                                            disabled
                                            title="{{ $deleteDisabledReason }}"
                                            aria-label="{{ $deleteDisabledReason }}"
                                        >
                                            <x-icon name="trash" :size="14" />
                                            <span>Delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
