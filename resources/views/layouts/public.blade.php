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

        <header class="py-4" id="main-header">
            <nav class="flex items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}">
                            <span class="animated-gradient text-3xl font-extrabold tracking-wider">STREAME</span>
                        </a>
                    </div>
                    <div class="hidden lg:flex items-center space-x-2">
                        <a href="{{ route('browse.index', ['type' => 'movie']) }}" class="flex items-center gap-1.5 rounded-full px-4 py-2.5 text-sm font-semibold transition-colors {{ request('type') == 'movie' ? 'bg-white text-black' : 'text-gray-300 hover:bg-gray-700' }}">
                            <span class="material-symbols-outlined">movie</span>
                            <span class="hidden sm:inline">Movies</span>
                        </a>
                        <a href="{{ route('browse.index', ['type' => 'tv_show']) }}" class="flex items-center gap-1.5 rounded-full px-4 py-2.5 text-sm font-semibold transition-colors {{ request('type') == 'tv_show' ? 'bg-white text-black' : 'text-gray-300 hover:bg-gray-700' }}">
                            <span class="material-symbols-outlined">tv_gen</span>
                            <span class="hidden sm:inline">TV Shows</span>
                        </a>
                        <a href="{{ route('browse.index') }}" class="flex items-center gap-1.5 rounded-full px-4 py-2.5 text-sm font-semibold transition-colors {{ request()->routeIs('browse.index') && !request('type') ? 'bg-white text-black' : 'text-gray-300 hover:bg-gray-700' }}">
                            <span class="material-symbols-outlined">explore</span>
                            <span class="hidden sm:inline">Browse</span>
                        </a>
                    </div>
                </div>

                <div class="flex flex-1 items-center justify-end gap-4">
                    <div class="hidden max-w-xs flex-1 justify-end md:flex">
                        <form action="{{ route('browse.index') }}" method="GET" class="relative w-full">
                            <label for="search" class="sr-only">Search</label>
                            <input id="search" name="q" value="{{ request('q') }}"
                                   class="h-11 w-full rounded-full border border-gray-700 bg-gray-800/80 py-2 pl-10 pr-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                   placeholder="Search..." type="search">
                            <input type="hidden" name="type" value="multi">
                            <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="material-symbols-outlined text-gray-400">search</span>
                            </button>
                        </form>
                    </div>
    
                    <div class="hidden items-center space-x-2 lg:flex">
                        <div class="h-6 w-px bg-gray-700"></div>
    
                        @guest
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-300 hover:text-white">Login</a>
                            <a href="{{ route('register') }}" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-purple-700">Register</a>
                        @else
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center rounded-full border border-transparent px-3 py-2 text-sm font-medium leading-4 text-gray-300 transition duration-150 ease-in-out hover:text-white focus:outline-none">
                                        <div>{{ Auth::user()->name }}</div>
                                        <div class="ms-1">
                                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
            
                                <x-slot name="content">
                                    @if(Auth::user()->isAdmin())
                                        <x-dropdown-link :href="route('dashboard')">
                                            {{ __('Admin Dashboard') }}
                                        </x-dropdown-link>
                                    @endif
                                    <x-dropdown-link :href="route('profile.show')">
                                        {{ __('My Profile') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Edit Profile') }}
                                    </x-dropdown-link>
                                    <div class="my-1 border-t border-gray-600"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @endguest
                    </div>
    
                    <div class="flex items-center lg:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white">
                            <span class="sr-only">Open menu</span>
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                    </div>
                </div>
            </nav>
        </header>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex lg:hidden" style="display: none;">
            
            <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm" aria-hidden="true"></div>
            
            <div class="relative flex w-full max-w-xs flex-1 flex-col border-r border-gray-700 bg-gray-900">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="mobileMenuOpen = false" type="button" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <span class="material-symbols-outlined text-white">close</span>
                    </button>
                </div>
                
                <div class="px-2 mb-4 m-3">
        <form action="{{ route('browse.index') }}" method="GET" class="relative">
            <input name="q" class="w-full rounded-full border-gray-600 bg-gray-700 py-2 pl-10 pr-4 text-white placeholder-gray-400 focus:border-purple-500 focus:ring-purple-500" placeholder="Search..." type="search">
            <input type="hidden" name="type" value="multi">
            <button type="submit" class="absolute inset-y-0 left-0 flex items-center pl-3">
                <span class="material-symbols-outlined text-gray-400">search</span>
            </button>
        </form>
    </div>
                <div class="h-0 flex-1 overflow-y-auto pt-5 pb-4">
                    <div class="mb-4 flex flex-shrink-0 items-center px-4">
                        <a href="{{ route('home') }}" class="text-3xl font-extrabold tracking-wider text-white">STREAME</a>
                    </div>
                    <nav class="mt-5 space-y-1 px-2">
                        <a href="{{ route('browse.index', ['type' => 'movie']) }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-base font-medium {{ request()->routeIs('movies.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <span class="material-symbols-outlined">movie</span>
                            <span>Movies</span>
                        </a>
                        <a href="{{ route('browse.index', ['type' => 'tv_show']) }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-base font-medium {{ request()->routeIs('tv-shows.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                             <span class="material-symbols-outlined">tv_gen</span>
                            <span>TV Shows</span>
                        </a>
                        <a href="{{ route('browse.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 text-base font-medium {{ request()->routeIs('browse.*') ? 'bg-purple-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            <span class="material-symbols-outlined">explore</span>
                            <span>Browse All</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <main class="mt-8">
            @yield('content')
        </main>

        <footer class="mt-12 border-t border-gray-800 py-8 text-center">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} STREAME. All rights reserved.</p>
        </footer>

    </div>

</body>
</html>