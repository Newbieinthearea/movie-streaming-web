@extends('layouts.public')

@section('title', $query ? 'Search Results for "' . e($query) . '"' : 'Browse')

@section('content')
    <div class="space-y-8">
        <section>
            {{-- Page Title --}}
            @if($query)
                <h1 class="text-3xl font-bold text-white mb-6">
                    Search Results for: <span class="text-purple-400">"{{ e($query) }}"</span>
                </h1>
            @else
                <h1 class="text-3xl font-bold text-white mb-6">
                    Browse Content
                </h1>
            @endif

            {{-- Conditionally display the filter form only when not searching --}}
            @if(!$query)
                {{-- Initialize Alpine.js component with genre data --}}
                <div x-data="{
                        movieGenres: {{ json_encode($movieGenres) }},
                        tvGenres: {{ json_encode($tvGenres) }},
                        selectedType: '{{ $currentType }}',
                        get currentGenres() {
                            return this.selectedType === 'tv_show' ? this.tvGenres : this.movieGenres;
                        }
                    }" 
                    class="bg-gray-800/50 p-6 rounded-xl">
                    <h2 class="text-xl font-semibold text-white mb-4">Filter Options</h2>
                    <form action="{{ route('browse.index') }}" method="GET">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
                            {{-- Type Filter --}}
                            <div>
                                <label for="type-filter" class="block text-sm font-medium text-gray-400 mb-1">Type</label>
                                <select id="type-filter" name="type" x-model="selectedType" class="w-full bg-gray-700 border-2 border-transparent text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 transition">
                                    <option value="movie">Movies</option>
                                    <option value="tv_show">TV Shows</option>
                                </select>
                            </div>

                            {{-- Dynamic Genre Filter --}}
                            <div>
                                <label for="genre-filter" class="block text-sm font-medium text-gray-400 mb-1">Genre</label>
                                <select id="genre-filter" name="genre" class="w-full bg-gray-700 border-2 border-transparent text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 transition">
                                    <option value="">All Genres</option>
                                    {{-- Loop through the dynamic genre list from Alpine.js --}}
                                    <template x-for="genre in currentGenres" :key="genre.id">
                                        <option :value="genre.id" :selected="genre.id == '{{ request('genre') }}'" x-text="genre.name"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- Year Filter --}}
                            <div>
                                <label for="year-filter" class="block text-sm font-medium text-gray-400 mb-1">Year</label>
                                <select id="year-filter" name="year" class="w-full bg-gray-700 border-2 border-transparent text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 transition">
                                    <option value="">Any Year</option>
                                     @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Sort By Filter --}}
                            <div>
                                <label for="sort-filter" class="block text-sm font-medium text-gray-400 mb-1">Sort By</label>
                                <select id="sort-filter" name="sort" class="w-full bg-gray-700 border-2 border-transparent text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 transition">
                                    <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
                                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popularity</option>
                                    <option value="top_rated" {{ request('sort') == 'top_rated' ? 'selected' : '' }}>Top Rated</option>
                                </select>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="w-full flex items-center justify-end space-x-3">
                                <a href="{{ route('browse.index') }}" class="px-5 py-2.5 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-500 transition-colors">
                                    Reset
                                </a>
                                <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors flex items-center">
                                    <span class="material-symbols-outlined mr-1 text-base">filter_alt</span>
                                    Apply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
            
            <div class="mt-8">
                @if($query && empty($results))
                    <div class="text-center text-gray-500 py-16">
                        <p class="text-lg">No results found for "{{ e($query) }}".</p>
                        <p class="text-sm mt-2">Try a different search term or check the spelling.</p>
                    </div>
                @elseif(!empty($results))
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
                        @foreach ($results as $item)
                            <x-api-content-card :item="$item" />
                        @endforeach
                    </div>

                    {{-- Simple Pagination --}}
                    @if($paginationData['total_pages'] > 1)
                    <div class="mt-8 flex justify-center text-white">
                        @if($paginationData['current_page'] > 1)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $paginationData['current_page'] - 1]) }}" class="px-4 py-2 bg-gray-700 rounded-l-lg hover:bg-purple-600">&laquo; Prev</a>
                        @endif
                        <span class="px-4 py-2 bg-gray-800">Page {{ $paginationData['current_page'] }} of {{ min($paginationData['total_pages'], 500) }}</span>
                        @if($paginationData['current_page'] < $paginationData['total_pages'] && $paginationData['current_page'] < 500)
                            <a href="{{ request()->fullUrlWithQuery(['page' => $paginationData['current_page'] + 1]) }}" class="px-4 py-2 bg-gray-700 rounded-r-lg hover:bg-purple-600">Next &raquo;</a>
                        @endif
                    </div>
                    @endif
                @else
                    <div class="text-center text-gray-500 py-16">
                        <p class="text-lg">No content found matching your criteria.</p>
                        <a href="{{ route('browse.index') }}" class="mt-4 inline-block text-purple-400 hover:text-purple-300 underline">Clear all filters</a>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection