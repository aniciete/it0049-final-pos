@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('products.index') }}" class="back-link">&larr; Back to Products</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Inventory Catalog</p>
            <h1 class="page-title">Edit Product: {{ $product->name }}</h1>
            <p class="page-lead">Update pricing, adjust stock count, or replace the product photo.</p>
        </div>
    </header>

    <div class="form-card" style="max-width: 720px;">
        <div class="form-card-header">
            <h2 class="form-section-title">Product Details</h2>
        </div>

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
</div>
@endsection
