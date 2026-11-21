@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <p class="eyebrow">Staff Directory</p>
            <h1 class="page-title">Staff User Accounts</h1>
            <p class="page-lead">Manage POS operator accounts, avatars, operational roles, and securely hashed credentials.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('users.create') }}" class="button button-primary">+ New Staff User</a>
        </div>
    </header>

    @if ($users->isEmpty())
        <div class="empty-ledger-card">
            <h2 class="empty-ledger-title">No Staff Accounts Found</h2>
            <p class="empty-ledger-desc">Create an operator account to grant authenticated POS access.</p>
            <a href="{{ route('users.create') }}" class="button button-primary">+ Add First Staff User</a>
        </div>
    @else
        <div class="table-wrapper" tabindex="0" role="region" aria-label="Staff accounts directory">
            <table class="data-table">
                <caption class="visually-hidden">Staff User Accounts</caption>
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
                                        @if ($staff->is(Auth::user()))
                                            <span class="cell-secondary">Current Session</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="Username / Email">
                                <span class="cell-primary mono-num">{{ $staff->username }}</span>
                                <span class="cell-secondary">{{ $staff->email ?: 'No email linked' }}</span>
                            </td>
                            <td data-label="Role">
                                <span class="status-pill status-ok">{{ $staff->role }}</span>
                            </td>
                            <td class="num-col mono-num" data-label="Sales">{{ $staff->sales_count }}</td>
                            <td class="col-actions" data-label="Actions">
                                <div class="table-actions-group">
                                    <a href="{{ route('users.edit', $staff) }}" class="button button-secondary button-sm">Edit</a>
                                    @if (! $staff->is(Auth::user()) && $staff->sales_count === 0)
                                        <form
                                            action="{{ route('users.destroy', $staff) }}"
                                            method="POST"
                                            class="inline-form"
                                            data-confirm-title="Delete Staff User"
                                            data-confirm-message="Delete staff account {{ $staff->full_name }} ({{ $staff->username }})? This action cannot be undone."
                                            data-confirm-action="Delete"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger button-sm">Delete</button>
                                        </form>
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
