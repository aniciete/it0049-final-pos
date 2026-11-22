@extends('layouts.app')

@section('content')
@php
    $unitPrice = (int) $sale->quantity > 0
        ? ((float) $sale->total_price / (int) $sale->quantity)
        : 0.0;
@endphp

<div class="container">
    <div class="back-link-row">
        <a href="{{ route('sales.index') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Sales History</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">Receipt <span class="mono-num">#{{ $sale->id }}</span></h1>
            <p class="page-lead">Recorded {{ $sale->created_at?->format('M j, Y \a\t g:i A') }}</p>
        </div>
        <div class="directory-header-actions">
            <button type="button" class="button button-primary" data-print-receipt>
                <x-icon name="printer" :size="15" />
                <span>Print Receipt</span>
            </button>
        </div>
    </header>

    <div class="workspace-split">
        <section class="details-card receipt-document-card" aria-label="Receipt breakdown">
            <div class="receipt-item-panel">
                <div class="receipt-item-head">
                    <div>
                        <span class="detail-attr-label">Item Sold</span>
                        <div class="receipt-item-name">
                            @if ($sale->product)
                                <a href="{{ route('products.show', $sale->product_id) }}" class="table-link">{{ $sale->product->name }}</a>
                                @if ($sale->product->trashed())
                                    <span class="status-pill status-neutral">Archived</span>
                                @endif
                            @else
                                Removed Product
                            @endif
                        </div>
                    </div>
                </div>

                <div class="receipt-item-math">
                    <span class="mono-num">{{ $sale->quantity }} &times; ${{ number_format($unitPrice, 2) }}</span>
                    <div class="receipt-item-total-wrap">
                        <span class="detail-attr-label">Total Paid</span>
                        <span class="receipt-item-total mono-num">${{ number_format((float) $sale->total_price, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="detail-attr-grid receipt-meta-grid">
                <div class="detail-attr-item" style="border-bottom: 0; padding-bottom: 0;">
                    <span class="detail-attr-label">Customer</span>
                    <span class="detail-attr-value">
                        @if ($sale->customer)
                            <a href="{{ route('customers.show', $sale->customer) }}" class="table-link">{{ $sale->customer->full_name }}</a>
                            <span class="cell-secondary">{{ $sale->customer->email }}</span>
                        @else
                            <span class="cell-muted">Walk-in</span>
                        @endif
                    </span>
                </div>
                <div class="detail-attr-item" style="border-bottom: 0; padding-bottom: 0;">
                    <span class="detail-attr-label">Cashier</span>
                    <span class="detail-attr-value">
                        @if ($sale->seller)
                            <a href="{{ route('users.show', $sale->seller) }}" class="table-link">{{ $sale->seller->full_name }}</a>
                            <span class="cell-secondary mono-num">{{ $sale->seller->username }}</span>
                        @else
                            <span class="cell-muted">System</span>
                        @endif
                    </span>
                </div>
            </div>
        </section>

        <aside class="context-panel no-print" aria-label="Receipt Actions">
            <h2 class="context-panel-title" style="margin-bottom: var(--space-3);">Quick Actions</h2>
            <ul class="context-list">
                @if ($sale->product)
                    <li class="context-list-item">
                        <a href="{{ route('products.show', $sale->product_id) }}" class="context-list-link">
                            <span class="context-list-top">
                                <span style="font-weight: 700;">View Product</span>
                                <x-icon name="caret-right" :size="14" class="context-chevron" />
                            </span>
                            <span class="context-list-bottom">
                                <span>{{ $sale->product->name }}</span>
                                @unless ($sale->product->trashed())
                                    <span class="mono-num">{{ $sale->product->stock_quantity }} in stock</span>
                                @endunless
                            </span>
                        </a>
                    </li>
                @endif

                @if ($sale->customer)
                    <li class="context-list-item">
                        <a href="{{ route('customers.show', $sale->customer) }}" class="context-list-link">
                            <span class="context-list-top">
                                <span style="font-weight: 700;">View Customer</span>
                                <x-icon name="caret-right" :size="14" class="context-chevron" />
                            </span>
                            <span class="context-list-bottom">
                                <span>{{ $sale->customer->full_name }}</span>
                            </span>
                        </a>
                    </li>
                @endif

                @if ($sale->seller)
                    <li class="context-list-item">
                        <a href="{{ route('sales.index', ['staff' => $sale->seller->id]) }}" class="context-list-link">
                            <span class="context-list-top">
                                <span style="font-weight: 700;">Cashier Sales History</span>
                                <x-icon name="caret-right" :size="14" class="context-chevron" />
                            </span>
                            <span class="context-list-bottom">
                                <span>{{ $sale->seller->full_name }}</span>
                            </span>
                        </a>
                    </li>
                @endif

                <li class="context-list-item">
                    <a href="{{ route('sales.create') }}" class="context-list-link">
                        <span class="context-list-top">
                            <span style="font-weight: 700;">Record Another Sale</span>
                            <x-icon name="caret-right" :size="14" class="context-chevron" />
                        </span>
                        <span class="context-list-bottom">
                            <span>Open counter register</span>
                        </span>
                    </a>
                </li>
            </ul>
        </aside>
    </div>
</div>
@endsection
