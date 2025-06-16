<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Episode for Season') }} {{ $season->season_number }}
            <span class="text-base text-gray-600 dark:text-gray-400"> ({{ $season->tvShow->title }})</span>
        </h2>
        <div class="mt-2">
            <a href="{{ route('admin.seasons.episodes.index', $season) }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                &larr; Back to Episodes for Season {{ $season->season_number }}
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

                    <form method="POST" action="{{ route('admin.seasons.episodes.store', $season) }}"> {{-- POST to the store route for this Season --}}
                        @csrf

                        <div class="mt-4">
                            <label for="episode_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Episode Number') }}</label>
                            <input id="episode_number" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="number" name="episode_number" value="{{ old('episode_number') }}" required min="0" />
                        </div>

                        <div class="mt-4">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Episode Title') }}</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="title" value="{{ old('title') }}" required />
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Description (Optional)') }}</label>
                            <textarea id="description" name="description" rows="3"
                                      class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="release_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Release Date (Optional)') }}</label>
                            <input id="release_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="date" name="release_date" value="{{ old('release_date') }}" />
                        </div>

                        <div class="mt-4">
                            <label for="duration" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Duration (minutes) (Optional)') }}</label>
                            <input id="duration" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="number" name="duration" value="{{ old('duration') }}" min="0" />
                        </div>

                        <div class="mt-4">
                            <label for="imdb_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Episode IMDB ID (Optional)') }}</label>
                            <input id="imdb_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="imdb_id" value="{{ old('imdb_id') }}" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">e.g., tt1234567. For VidSrc API.</p>
                        </div>

                        <div class="mt-4">
                            <label for="tmdb_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Episode TMDB ID (Optional)') }}</label>
                            <input id="tmdb_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="tmdb_id" value="{{ old('tmdb_id') }}" />
                        </div>

                        <div class="mt-4">
                            <label for="custom_sub_url" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Custom Subtitle URL (.srt/.vtt) (Optional)') }}</label>
                            <input id="custom_sub_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="url" name="custom_sub_url" value="{{ old('custom_sub_url') }}" />
                        </div>

                        <div class="mt-4">
                            <label for="default_sub_lang" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Default Subtitle Language (ISO 639 code) (Optional)') }}</label>
                            <input id="default_sub_lang" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="default_sub_lang" value="{{ old('default_sub_lang') }}" placeholder="e.g., en, es" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.seasons.episodes.index', $season) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Save Episode') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>