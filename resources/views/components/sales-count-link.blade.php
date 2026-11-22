@props([
    'count',
    'url',
])

@if ((int) $count > 0)
    <a href="{{ $url }}" class="table-link">{{ $count }}</a>
@else
    0
@endif
