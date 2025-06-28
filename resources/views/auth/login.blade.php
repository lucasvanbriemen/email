<x-guest-layout class="login">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <x-input type="email" name="email" class="email-input" :value="old('email')" />
        <x-input type="password" name="password" :value="old('password')" />
        <x-input type="checkbox" name="remember" id="remember_me" class="remember-me-checkbox" />

        <x-input type="submit" class="call-to-action" value="{{ __('Log in') }}" name="Log in" />
    </form>

    <div class='side-container'>
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="logo">
        <img src="{{ asset('images/login-image-light.jpg') }}" alt="Login Illustration" class="login-illustration">
    </div>s

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            login.init();
        });
    </script>
</x-guest-layout>
