@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <p class="eyebrow">Authenticated Workspace</p>
            <h1 class="page-title">Operations Dashboard</h1>
            <p class="page-lead">
                Signed in as <strong>{{ Auth::user()->full_name }}</strong>
                (<span class="mono-num">{{ Auth::user()->username }}</span> &bull; {{ Auth::user()->role }}).
            </p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('sales.create') }}" class="button button-primary">+ Record Sale</a>
            <a href="{{ route('profile.edit') }}" class="button button-secondary">My Profile</a>
        </div>
    </header>

    <div class="kpi-ledger-grid" aria-label="Operations Metrics">
        <div class="kpi-cell">
            <span class="metric-label">Active Products</span>
            <span class="kpi-value mono-num">{{ $productCount }}</span>
            <span class="kpi-sub">{{ $lowStockCount }} low/out of stock &bull; <a href="{{ route('products.index') }}" class="table-link">Manage</a></span>
        </div>
        <div class="kpi-cell">
            <span class="metric-label">Completed Sales</span>
            <span class="kpi-value mono-num">{{ $salesCount }}</span>
            <span class="kpi-sub">${{ number_format($totalRevenue, 2) }} gross revenue &bull; <a href="{{ route('sales.index') }}" class="table-link">History</a></span>
        </div>
        <div class="kpi-cell">
            <span class="metric-label">Customers</span>
            <span class="kpi-value mono-num">{{ $customerCount }}</span>
            <span class="kpi-sub">Active accounts &bull; <a href="{{ route('customers.index') }}" class="table-link">Directory</a></span>
        </div>
        <div class="kpi-cell">
            <span class="metric-label">Staff Operators</span>
            <span class="kpi-value mono-num">{{ $userCount }}</span>
            <span class="kpi-sub">Authorized personnel &bull; <a href="{{ route('users.index') }}" class="table-link">Manage</a></span>
        </div>
    </div>

    <section style="margin-top: var(--space-6);" aria-labelledby="recent-sales-heading">
        <div class="section-header-row">
            <h2 id="recent-sales-heading" class="section-title">Recent Transactions</h2>
            <a href="{{ route('sales.index') }}" class="table-link">View Full Sales History</a>
        </div>

        @if ($recentSales->isEmpty())
            <div class="empty-ledger-card">
                <h3 class="empty-ledger-title">No Sales Recorded Yet</h3>
                <p class="empty-ledger-desc">Open the Checkout Register to process your first order.</p>
                <a href="{{ route('sales.create') }}" class="button button-primary">Open Sale Register</a>
            </div>
        @else
            <div class="table-wrapper" tabindex="0" role="region" aria-label="Recent sales transactions">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col" class="num-col">Receipt</th>
                            <th scope="col">Product</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Staff</th>
                            <th scope="col" class="num-col">Qty</th>
                            <th scope="col" class="num-col">Total</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentSales as $sale)
                            <tr>
                                <td class="num-col mono-num" data-label="Receipt">#{{ $sale->id }}</td>
                                <td data-label="Product">
                                    <span class="cell-primary">{{ $sale->product?->name ?? 'Archived Product' }}</span>
                                </td>
                                <td data-label="Customer">
                                    {{ $sale->customer?->full_name ?? 'Walk-in Customer' }}
                                </td>
                                <td data-label="Staff">{{ $sale->seller?->full_name ?? 'System' }}</td>
                                <td class="num-col mono-num" data-label="Qty">{{ $sale->quantity }}</td>
                                <td class="num-col mono-num" data-label="Total">${{ number_format((float) $sale->total_price, 2) }}</td>
                                <td class="mono-num" data-label="Date">{{ $sale->created_at?->format('M j, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
