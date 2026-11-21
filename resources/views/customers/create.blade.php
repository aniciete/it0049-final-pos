@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('customers.index') }}" class="back-link">&larr; Back to Customers</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Customer Directory</p>
            <h1 class="page-title">Add Customer</h1>
            <p class="page-lead">Register a customer profile for checkout linking and receipt lookup.</p>
        </div>
    </header>

    <div class="form-card" style="max-width: 720px;">
        <div class="form-card-header">
            <h2 class="form-section-title">Customer Profile</h2>
        </div>

        @include('partials.error_summary')

        <form action="{{ route('customers.store') }}" method="POST" novalidate>
            @csrf
            @include('customers._form')

            <div class="form-actions">
                <a href="{{ route('customers.index') }}" class="button button-secondary">Cancel</a>
                <button type="submit" class="button button-primary" data-busy-text="Saving customer...">Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
