@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('customers.index') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Customers</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">Add Customer</h1>
            <p class="page-lead">Register a customer profile for checkout linking and receipt lookup.</p>
        </div>
    </header>

    <div class="form-card form-measure">
        @include('partials.error_summary')

        <form action="{{ route('customers.store') }}" method="POST" novalidate>
            @csrf
            @include('customers._form')

            <div class="form-actions">
                <a href="{{ route('customers.index') }}" class="button button-secondary">Cancel</a>
                <button type="submit" class="button button-primary" data-busy-text="Adding customer...">Add Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
