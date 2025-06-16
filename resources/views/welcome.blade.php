@extends('layouts.public')

@section('title', 'Homepage')

@section('content')
    <div class="space-y-12">
        {{-- Trending Slides Section --}}
        @if(!empty($trendingSlides))
        <section x-data="{
            activeSlide: 1,
            slides: {{ json_encode(
                collect($trendingSlides)->map(function($slide) {
                    return [
                        'title' => $slide['title'],
                        'description' => Str::limit($slide['overview'], 150),
                        'image' => 'https://image.tmdb.org/t/p/w1280' . $slide['backdrop_path'],
                        'url' => route('movies.show', $slide['id']),
                        'watch_url' => route('movies.watch', $slide['id'])
                    ];
                })
            ) }},
            autoplay() {
                setInterval(() => { this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1 }, 5000)
            }
        }" x-init="autoplay()">
            {{-- Your existing slide HTML --}}
        </section>
        @endif

        {{-- Continue Watching Section --}}
        @auth
            @if($watchHistory->isNotEmpty())
                <section>
                    <h2 class="text-2xl font-semibold text-white mb-4">Continue Watching</h2>
                    <div class="grid grid-cols-[repeat(auto-fill,minmax(180px,1fr))] gap-4 md:gap-6">
                        @foreach($watchHistory as $historyItem)
                            @if($historyItem->watchable)
                                <x-continue-watching-card :historyItem="$historyItem" />
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif
        @endauth

        {{-- Latest Movies --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-white">Latest Movies</h2>
                <a href="{{ route('browse.index', ['type' => 'movie', 'sort' => 'latest']) }}" class="text-sm text-purple-400 hover:underline">See More</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                @foreach ($latestMovies as $item)
                    <x-api-content-card :item="$item" />
                @endforeach
            </div>
        </section>

        {{-- Popular Movies --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-white">Popular Movies</h2>
                <a href="{{ route('browse.index', ['type' => 'movie', 'sort' => 'popular']) }}" class="text-sm text-purple-400 hover:underline">See More</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                @foreach ($popularMovies as $item)
                    <x-api-content-card :item="$item" />
                @endforeach
            </div>
        </section>

        {{-- Top Rated Movies --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-white">Top Rated Movies</h2>
                <a href="{{ route('browse.index', ['type' => 'movie', 'sort' => 'top_rated']) }}" class="text-sm text-purple-400 hover:underline">See More</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                @foreach ($topRatedMovies as $item)
                    <x-api-content-card :item="$item" />
                @endforeach
            </div>
        </section>

        {{-- Latest TV Shows --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-white">Latest TV Shows</h2>
                <a href="{{ route('browse.index', ['type' => 'tv_show', 'sort' => 'latest']) }}" class="text-sm text-purple-400 hover:underline">See More</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                @foreach ($latestTvShows as $item)
                    <x-api-content-card :item="$item" />
                @endforeach
            </div>
        </section>

        {{-- Popular TV Shows --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-white">Popular TV Shows</h2>
                <a href="{{ route('browse.index', ['type' => 'tv_show', 'sort' => 'popular']) }}" class="text-sm text-purple-400 hover:underline">See More</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                @foreach ($popularTvShows as $item)
                    <x-api-content-card :item="$item" />
                @endforeach
            </div>
        </section>

        {{-- Top Rated TV Shows --}}
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-white">Top Rated TV Shows</h2>
                <a href="{{ route('browse.index', ['type' => 'tv_show', 'sort' => 'top_rated']) }}" class="text-sm text-purple-400 hover:underline">See More</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
                @foreach ($topRatedTvShows as $item)
                    <x-api-content-card :item="$item" />
                @endforeach
            </div>
        </section>
    </div>
@endsection