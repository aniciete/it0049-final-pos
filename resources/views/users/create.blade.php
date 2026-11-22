@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('users.index') }}" class="back-link">
            <x-icon name="arrow-left" :size="15" />
            <span>Back to Staff</span>
        </a>
    </div>

    <header class="directory-header">
        <div>
            <h1 class="page-title">Add Staff</h1>
            <p class="page-lead">Create an authenticated POS operator account with role assignment and avatar.</p>
        </div>
    </header>

    <div class="form-card form-measure">
        @include('partials.error_summary')

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @include('users._form')

            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="button button-secondary">Cancel</a>
                <button type="submit" class="button button-primary" data-busy-text="Saving staff...">Save Staff</button>
            </div>
        </form>
    </div>
</div>
@endsection
