
@extends('layout.app')

@section('style')
    @vite([
        'resources/css/dashboard.css',
        'resources/css/login.css'
    ])
@endsection

@section('title', 'Sign In | Inventory Management System')

@section('content')

<main class="login-page">

    <div class="login-container">

        {{-- LEFT BRAND SECTION --}}
        <section class="login-brand-panel">

            <div class="login-brand-top">
                <div class="login-logo">
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="m12 3 9 5-9 5-9-5 9-5Z"/>
                        <path d="M3 8v9l9 5 9-5V8"/>
                        <path d="M12 13v9"/>
                        <path d="m7.5 5.5 9 5"/>
                    </svg>
                </div>

                <div class="login-brand-name">
                    <strong>IMS</strong>
                    <span>Inventory Management System</span>
                </div>
            </div>

            <div class="login-brand-content">

                <div class="login-brand-badge">
                    <span class="login-badge-dot"></span>
                    Smart Inventory Management
                </div>

                <h1>
                    Manage smarter.<br>
                    <span>Work better.</span>
                </h1>

                <p>
                    Manage products, monitor stock levels,
                    and keep track of transactions with
                    one organized inventory system.
                </p>

                <div class="login-feature-list">

                    <div class="login-feature">
                        <div class="login-feature-icon">✓</div>
                        <span>Product & Inventory Tracking</span>
                    </div>

                    <div class="login-feature">
                        <div class="login-feature-icon">✓</div>
                        <span>Sales & Stock Monitoring</span>
                    </div>

                    <div class="login-feature">
                        <div class="login-feature-icon">✓</div>
                        <span>Role-Based System Access</span>
                    </div>

                </div>
            </div>

            <div class="login-brand-footer">
                <span>Inventory Management System</span>
                <span>© {{ date('Y') }}</span>
            </div>

        </section>

        {{-- RIGHT LOGIN SECTION --}}
        <section class="login-form-panel">

            <div class="login-form-wrapper">

                {{-- MOBILE LOGO --}}
                <div class="login-mobile-brand">
                    <div class="login-mobile-logo">IMS</div>
                    <span>Inventory Management System</span>
                </div>

                <div class="login-form-heading">

                    <div class="login-welcome-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21a8 8 0 0 0-16 0"/>
                            <circle cx="12" cy="8" r="4"/>
                        </svg>
                    </div>

                    <h2>Welcome back</h2>

                    <p>
                        Please enter your credentials to
                        access your account.
                    </p>

                </div>

                @if (session('status'))
                    <div class="login-alert-success" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- LOGIN FORM --}}
                <form method="POST"
                      action="{{ route('login') }}"
                      class="login-form">

                    @csrf

                    {{-- EMAIL --}}
                    <div class="login-field">

                        <label for="email">
                            Email Address
                        </label>

                        <div class="login-input-wrapper">

                            <svg class="login-input-icon"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="3" y="5" width="18"
                                      height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                autocomplete="username"
                                maxlength="100"
                                required
                                autofocus
                                class="@error('email') is-invalid @enderror"
                            >

                        </div>

                        @error('email')
                            <span class="login-error" role="alert">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    {{-- PASSWORD --}}
                    <div class="login-field">

                        <label for="password">
                            Password
                        </label>

                        <div class="login-input-wrapper">

                            <svg class="login-input-icon"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <rect x="5" y="10" width="14"
                                      height="11" rx="2"/>
                                <path d="M8 10V7a4 4 0 1 1 8 0v3"/>
                            </svg>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                                class="@error('password') is-invalid @enderror"
                            >

                            <button
                                type="button"
                                class="login-password-toggle"
                                id="togglePassword"
                                aria-label="Show password"
                                aria-pressed="false">
                                Show
                            </button>

                        </div>

                        @error('password')
                            <span class="login-error" role="alert">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    {{-- REMEMBER ME --}}
                    <div class="login-options">

                        <label class="login-remember">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                            >

                            <span>Remember me</span>

                        </label>

                    </div>

                    {{-- SUBMIT --}}
                    <button type="submit" class="login-submit-btn">

                        <span>Sign In</span>

                        <svg viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="m13 6 6 6-6 6"/>
                        </svg>

                    </button>

                </form>

                <div class="login-security-note">

                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="5" y="10" width="14" height="11" rx="2"/>
                        <path d="M8 10V7a4 4 0 1 1 8 0v3"/>
                    </svg>

                    <span>Authorized personnel only</span>

                </div>

            </div>

            <div class="login-form-footer">
                © {{ date('Y') }} Inventory Management System
            </div>

        </section>

    </div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const toggleButton = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (toggleButton && passwordInput) {

        toggleButton.addEventListener('click', function () {

            const isHidden = passwordInput.type === 'password';

            passwordInput.type = isHidden ? 'text' : 'password';

            toggleButton.textContent = isHidden ? 'Hide' : 'Show';

            toggleButton.setAttribute(
                'aria-label',
                isHidden ? 'Hide password' : 'Show password'
            );

            toggleButton.setAttribute(
                'aria-pressed',
                String(isHidden)
            );

        });

    }

});
</script>

@endsection
