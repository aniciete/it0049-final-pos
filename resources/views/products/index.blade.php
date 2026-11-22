@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <h1 class="page-title">Product Catalog</h1>
            <p class="page-lead">
                {{ $viewArchived
                    ? 'Archived products remain linked to historical receipts and can be restored anytime.'
                    : 'Manage active retail products, unit prices, inventory counts, and catalog photos.' }}
            </p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('products.create') }}" class="button button-primary">
                <x-icon name="plus" :size="15" />
                <span>Add Product</span>
            </a>
        </div>
    </header>

    <div class="filter-bar" role="navigation" aria-label="Product Catalog Filter">
        <div class="filter-tabs">
            <a href="{{ route('products.index') }}" class="filter-tab {{ ! $viewArchived ? 'active' : '' }}" {!! ! $viewArchived ? 'aria-current="page"' : '' !!}>
                Active Catalog <span class="mono-num">({{ $activeCount }})</span>
            </a>
            <a href="{{ route('products.index', ['view' => 'archived']) }}" class="filter-tab {{ $viewArchived ? 'active' : '' }}" {!! $viewArchived ? 'aria-current="page"' : '' !!}>
                Archived <span class="mono-num">({{ $archivedCount }})</span>
            </a>
        </div>
        <p class="section-meta">
            Showing <span class="mono-num">{{ $products->count() }}</span> {{ $viewArchived ? 'archived' : 'active' }} {{ \Illuminate\Support\Str::plural('item', $products->count()) }}
        </p>
    </div>

    @if ($products->isEmpty())
        <div class="empty-ledger-card">
            <div class="empty-ledger-icon"><x-icon name="package" :size="32" /></div>
            <h2 class="empty-ledger-title">{{ $viewArchived ? 'No Archived Products' : 'No Active Products' }}</h2>
            <p class="empty-ledger-desc">
                {{ $viewArchived
                    ? 'Products archived from the active catalog appear here with their historical sales intact.'
                    : 'Add your first retail product to enable inventory tracking and checkout sales.' }}
            </p>
            @if ($viewArchived)
                <a href="{{ route('products.index') }}" class="button button-secondary">Return to Active Catalog</a>
            @else
                <a href="{{ route('products.create') }}" class="button button-primary">
                    <x-icon name="plus" :size="15" />
                    <span>Add Product</span>
                </a>
            @endif
        </div>
    @else
        <div class="table-wrapper" tabindex="0" role="region" aria-label="Product catalog directory">
            <table class="data-table">
                <caption class="visually-hidden">{{ $viewArchived ? 'Archived Inventory Catalog' : 'Active Inventory Catalog' }}</caption>
                <thead>
                    <tr>
                        <th scope="col" class="num-col">ID</th>
                        <th scope="col">Product</th>
                        <th scope="col" class="num-col">Unit Price</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Added</th>
                        <th scope="col" class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        @php
                            $stockQty = (int) $product->stock_quantity;
                            $salesCount = (int) $product->sales_count;
                        @endphp
                        <tr>
                            <td class="num-col" data-label="ID">{{ $product->id }}</td>
                            <td data-label="Product">
                                <div class="cell-product-lockup">
                                    <img
                                        src="{{ $product->imageUrl() }}"
                                        alt="{{ $product->name }} product image"
                                        class="table-thumb"
                                        width="48"
                                        height="48"
                                        loading="lazy"
                                    >
                                    <div>
                                        <a href="{{ route('products.show', $product->id) }}" class="cell-primary table-link">{{ $product->name }}</a>
                                        @if ($salesCount > 0)
                                            <span class="cell-secondary">{{ $salesCount }} recorded {{ \Illuminate\Support\Str::plural('sale', $salesCount) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="num-col" data-label="Unit Price">
                                ${{ number_format((float) $product->price, 2) }}
                            </td>
                            <td data-label="Stock">
                                <x-stock-indicator :quantity="$product->stock_quantity" />
                            </td>
                            <td class="mono-num" data-label="Added">
                                {{ $product->created_at?->format('M j, Y') }}
                            </td>
                            <td class="col-actions" data-label="Actions">
                                <div class="table-actions-group">
                                    @if (! $viewArchived)
                                        <a href="{{ route('products.edit', $product) }}" class="button button-secondary button-sm">
                                            <x-icon name="pencil-simple" :size="14" />
                                            <span>Edit</span>
                                        </a>
                                        <form
                                            action="{{ route('products.destroy', $product->id) }}"
                                            method="POST"
                                            class="inline-form"
                                            data-confirm-title="Archive Product"
                                            data-confirm-message="Archive {{ $product->name }}? It will be hidden from the register while keeping sales history intact."
                                            data-confirm-action="Archive"
                                            data-confirm-tone="primary"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-ghost-action button-sm">
                                                <x-icon name="archive" :size="14" />
                                                <span>Archive</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('products.restore', $product->id) }}" method="POST" class="inline-form">
                                            @csrf
                                            <button type="submit" class="button button-primary button-sm">
                                                <x-icon name="arrow-counter-clockwise" :size="14" />
                                                <span>Restore</span>
                                            </button>
                                        </form>
                                        @if ($salesCount === 0)
                                            <form
                                                action="{{ route('products.destroy', [$product->id, 'purge' => 1]) }}"
                                                method="POST"
                                                class="inline-form"
                                                data-confirm-title="Delete Product Permanently"
                                                data-confirm-message="Permanently delete {{ $product->name }}? This action cannot be undone."
                                                data-confirm-action="Delete Forever"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button button-ghost-danger button-sm">
                                                    <x-icon name="trash" :size="14" />
                                                    <span>Purge</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="table-action-spacer" aria-hidden="true"></span>
                                        @endif
                                    @endif
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
