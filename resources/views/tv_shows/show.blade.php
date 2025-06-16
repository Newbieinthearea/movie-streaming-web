@extends('layouts.public')

@section('title', $tvShow['name'])

@section('content')
    @php
        // Helper to find the official trailer from the 'videos' results
        $trailer = collect($tvShow['videos']['results'] ?? [])->firstWhere('type', 'Trailer');
    @endphp
    <div>
        <section class="relative h-[60vh] md:h-[80vh] bg-cover bg-center bg-no-repeat" style="background-image: linear-gradient(rgba(18,18,18,0.7), rgba(18,18,18,1)), url('https://image.tmdb.org/t/p/w1280{{ $tvShow['backdrop_path'] }}');">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-end pb-12">
                <div class="max-w-3xl text-white">
                    <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight">{{ $tvShow['name'] }}</h1>

                    <div class="flex items-center flex-wrap gap-x-4 gap-y-2 text-gray-300 font-medium mb-4">
                        @if(!empty($tvShow['first_air_date']))
                            <span>First Aired: {{ \Carbon\Carbon::parse($tvShow['first_air_date'])->format('Y') }}</span>
                            <span class="opacity-50">•</span>
                        @endif
                        <span>{{ $tvShow['number_of_seasons'] }} Seasons</span>
                        <span class="opacity-50">•</span>
                        <span class="flex items-center">
                            <span class="material-symbols-outlined text-yellow-400 text-base mr-1">star</span>
                            <span>{{ number_format($tvShow['vote_average'], 1) }} / 10</span>
                        </span>
                         <span class="opacity-50">•</span>
                         <span class="capitalize">{{ $tvShow['status'] }}</span>
                    </div>

                    @if(!empty($tvShow['genres']))
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($tvShow['genres'] as $genre)
                            <a href="{{ route('browse.index', ['genre' => $genre['id'], 'type' => 'tv_show']) }}" class="px-3 py-1 bg-gray-700/80 hover:bg-purple-600 text-gray-300 hover:text-white text-xs font-semibold rounded-full transition-colors">
                                {{ $genre['name'] }}
                            </a>
                        @endforeach
                    </div>
                    @endif

                    <p class="text-gray-300 leading-relaxed mb-8 max-w-2xl">{{ $tvShow['overview'] }}</p>

                    <div class="flex items-center space-x-4">
                        {{-- Find the first episode of the first season to link to --}}
                        @php
                            $firstEpisode = collect($tvShow['seasons'] ?? [])->first(fn($s) => $s['season_number'] > 0)['episodes'][0] ?? null;
                        @endphp
                        @if($firstEpisode)
                            {{-- This watch link will need to be adjusted later when we re-implement the watch page with API data --}}
                            <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg transition-colors flex items-center">
                                <span class="material-symbols-outlined mr-2">play_arrow</span>
                                Play S1 E1
                            </a>
                        @endif
                        @if($trailer)
                        <a href="https://www.youtube.com/watch?v={{ $trailer['key'] }}" target="_blank" class="border-2 border-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg transition-colors flex items-center">
                             <span class="material-symbols-outlined mr-2">movie</span>
                            Trailer
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-12">
            <h2 class="text-2xl font-semibold text-white mb-4">Seasons & Episodes</h2>
            <div class="space-y-3" x-data="{ openSeason: 1 }">
                @forelse($tvShow['seasons'] as $season)
                    @if($season['season_number'] > 0) {{-- Often season 0 is for "Specials", we can skip it --}}
                    <div class="bg-gray-800/70 rounded-lg overflow-hidden">
                        <button @click="openSeason = (openSeason === {{ $season['season_number'] }} ? null : {{ $season['season_number'] }})" class="w-full flex justify-between items-center p-4 text-left hover:bg-gray-700 focus:outline-none">
                            <h3 class="text-lg font-semibold text-white">
                                {{ $season['name'] }}
                                <span class="text-sm font-normal text-gray-400 ml-2">({{ $season['episode_count'] }} Episodes)</span>
                            </h3>
                            <span class="material-symbols-outlined text-white transition-transform" :class="openSeason === {{ $season['season_number'] }} ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        
                        {{-- Episode List (Collapsible) --}}
                        {{-- Note: The free TMDB details endpoint doesn't include the full episode list for each season. --}}
                        {{-- A separate API call per season would be needed to get all episodes. --}}
                        {{-- For now, we'll just show a message. --}}
                        <div x-show="openSeason === {{ $season['season_number'] }}" x-transition class="border-t border-gray-700 p-4">
                            <p class="text-sm text-gray-400">Loading episodes for {{ $season['name'] }} would require an additional API call. This functionality can be added next!</p>
                            {{-- Placeholder for where the episode list would go --}}
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="bg-gray-800 p-6 rounded-lg text-center text-gray-500">
                        <p>No season information available for this TV Show.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection