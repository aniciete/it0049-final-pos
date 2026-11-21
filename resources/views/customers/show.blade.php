@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('customers.index') }}" class="back-link">&larr; Back to Customers</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Customer #{{ $customer->id }}</p>
            <h1 class="page-title">{{ $customer->full_name }}</h1>
            <p class="page-lead">Registered retail customer profile.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('customers.edit', $customer) }}" class="button button-primary">Edit Customer</a>
        </div>
    </header>

    <div class="details-card" style="max-width: 680px;">
        <ul class="details-list">
            <li>
                <strong>Full Name</strong>
                <span>{{ $customer->full_name }}</span>
            </li>
            <li>
                <strong>Email Address</strong>
                <span>{{ $customer->email }}</span>
            </li>
            <li>
                <strong>Phone Number</strong>
                <span class="mono-num">{{ $customer->phone ?: 'Not provided' }}</span>
            </li>
            <li>
                <strong>Linked Sales</strong>
                <span class="mono-num">{{ $customer->sales_count }}</span>
            </li>
            <li>
                <strong>Created</strong>
                <span class="mono-num">{{ $customer->created_at?->format('M j, Y H:i') }}</span>
            </li>
        </ul>
    </div>
</div>
@endsection
