@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-lead">
                Today: <span class="mono-num">{{ $todaySalesCount }}</span> {{ \Illuminate\Support\Str::plural('sale', $todaySalesCount) }}
                (<span class="mono-num">${{ number_format($todayRevenue, 2) }}</span>)
                @if ($lowStockCount > 0)
                    &middot; <span class="kpi-alert-text">{{ $lowStockCount }} {{ \Illuminate\Support\Str::plural('item', $lowStockCount) }} need restock</span>
                @endif
            </p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('sales.create') }}" class="button button-primary">
                <x-icon name="plus" :size="15" />
                <span>Record Sale</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="button button-secondary">My Profile</a>
        </div>
    </header>

    <div class="kpi-ledger-grid" aria-label="Operations Metrics">
        <a href="{{ route('products.index') }}" class="kpi-cell kpi-cell-link">
            <span class="metric-label">Active Products</span>
            <span class="kpi-value mono-num">{{ $productCount }}</span>
            <span class="kpi-sub {{ $lowStockCount > 0 ? 'kpi-alert-text' : '' }}">
                {{ $lowStockCount }} low or out of stock
            </span>
        </a>
        <a href="{{ route('sales.index') }}" class="kpi-cell kpi-cell-link">
            <span class="metric-label">Completed Sales</span>
            <span class="kpi-value mono-num">{{ $salesCount }}</span>
            <span class="kpi-sub">${{ number_format($totalRevenue, 2) }} total revenue</span>
        </a>
        <a href="{{ route('customers.index') }}" class="kpi-cell kpi-cell-link">
            <span class="metric-label">Customers</span>
            <span class="kpi-value mono-num">{{ $customerCount }}</span>
            <span class="kpi-sub">Registered customer directory</span>
        </a>
        <a href="{{ route('users.index') }}" class="kpi-cell kpi-cell-link">
            <span class="metric-label">Staff</span>
            <span class="kpi-value mono-num">{{ $userCount }}</span>
            <span class="kpi-sub">Authorized store accounts</span>
        </a>
    </div>

    <section class="dashboard-recent-section" aria-labelledby="recent-sales-heading">
        <div class="section-header-row">
            <h2 id="recent-sales-heading" class="section-title">Recent Transactions</h2>
            <a href="{{ route('sales.index') }}" class="table-link">Sales History</a>
        </div>

        @if ($recentSales->isEmpty())
            <div class="empty-ledger-card">
                <div class="empty-ledger-icon"><x-icon name="receipt" :size="32" /></div>
                <h3 class="empty-ledger-title">No Sales Recorded Yet</h3>
                <p class="empty-ledger-desc">Open Record Sale to process your first order.</p>
                <a href="{{ route('sales.create') }}" class="button button-primary">Record Sale</a>
            </div>
        @else
            <div class="table-wrapper" tabindex="0" role="region" aria-label="Recent sales transactions">
                <table class="data-table ledger-mobile-compact">
                    <thead>
                        <tr>
                            <th scope="col" class="num-col">Receipt</th>
                            <th scope="col">Product</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Staff Member</th>
                            <th scope="col" class="num-col">Qty</th>
                            <th scope="col" class="num-col">Total</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentSales as $sale)
                            <tr>
                                <td class="num-col mono-num" data-label="Receipt">
                                    <a href="{{ route('sales.show', $sale) }}" class="table-link ledger-receipt-link">#{{ $sale->id }}</a>
                                </td>
                                <td data-label="Product">
                                    @if ($sale->product)
                                        <a href="{{ route('products.show', $sale->product_id) }}" class="cell-product-title table-link">{{ $sale->product->name }}</a>
                                    @else
                                        <span class="cell-product-title">Removed Product</span>
                                    @endif
                                </td>
                                <td data-label="Customer">
                                    @if ($sale->customer)
                                        <a href="{{ route('customers.show', $sale->customer) }}" class="cell-primary table-link">{{ $sale->customer->full_name }}</a>
                                    @else
                                        <span class="cell-muted">Walk-in</span>
                                    @endif
                                </td>
                                <td data-label="Staff Member">
                                    @if ($sale->seller)
                                        <a href="{{ route('users.show', $sale->seller) }}" class="cell-primary table-link">{{ $sale->seller->full_name }}</a>
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
    </section>
</div>
@endsection
