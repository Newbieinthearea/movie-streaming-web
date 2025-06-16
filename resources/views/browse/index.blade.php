
@extends('layouts.public')

@section('title', $query ? 'Search Results for "' . e($query) . '"' : 'Browse')

@section('content')
    <div class="space-y-8">
        <section>
            @if($query)
                <h1 class="text-3xl font-bold text-white mb-6">
                    Search Results for: <span class="text-purple-400">"{{ e($query) }}"</span>
                </h1>
            @endif

            <form action="{{ route('browse.index') }}" method="GET" class="bg-gray-800/50 p-4 rounded-xl">
                {{-- Keep search query when filtering --}}
                @if($query)
                    <input type="hidden" name="q" value="{{ $query }}">
                @endif
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                        <div class="w-full">
                        <label for="type-filter" class="text-xs font-semibold text-gray-400">Type</label>
                        <select id="type-filter" name="type" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            @if($query)
                                <option value="multi" {{ $currentType == 'multi' ? 'selected' : '' }}>All</option>
                            @endif
                            <option value="movie" {{ $currentType == 'movie' ? 'selected' : '' }}>Movies</option>
                            <option value="tv_show" {{ $currentType == 'tv_show' ? 'selected' : '' }}>TV Shows</option>
                        </select>
                    </div>

                    <div class="w-full">
                        <label for="genre-filter" class="text-xs font-semibold text-gray-400">Genre</label>
                        <select id="genre-filter" name="genre" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            <option value="">All Genres</option>
                            @foreach ($genres as $genre)
                                <option value="{{ $genre['id'] }}" {{ request('genre') == $genre['id'] ? 'selected' : '' }}>
                                    {{ $genre['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full">
                        <label for="year-filter" class="text-xs font-semibold text-gray-400">Year</label>
                        <select id="year-filter" name="year" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            <option value="">Any Year</option>
                             @foreach ($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(!$query)
                    <div class="w-full">
                        <label for="sort-filter" class="text-xs font-semibold text-gray-400">Sort By</label>
                        <select id="sort-filter" name="sort" class="mt-1 block w-full bg-gray-700 border-gray-600 text-white rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popularity</option>
                            <option value="top_rated" {{ request('sort') == 'top_rated' ? 'selected' : '' }}>Top Rated</option>
                        </select>
                    </div>
                    @endif

                    <div class="w-full lg:col-span-2 flex justify-end items-end space-x-3">
                        <a href="{{ route('browse.index', ['q' => request('q')]) }}" class="w-full lg:w-auto text-center px-5 py-2.5 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-500 transition-colors">
                            Reset Filters
                        </a>
                        <button type="submit" class="w-full lg:w-auto text-center px-5 py-2.5 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                            Apply
                        </button>
                    </div>
                </div>
            </form>
            
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
                @endif
            </div>
        </section>
    </div>
@endsection