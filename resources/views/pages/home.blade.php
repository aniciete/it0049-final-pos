@extends('layouts.app')

@section('content')
<div class="container">
    <section class="home-hero">
        <div>
            <p class="eyebrow">Laravel Point-of-Sale System</p>
            <h1 class="page-title">Bright-Market POS &amp; Inventory Ledger</h1>
            <p class="page-lead">
                Manage catalog products with image uploads, maintain customer and staff directories, record sales
                atomically inside database transactions, and prevent overselling.
            </p>
            <div class="home-actions">
                @auth
                    <a href="{{ route('sales.create') }}" class="button button-primary">Open Sale Register</a>
                    <a href="{{ route('products.index') }}" class="button button-secondary">Manage Products</a>
                    <a href="{{ route('sales.index') }}" class="button button-secondary">View Sales History</a>
                @else
                    <a href="{{ route('login') }}" class="button button-primary">Staff Sign In</a>
                    <a href="{{ route('about') }}" class="button button-secondary">View Architecture &amp; Schema</a>
                @endauth
            </div>
        </div>

        <div class="kpi-ledger-grid" aria-label="Store Operational Snapshot">
            <div class="kpi-cell">
                <span class="metric-label">Active Products</span>
                <span class="kpi-value mono-num">{{ $productCount }}</span>
                <span class="kpi-sub">{{ $lowStockCount }} low or out of stock</span>
            </div>
            <div class="kpi-cell">
                <span class="metric-label">Sales Recorded</span>
                <span class="kpi-value mono-num">{{ $salesCount }}</span>
                <span class="kpi-sub">${{ number_format($totalRevenue, 2) }} gross ledger total</span>
            </div>
            <div class="kpi-cell">
                <span class="metric-label">Customers</span>
                <span class="kpi-value mono-num">{{ $customerCount }}</span>
                <span class="kpi-sub">Registered customer directory</span>
            </div>
            <div class="kpi-cell">
                <span class="metric-label">Staff Accounts</span>
                <span class="kpi-value mono-num">{{ $userCount }}</span>
                <span class="kpi-sub">Authenticated POS operators</span>
            </div>
        </div>
    </section>

    <section aria-labelledby="modules-heading">
        <div class="section-header-row">
            <h2 id="modules-heading" class="section-title">Operational Modules</h2>
            <p class="section-meta">All management and register routes require staff authentication.</p>
        </div>

        <ul class="ops-ledger-list">
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Checkout Register (Record Sale)</h3>
                    <p class="ops-ledger-desc">Select an active product, optional customer (or walk-in), and quantity with atomic stock deduction.</p>
                </div>
                <div class="ops-ledger-actions">
                    <a href="{{ route('sales.create') }}" class="button button-primary button-sm">Record Sale</a>
                </div>
            </li>
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Products Catalog</h3>
                    <p class="ops-ledger-desc">Create, edit, upload product photos, archive sold items, or restore archived SKUs.</p>
                </div>
                <div class="ops-ledger-actions">
                    <a href="{{ route('products.index') }}" class="button button-secondary button-sm">Open Catalog</a>
                </div>
            </li>
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Sales History Ledger</h3>
                    <p class="ops-ledger-desc">Audit completed receipts with product, customer or walk-in, cashier, quantity, total, and date.</p>
                </div>
                <div class="ops-ledger-actions">
                    <a href="{{ route('sales.index') }}" class="button button-secondary button-sm">View Ledger</a>
                </div>
            </li>
            <li class="ops-ledger-row">
                <div class="ops-ledger-info">
                    <h3 class="ops-ledger-title">Customers &amp; Staff Directories</h3>
                    <p class="ops-ledger-desc">Maintain customer contacts and staff accounts with avatars and hashed passwords.</p>
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
