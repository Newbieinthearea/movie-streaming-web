@extends('layouts.public')

@section('title', 'Homepage')

@section('content')

{{-- Hero Slideshow Section --}}
@if(!empty($trendingSlides))
<section class="hero-slideshow -mx-4 -mt-8 mb-12">
    <div x-data="{
            activeSlide: 1,
            slides: {{ json_encode(
                collect($trendingSlides)->map(function($slide) {
                    $type = $slide['media_type'];
                    $id = $slide['id'];
                    $title = $slide['title'] ?? $slide['name'];
                    $watchUrl = '#';
                    if ($type === 'movie') {
                        $watchUrl = route('movies.watch', $id);
                    } elseif ($type === 'tv') {
                        $watchUrl = route('tv-shows.show', $id);
                    }

                    return [
                        'title' => $title,
                        'description' => \Illuminate\Support\Str::limit($slide['overview'], 150),
                        'image' => 'https://image.tmdb.org/t/p/original' . $slide['backdrop_path'],
                        'url' => $type === 'movie' ? route('movies.show', $id) : route('tv-shows.show', $id),
                        'watch_url' => $watchUrl,
                    ];
                })
            ) }},
            heroHeight: 0,
            setHeroHeight() {
                const header = document.getElementById('main-header');
                if (header) {
                    this.heroHeight = window.innerHeight - header.offsetHeight;
                } else {
                    this.heroHeight = window.innerHeight;
                }
            },
            autoplay() {
                setInterval(() => { this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1 }, 7000)
            }
        }" 
        x-init="
            setHeroHeight();
            window.addEventListener('resize', () => setHeroHeight());
            autoplay();
        "
        class="relative w-full overflow-hidden"
        :style="`height: ${heroHeight}px`">

        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index + 1"
                 class="absolute inset-0 duration-1000 ease-in-out transition-opacity">
                <img :src="slide.image" class="absolute inset-0 h-full w-full object-cover" :alt="slide.title">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            </div>
        </template>

        <div class="relative z-10 flex h-full flex-col justify-end p-8 text-white md:p-16">
            <div x-show="activeSlide" x-transition.opacity.duration.500ms>
                <h2 class="text-4xl font-black uppercase tracking-wider md:text-6xl" x-text="slides[activeSlide - 1].title"></h2>
                <p class="mt-4 max-w-xl text-gray-300" x-text="slides[activeSlide - 1].description"></p>
                <div class="mt-8 flex items-center gap-4">
                    <a :href="slides[activeSlide - 1].watch_url" class="flex items-center rounded-lg bg-purple-600 py-3 px-8 font-bold text-white transition-colors hover:bg-purple-700">
                        <span class="material-symbols-outlined mr-2">play_arrow</span>
                        Watch Now
                    </a>
                     <a :href="slides[activeSlide - 1].url" class="flex items-center rounded-lg border-2 border-gray-500 py-3 px-8 font-bold text-white transition-colors hover:bg-gray-700">
                        <span class="material-symbols-outlined mr-2">info</span>
                        Details
                    </a>
                </div>
            </div>
        </div>

        <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 space-x-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index + 1"
                        :class="{'bg-white': activeSlide === index + 1, 'bg-gray-500': activeSlide !== index + 1}"
                        class="h-3 w-3 rounded-full transition hover:bg-white"></button>
            </template>
        </div>
    </div>
</section>
@endif

<div class="container mx-auto px-4">
    <div class="space-y-12">
        {{-- Continue Watching Section --}}
        @auth
            @if($watchHistory->isNotEmpty())
                <section>
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-white">Continue Watching</h2>
                        <a href="{{ route('profile.show') }}" class="rounded-full border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors hover:border-gray-500 hover:bg-gray-800">See More</a>
                    </div>
                    <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
                        @foreach($watchHistory as $historyItem)
                            @if($historyItem->watchable)
                                <div class="w-40 flex-shrink-0 sm:w-48">
                                    <x-continue-watching-card :historyItem="$historyItem" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif
        @endauth

        {{-- Latest Movies --}}
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">Latest Movies</h2>
                <a href="{{ route('browse.index', ['type' => 'movie', 'sort' => 'latest']) }}" class="rounded-full border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors hover:border-gray-500 hover:bg-gray-800">See More</a>
            </div>
            <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
                @foreach ($latestMovies as $item)
                    <div class="w-40 flex-shrink-0 sm:w-48">
                        <x-api-content-card :item="$item" />
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Popular Movies --}}
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">Popular Movies</h2>
                <a href="{{ route('browse.index', ['type' => 'movie', 'sort' => 'popular']) }}" class="rounded-full border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors hover:border-gray-500 hover:bg-gray-800">See More</a>
            </div>
            <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
                @foreach ($popularMovies as $item)
                    <div class="w-40 flex-shrink-0 sm:w-48">
                        <x-api-content-card :item="$item" />
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Top Rated Movies --}}
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">Top Rated Movies</h2>
                <a href="{{ route('browse.index', ['type' => 'movie', 'sort' => 'top_rated']) }}" class="rounded-full border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors hover:border-gray-500 hover:bg-gray-800">See More</a>
            </div>
            <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
                @foreach ($topRatedMovies as $item)
                    <div class="w-40 flex-shrink-0 sm:w-48">
                        <x-api-content-card :item="$item" />
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Latest TV Shows --}}
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">Latest TV Shows</h2>
                <a href="{{ route('browse.index', ['type' => 'tv_show', 'sort' => 'latest']) }}" class="rounded-full border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors hover:border-gray-500 hover:bg-gray-800">See More</a>
            </div>
            <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
                @foreach ($latestTvShows as $item)
                    <div class="w-40 flex-shrink-0 sm:w-48">
                        <x-api-content-card :item="$item" />
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Popular TV Shows --}}
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">Popular TV Shows</h2>
                <a href="{{ route('browse.index', ['type' => 'tv_show', 'sort' => 'popular']) }}" class="rounded-full border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors hover:border-gray-500 hover:bg-gray-800">See More</a>
            </div>
            <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
                @foreach ($popularTvShows as $item)
                    <div class="w-40 flex-shrink-0 sm:w-48">
                        <x-api-content-card :item="$item" />
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Top Rated TV Shows --}}
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-white">Top Rated TV Shows</h2>
                <a href="{{ route('browse.index', ['type' => 'tv_show', 'sort' => 'top_rated']) }}" class="rounded-full border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-colors hover:border-gray-500 hover:bg-gray-800">See More</a>
            </div>
            <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
                @foreach ($topRatedTvShows as $item)
                    <div class="w-40 flex-shrink-0 sm:w-48">
                        <x-api-content-card :item="$item" />
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection