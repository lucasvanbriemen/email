<x-guest-layout class="login">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class='form-wrapper'>
        <form method="POST" action="{{ route('login') }}" class='login-form'>
            @csrf

            <h1 class='login-title'>{{ __('Welcome back!') }}</h1>
            <p class='login-subtitle'>{{ __('Please enter your email and password to continue.') }}</p>

            <x-input type="email" name="email" class="email-input" :value="old('email')" />
            <x-input type="password" name="password" :value="old('password')" />
            <x-input type="checkbox" name="remember" id="remember_me" class="remember-me-checkbox" value="checked" />

            <x-input type="submit" class="call-to-action" value="{{ __('Log in') }}" name="Log in" />
        </form>
    </div>


    <div class='side-container'>
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="logo">
        <img src="{{ asset('images/login-image-light.jpg') }}" alt="Login Illustration" class="login-illustration">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            login.init();
        });
    </script>
</x-guest-layout>
