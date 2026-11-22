@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <h1 class="page-title">Record Sale</h1>
            <p class="page-lead">Select an item, set quantity, and log the completed receipt.</p>
        </div>
    </header>

    @php
        $availableStockFlash = session('available_stock');
    @endphp

    <div class="register-layout">
        <div class="form-card">
            @if ($errors->count() > 1 || ($errors->any() && ! $errors->has('quantity')))
                @include('partials.error_summary')
            @endif

            <form action="{{ route('sales.store') }}" method="POST" id="pos-sale-form" novalidate>
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="sale-product" class="form-label">Product</label>
                        <select
                            id="sale-product"
                            name="product_id"
                            class="form-control"
                            required
                            aria-invalid="{{ $errors->has('product_id') ? 'true' : 'false' }}"
                            @error('product_id') aria-describedby="sale_product_error" @enderror
                        >
                            <option value="">Select a product from inventory...</option>
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
                        @error('product_id')
                            <p id="sale_product_error" class="form-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sale-customer" class="form-label">Customer</label>
                        <select
                            id="sale-customer"
                            name="customer_id"
                            class="form-control"
                            aria-invalid="{{ $errors->has('customer_id') ? 'true' : 'false' }}"
                            @error('customer_id') aria-describedby="sale_customer_error" @enderror
                        >
                            <option value="" {{ old('customer_id') === null || old('customer_id') === '' ? 'selected' : '' }}>
                                Walk-in Customer
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
                        <p class="form-hint">Optional. Leave as Walk-in Customer for unregistered counter orders.</p>
                        @error('customer_id')
                            <p id="sale_customer_error" class="form-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
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
                                aria-invalid="{{ $errors->has('quantity') ? 'true' : 'false' }}"
                                @error('quantity') aria-describedby="sale_quantity_error" @enderror
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
                        @error('quantity')
                            <p id="sale_quantity_error" class="form-field-error" role="alert">{{ $message }}</p>
                        @enderror
                        @if ($availableStockFlash !== null && (int) $availableStockFlash > 0)
                            <button
                                type="button"
                                id="qty-set-max"
                                class="inline-recovery-btn"
                                data-max-stock="{{ (int) $availableStockFlash }}"
                            >
                                Set to {{ (int) $availableStockFlash }}
                            </button>
                        @endif
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

            <p id="preview-stock-warning" class="receipt-warning" hidden>
                Requested quantity exceeds available stock. Reduce quantity before submitting.
            </p>

            <div style="margin-top: var(--space-4);">
                <button
                    type="submit"
                    form="pos-sale-form"
                    class="button button-primary button-block"
                    data-busy-text="Recording sale..."
                    {{ $products->isEmpty() ? 'disabled' : '' }}
                >
                    Complete Sale
                </button>
            </div>
        </aside>
    </div>
</div>
@endsection
