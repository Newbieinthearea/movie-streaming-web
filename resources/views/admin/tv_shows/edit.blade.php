<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit TV Show') }}: {{ $tv_show->title }}
        </h2>
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
                    <form method="POST" action="{{ route('admin.tv-shows.update', ['tv_show' => $tv_show->id]) }}"> {{-- Pass TVShow ID --}}
                        @csrf
                        @method('PUT') {{-- Important: Use PUT or PATCH for updates --}}

                        <div class="mt-4">
                            <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Title') }}</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="title" value="{{ old('title', $tv_show->title) }}" required />
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Description') }}</label>
                            <textarea id="description" name="description" rows="5"
                                      class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $tv_show->description) }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="poster_url" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Poster URL') }}</label>
                            <input id="poster_url" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="url" name="poster_url" value="{{ old('poster_url', $tv_show->poster_url) }}" />
                        </div>

                        <div class="mt-4">
                            <label for="release_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Release Date (First Episode)') }}</label>
                            <input id="release_date" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="date" name="release_date" value="{{ old('release_date', $tv_show->release_date ? $tv_show->release_date->format('Y-m-d') : '') }}" />
                        </div>

                        <div class="mt-4">
                            <label for="status" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                            <select id="status" name="status" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Status</option>
                                @php
                                    $statuses = ['Returning Series', 'Ended', 'Canceled', 'In Development', 'Pilot'];
                                @endphp
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" @if(old('status', $tv_show->status) == $statusOption) selected @endif>{{ $statusOption }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label for="tmdb_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('TMDB ID (Optional)') }}</label>
                            <input id="tmdb_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   type="text" name="tmdb_id" value="{{ old('tmdb_id', $tv_show->tmdb_id) }}" />
                        </div>

                        <div class="mt-6">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Genres') }}</label>
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @forelse ($genres as $genre)
                                    <label for="genre_{{ $genre->id }}" class="inline-flex items-center">
                                        <input type="checkbox" name="genres[]" value="{{ $genre->id }}" id="genre_{{ $genre->id }}"
                                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:checked:bg-indigo-600"
                                               @if(is_array(old('genres')) ? in_array($genre->id, old('genres')) : (isset($selectedGenreIds) && in_array($genre->id, $selectedGenreIds))) checked @endif >
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $genre->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400 col-span-full">No genres available.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.tv-shows.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4 underline">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Update TV Show') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>