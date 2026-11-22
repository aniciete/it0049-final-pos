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
            <h1 class="page-title">Add Product</h1>
            <p class="page-lead">Create a new retail SKU with pricing, opening stock, and an optional photo.</p>
        </div>
    </header>

    <div class="workspace-split">
        <div class="form-card">
            @include('partials.error_summary')

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @include('products._form')

                <div class="form-actions">
                    <a href="{{ route('products.index') }}" class="button button-secondary">Cancel</a>
                    <button type="submit" class="button button-primary" data-busy-text="Adding product...">Add Product</button>
                </div>
            </form>
        </div>

        <aside class="context-panel" aria-label="Catalog Preview">
            <h2 class="context-panel-title">Catalog Preview</h2>

            <div class="product-preview-card">
                <img
                    id="product-preview-card-img"
                    src="{{ $product->imageUrl() }}"
                    data-default-src="{{ $product->imageUrl() }}"
                    alt="Catalog entry preview"
                    class="table-thumb"
                    width="48"
                    height="48"
                >
                <div class="product-preview-meta">
                    <span id="product-preview-card-name" class="cell-primary">{{ old('name') ?: 'New Product' }}</span>
                    <div class="product-preview-stats">
                        <span id="product-preview-card-price" class="mono-num">
                            ${{ number_format((float) (old('price') ?: 0), 2) }}
                        </span>
                        <span id="product-preview-card-stock" class="status-pill status-out">
                            Out of stock
                        </span>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection
