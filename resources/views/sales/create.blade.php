@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <p class="eyebrow">Checkout Register</p>
            <h1 class="page-title">Record Sale</h1>
            <p class="page-lead">Process an order, deduct item stock atomically, and log the receipt in Sales History.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('sales.index') }}" class="button button-secondary">View Sales History</a>
        </div>
    </header>

    @include('partials.error_summary', ['summaryTitle' => 'Unable to complete sale:'])

    <div class="register-layout">
        <div class="form-card">
            <div class="form-card-header">
                <h2 class="form-section-title">Sale Transaction Details</h2>
            </div>

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
                                    {{ $product->name }} (${{ number_format((float) $product->price, 2) }}, {{ $product->stock_quantity }} in stock)
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p id="sale_product_error" class="form-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-row-two">
                        <div class="form-group">
                            <label for="sale-customer" class="form-label">
                                Customer <span class="form-label-optional">(Optional)</span>
                            </label>
                            <select
                                id="sale-customer"
                                name="customer_id"
                                class="form-control"
                                aria-invalid="{{ $errors->has('customer_id') ? 'true' : 'false' }}"
                                @error('customer_id') aria-describedby="sale_customer_error" @enderror
                            >
                                <option value="" {{ old('customer_id') === null || old('customer_id') === '' ? 'selected' : '' }}>
                                    Walk-in Customer (No Account)
                                </option>
                                @foreach ($customers as $customer)
                                    <option
                                        value="{{ $customer->id }}"
                                        {{ (string) old('customer_id') === (string) $customer->id ? 'selected' : '' }}
                                    >
                                        {{ $customer->full_name }} ({{ $customer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p id="sale_customer_error" class="form-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sale-quantity" class="form-label">Quantity</label>
                            <input
                                type="number"
                                id="sale-quantity"
                                name="quantity"
                                class="form-control mono-num"
                                value="{{ old('quantity', '1') }}"
                                min="1"
                                step="1"
                                inputmode="numeric"
                                required
                                aria-invalid="{{ $errors->has('quantity') ? 'true' : 'false' }}"
                                @error('quantity') aria-describedby="sale_quantity_error" @enderror
                            >
                            @error('quantity')
                                <p id="sale_quantity_error" class="form-field-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                @if ($products->isEmpty())
                    <p class="form-meta-note">No in-stock products available. Add or restock a product before recording a sale.</p>
                @endif

                <div class="form-actions">
                    <button
                        type="submit"
                        class="button button-primary button-block"
                        data-busy-text="Recording sale..."
                        {{ $products->isEmpty() ? 'disabled' : '' }}
                    >
                        Complete Sale &amp; Deduct Stock
                    </button>
                </div>
            </form>
        </div>

        <aside class="receipt-preview-card" aria-label="Live Sale Receipt Preview">
            <h2 class="receipt-heading">Order Summary</h2>
            <dl class="receipt-lines">
                <div class="receipt-line">
                    <dt>Selected Product</dt>
                    <dd id="preview-product-name">None selected</dd>
                </div>
                <div class="receipt-line">
                    <dt>Unit Price</dt>
                    <dd id="preview-unit-price" class="mono-num">$0.00</dd>
                </div>
                <div class="receipt-line">
                    <dt>Available Stock</dt>
                    <dd id="preview-available-stock">Select product</dd>
                </div>
                <div class="receipt-line">
                    <dt>Stock After Sale</dt>
                    <dd id="preview-remaining-stock">Select product</dd>
                </div>
                <div class="receipt-line">
                    <dt>Staff Cashier</dt>
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
        </aside>
    </div>
</div>
@endsection
