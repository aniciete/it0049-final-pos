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

    <div class="form-group">
        <label for="product-image" class="form-label">
            Product Image <span class="form-label-optional">(Optional — JPG, PNG, WebP up to 2 MB)</span>
        </label>

        <div class="media-preview-row">
            <img
                id="product-image-preview"
                src="{{ $product->imageUrl() }}"
                data-default-src="{{ $product->imageUrl() }}"
                alt="{{ $product->name ?: 'Product' }} preview"
                class="media-preview-thumb"
                width="64"
                height="64"
            >
            <div>
                <p id="product-image-status" class="form-hint" style="font-weight: 700; color: var(--basil);">
                    {{ $product->hasImage() ? 'Current photo stored on public disk' : 'No custom image uploaded (using placeholder)' }}
                </p>
                <p class="form-hint">Images are resized cleanly to 640&times;640 on save.</p>
            </div>
        </div>

        <input
            type="file"
            id="product-image"
            name="image"
            class="form-control"
            accept="image/jpeg,image/png,image/webp"
            data-preview-img="product-image-preview"
            data-preview-status="product-image-status"
            data-remove-checkbox="remove_image"
            aria-invalid="{{ $errors->has('image') ? 'true' : 'false' }}"
            @error('image') aria-describedby="product_image_error" @enderror
        >
        @error('image')
            <p id="product_image_error" class="form-field-error">{{ $message }}</p>
        @enderror

        @if ($isEdit && $product->hasImage())
            <label for="remove_image" class="checkbox-inline" style="margin-top: var(--space-1);">
                <input id="remove_image" type="checkbox" name="remove_image" value="1" class="checkbox-input" {{ old('remove_image') ? 'checked' : '' }}>
                <span>Remove existing product image on save</span>
            </label>
        @endif
    </div>
</div>
