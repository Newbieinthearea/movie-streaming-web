<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title> {{-- Added @yield('title') for consistency --}}

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-900"> {{-- Changed default bg to gray-900 --}}
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-gray-800 shadow"> {{-- Changed header bg to gray-800 for consistency --}}
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }} {{-- Ensure $header slot content (e.g., h2) has light text color --}}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }} {{-- Content here will need light text if not already handled --}}
            </main>
        </div>
    </body>
</html>