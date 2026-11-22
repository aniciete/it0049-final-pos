@extends('layouts.app')

@section('content')
<div class="container">
    <section class="home-hero">
        <div>
            <h1 class="hero-title">Counter checkout and live store inventory</h1>
            <p class="page-lead">
                Record counter sales with stock protection, manage products and accounts, and audit every receipt.
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
                    <a href="{{ route('about') }}" class="button button-secondary">About</a>
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
            <div class="auth-card home-signin-card" aria-label="Staff Sign In">
                <header class="auth-header">
                    <h2 class="context-panel-title">Staff Sign In</h2>
                    <p class="context-panel-sub" style="margin-bottom: 0;">Open the counter register and store ledgers.</p>
                </header>

                @include('auth._login_form', ['idPrefix' => 'home', 'autofocus' => false, 'showSummary' => false])
            </div>
        @endauth
    </section>

    <section aria-labelledby="modules-heading">
        <div class="section-header-row">
            <h2 id="modules-heading" class="section-title">Counter Modules</h2>
        </div>

        <ul class="ops-ledger-list">
            <li class="ops-ledger-row-item">
                <a href="{{ route('sales.create') }}" class="ops-ledger-link">
                    <div class="ops-ledger-info">
                        <h3 class="ops-ledger-title">Record Sale</h3>
                        <p class="ops-ledger-desc">Pick an active product, optionally link a customer, set quantity, and complete checkout.</p>
                    </div>
                    <span class="ops-ledger-trailing" aria-hidden="true">
                        @guest
                            <x-icon name="lock" :size="15" />
                        @endguest
                        <x-icon name="caret-right" :size="16" />
                    </span>
                </a>
            </li>
            <li class="ops-ledger-row-item">
                <a href="{{ route('products.index') }}" class="ops-ledger-link">
                    <div class="ops-ledger-info">
                        <h3 class="ops-ledger-title">Products</h3>
                        <p class="ops-ledger-desc">Manage unit pricing, stock counts, product photos, and archived SKUs.</p>
                    </div>
                    <span class="ops-ledger-trailing" aria-hidden="true">
                        @guest
                            <x-icon name="lock" :size="15" />
                        @endguest
                        <x-icon name="caret-right" :size="16" />
                    </span>
                </a>
            </li>
            <li class="ops-ledger-row-item">
                <a href="{{ route('sales.index') }}" class="ops-ledger-link">
                    <div class="ops-ledger-info">
                        <h3 class="ops-ledger-title">Sales History</h3>
                        <p class="ops-ledger-desc">Look up completed receipts by product, customer, staff cashier, or date.</p>
                    </div>
                    <span class="ops-ledger-trailing" aria-hidden="true">
                        @guest
                            <x-icon name="lock" :size="15" />
                        @endguest
                        <x-icon name="caret-right" :size="16" />
                    </span>
                </a>
            </li>
            <li class="ops-ledger-row-item">
                <a href="{{ route('customers.index') }}" class="ops-ledger-link">
                    <div class="ops-ledger-info">
                        <h3 class="ops-ledger-title">Customers</h3>
                        <p class="ops-ledger-desc">Maintain registered customer contacts while preserving historical walk-in receipts.</p>
                    </div>
                    <span class="ops-ledger-trailing" aria-hidden="true">
                        @guest
                            <x-icon name="lock" :size="15" />
                        @endguest
                        <x-icon name="caret-right" :size="16" />
                    </span>
                </a>
            </li>
            <li class="ops-ledger-row-item">
                <a href="{{ route('users.index') }}" class="ops-ledger-link">
                    <div class="ops-ledger-info">
                        <h3 class="ops-ledger-title">Staff</h3>
                        <p class="ops-ledger-desc">Manage operator accounts, roles, portraits, and session credentials.</p>
                    </div>
                    <span class="ops-ledger-trailing" aria-hidden="true">
                        @guest
                            <x-icon name="lock" :size="15" />
                        @endguest
                        <x-icon name="caret-right" :size="16" />
                    </span>
                </a>
            </li>
        </ul>
    </section>
</div>
@endsection
