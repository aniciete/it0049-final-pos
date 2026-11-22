@props([
    'id',
    'name',
    'label',
    'autocomplete' => 'current-password',
    'required' => false,
    'errorId' => null,
    'errorBag' => null,
    'hint' => null,
    'toggleLabel' => 'Show password',
    'showErrors' => true,
])

@php
    $bag = $errorBag ? $errors->getBag($errorBag) : $errors;
    $hasError = $showErrors && $bag->has($name);
@endphp

<div class="form-group">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <div class="password-field-wrap">
        <input
            type="password"
            id="{{ $id }}"
            name="{{ $name }}"
            class="form-control"
            autocomplete="{{ $autocomplete }}"
            {{ $required ? 'required' : '' }}
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if ($hasError && $errorId) aria-describedby="{{ $errorId }}" @endif
        >
        <button
            type="button"
            class="password-eye-btn"
            data-password-toggle="{{ $id }}"
            aria-controls="{{ $id }}"
            aria-pressed="false"
            aria-label="{{ $toggleLabel }}"
        >
            <span data-icon-show><x-icon name="eye" :size="16" /></span>
            <span data-icon-hide hidden><x-icon name="eye-off" :size="16" /></span>
        </button>
    </div>
    @if ($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
    @if ($hasError)
        <p @if ($errorId) id="{{ $errorId }}" @endif class="form-field-error">{{ $bag->first($name) }}</p>
    @endif
</div>
