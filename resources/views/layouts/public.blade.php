<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>STREAME - @yield('title', 'Welcome')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
        }
        .material-symbols-outlined {
          vertical-align: middle;
          font-size: 20px; /* Consistent icon size */
        }
    </style>
</head>
<body class="text-gray-300" x-data="{ mobileMenuOpen: false }">

    <div class="container mx-auto px-4">

        <header class="py-4">
            <nav class="flex items-center justify-between gap-6">
                <div class="flex-shrink-0">
    <a href="{{ route('home') }}" class="text-3xl font-extrabold tracking-wider">
        {{-- Add the new class to the span --}}
        <span class="animated-gradient">STREAME</span>
    </a>
</div>

                <div class="hidden md:flex md:w-2/4 lg:flex flex-1 max-w-lg justify-center">
                    <form action="{{ route('browse.index') }}" method="GET" class="relative w-full">
                        <label for="search" class="sr-only">Search</label>
                        
                        <input id="search" name="q" value="{{ request('q') }}"
                               class="w-full h-11 pl-10 pr-4 py-2 bg-gray-800/80 border border-gray-700 rounded-full text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="Search movies, TV shows..." type="search">

                        {{-- Add hidden input to default to multi-search --}}
                        <input type="hidden" name="type" value="multi">

                        {{-- This button holds the icon and submits the form --}}
                        <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <span class="material-symbols-outlined text-gray-400">search</span>
                        </button>
                    </form>
                </div>

                <div class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('browse.index', ['type' => 'movie']) }}" class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-full {{ request('type') == 'movie' ? 'bg-white text-black' : 'text-gray-300 hover:bg-gray-700' }} transition-colors">
        <span class="material-symbols-outlined">movie</span>
        Movies
    </a>
    {{-- UPDATED: Link to the browse page, pre-filtered for TV shows --}}
    <a href="{{ route('browse.index', ['type' => 'tv_show']) }}" class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-full {{ request('type') == 'tv_show' ? 'bg-white text-black' : 'text-gray-300 hover:bg-gray-700' }} transition-colors">
        <span class="material-symbols-outlined">tv_gen</span>
        TV Shows
    </a>
    {{-- NEW: "Browse" link that goes to the main browse page --}}
    <a href="{{ route('browse.index') }}" class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-full {{ request()->routeIs('browse.index') && !request('type') ? 'bg-white text-black' : 'text-gray-300 hover:bg-gray-700' }} transition-colors">
        <span class="material-symbols-outlined">explore</span>
        Browse
    </a>

                    <div class="w-px h-6 bg-gray-700"></div> {{-- Divider --}}

                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white px-4 py-2">Login</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">Register</a>
                    @else
                        {{-- Logged-in user icon/dropdown can go here --}}
                        <a href="{{ route('profile.show') }}" class="p-2 rounded-full hover:bg-gray-700">
                             <span class="material-symbols-outlined">person</span>
                        </a>
                    @endguest
                </div>

                 <div class="flex items-center lg:hidden">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white">Login</a>
                    @else
                         <a href="{{ route('profile.show') }}" class="p-2 rounded-full hover:bg-gray-700">
                             <span class="material-symbols-outlined">person</span>
                        </a>
                    @endguest
                </div>
            </nav>
        </header>

        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex lg:hidden" style="display: none;">
            
            <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm" aria-hidden="true"></div>
            
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-900 border-r border-gray-700">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="mobileMenuOpen = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <span class="material-symbols-outlined text-white">close</span>
                    </button>
                </div>
                
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4 mb-4">
                        <a href="{{ route('home') }}" class="text-3xl font-extrabold text-white tracking-wider">STREAME</a>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <a href="{{ route('browse.index', ['type' => 'movie']) }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('movies.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <span class="material-symbols-outlined">movie</span>
                            <span>Movies</span>
                        </a>
                        <a href="{{ route('browse.index', ['type' => 'tv_show']) }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('tv-shows.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                             <span class="material-symbols-outlined">tv_gen</span>
                            <span>TV Shows</span>
                        </a>
                        <a href="{{ route('browse.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('browse.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <span class="material-symbols-outlined">browse</span>
                            <span>Browse All</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <main class="mt-8">
            @yield('content')
        </main>

        <footer class="text-center py-8 mt-12 border-t border-gray-800">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} STREAME. All rights reserved.</p>
        </footer>

    </div>

</body>
</html>