<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Bright-Market POS' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ preg_replace('~^https?:~', '', asset('favicon.svg')) }}">
    <link rel="stylesheet" href="{{ preg_replace('~^https?:~', '', asset('assets/css/style.css')) }}?v={{ @filemtime(public_path('assets/css/style.css')) ?: '1' }}">
    <script src="{{ preg_replace('~^https?:~', '', asset('assets/js/app.js')) }}?v={{ @filemtime(public_path('assets/js/app.js')) ?: '1' }}" defer></script>
</head>
@php
    $isAuthRoute = request()->routeIs('login', 'register', 'password.*', 'verification.*');
@endphp
<body class="page-{{ $activePage ?? 'home' }}">
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header class="site-header" role="banner">
        <div class="container header-container">
            <div class="brand-lockup">
                <a href="{{ route('home') }}" class="brand-link" aria-label="Bright-Market POS Home">
                    <x-application-logo class="brand-mark" width="32" height="32" aria-hidden="true" />
                    <span class="brand-text">
                        <span class="brand-badge">Bright-Market</span>
                        <span class="brand-title">POS</span>
                    </span>
                </a>
            </div>

            @unless ($isAuthRoute)
                <nav class="site-nav" aria-label="Main Navigation">
                    <button
                        type="button"
                        class="nav-menu-toggle"
                        popovertarget="main-nav-menu"
                        popovertargetaction="toggle"
                        aria-controls="main-nav-menu"
                        aria-expanded="false"
                    >
                        <x-icon name="list" :size="16" />
                        <span>Menu</span>
                    </button>
                    <ul id="main-nav-menu" class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('home') }}" class="nav-link {{ ($activePage ?? '') === 'home' ? 'active' : '' }}" {!! ($activePage ?? '') === 'home' ? 'aria-current="page"' : '' !!}>Home</a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link {{ ($activePage ?? '') === 'dashboard' || request()->routeIs('dashboard') ? 'active' : '' }}" {!! ($activePage ?? '') === 'dashboard' || request()->routeIs('dashboard') ? 'aria-current="page"' : '' !!}>Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('products.index') }}" class="nav-link {{ ($activePage ?? '') === 'products' ? 'active' : '' }}" {!! ($activePage ?? '') === 'products' ? 'aria-current="page"' : '' !!}>Products</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sales.create') }}" class="nav-link {{ ($activePage ?? '') === 'sales_create' ? 'active' : '' }}" {!! ($activePage ?? '') === 'sales_create' ? 'aria-current="page"' : '' !!}>Record Sale</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sales.index') }}" class="nav-link {{ ($activePage ?? '') === 'sales' ? 'active' : '' }}" {!! ($activePage ?? '') === 'sales' ? 'aria-current="page"' : '' !!}>Sales History</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('customers.index') }}" class="nav-link {{ ($activePage ?? '') === 'customers' ? 'active' : '' }}" {!! ($activePage ?? '') === 'customers' ? 'aria-current="page"' : '' !!}>Customers</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link {{ ($activePage ?? '') === 'users' ? 'active' : '' }}" {!! ($activePage ?? '') === 'users' ? 'aria-current="page"' : '' !!}>Staff</a>
                            </li>
                        @endauth
                        <li class="nav-item">
                            <a href="{{ route('about') }}" class="nav-link {{ ($activePage ?? '') === 'about' ? 'active' : '' }}" {!! ($activePage ?? '') === 'about' ? 'aria-current="page"' : '' !!}>About</a>
                        </li>
                        @auth
                            <li class="nav-item nav-item-mobile-only">
                                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="nav-link nav-logout-btn">
                                        <x-icon name="sign-out" :size="15" />
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </li>
                        @endauth
                    </ul>
                </nav>
            @endunless

            <div class="header-session-zone">
                @auth
                    <a
                        href="{{ route('profile.edit') }}"
                        class="session-user-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                        title="Signed-in Staff Profile"
                    >
                        <img
                            src="{{ Auth::user()->avatarUrl() }}"
                            alt=""
                            width="24"
                            height="24"
                            loading="eager"
                            decoding="sync"
                            class="session-user-avatar"
                        >
                        <span>{{ Auth::user()->username ?: Auth::user()->full_name }}</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="header-logout-form">
                        @csrf
                        <button type="submit" class="button button-secondary button-sm">
                            <x-icon name="sign-out" :size="14" />
                            <span>Log Out</span>
                        </button>
                    </form>
                @else
                    @if ($isAuthRoute)
                        <a href="{{ route('about') }}" class="nav-link">About</a>
                    @else
                        <a href="{{ route('login') }}" class="button button-secondary button-sm">
                            <x-icon name="sign-in" :size="14" />
                            <span>Sign In</span>
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main id="main-content" class="site-main" role="main" tabindex="-1">
        @include('partials.flash')

        @hasSection('content')
            @yield('content')
        @elseif (isset($slot))
            <div class="container">
                {{ $slot }}
            </div>
        @endif
    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="container footer-container">
            <div class="footer-colophon">
                <span class="footer-brand">Bright-Market POS</span>
            </div>
        </div>
    </footer>

    <dialog id="confirm-action-dialog" class="confirm-dialog" aria-labelledby="confirm-dialog-title" aria-describedby="confirm-dialog-message">
        <div class="confirm-dialog-body">
            <h2 id="confirm-dialog-title" class="confirm-dialog-title">Confirm Action</h2>
            <p id="confirm-dialog-message" class="confirm-dialog-message">Confirm this update?</p>
            <div class="confirm-dialog-actions">
                <button type="button" id="confirm-dialog-cancel" class="button button-secondary">Cancel</button>
                <button type="button" id="confirm-dialog-submit" class="button button-danger">Confirm</button>
            </div>
        </div>
    </dialog>
</body>
</html>
