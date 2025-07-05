<x-guest-layout class="register">
    <div class='side-container'>
        <div class='logo'>
            {!! svg('logo') !!}
        </div>

        <img src="{{ asset('images/login-image-light.jpg') }}" alt="Login Illustration" class="login-illustration">
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class='form-wrapper'>
        <form method="POST" action="{{ route('register') }}" class='login-form'>
            @csrf

            <h1 class='login-title'>{{ __('Welcome!') }}</h1>
            <p class='login-subtitle'>{{ __('Create an account to get started.') }}</p>

            <x-input type="text" name="name" class="name-input" :value="old('name')" />
            <x-input type="email" name="email" :value="old('email')" />
            <x-input type="password" name="password" :value="old('password')" />
            <x-input type="password" name="password_confirmation" label="Confirm Password" :value="old('password_confirmation')" />

            <x-input type="submit" class="call-to-action" value="{{ __('Log in') }}" name="Log in" />
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            login.init();
        });
    </script>
</x-guest-layout>
