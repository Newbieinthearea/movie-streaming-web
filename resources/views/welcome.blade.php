@extends('layouts.public')

@section('title', 'Homepage')

@section('content')
    {{-- Hero Slideshow Section --}}
    @if(!empty($trendingSlides))
        <div x-data="{
                slides: {{ json_encode($trendingSlides) }},
                activeSlide: 1,
                autoplay: null,
                startAutoplay() {
                    this.autoplay = setInterval(() => {
                        this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1;
                    }, 8000);
                },
                stopAutoplay() {
                    clearInterval(this.autoplay);
                },
                getMediaType(slide) {
                    return slide.media_type === 'tv' ? 'tv-shows' : 'movies';
                },
                getDetailsUrl(slide) {
                    let mediaType = this.getMediaType(slide);
                    return `/${mediaType}/${slide.id}`;
                },
                getWatchUrl(slide) {
                    let mediaType = this.getMediaType(slide);
                    if (mediaType === 'tv-shows') {
                        // Default to the details page for TV shows, as we don't know the first episode here
                        return `/${mediaType}/${slide.id}`;
                    }
                    return `/${mediaType}/${slide.id}/watch`;
                }
             }"
             x-init="startAutoplay()"
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()"
             class="relative w-full h-[60vh] md:h-[80vh] -mt-8 -mx-4 sm:-mx-6 lg:-mx-8 mb-8">

            {{-- Slides --}}
            @foreach($trendingSlides as $index => $slide)
                <div x-show="activeSlide === {{ $index + 1 }}"
                     class="absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out"
                     x-transition:enter="opacity-0"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="opacity-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    {{-- Background Image --}}
                    <div class="absolute inset-0 bg-cover bg-center"
                         style="background-image: linear-gradient(to top, rgba(17,24,39,1) 10%, rgba(17,24,39,0.5) 50%, rgba(17,24,39,1) 100%), url('https://image.tmdb.org/t/p/w1280{{ $slide['backdrop_path'] }}');">
                    </div>

                    {{-- Content --}}
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-end pb-16 md:pb-24">
                        <div class="max-w-2xl text-white">
                            <h2 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight">{{ $slide['title'] ?? $slide['name'] }}</h2>
                            <div class="flex items-center flex-wrap gap-x-4 gap-y-2 text-gray-300 font-medium mb-4">
                                <span class="flex items-center">
                                    <span class="material-symbols-outlined text-yellow-400 text-base mr-1">star</span>
                                    <span>{{ number_format($slide['vote_average'], 1) }} / 10</span>
                                </span>
                                <span class="opacity-50">•</span>
                                <span>{{ \Carbon\Carbon::parse($slide['release_date'] ?? $slide['first_air_date'])->format('Y') }}</span>
                            </div>
                            <p class="text-gray-300 leading-relaxed mb-8 max-w-2xl line-clamp-3">{{ $slide['overview'] }}</p>
                            <div class="flex items-center space-x-4">
                                <a :href="getWatchUrl(slides[{{ $index }}])" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg transition-colors flex items-center">
                                    <span class="material-symbols-outlined mr-2">play_arrow</span>
                                    Play
                                </a>
                                <a :href="getDetailsUrl(slides[{{ $index }}])" class="border-2 border-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg transition-colors flex items-center">
                                    <span class="material-symbols-outlined mr-2">info</span>
                                    Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Navigation Dots --}}
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-3">
                @foreach($trendingSlides as $index => $slide)
                    <button @click="activeSlide = {{ $index + 1 }}"
                            :class="{'bg-purple-500': activeSlide === {{ $index + 1 }}, 'bg-gray-500/50': activeSlide !== {{ $index + 1 }} }"
                            class="w-3 h-3 rounded-full hover:bg-purple-400 transition-colors"></button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- The rest of your content remains the same --}}
    {{-- Continue Watching, Latest Movies, etc. --}}
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
@endsection