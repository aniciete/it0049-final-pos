<x-guest-layout>
    <div class="auth-card">
        <header class="auth-header">
            <h1 class="auth-title">Sign In</h1>
        </header>

        @include('auth._login_form', ['idPrefix' => 'login', 'autofocus' => true, 'showSummary' => true])
    </div>
</x-guest-layout>
