@extends('layouts.public')

@section('title', 'All TV Shows')

@section('content')
    <div class="space-y-8">
        <section>
            <!-- Page Header -->
            <h1 class="text-3xl font-bold text-white mb-4">Browse TV Shows</h1>

            <!-- Filter Bar with Dropdowns -->
            <div class="bg-gray-800/50 p-4 rounded-lg flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <label for="genre-filter" class="text-sm font-semibold text-gray-400">Genre</label>
                    <select id="genre-filter" name="genre"
                            onchange="window.location = this.value;"
                            class="mt-1 block w-24 h-12 bg-gray-700 border-gray-600 text-white rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-center">
                        <option value="{{ route('tv-shows.index') }}" {{ !request('genre') || request('genre') === 'all' ? 'selected' : '' }}>
                            Choose
                        </option>

                        @foreach ($genres as $genre)
                            <option value="{{ route('tv-shows.index', ['genre' => $genre->slug]) }}"
                                    {{ request('genre') == $genre->slug ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Results Grid -->
            <div class="mt-8">
                @if($tvShows->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                        @foreach ($tvShows as $tvShow)
                            <x-tv-show-card :item="$tvShow" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $tvShows->links() }}
                    </div>
                @else
                    <div class="text-center text-gray-500 py-16">
                        <p class="text-lg">No TV shows found matching your criteria.</p>
                        <a href="{{ route('tv-shows.index') }}" class="mt-4 inline-block text-purple-400 hover:text-purple-300 underline">Clear all filters</a>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
