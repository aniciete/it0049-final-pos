@php
    $successMessage = session('success') ?? session('message');
    $errorMessage = session('error');
    $statusMessage = session('status');
@endphp

@if ($successMessage || $errorMessage || $statusMessage)
    <div class="container flash-stack">
        @if ($successMessage)
            <div class="alert alert-success" role="status">
                {{ $successMessage }}
            </div>
        @endif

        @if ($statusMessage && ! $successMessage)
            <div class="alert alert-success" role="status">
                {{ $statusMessage }}
            </div>
        @endif

        @if ($errorMessage)
            <div class="alert alert-error" role="alert">
                {{ $errorMessage }}
            </div>
        @endif
    </div>
@endif
