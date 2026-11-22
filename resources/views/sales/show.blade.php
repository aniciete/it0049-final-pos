@extends('layouts.app')

@section('content')
@php
    $unitPrice = (int) $sale->quantity > 0
        ? ((float) $sale->total_price / (int) $sale->quantity)
        : 0.0;
@endphp

<div class="container">
    <div class="back-link-row">
        <a href="{{ route('sales.index') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Sales History</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">Receipt <span class="mono-num">#{{ $sale->id }}</span></h1>
            <p class="page-lead">Completed counter sale recorded {{ $sale->created_at?->format('M j, Y g:i A') }}.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('sales.create') }}" class="button button-primary">
                <x-icon name="plus" :size="15" />
                <span>Record Sale</span>
            </a>
        </div>
    </header>

    <section class="details-card form-measure" aria-label="Receipt breakdown">
        <div class="detail-kv-row">
            <span class="detail-kv-label">Receipt Number</span>
            <span class="detail-kv-value mono-num">#{{ $sale->id }}</span>
        </div>
        <div class="detail-kv-row">
            <span class="detail-kv-label">Transaction Date</span>
            <span class="detail-kv-value mono-num">{{ $sale->created_at?->format('M j, Y g:i A') }}</span>
        </div>
        <div class="detail-kv-row">
            <span class="detail-kv-label">Product</span>
            <span class="detail-kv-value">
                @if ($sale->product)
                    <a href="{{ route('products.show', $sale->product_id) }}" class="table-link">{{ $sale->product->name }}</a>
                    @if ($sale->product->trashed())
                        <span class="cell-muted">(Archived)</span>
                    @endif
                @else
                    Removed Product
                @endif
            </span>
        </div>
        <div class="detail-kv-row">
            <span class="detail-kv-label">Quantity</span>
            <span class="detail-kv-value mono-num">{{ $sale->quantity }}</span>
        </div>
        <div class="detail-kv-row">
            <span class="detail-kv-label">Unit Price</span>
            <span class="detail-kv-value mono-num">${{ number_format($unitPrice, 2) }}</span>
        </div>
        <div class="detail-kv-row">
            <span class="detail-kv-label">Total Price</span>
            <span class="detail-kv-value mono-num" style="font-weight: 800; font-size: var(--text-md);">${{ number_format((float) $sale->total_price, 2) }}</span>
        </div>
        <div class="detail-kv-row">
            <span class="detail-kv-label">Customer</span>
            <span class="detail-kv-value">
                @if ($sale->customer)
                    <a href="{{ route('customers.show', $sale->customer) }}" class="table-link">{{ $sale->customer->full_name }}</a>
                @else
                    <span class="cell-muted">Walk-in</span>
                @endif
            </span>
        </div>
        <div class="detail-kv-row">
            <span class="detail-kv-label">Staff Member</span>
            <span class="detail-kv-value">
                @if ($sale->seller)
                    <a href="{{ route('users.show', $sale->seller) }}" class="table-link">{{ $sale->seller->full_name }}</a>
                @else
                    <span class="cell-muted">System</span>
                @endif
            </span>
        </div>
    </section>
</div>
@endsection
