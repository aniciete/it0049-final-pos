@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <p class="eyebrow">Transaction Ledger</p>
            <h1 class="page-title">Sales History</h1>
            <p class="page-lead">Chronological record of completed counter sales, customer links, staff cashiers, and revenue.</p>
        </div>
        <div class="directory-header-actions">
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
            <a href="{{ route('sales.create') }}" class="button button-primary">+ Record Sale</a>
        </div>
    </header>

    @if ($sales->isEmpty())
        <div class="empty-ledger-card">
            <h2 class="empty-ledger-title">No Sales Transactions Yet</h2>
            <p class="empty-ledger-desc">Record your first counter sale to populate the transaction ledger.</p>
            <a href="{{ route('sales.create') }}" class="button button-primary">+ Record First Sale</a>
        </div>
    @else
        <div class="table-wrapper" tabindex="0" role="region" aria-label="Sales history ledger">
            <table class="data-table">
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
                            <td class="num-col mono-num" data-label="Receipt">#{{ $sale->id }}</td>
                            <td data-label="Product">
                                <div>
                                    <span class="cell-primary">{{ $sale->product?->name ?? 'Removed Product' }}</span>
                                    @if ($sale->product?->trashed())
                                        <span class="cell-secondary">Archived SKU</span>
                                    @endif
                                </div>
                            </td>
                            <td data-label="Customer">
                                @if ($sale->customer)
                                    <div>
                                        <span class="cell-primary">{{ $sale->customer->full_name }}</span>
                                        <span class="cell-secondary">{{ $sale->customer->email }}</span>
                                    </div>
                                @else
                                    <span class="status-pill status-low">Walk-in Customer</span>
                                @endif
                            </td>
                            <td data-label="Staff Member">
                                @if ($sale->seller)
                                    <div>
                                        <span class="cell-primary">{{ $sale->seller->full_name }}</span>
                                        <span class="cell-secondary mono-num">{{ $sale->seller->username }}</span>
                                    </div>
                                @else
                                    <span>System</span>
                                @endif
                            </td>
                            <td class="num-col mono-num" data-label="Qty">{{ $sale->quantity }}</td>
                            <td class="num-col mono-num" data-label="Total Price">${{ number_format((float) $sale->total_price, 2) }}</td>
                            <td class="mono-num" data-label="Transaction Date">{{ $sale->created_at?->format('M j, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
