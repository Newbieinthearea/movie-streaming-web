<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Season') }} {{ $season->season_number }} {{ __('for TV Show') }}: {{ $tv_show->title }}
        </h2>
        <div class="mt-2">
            <a href="{{ route('admin.tv-shows.seasons.index', $tv_show) }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                &larr; Back to Seasons for {{ $tv_show->title }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600 dark:text-red-400">{{ __('Whoops! Something went wrong.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.tv-shows.seasons.update', [$tv_show, $season]) }}">
                        @csrf
                        @method('PUT') {{-- Use PUT or PATCH for updates --}}

                        <div class="mt-4">
                            <label for="season_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Season Number') }}</label>
                            <input id="season_number" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="number" name="season_number" value="{{ old('season_number', $season->season_number) }}" required min="0" />
                        </div>

                        <div class="mt-4">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Season Title (Optional)') }}</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="title" value="{{ old('title', $season->title) }}" />
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Description (Optional)') }}</label>
                            <textarea id="description" name="description" rows="3"
                                      class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $season->description) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="release_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Release Date (Optional)') }}</label>
                            <input id="release_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="date" name="release_date" value="{{ old('release_date', $season->release_date ? $season->release_date->format('Y-m-d') : '') }}" />
                        </div>

                        <div class="mt-4">
                            <label for="poster_url" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Poster URL (Optional)') }}</label>
                            <input id="poster_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="url" name="poster_url" value="{{ old('poster_url', $season->poster_url) }}" />
                        </div>

                        <div class="mt-4">
                            <label for="tmdb_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Season TMDB ID (Optional)') }}</label>
                            <input id="tmdb_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="tmdb_id" value="{{ old('tmdb_id', $season->tmdb_id) }}" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.tv-shows.seasons.index', $tv_show) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Update Season') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>