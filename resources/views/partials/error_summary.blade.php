@if ($errors->any())
    <div id="form-error-summary" class="form-error-summary" role="alert" aria-live="assertive" tabindex="-1">
        <h2 class="guardrail-heading-row">
            <x-icon name="warning-circle" :size="16" />
            <span>{{ $summaryTitle ?? ('Please resolve the following ' . ($errors->count() === 1 ? 'error' : 'errors') . ':') }}</span>
        </h2>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
