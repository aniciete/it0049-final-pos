@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <h1 class="page-title">Record Sale</h1>
            <p class="page-lead">Select one product per receipt, adjust quantity, and complete counter checkout.</p>
        </div>
    </header>

    @php
        $availableStockFlash = session('available_stock');
        $hasQtyError = $errors->has('quantity');
        $hasRecovery = $availableStockFlash !== null && (int) $availableStockFlash > 0;
    @endphp

    <div class="register-layout">
        <div class="form-card">
            @if ($errors->count() > 1 || ($errors->any() && ! $hasQtyError))
                @include('partials.error_summary')
            @endif

            <form action="{{ route('sales.store') }}" method="POST" id="pos-sale-form" novalidate>
                @csrf

                <div class="form-grid">
                    <div class="form-group" style="margin-bottom: 0;">
                        <div class="sku-picker-header">
                            <label for="sku-picker-search" class="form-label">Product</label>
                            @if ($products->isNotEmpty())
                                <div class="input-with-leading-icon sku-search-box">
                                    <x-icon name="search" :size="15" class="input-leading-icon" />
                                    <input
                                        type="search"
                                        id="sku-picker-search"
                                        class="form-control form-control-sm"
                                        placeholder="Filter products..."
                                        autocomplete="off"
                                    >
                                </div>
                            @endif
                        </div>

                        <select
                            id="sale-product"
                            name="product_id"
                            class="form-control sku-native-select"
                            aria-label="Product"
                            tabindex="-1"
                            aria-hidden="true"
                            required
                            aria-invalid="{{ $errors->has('product_id') ? 'true' : 'false' }}"
                            @error('product_id') aria-describedby="sale_product_error" @enderror
                        >
                            <option value="">Select an active product...</option>
                            @foreach ($products as $product)
                                <option
                                    value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ number_format((float) $product->price, 2, '.', '') }}"
                                    data-stock="{{ $product->stock_quantity }}"
                                    {{ (string) old('product_id') === (string) $product->id ? 'selected' : '' }}
                                >
                                    {{ $product->name }} (${{ number_format((float) $product->price, 2) }}, {{ $product->stock_quantity }} left)
                                </option>
                            @endforeach
                        </select>

                        @if ($products->isNotEmpty())
                            <div class="sku-picker-grid" id="sku-picker-grid" role="group" aria-label="In-stock catalog items">
                                @foreach ($products as $product)
                                    @php
                                        $isSelected = (string) old('product_id') === (string) $product->id;
                                    @endphp
                                    <button
                                        type="button"
                                        class="sku-tile {{ $isSelected ? 'is-selected' : '' }}"
                                        data-sku-tile
                                        data-product-id="{{ $product->id }}"
                                        data-search-text="{{ strtolower($product->name) }}"
                                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                                    >
                                        <img
                                            src="{{ $product->thumbUrl() }}"
                                            alt=""
                                            class="sku-tile-thumb"
                                            width="40"
                                            height="40"
                                            loading="eager"
                                            decoding="async"
                                        >
                                        <span class="sku-tile-body">
                                            <span class="sku-tile-name">{{ $product->name }}</span>
                                            <span class="sku-tile-meta">
                                                <span class="mono-num sku-tile-price">${{ number_format((float) $product->price, 2) }}</span>
                                                <x-stock-indicator :quantity="$product->stock_quantity" ok-suffix="left" />
                                            </span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                            <p id="sku-empty-filter" class="form-hint" hidden>No matching in-stock products found.</p>
                        @endif

                        @error('product_id')
                            <p id="sale_product_error" class="form-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="register-controls-row">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="sale-customer" class="form-label">Customer</label>
                            <select
                                id="sale-customer"
                                name="customer_id"
                                class="form-control"
                                aria-invalid="{{ $errors->has('customer_id') ? 'true' : 'false' }}"
                                @error('customer_id') aria-describedby="sale_customer_error" @enderror
                            >
                                <option value="" {{ old('customer_id') === null || old('customer_id') === '' ? 'selected' : '' }}>
                                    Walk-in
                                </option>
                                @foreach ($customers as $customer)
                                    <option
                                        value="{{ $customer->id }}"
                                        {{ (string) old('customer_id') === (string) $customer->id ? 'selected' : '' }}
                                    >
                                        {{ $customer->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-hint">Optional. Leave as Walk-in for unregistered counter orders.</p>
                            @error('customer_id')
                                <p id="sale_customer_error" class="form-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="sale-quantity" class="form-label">Quantity</label>
                            <div class="qty-stepper-wrap">
                                <button
                                    type="button"
                                    id="qty-decrement"
                                    class="qty-stepper-btn qty-stepper-btn-dec"
                                    aria-label="Decrease quantity"
                                >
                                    <x-icon name="minus" :size="15" />
                                </button>
                                <input
                                    type="number"
                                    id="sale-quantity"
                                    name="quantity"
                                    class="form-control mono-num qty-stepper-input"
                                    value="{{ old('quantity', '1') }}"
                                    min="1"
                                    step="1"
                                    inputmode="numeric"
                                    required
                                    aria-invalid="{{ $hasQtyError ? 'true' : 'false' }}"
                                    @if ($hasQtyError) aria-describedby="sale_quantity_error" @endif
                                >
                                <button
                                    type="button"
                                    id="qty-increment"
                                    class="qty-stepper-btn qty-stepper-btn-inc"
                                    aria-label="Increase quantity"
                                >
                                    <x-icon name="plus" :size="15" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="field-error-row" id="sale_quantity_feedback" @unless ($hasQtyError) hidden @endunless>
                        <p id="sale_quantity_error" class="form-field-error" role="alert">@error('quantity'){{ $message }}@enderror</p>
                        <button
                            type="button"
                            id="qty-set-max"
                            class="inline-recovery-btn"
                            data-max-stock="{{ $hasRecovery ? (int) $availableStockFlash : '' }}"
                            @unless ($hasRecovery) hidden @endunless
                        >
                            @if ($hasRecovery)
                                Set to {{ (int) $availableStockFlash }}
                            @endif
                        </button>
                    </div>
                </div>

                @if ($products->isEmpty())
                    <p class="form-meta-note">No in-stock products available. Add or restock a product before recording a sale.</p>
                @endif
            </form>
        </div>

        <aside class="receipt-preview-card" aria-label="Order Summary">
            <h2 class="receipt-heading">Order Summary</h2>
            <dl class="receipt-lines">
                <div class="receipt-line">
                    <dt>Item</dt>
                    <dd id="preview-item-summary" class="cell-muted">No item selected</dd>
                </div>
                <div class="receipt-line">
                    <dt>Unit Price</dt>
                    <dd id="preview-unit-price" class="mono-num">$0.00</dd>
                </div>
                <div class="receipt-line">
                    <dt>Stock After Sale</dt>
                    <dd id="preview-remaining-stock">Select product</dd>
                </div>
                <div class="receipt-line">
                    <dt>Cashier</dt>
                    <dd>{{ Auth::user()->full_name }}</dd>
                </div>
            </dl>

            <div class="receipt-total-row">
                <span class="receipt-total-label">Total Due</span>
                <span id="preview-order-total" class="receipt-total-amount mono-num">$0.00</span>
            </div>

            <div style="margin-top: var(--space-4);">
                <button
                    type="submit"
                    id="complete-sale-btn"
                    form="pos-sale-form"
                    class="button button-primary button-block"
                    data-busy-text="Recording sale..."
                    {{ ($products->isEmpty() || $hasQtyError) ? 'disabled' : '' }}
                >
                    Complete Sale
                </button>
            </div>
        </aside>
    </div>
</div>
@endsection
