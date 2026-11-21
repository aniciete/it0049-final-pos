@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('products.index') }}" class="back-link">&larr; Back to Products</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Inventory Catalog</p>
            <h1 class="page-title">Add Product</h1>
            <p class="page-lead">Create a new retail SKU with pricing, opening stock, and an optional photo.</p>
        </div>
    </header>

    <div class="form-card" style="max-width: 720px;">
        <div class="form-card-header">
            <h2 class="form-section-title">Product Details</h2>
        </div>

        @include('partials.error_summary')

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @include('products._form')

            <div class="form-actions">
                <a href="{{ route('products.index') }}" class="button button-secondary">Cancel</a>
                <button type="submit" class="button button-primary" data-busy-text="Saving product...">Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
