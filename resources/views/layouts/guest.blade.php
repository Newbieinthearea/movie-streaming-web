<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-300 antialiased"> {{-- Changed default text to gray-300 for dark theme --}}
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-900"> {{-- Changed default bg to gray-900 --}}
            <div>
                <a href="/">
                    {{-- Ensure x-application-logo handles dark theme text/fill appropriately --}}
                    {{-- Or, if it uses fill-current, the parent text color will influence it --}}
                    <x-application-logo class="w-20 h-20 fill-current text-gray-400 dark:text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"> {{-- Changed card bg to gray-800 for contrast --}}
                {{ $slot }} {{-- Forms here will need light text for inputs/labels if not already handled --}}
            </div>
        </div>
    </body>
</html>