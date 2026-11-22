@props([
    'fieldId',
    'inputName',
    'label',
    'previewId',
    'statusId',
    'defaultSrc',
    'alt',
    'hasMedia' => false,
    'helpText' => 'Optional. JPG, PNG, or WebP up to 2 MB.',
    'removeName' => null,
    'removeLabel' => 'Remove current photo on save',
    'errorId' => null,
    'isEdit' => false,
])

<div class="form-group">
    <label for="{{ $fieldId }}" class="form-label">{{ $label }}</label>

    <div class="file-picker-row">
        <img
            id="{{ $previewId }}"
            src="{{ $defaultSrc }}"
            data-default-src="{{ $defaultSrc }}"
            alt="{{ $alt }}"
            class="file-picker-thumb"
            width="64"
            height="64"
        >

        <div class="file-picker-meta">
            <div class="file-picker-actions">
                <button
                    type="button"
                    class="button button-secondary button-sm"
                    data-file-trigger="{{ $fieldId }}"
                >
                    <x-icon name="upload" :size="15" />
                    <span>Choose Photo</span>
                </button>
                <span id="{{ $statusId }}" class="form-hint media-selected-status" aria-live="polite"></span>
            </div>
            <p class="form-hint">{{ $helpText }}</p>
        </div>
    </div>

    <input
        type="file"
        id="{{ $fieldId }}"
        name="{{ $inputName }}"
        class="visually-hidden"
        accept="image/jpeg,image/png,image/webp"
        data-preview-img="{{ $previewId }}"
        data-preview-status="{{ $statusId }}"
        @if ($removeName) data-remove-checkbox="{{ $removeName }}" @endif
        aria-invalid="{{ $errors->has($inputName) ? 'true' : 'false' }}"
        @if ($errors->has($inputName) && $errorId) aria-describedby="{{ $errorId }}" @endif
    >
    @error($inputName)
        <p @if ($errorId) id="{{ $errorId }}" @endif class="form-field-error">{{ $message }}</p>
    @enderror

    @if ($isEdit && $hasMedia && $removeName)
        <label for="{{ $removeName }}" class="checkbox-inline media-remove-toggle">
            <input id="{{ $removeName }}" type="checkbox" name="{{ $removeName }}" value="1" class="checkbox-input" {{ old($removeName) ? 'checked' : '' }}>
            <span>{{ $removeLabel }}</span>
        </label>
    @endif
</div>
