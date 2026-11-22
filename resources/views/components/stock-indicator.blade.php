@props([
    'quantity',
    'okSuffix' => 'in stock',
])

@php
    $stockQty = (int) $quantity;
@endphp

@if ($stockQty <= 0)
    <span class="status-pill status-out">Out of stock</span>
@elseif ($stockQty <= \App\Models\Product::LOW_STOCK_THRESHOLD)
    <span class="status-pill status-low"><span class="mono-num">{{ $stockQty }}</span>&nbsp;low stock</span>
@else
    <span class="stock-readout"><span class="mono-num">{{ $stockQty }}</span> <span class="cell-muted">{{ $okSuffix }}</span></span>
@endif
