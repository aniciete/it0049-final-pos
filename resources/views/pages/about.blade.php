@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('home') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Home</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">About</h1>
            <p class="page-lead">
                How Bright-Market POS connects inventory, customer accounts, staff operators, and the sales ledger.
            </p>
        </div>
    </header>

    <div class="about-layout">
        <section class="context-panel" aria-labelledby="architecture-heading">
            <h2 id="architecture-heading" class="sidebar-panel-title">How Counter Data Connects</h2>
            <p class="context-panel-sub">
                Every completed checkout links a catalog item, an optional customer profile, and the signed-in cashier inside one balanced ledger record.
            </p>

            <div class="arch-diagram" role="region" aria-label="Operational entity relationships">
                <div class="arch-hub">
                    <div class="arch-node arch-node-hub">
                        <span class="arch-node-title">Sales Ledger</span>
                        <span class="arch-node-desc">Records quantity, server-calculated total, and timestamp in one transaction.</span>
                    </div>
                </div>

                <div class="arch-spokes">
                    <div class="arch-spoke">
                        <span class="arch-link-tag">Deducts stock</span>
                        <div class="arch-node">
                            <span class="arch-node-title">Products</span>
                            <span class="arch-node-desc">Pricing, live inventory counts, photos, and archive-safe history.</span>
                        </div>
                    </div>

                    <div class="arch-spoke">
                        <span class="arch-link-tag">Optional link</span>
                        <div class="arch-node">
                            <span class="arch-node-title">Customers</span>
                            <span class="arch-node-desc">Contact directory; defaults to Walk-in when omitted or removed.</span>
                        </div>
                    </div>

                    <div class="arch-spoke">
                        <span class="arch-link-tag">Recorded by</span>
                        <div class="arch-node">
                            <span class="arch-node-title">Staff</span>
                            <span class="arch-node-desc">Authenticated operator tied to every receipt for audit traceability.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="arch-roles-block">
                <h3 class="arch-roles-title">Authorized Operator Roles</h3>
                <div class="token-cluster">
                    @foreach (\App\Models\User::ROLES as $role)
                        <span class="status-pill status-neutral">{{ $role }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        <aside class="sidebar-ledger-panel" aria-labelledby="guardrails-heading">
            <h2 id="guardrails-heading" class="sidebar-panel-title">Operational Safeguards</h2>
            <ul class="guardrail-list">
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="lock" :size="16" /><span>Staff Session Guard</span></span>
                    <span class="guardrail-detail">Catalog, customer, staff, and sales screens require an active staff sign-in.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="database" :size="16" /><span>Oversell Protection</span></span>
                    <span class="guardrail-detail">Checkout checks stock and deducts inventory together so orders never exceed stock on hand.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="image" :size="16" /><span>Safe Photo Uploads</span></span>
                    <span class="guardrail-detail">Uploads accept JPG, PNG, or WebP files up to 2 MB and resize to square display assets on save.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="key" :size="16" /><span>Credential Protection</span></span>
                    <span class="guardrail-detail">Passwords are stored as one-way hashes and forms verify session tokens on submit.</span>
                </li>
            </ul>
        </aside>
    </div>
</div>
@endsection
