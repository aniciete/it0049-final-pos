@extends('layouts.app')

@section('content')
@php
    $hasFilters = ($filters['q'] ?? '') !== ''
        || ($filters['staff'] ?? null) !== null
        || ($filters['customer'] ?? null) !== null
        || ($filters['from'] ?? '') !== ''
        || ($filters['to'] ?? '') !== '';
@endphp

<div class="container">
    <header class="directory-header">
        <div>
            <h1 class="page-title">Sales History</h1>
            <p class="page-lead">Chronological record of completed counter sales, customer accounts, and cashiers.</p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('sales.create') }}" class="button button-primary">
                <x-icon name="plus" :size="15" />
                <span>Record Sale</span>
            </a>
        </div>
    </header>

    <div class="ledger-filter-bar">
        <div class="metric-summary-strip" aria-label="Sales Summary">
            <div class="metric-summary-item">
                <span class="metric-label">Transactions</span>
                <span class="metric-value mono-num">{{ $summary['transaction_count'] }}</span>
            </div>
            <div class="metric-summary-item">
                <span class="metric-label">Units Sold</span>
                <span class="metric-value mono-num">{{ $summary['total_units'] }}</span>
            </div>
            <div class="metric-summary-item">
                <span class="metric-label">Gross Revenue</span>
                <span class="metric-value mono-num">${{ number_format($summary['total_revenue'], 2) }}</span>
            </div>
        </div>

        <form method="GET" action="{{ route('sales.index') }}" class="ledger-search-form" role="search" aria-label="Filter sales history">
            @if ($filters['staff'])
                <input type="hidden" name="staff" value="{{ $filters['staff'] }}">
            @endif
            @if ($filters['customer'])
                <input type="hidden" name="customer" value="{{ $filters['customer'] }}">
            @endif

            <label for="sales-search" class="visually-hidden">Search sales by receipt, product, customer, or cashier</label>
            <input
                type="search"
                id="sales-search"
                name="q"
                value="{{ $filters['q'] }}"
                placeholder="Search receipt, item, customer..."
                class="form-control ledger-search-input"
            >

            <label for="sales-from" class="visually-hidden">From date</label>
            <input
                type="date"
                id="sales-from"
                name="from"
                value="{{ $filters['from'] }}"
                class="form-control ledger-date-input"
                title="From date"
            >

            <label for="sales-to" class="visually-hidden">To date</label>
            <input
                type="date"
                id="sales-to"
                name="to"
                value="{{ $filters['to'] }}"
                class="form-control ledger-date-input"
                title="To date"
            >

            <button type="submit" class="button button-secondary button-sm">
                <x-icon name="search" :size="14" />
                <span>Filter</span>
            </button>

            @if ($hasFilters)
                <a href="{{ route('sales.index') }}" class="button button-ghost-action button-sm">Clear</a>
            @endif
        </form>
    </div>

    @if ($staffFilterUser || $customerFilterRecord)
        <div class="filter-bar" style="margin-top: calc(var(--space-2) * -1);">
            <div style="display: flex; flex-wrap: wrap; gap: var(--space-2); align-items: center;">
                @if ($staffFilterUser)
                    <span class="status-pill status-neutral">
                        Cashier: {{ $staffFilterUser->full_name }}
                    </span>
                @endif
                @if ($customerFilterRecord)
                    <span class="status-pill status-neutral">
                        Customer: {{ $customerFilterRecord->full_name }}
                    </span>
                @endif
                <a href="{{ route('sales.index') }}" class="table-link" style="font-size: var(--text-xs);">Reset filter</a>
            </div>
        </div>
    @endif

    @if ($sales->isEmpty())
        <div class="empty-ledger-card">
            <div class="empty-ledger-icon"><x-icon name="receipt" :size="32" /></div>
            <h2 class="empty-ledger-title">{{ $hasFilters ? 'No Matching Sales Found' : 'No Sales Transactions Yet' }}</h2>
            <p class="empty-ledger-desc">
                {{ $hasFilters
                    ? 'Try clearing or widening your search query and date filters.'
                    : 'Record your first counter sale to populate the transaction ledger.' }}
            </p>
            @if ($hasFilters)
                <a href="{{ route('sales.index') }}" class="button button-secondary">Clear Filters</a>
            @else
                <a href="{{ route('sales.create') }}" class="button button-primary">
                    <x-icon name="plus" :size="15" />
                    <span>Record Sale</span>
                </a>
            @endif
        </div>
    @else
        <div class="table-wrapper" tabindex="0" role="region" aria-label="Sales history ledger">
            <table class="data-table ledger-mobile-compact">
                <caption class="visually-hidden">Completed Sales History</caption>
                <thead>
                    <tr>
                        <th scope="col" class="num-col">Receipt</th>
                        <th scope="col">Product</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Staff Member</th>
                        <th scope="col" class="num-col">Qty</th>
                        <th scope="col" class="num-col">Total Price</th>
                        <th scope="col">Transaction Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td class="num-col mono-num" data-label="Receipt">
                                <a href="{{ route('sales.show', $sale) }}" class="table-link">#{{ $sale->id }}</a>
                            </td>
                            <td data-label="Product">
                                <div>
                                    @if ($sale->product)
                                        <a href="{{ route('products.show', $sale->product_id) }}" class="cell-product-title table-link">{{ $sale->product->name }}</a>
                                    @else
                                        <span class="cell-product-title">Removed Product</span>
                                    @endif
                                    @if ($sale->product?->trashed())
                                        <span class="cell-secondary">Archived SKU</span>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Customer">
                                @if ($sale->customer)
                                    <div>
                                        <a href="{{ route('customers.show', $sale->customer) }}" class="cell-primary table-link">{{ $sale->customer->full_name }}</a>
                                        <span class="cell-secondary">{{ $sale->customer->email }}</span>
                                    </div>
                                @else
                                    <span class="cell-muted">Walk-in</span>
                                @endif
                            </td>
                            <td data-label="Staff Member">
                                @if ($sale->seller)
                                    <div>
                                        <a href="{{ route('users.show', $sale->seller) }}" class="cell-primary table-link">{{ $sale->seller->full_name }}</a>
                                        <span class="cell-secondary mono-num">{{ $sale->seller->username }}</span>
                                    </div>
                                @else
                                    <span class="cell-muted">System</span>
                                @endif
                            </td>
                            <td class="num-col mono-num" data-label="Qty">{{ $sale->quantity }}</td>
                            <td class="num-col mono-num" data-label="Total Price">${{ number_format((float) $sale->total_price, 2) }}</td>
                            <td class="mono-num" data-label="Transaction Date">{{ $sale->created_at?->format('M j, Y g:i A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
