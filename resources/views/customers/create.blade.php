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

    <div class="workspace-split">
        <div class="form-card">
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

        <aside class="context-panel" aria-label="Recently Added Customers">
            <h2 class="context-panel-title">Recently Added</h2>

            @if ($recentCustomers->isEmpty())
                <p class="section-meta">No customers registered yet.</p>
            @else
                <ul class="context-list">
                    @foreach ($recentCustomers as $recent)
                        <li class="context-list-item">
                            <a href="{{ route('customers.show', $recent) }}" class="context-list-link">
                                <span class="context-list-top">
                                    <span style="font-weight: 700;">{{ $recent->full_name }}</span>
                                    <x-icon name="caret-right" :size="14" class="context-chevron" />
                                </span>
                                <span class="context-list-bottom">
                                    <span>{{ $recent->email }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>
    </div>
</div>
@endsection
