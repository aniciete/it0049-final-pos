@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('products.index') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Products</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-lead">
                {{ $product->trashed() ? 'Archived inventory item kept for historical sales receipts.' : 'Active catalog item available for POS checkout.' }}
            </p>
        </div>
        <div class="directory-header-actions">
            @if (! $product->trashed())
                <a href="{{ route('products.edit', $product) }}" class="button button-primary">
                    <x-icon name="pencil-simple" :size="15" />
                    <span>Edit Product</span>
                </a>
            @endif
        </div>
    </header>

    <div class="workspace-split">
        <section class="details-card" aria-label="Product specifications">
            <div class="detail-identity-row">
                <img
                    src="{{ $product->imageUrl() }}"
                    alt="{{ $product->name }} product image"
                    class="detail-identity-avatar"
                    width="56"
                    height="56"
                >
                <div>
                    <h2 class="section-title">{{ $product->name }}</h2>
                    <p class="section-meta">
                        SKU <span class="mono-num">#{{ $product->id }}</span> &middot; {{ $product->trashed() ? 'Archived' : 'Active' }}
                    </p>
                </div>
            </div>

            <div class="detail-kv-row">
                <span class="detail-kv-label">Unit Price</span>
                <span class="detail-kv-value mono-num">${{ number_format((float) $product->price, 2) }}</span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Stock Quantity</span>
                <span class="detail-kv-value">
                    <x-stock-indicator :quantity="$product->stock_quantity" ok-suffix="units in stock" />
                </span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Recorded Sales</span>
                <span class="detail-kv-value mono-num">
                    <x-sales-count-link :count="$product->sales_count" :url="route('sales.index', ['q' => $product->name])" />
                </span>
            </div>
            <div class="detail-kv-row">
                <span class="detail-kv-label">Added On</span>
                <span class="detail-kv-value mono-num">{{ $product->created_at?->format('M j, Y') }}</span>
            </div>
        </section>

        <x-recent-sales-panel
            title="Recent Sales"
            :sales="$recentSales"
            empty-text="No sales recorded for this product yet."
            :show-product="false"
            :filter-url="(int) $product->sales_count > 0 ? route('sales.index', ['q' => $product->name]) : null"
        />
    </div>
</div>
@endsection
