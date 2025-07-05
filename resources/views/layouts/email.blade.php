<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

        <link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />
        <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script>

        @vite(['resources/js/root.js', 'resources/css/root.scss'])
    </head>
    <body class='{{ $class ?? '' }}'>

        @include('layouts.email.sidebar')

        <main>
            {{ $slot }}
        </main>

        <x-toast></x-toast>
    </body>
</html>
