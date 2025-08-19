<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/root.scss', 'resources/js/root.js'])
    </head>
    <body class='{{ $class ?? '' }}'>
        <main>
            {{ $slot }}
        </main>

        <!-- Global Loader Overlay -->
        <div class="app-loader hidden" aria-live="polite" aria-busy="false" role="status">
            <div class="spinner" aria-hidden="true"></div>
            <div class="label">Loading…</div>
        </div>
    </body>
</html>
