@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('customers.index') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Customers</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">{{ $customer->full_name }}</h1>
            <p class="page-lead">Customer account #{{ $customer->id }}</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('customers.edit', $customer) }}" class="button button-primary">
                <x-icon name="pencil-simple" :size="15" />
                <span>Edit Customer</span>
            </a>
        </div>
    </header>

    <div class="workspace-split">
        <section class="details-card" aria-label="Customer details">
            <div class="detail-kv-row">
                <span class="detail-kv-label">Full Name</span>
                <span class="detail-kv-value">{{ $customer->full_name }}</span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Email Address</span>
                <span class="detail-kv-value">{{ $customer->email }}</span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Phone Number</span>
                <span class="detail-kv-value">
                    @if ($customer->phone)
                        <span class="mono-num">{{ $customer->phone }}</span>
                    @else
                        <span class="cell-muted">Not provided</span>
                    @endif
                </span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Linked Sales</span>
                <span class="detail-kv-value mono-num">
                    <x-sales-count-link :count="$customer->sales_count" :url="route('sales.index', ['customer' => $customer->id])" />
                </span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Added On</span>
                <span class="detail-kv-value mono-num">{{ $customer->created_at?->format('M j, Y') }}</span>
            </div>
        </section>

        <x-recent-sales-panel
            title="Linked Orders"
            subtitle="Latest sales attached to this customer."
            :sales="$recentSales"
            empty-text="No orders linked to this customer yet."
            :show-customer="false"
            :filter-url="(int) $customer->sales_count > 0 ? route('sales.index', ['customer' => $customer->id]) : null"
        />
    </div>
</div>
@endsection
