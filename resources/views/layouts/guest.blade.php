@extends('layouts.app', ['title' => $title ?? 'Sign In | Bright-Market POS', 'activePage' => $activePage ?? 'login'])

@section('content')
    <div class="container">
        <div class="auth-shell">
            {{ $slot }}
        </div>
    </div>
@endsection
