@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('products.index') }}" class="back-link">&larr; Back to Products</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Product Record #{{ $product->id }}</p>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-lead">
                {{ $product->trashed() ? 'Archived inventory record (preserved for sales history).' : 'Active catalog item available for POS checkout.' }}
            </p>
        </div>
        <div class="directory-header-actions">
            @if (! $product->trashed())
                <a href="{{ route('products.edit', $product) }}" class="button button-primary">Edit Product</a>
            @endif
        </div>
    </header>

    <div class="details-card" style="max-width: 680px;">
        <div class="media-preview-row" style="margin-bottom: var(--space-5);">
            <img
                src="{{ $product->imageUrl() }}"
                alt="{{ $product->name }} product image"
                class="media-preview-thumb"
                width="64"
                height="64"
            >
            <div>
                <h2 class="section-title">{{ $product->name }}</h2>
                <p class="section-meta">{{ $product->trashed() ? 'Archived SKU' : 'Active SKU' }}</p>
            </div>
        </div>

        <ul class="details-list">
            <li>
                <strong>Unit Price</strong>
                <span class="mono-num">${{ number_format((float) $product->price, 2) }}</span>
            </li>
            <li>
                <strong>Stock Quantity</strong>
                <span class="mono-num">{{ $product->stock_quantity }}</span>
            </li>
            <li>
                <strong>Recorded Sales</strong>
                <span class="mono-num">{{ $product->sales_count }}</span>
            </li>
            <li>
                <strong>Created</strong>
                <span class="mono-num">{{ $product->created_at?->format('M j, Y H:i') }}</span>
            </li>
        </ul>
    </div>
</div>
@endsection
