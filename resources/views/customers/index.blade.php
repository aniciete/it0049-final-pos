@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <p class="eyebrow">Customer Directory</p>
            <h1 class="page-title">Customer Accounts</h1>
            <p class="page-lead">Manage registered retail customers linked optionally to checkout sales.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('customers.create') }}" class="button button-primary">+ New Customer</a>
        </div>
    </header>

    @if ($customers->isEmpty())
        <div class="empty-ledger-card">
            <h2 class="empty-ledger-title">No Customer Accounts Yet</h2>
            <p class="empty-ledger-desc">Add a customer profile to link orders, or continue processing walk-in sales.</p>
            <a href="{{ route('customers.create') }}" class="button button-primary">+ Add First Customer</a>
        </div>
    @else
        <div class="table-wrapper" tabindex="0" role="region" aria-label="Customer accounts directory">
            <table class="data-table">
                <caption class="visually-hidden">Registered Customer Directory</caption>
                <thead>
                    <tr>
                        <th scope="col" class="num-col">ID</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Email Address</th>
                        <th scope="col">Phone</th>
                        <th scope="col" class="num-col">Orders</th>
                        <th scope="col" class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="num-col" data-label="ID">{{ $customer->id }}</td>
                            <td data-label="Full Name">
                                <a href="{{ route('customers.show', $customer) }}" class="cell-primary table-link">{{ $customer->full_name }}</a>
                            </td>
                            <td data-label="Email Address">{{ $customer->email }}</td>
                            <td class="mono-num" data-label="Phone">{{ $customer->phone ?: '—' }}</td>
                            <td class="num-col mono-num" data-label="Orders">{{ $customer->sales_count }}</td>
                            <td class="col-actions" data-label="Actions">
                                <div class="table-actions-group">
                                    <a href="{{ route('customers.edit', $customer) }}" class="button button-secondary button-sm">Edit</a>
                                    <form
                                        action="{{ route('customers.destroy', $customer) }}"
                                        method="POST"
                                        class="inline-form"
                                        data-confirm-title="Delete Customer"
                                        data-confirm-message="Delete customer {{ $customer->full_name }}? Any past sales linked to this customer will remain in Sales History as Walk-in."
                                        data-confirm-action="Delete"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-danger button-sm">Delete</button>
                                    </form>
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
