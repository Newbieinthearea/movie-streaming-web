<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Movie Streaming')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-300">
    <div class="min-h-screen">
        {{-- Public Navigation --}}
{{-- New Responsive Header --}}
<header x-data="{ mobileMenuOpen: false }" class="bg-gray-800/80 backdrop-blur-lg border-b border-gray-700 sticky top-0 z-40">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                    <x-application-logo class="block h-9 w-auto fill-current text-purple-500" />
                    <span class="font-bold text-white text-lg hidden sm:block">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <nav class="hidden md:flex space-x-6">
                    <a href="{{ route('browse.index', ['type' => 'movie']) }}"
                       class="text-sm font-semibold transition-colors {{ request()->input('type') === 'movie' && request()->routeIs('browse.index') ? 'text-purple-400' : 'text-gray-300 hover:text-white' }}">
                        Movies
                    </a>
                    <a href="{{ route('browse.index', ['type' => 'tv_show']) }}"
                       class="text-sm font-semibold transition-colors {{ request()->input('type') === 'tv_show' && request()->routeIs('browse.index') ? 'text-purple-400' : 'text-gray-300 hover:text-white' }}">
                        TV Shows
                    </a>
                    <a href="{{ route('browse.index') }}"
                       class="text-sm font-semibold transition-colors {{ !request()->input('type') && request()->routeIs('browse.index') ? 'text-purple-400' : 'text-gray-300 hover:text-white' }}">
                        Browse
                    </a>
                </nav>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden sm:block">
                     <form action="{{ route('browse.index') }}" method="GET">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                            <input type="text" name="q" placeholder="Search..."
                                class="w-32 sm:w-48 bg-gray-700/50 border-transparent rounded-full pl-10 pr-4 py-2 text-white focus:ring-purple-500 focus:border-purple-500 focus:w-48 sm:focus:w-64 transition-all duration-300">
                        </div>
                    </form>
                </div>

                <div class="hidden md:block">
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-400 bg-gray-800 hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1"><svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @if(Auth::user()->isAdmin())
                                    <x-dropdown-link :href="route('dashboard')">{{ __('Admin Dashboard') }}</x-dropdown-link>
                                @endif
                                <x-dropdown-link :href="route('profile.show')">{{ __('My Profile') }}</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="space-x-2">
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white px-3 py-2 rounded-md">Log in</a>
                            <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-purple-600 px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">Register</a>
                        </div>
                    @endauth
                </div>

                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-300 hover:text-white">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen"
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm md:hidden" style="display: none;">
    </div>

    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 border-r border-gray-700 p-4 md:hidden" style="display: none;">
        
        <div class="flex items-center justify-between mb-6">
             <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2">
                <x-application-logo class="block h-9 w-auto fill-current text-purple-500" />
                <span class="font-bold text-white text-lg">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-white">
                 <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <nav class="flex flex-col space-y-2">
            <a href="{{ route('browse.index', ['type' => 'movie']) }}" class="px-3 py-2 rounded-md text-base font-medium {{ request()->input('type') === 'movie' ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Movies</a>
            <a href="{{ route('browse.index', ['type' => 'tv_show']) }}" class="px-3 py-2 rounded-md text-base font-medium {{ request()->input('type') === 'tv_show' ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">TV Shows</a>
            <a href="{{ route('browse.index') }}" class="px-3 py-2 rounded-md text-base font-medium {{ !request()->input('type') && request()->routeIs('browse.index') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">Browse</a>

            <hr class="border-gray-700 my-4">

            @auth
                 @if(Auth::user()->isAdmin())
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Admin Dashboard</a>
                @endif
                <a href="{{ route('profile.show') }}" class="px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">My Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
                        Log Out
                    </a>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Log In</a>
                <a href="{{ route('register') }}" class="px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Register</a>
            @endauth
        </nav>
    </div>
</header>

        <main class="container mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
            @yield('content')
        </main>
    </div>
</body>
</html>