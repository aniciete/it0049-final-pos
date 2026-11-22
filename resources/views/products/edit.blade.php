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
            <h1 class="page-title">Edit Product: {{ $product->name }}</h1>
            <p class="page-lead">Update pricing, adjust stock count, or replace the product photo.</p>
        </div>
    </header>

    <div class="workspace-split">
        <div class="form-card">
            @include('partials.error_summary')

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('products._form')

                <div class="form-actions">
                    <a href="{{ route('products.index') }}" class="button button-secondary">Cancel</a>
                    <button type="submit" class="button button-primary" data-busy-text="Updating product...">Update Product</button>
                </div>
            </form>
        </div>

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
