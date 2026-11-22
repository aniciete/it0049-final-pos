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
            <h1 class="page-title">Edit Customer: {{ $customer->full_name }}</h1>
            <p class="page-lead">Update customer contact details while preserving recorded sales.</p>
        </div>
    </header>

    <div class="workspace-split">
        <div class="form-card">
            @include('partials.error_summary')

            <form action="{{ route('customers.update', $customer) }}" method="POST" novalidate>
                @csrf
                @method('PUT')
                @include('customers._form')

                <div class="form-actions">
                    <a href="{{ route('customers.index') }}" class="button button-secondary">Cancel</a>
                    <button type="submit" class="button button-primary" data-busy-text="Updating customer...">Update Customer</button>
                </div>
            </form>
        </div>

        <x-recent-sales-panel
            title="Linked Orders"
            subtitle="Latest sales attached to this customer."
            :sales="$recentSales"
            empty-text="No orders linked to this customer yet."
            :show-customer="false"
            :filter-url="(int) $customer->sales_count > 0 ? route('sales.index', ['customer' => $customer->id]) : null"
        />
    </div>
</div>
@endsection
