@extends('layouts.public')

@section('title', 'All Movies')

@section('content')
    <div class="space-y-8">
        <section>
            <!-- Page Header -->
            <h1 class="text-3xl font-bold text-white mb-4">Browse Movies</h1>

            <!-- Filter Bar with Dropdowns -->
            <div class="bg-gray-800/50 p-4 rounded-lg flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <label for="genre-filter" class="text-sm font-semibold text-gray-400">Genre</label>
                    <select id="genre-filter" name="genre"
                            onchange="window.location = this.value;"
                            class="mt-1 block w-24 h-12 bg-gray-700 border-gray-600 text-white rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm text-center">
                        
                        <option value="{{ route('movies.index') }}" {{ !request('genre') || request('genre') === 'all' ? 'selected' : '' }}>
                            Choose
                        </option>

                        @foreach ($genres as $genre)
                            <option value="{{ route('movies.index', ['genre' => $genre->slug]) }}" 
                                    {{ request('genre') == $genre->slug ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Add other dropdowns here for Year, Status, etc., following the same pattern --}}

            </div>
            
            <!-- Results Grid -->
            <div class="mt-8">
                @if($movies->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                        @foreach ($movies as $movie)
                            <x-movie-card :item="$movie" />
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $movies->links() }}
                    </div>
                @else
                    <div class="text-center text-gray-500 py-16">
                        <p class="text-lg">No movies found matching your criteria.</p>
                        <a href="{{ route('movies.index') }}" class="mt-4 inline-block text-purple-400 hover:text-purple-300 underline">Clear all filters</a>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
