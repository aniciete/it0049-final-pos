@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('users.index') }}" class="back-link">&larr; Back to Staff</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Staff Directory</p>
            <h1 class="page-title">Add Staff Account</h1>
            <p class="page-lead">Create a staff login with hashed credentials and an optional avatar.</p>
        </div>
    </header>

    <div class="form-card" style="max-width: 760px;">
        <div class="form-card-header">
            <h2 class="form-section-title">Staff Account Details</h2>
        </div>

        @include('partials.error_summary')

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @include('users._form')

            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="button button-secondary">Cancel</a>
                <button type="submit" class="button button-primary" data-busy-text="Creating account...">Create Staff User</button>
            </div>
        </form>
    </div>
</div>
@endsection
