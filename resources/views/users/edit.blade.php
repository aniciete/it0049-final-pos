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
            <h1 class="page-title">Edit Staff: {{ $user->full_name }}</h1>
            <p class="page-lead">Update operator details, set a new password, or replace the staff photo.</p>
        </div>
    </header>

    <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        @include('users._form')
    </form>
</div>
@endsection
