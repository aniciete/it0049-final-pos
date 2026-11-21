@if ($errors->any())
    <div id="form-error-summary" class="form-error-summary" role="alert" aria-live="assertive" tabindex="-1">
        <h2>{{ $summaryTitle ?? ('Please resolve the following ' . ($errors->count() === 1 ? 'error' : 'errors') . ':') }}</h2>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
