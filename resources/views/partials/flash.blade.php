@php
    $noticeMessage = session('success') ?? session('message') ?? session('status');
    $successLink = session('success_link');
    $errorMessage = session('error');
@endphp

@if ($noticeMessage || $errorMessage)
    <div class="container flash-stack flash-toast-region">
        @if ($noticeMessage)
            <div class="alert alert-success" role="status" data-auto-dismiss="{{ is_array($successLink) ? '6500' : '5000' }}">
                <span class="alert-body">
                    <x-icon name="check-circle" :size="18" />
                    <span>{{ $noticeMessage }}</span>
                </span>
                <span class="alert-actions">
                    @if (is_array($successLink) && ! empty($successLink['url']) && ! empty($successLink['label']))
                        <a href="{{ $successLink['url'] }}" class="alert-action-link">{{ $successLink['label'] }}</a>
                    @endif
                    <button type="button" class="alert-dismiss" data-dismiss-alert aria-label="Dismiss notice">
                        <x-icon name="x" :size="14" />
                    </button>
                </span>
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
