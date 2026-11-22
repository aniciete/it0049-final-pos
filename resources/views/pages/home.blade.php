@extends('layouts.app')

@section('content')
<div class="container">
    <section class="home-hero">
        <div>
            <h1 class="hero-title">Bright-Market POS</h1>
            <p class="page-lead">
                Track inventory, manage customer and staff accounts, and record counter sales without overselling.
            </p>
            <div class="home-actions">
                @auth
                    <a href="{{ route('sales.create') }}" class="button button-primary">
                        <x-icon name="plus" :size="15" />
                        <span>Record Sale</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="button button-secondary">Products</a>
                    <a href="{{ route('sales.index') }}" class="button button-secondary">Sales History</a>
                @else
                    <a href="{{ route('login') }}" class="button button-primary">
                        <x-icon name="sign-in" :size="15" />
                        <span>Sign In</span>
                    </a>
                    <a href="{{ route('about') }}" class="button button-secondary">System Overview</a>
                @endauth
            </div>
        </div>

        @auth
            <div class="kpi-ledger-grid" aria-label="Store Operational Snapshot">
                <a href="{{ route('products.index') }}" class="kpi-cell kpi-cell-link">
                    <span class="metric-label">Active Products</span>
                    <span class="kpi-value mono-num">{{ $productCount }}</span>
                    <span class="kpi-sub {{ $lowStockCount > 0 ? 'kpi-alert-text' : '' }}">{{ $lowStockCount }} low or out of stock</span>
                </a>
                <a href="{{ route('sales.index') }}" class="kpi-cell kpi-cell-link">
                    <span class="metric-label">Sales Recorded</span>
                    <span class="kpi-value mono-num">{{ $salesCount }}</span>
                    <span class="kpi-sub">${{ number_format($totalRevenue, 2) }} gross total</span>
                </a>
                <a href="{{ route('customers.index') }}" class="kpi-cell kpi-cell-link">
                    <span class="metric-label">Customers</span>
                    <span class="kpi-value mono-num">{{ $customerCount }}</span>
                    <span class="kpi-sub">Registered accounts</span>
                </a>
                <a href="{{ route('users.index') }}" class="kpi-cell kpi-cell-link">
                    <span class="metric-label">Staff</span>
                    <span class="kpi-value mono-num">{{ $userCount }}</span>
                    <span class="kpi-sub">Authorized operators</span>
                </a>
            </div>
        @else
            <div class="context-panel" aria-label="Counter Terminal Overview">
                <h2 class="context-panel-title">Counter Terminal Ready</h2>
                <p class="context-panel-sub">Sign in with your staff account to open the register and store ledgers.</p>
                <ul class="context-list">
                    <li class="context-list-item">
                        <span>Stock Protection</span>
                        <span class="mono-num">Oversell guard active</span>
                    </li>
                    <li class="context-list-item">
                        <span>Receipt Ledger</span>
                        <span class="mono-num">Walk-in &amp; linked sales</span>
                    </li>
                    <li class="context-list-item">
                        <span>Access Control</span>
                        <span class="mono-num">Staff session required</span>
                    </li>
                </ul>
            </div>
        @endauth
    </section>

    <section aria-labelledby="modules-heading">
        <div class="section-header-row">
            <h2 id="modules-heading" class="section-title">Counter Modules</h2>
            <p class="section-meta">Staff sign-in is required for catalog, customer, staff, and sales screens.</p>
        </div>

        <ul class="ops-ledger-list">
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Record Sale</h3>
                    <p class="ops-ledger-desc">Pick an active product, optionally link a customer, set quantity, and complete checkout.</p>
                </div>
                <div class="ops-ledger-actions">
                    <a href="{{ route('sales.create') }}" class="button button-primary button-sm">Record Sale</a>
                </div>
            </li>
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Products</h3>
                    <p class="ops-ledger-desc">Manage unit pricing, stock counts, product photos, and archived SKUs.</p>
                </div>
                <div class="ops-ledger-actions">
                    <a href="{{ route('products.index') }}" class="button button-secondary button-sm">Open Catalog</a>
                </div>
            </li>
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Sales History</h3>
                    <p class="ops-ledger-desc">Look up completed receipts by product, customer, staff cashier, or date.</p>
                </div>
                <div class="ops-ledger-actions">
                    <a href="{{ route('sales.index') }}" class="button button-secondary button-sm">Sales History</a>
                </div>
            </li>
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Customers &amp; Staff</h3>
                    <p class="ops-ledger-desc">Maintain customer contacts and staff accounts with roles and portraits.</p>
                </div>
                <div class="ops-ledger-actions">
                    <a href="{{ route('customers.index') }}" class="button button-secondary button-sm">Customers</a>
                    <a href="{{ route('users.index') }}" class="button button-secondary button-sm">Staff</a>
                </div>
            </li>
        </ul>
    </section>
</div>
@endsection
