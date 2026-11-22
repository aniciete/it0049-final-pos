@php
    $noticeMessage = session('success') ?? session('message') ?? session('status');
    $errorMessage = session('error');
@endphp

@if ($noticeMessage || $errorMessage)
    <div class="container flash-stack">
        @if ($noticeMessage)
            <div class="alert alert-success" role="status" data-auto-dismiss="5000">
                <span class="alert-body">
                    <x-icon name="check-circle" :size="18" />
                    <span>{{ $noticeMessage }}</span>
                </span>
                <button type="button" class="alert-dismiss" data-dismiss-alert aria-label="Dismiss notice">
                    <x-icon name="x" :size="14" />
                </button>
            </div>
        @endif

        @if ($errorMessage)
            <div class="alert alert-error" role="alert">
                <span class="alert-body">
                    <x-icon name="warning-circle" :size="18" />
                    <span>{{ $errorMessage }}</span>
                </span>
                <button type="button" class="alert-dismiss" data-dismiss-alert aria-label="Dismiss alert">
                    <x-icon name="x" :size="14" />
                </button>
            </div>
        @endif
    </div>
@endif
