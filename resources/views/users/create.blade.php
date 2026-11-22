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
            <p class="page-lead">Create an authenticated POS operator account with role assignment and photo.</p>
        </div>
    </header>

    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @include('users._form')
    </form>
</div>
@endsection
