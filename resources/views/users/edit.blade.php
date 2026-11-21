@extends('layouts.app')

@section('content')
<div class="container">
    <div class="back-link-row">
        <a href="{{ route('users.index') }}" class="back-link">&larr; Back to Staff</a>
    </div>

    <header class="directory-header">
        <div>
            <p class="eyebrow">Staff Directory</p>
            <h1 class="page-title">Edit Staff: {{ $user->full_name }}</h1>
            <p class="page-lead">Update account details, rotate password, or upload a new avatar.</p>
        </div>
    </header>

    <div class="form-card" style="max-width: 760px;">
        <div class="form-card-header">
            <h2 class="form-section-title">Staff Account Details</h2>
        </div>

        @include('partials.error_summary')

        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            @include('users._form')

            <div class="form-actions">
                <a href="{{ route('users.index') }}" class="button button-secondary">Cancel</a>
                <button type="submit" class="button button-primary" data-busy-text="Updating account...">Update Staff User</button>
            </div>
        </form>
    </div>
</div>
@endsection
