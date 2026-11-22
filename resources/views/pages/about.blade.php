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
            <h1 class="page-title">System Overview</h1>
            <p class="page-lead">
                Operational data structure and checkout safeguards powering Bright-Market POS (one line item per receipt).
            </p>
        </div>
    </header>

    <div class="about-layout">
        <section aria-labelledby="schema-heading">
            <h2 id="schema-heading" class="section-title section-heading-spaced">Database Tables</h2>
            <div class="schema-grid">
                <article class="schema-card">
                    <h3 class="schema-card-title">products</h3>
                    <p class="schema-card-note">Tracks retail prices, stock levels, photos, and archived items without losing past receipts.</p>
                    <div class="token-cluster">
                        <code>id</code>
                        <code>name</code>
                        <code>price</code>
                        <code>stock_quantity</code>
                        <code>image</code>
                        <code>deleted_at</code>
                        <code>timestamps</code>
                    </div>
                </article>

                <article class="schema-card">
                    <h3 class="schema-card-title">customers</h3>
                    <p class="schema-card-note">Stores customer contact details for optional linking during counter checkout.</p>
                    <div class="token-cluster">
                        <code>id</code>
                        <code>full_name</code>
                        <code>email</code>
                        <code>phone</code>
                        <code>timestamps</code>
                    </div>
                </article>

                <article class="schema-card">
                    <h3 class="schema-card-title">users</h3>
                    <p class="schema-card-note">Manages staff sign-in identities, roles, portraits, and hashed passwords.</p>
                    <div class="token-cluster">
                        <code>id</code>
                        <code>username</code>
                        <code>full_name</code>
                        <code>email</code>
                        <code>password</code>
                        <code>role</code>
                        <code>avatar</code>
                        <code>timestamps</code>
                    </div>
                </article>

                <article class="schema-card">
                    <h3 class="schema-card-title">sales</h3>
                    <p class="schema-card-note">Records each completed sale, linking the product, optional customer, and cashier.</p>
                    <div class="token-cluster">
                        <code>id</code>
                        <code>product_id</code>
                        <code>customer_id</code>
                        <code>sold_by</code>
                        <code>quantity</code>
                        <code>total_price</code>
                        <code>timestamps</code>
                    </div>
                </article>
            </div>
        </section>

        <aside class="sidebar-ledger-panel" aria-labelledby="guardrails-heading">
            <h2 id="guardrails-heading" class="sidebar-panel-title">Operational Safeguards</h2>
            <ul class="guardrail-list">
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="shield-check" :size="16" /><span>Staff Session Guard</span></span>
                    <span class="guardrail-detail">Catalog, customer, staff, and sales screens require an active staff sign-in.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="shield-check" :size="16" /><span>Oversell Protection</span></span>
                    <span class="guardrail-detail">Checkout checks stock and deducts inventory together so orders never exceed stock on hand.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="shield-check" :size="16" /><span>Safe Photo Uploads</span></span>
                    <span class="guardrail-detail">Uploads accept JPG, PNG, or WebP files up to 2 MB and resize cleanly for display.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name guardrail-heading-row"><x-icon name="shield-check" :size="16" /><span>Credential Protection</span></span>
                    <span class="guardrail-detail">Passwords are never stored in plain text and forms verify session tokens on submit.</span>
                </li>
            </ul>
        </aside>
    </div>
</div>
@endsection
