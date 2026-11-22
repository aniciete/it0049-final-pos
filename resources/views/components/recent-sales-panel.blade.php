@props([
    'title' => 'Recent Sales',
    'subtitle' => null,
    'sales',
    'emptyText' => 'No sales recorded yet.',
    'filterUrl' => null,
    'showProduct' => true,
    'showCustomer' => true,
    'showSeller' => true,
])

<aside class="context-panel" aria-label="{{ $title }}">
    <div class="section-header-row" style="margin-bottom: var(--space-1);">
        <h2 class="context-panel-title">{{ $title }}</h2>
        @if ($filterUrl)
            <a href="{{ $filterUrl }}" class="table-link" style="font-size: var(--text-xs);">View in Sales History</a>
        @endif
    </div>

    @if ($subtitle)
        <p class="context-panel-sub">{{ $subtitle }}</p>
    @endif

    @if ($sales->isEmpty())
        <p class="section-meta">{{ $emptyText }}</p>
    @else
        <ul class="context-list">
            @foreach ($sales as $sale)
                <li class="context-list-item">
                    <div style="min-width: 0;">
                        <a href="{{ route('sales.show', $sale) }}" class="table-link mono-num">#{{ $sale->id }}</a>
                        @if ($showProduct)
                            <span style="font-weight: 500;">{{ $sale->product?->name ?? 'Archived item' }}</span>
                        @endif
                        <span class="cell-secondary">
                            Qty {{ $sale->quantity }}
                            @if ($showCustomer)
                                &middot; {{ $sale->customer?->full_name ?? 'Walk-in' }}
                            @endif
                            @if ($showSeller && $sale->seller)
                                &middot; {{ $sale->seller->full_name }}
                            @endif
                            &middot; {{ $sale->created_at?->format('M j, Y') }}
                        </span>
                    </div>
                    <span class="mono-num" style="font-weight: 700; flex-shrink: 0;">${{ number_format((float) $sale->total_price, 2) }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</aside>
