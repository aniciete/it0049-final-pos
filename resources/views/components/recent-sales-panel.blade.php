@props([
    'title' => 'Recent Sales',
    'sales',
    'emptyText' => 'No sales recorded yet.',
    'filterUrl' => null,
    'showProduct' => true,
    'showCustomer' => true,
    'showSeller' => true,
])

<aside class="context-panel" aria-label="{{ $title }}">
    <div class="section-header-row" style="margin-bottom: var(--space-3);">
        <h2 class="context-panel-title">{{ $title }}</h2>
        @if ($filterUrl)
            <a href="{{ $filterUrl }}" class="table-link context-panel-action">View in Sales History</a>
        @endif
    </div>

    @if ($sales->isEmpty())
        <p class="section-meta">{{ $emptyText }}</p>
    @else
        <ul class="context-list">
            @foreach ($sales as $sale)
                <li class="context-list-item">
                    <a href="{{ route('sales.show', $sale) }}" class="context-list-link">
                        <span class="context-list-top">
                            <span class="context-list-top-main">
                                <span class="mono-num context-receipt-id">#{{ $sale->id }}</span>
                                @if ($showProduct)
                                    <span>{{ $sale->quantity }}× {{ $sale->product?->name ?? 'Archived item' }}</span>
                                @else
                                    <span>{{ $sale->quantity }} {{ \Illuminate\Support\Str::plural('unit', $sale->quantity) }}</span>
                                @endif
                            </span>
                            <span class="context-list-top-meta">
                                <span class="mono-num context-amount">${{ number_format((float) $sale->total_price, 2) }}</span>
                                <x-icon name="caret-right" :size="14" class="context-chevron" />
                            </span>
                        </span>
                        <span class="context-list-bottom">
                            <span>
                                @if (! $showCustomer)
                                    by {{ $sale->seller?->full_name ?? 'System' }}
                                @elseif (! $showSeller)
                                    {{ $sale->customer?->full_name ?? 'Walk-in' }}
                                @else
                                    {{ $sale->customer?->full_name ?? 'Walk-in' }}, by {{ $sale->seller?->full_name ?? 'System' }}
                                @endif
                            </span>
                            <span class="mono-num">{{ $sale->created_at?->format('M j, Y') }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</aside>
