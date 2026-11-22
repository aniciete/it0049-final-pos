@php
    $isEdit = $product->exists;
@endphp

<div class="form-grid">
    <div class="form-group">
        <label for="product-name" class="form-label">Product Name</label>
        <input
            type="text"
            id="product-name"
            name="name"
            class="form-control"
            value="{{ old('name', $product->name) }}"
            maxlength="100"
            required
            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
            @error('name') aria-describedby="product_name_error" @enderror
        >
        @error('name')
            <p id="product_name_error" class="form-field-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-row-two">
        <div class="form-group">
            <label for="product-price" class="form-label">Unit Price (USD)</label>
            <div class="input-with-prefix">
                <span class="input-prefix" aria-hidden="true">$</span>
                <input
                    type="number"
                    id="product-price"
                    name="price"
                    class="form-control mono-num"
                    value="{{ old('price', $isEdit ? number_format((float) $product->price, 2, '.', '') : '') }}"
                    step="0.01"
                    min="0.01"
                    required
                    aria-invalid="{{ $errors->has('price') ? 'true' : 'false' }}"
                    @error('price') aria-describedby="product_price_error" @enderror
                >
            </div>
            @error('price')
                <p id="product_price_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="product-stock" class="form-label">Stock Quantity</label>
            <input
                type="number"
                id="product-stock"
                name="stock_quantity"
                class="form-control mono-num"
                value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}"
                min="0"
                step="1"
                required
                aria-invalid="{{ $errors->has('stock_quantity') ? 'true' : 'false' }}"
                @error('stock_quantity') aria-describedby="product_stock_error" @enderror
            >
            @error('stock_quantity')
                <p id="product_stock_error" class="form-field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <x-media-picker
        field-id="product-image"
        input-name="image"
        label="Product Photo"
        preview-id="product-image-preview"
        status-id="product-image-status"
        :default-src="$product->imageUrl()"
        :alt="($product->name ?: 'Product').' preview'"
        :has-media="$product->hasImage()"
        remove-name="remove_image"
        error-id="product_image_error"
        :is-edit="$isEdit"
    />
</div>
