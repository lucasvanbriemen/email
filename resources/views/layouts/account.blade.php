<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

        @vite(['resources/js/app.js', 'resources/js/theme.js', 'resources/css/root.scss', 'resources/css/account/layout.scss', 'resources/css/app.css'])
    </head>
    <body>

        @include('layouts.account.sidebar')

        <main>
            {{ $slot }}
        </main>
    </body>
</html>
