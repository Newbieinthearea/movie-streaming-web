{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin CMS Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Welcome, {{ Auth::user()->name }}!</h3>
                    <p class="mb-6">Select a section below to manage your site's content.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
                            <h4 class="font-semibold text-lg mb-2">Genres</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Manage movie and TV show genres.
                            </p>
                            <a href="{{ route('admin.genres.index') }}" {{-- This route doesn't exist yet, we'll create it next --}}
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Manage Genres
                            </a>
                        </div>

                        <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow opacity-50">
                            <h4 class="font-semibold text-lg mb-2">Movies</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                Manage movie entries and details.
                            </p>
                            <a href="{{route('admin.movies.index')}}" class="inline-flex items-center px-4 py-2 bg-blue-600 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Manage Movies
                            </a>
                        </div>

                        <div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
    <h4 class="font-semibold text-lg mb-2">TV Shows</h4>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
        Manage TV shows, seasons, and episodes.
    </p>
    <a href="{{ route('admin.tv-shows.index') }}"
       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        Manage TV Shows
    </a>
</div>
<div class="p-6 bg-gray-50 dark:bg-gray-700 rounded-lg shadow">
    <h4 class="font-semibold text-lg mb-2">Content Moderation</h4>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
        Block movies or TV shows from appearing on the site.
    </p>
    <a href="{{ route('admin.moderation.index') }}"
       class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500">
        Manage Blocklist
    </a>
</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>