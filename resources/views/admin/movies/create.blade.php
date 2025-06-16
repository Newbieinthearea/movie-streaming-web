<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Movie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Display any validation errors --}}
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

                    <form method="POST" action="{{ route('admin.movies.store') }}">
                        @csrf

                        <div class="mt-4">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Title') }}</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="title" value="{{ old('title') }}" required autofocus />
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                            <textarea id="description" name="description" rows="5"
                                      class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="poster_url" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Poster URL') }}</label>
                            <input id="poster_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="url" name="poster_url" value="{{ old('poster_url') }}" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Full URL to the movie poster image.</p>
                        </div>

                        <div class="mt-4">
                            <label for="release_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Release Date') }}</label>
                            <input id="release_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="date" name="release_date" value="{{ old('release_date') }}" />
                        </div>

                        <div class="mt-4">
                            <label for="duration" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Duration (minutes)') }}</label>
                            <input id="duration" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="number" name="duration" value="{{ old('duration') }}" min="0" />
                        </div>

                        <div class="mt-4">
    <label for="tmdb_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('TMDB ID (Required)') }}</label>
    <input id="tmdb_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 ..."
           type="text" name="tmdb_id" value="{{ old('tmdb_id', $movie->tmdb_id ?? '') }}" required />
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The Movie Database ID. Required for streaming.</p>
</div>

<div class="mt-4">
    <label for="imdb_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('IMDB ID (Optional)') }}</label>
    <input id="imdb_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 ..."
           type="text" name="imdb_id" value="{{ old('imdb_id', $movie->imdb_id ?? '') }}" />
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">e.g., tt0123456. Optional, for reference.</p>
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

                        <div class="mt-6">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Genres') }}</label>
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @forelse ($genres as $genre)
                                    <label for="genre_{{ $genre->id }}" class="inline-flex items-center">
                                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}" id="genre_{{ $genre->id }}"
                                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:checked:bg-indigo-600"
                                               @if(is_array(old('genres')) && in_array($genre->id, old('genres'))) checked @endif >
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $genre->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400 col-span-full">No genres available. Please add genres first.</p>
                                @endforelse
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.movies.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Save Movie') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>