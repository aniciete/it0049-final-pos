@extends('layouts.app')

@section('content')
<div class="container">
    <header class="directory-header">
        <div>
            <p class="eyebrow">System Architecture</p>
            <h1 class="page-title">Laravel MVC &amp; Relational Schema</h1>
            <p class="page-lead">
                Built with Laravel 13 Eloquent models, migrations, seeders, Form Requests, Breeze authentication
                middleware, and public storage handling.
            </p>
        </div>
        <div class="directory-header-actions">
            <a href="{{ route('home') }}" class="button button-secondary">Return Home</a>
        </div>
    </header>

    <div class="about-layout">
        <section aria-labelledby="schema-heading">
            <h2 id="schema-heading" class="section-title">Relational Database Tables</h2>
            <ol class="schema-list">
                <li class="schema-list-item">
                    <span class="schema-step-num">01</span>
                    <div>
                        <h3 class="schema-item-title">products</h3>
                        <p class="schema-item-desc">
                            <code>id</code>, <code>name</code>, <code>price</code>, <code>stock_quantity</code>,
                            <code>image</code>, <code>timestamps</code>, <code>deleted_at</code>. Uses Laravel
                            <code>SoftDeletes</code> so archived products keep historical sales intact.
                        </p>
                    </div>
                </li>
                <li class="schema-list-item">
                    <span class="schema-step-num">02</span>
                    <div>
                        <h3 class="schema-item-title">customers</h3>
                        <p class="schema-item-desc">
                            <code>id</code>, <code>full_name</code>, <code>email</code> (unique), <code>phone</code>
                            (nullable), <code>timestamps</code>. Customer selection is optional during a sale.
                        </p>
                    </div>
                </li>
                <li class="schema-list-item">
                    <span class="schema-step-num">03</span>
                    <div>
                        <h3 class="schema-item-title">users</h3>
                        <p class="schema-item-desc">
                            <code>id</code>, <code>username</code> (unique), <code>full_name</code>,
                            <code>email</code> (unique), <code>password</code> (bcrypt hashed), <code>role</code>,
                            <code>avatar</code>, <code>timestamps</code>. One staff user records many sales.
                        </p>
                    </div>
                </li>
                <li class="schema-list-item">
                    <span class="schema-step-num">04</span>
                    <div>
                        <h3 class="schema-item-title">sales</h3>
                        <p class="schema-item-desc">
                            <code>id</code>, <code>product_id</code> (FK RESTRICT), <code>customer_id</code> (nullable FK SET NULL),
                            <code>sold_by</code> (FK RESTRICT), <code>quantity</code>, <code>total_price</code>,
                            <code>timestamps</code>. Recorded atomically inside <code>DB::transaction()</code>.
                        </p>
                    </div>
                </li>
            </ol>
        </section>

        <aside class="sidebar-ledger-panel" aria-labelledby="guardrails-heading">
            <h2 id="guardrails-heading" class="sidebar-panel-title">Security &amp; Integrity Guardrails</h2>
            <ul class="guardrail-list">
                <li class="guardrail-item">
                    <span class="guardrail-name">Auth Middleware Guard</span>
                    <span class="guardrail-detail">All Products, Customers, Staff, Sales, and Profile routes enforce <code>auth</code> middleware.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name">Atomic Stock Protection</span>
                    <span class="guardrail-detail">Conditional decrement inside <code>DB::transaction()</code> rejects overselling with zero partial writes.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name">Safe Storage Uploads</span>
                    <span class="guardrail-detail">Images validated for JPG/PNG/WebP (&le; 2 MB), processed via Intervention Image, and stored on <code>Storage::disk('public')</code>.</span>
                </li>
                <li class="guardrail-item">
                    <span class="guardrail-name">CSRF &amp; Password Hashing</span>
                    <span class="guardrail-detail">Every mutating form includes <code>@csrf</code> and passwords are always hashed via Laravel <code>Hash::make()</code>.</span>
                </li>
            </ul>
        </aside>
    </div>
</div>
@endsection
