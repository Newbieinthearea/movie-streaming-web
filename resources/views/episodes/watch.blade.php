@extends('layouts.public')

@section('title', 'Watching: ' . $tvShow['name'] . ' - S' . $seasonNumber . 'E' . $currentEpisodeNumber)

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="lg:w-3/4 space-y-6">
            <section>
                <div class="aspect-video bg-black rounded-lg overflow-hidden shadow-2xl">
                    <iframe class="w-full h-full" src="{{ $embedUrl }}" title="Video player for {{ $tvShow['name'] }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>

                <div class="mt-4 p-4 bg-gray-800/50 rounded-lg">
                    <h3 class="text-sm text-purple-400 font-semibold">{{ $tvShow['name'] }}</h3>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mt-1">
                        Season {{ $seasonNumber }} &middot; Episode {{ $currentEpisodeNumber }}
                    </h1>
                     <p class="text-gray-300 leading-relaxed mt-4 max-w-3xl text-sm">
                        {{-- To get a specific episode overview, a separate API call is needed. Using season overview as a fallback. --}}
                        {{ $currentSeason['overview'] ?: $tvShow['overview'] }}
                    </p>
                </div>
            </section>
        </div>

        <aside class="lg:w-1/4 flex-shrink-0">
            <div class="bg-gray-800/50 p-4 rounded-lg">
                <h3 class="font-semibold text-white mb-3">Episodes in {{ $currentSeason['name'] ?? "Season $seasonNumber" }}</h3>
                <div class="space-y-2 max-h-[60vh] overflow-y-auto">
                    {{-- Note: The TMDB 'details' endpoint doesn't return a full episode list.
                         A full implementation requires a separate API call to get a season's details.
                         We will just render a placeholder for now. --}}

                    @for ($i = 1; $i <= ($currentSeason['episode_count'] ?? 10); $i++)
                        <a href="{{ route('episodes.watch', ['tv_id' => $tvShow['id'], 'season_number' => $seasonNumber, 'episode_number' => $i]) }}"
                           class="flex items-center gap-3 p-2 rounded-md transition-colors {{ $i == $currentEpisodeNumber ? 'bg-purple-600/50' : 'hover:bg-gray-700' }}">
                            <div class="w-8 text-center font-bold text-gray-400">{{ $i }}</div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-white">Episode {{ $i }}</p>
                                <p class="text-xs text-gray-400">45 min</p> {{-- Placeholder duration --}}
                            </div>
                            <div class="text-white">
                                <span class="material-symbols-outlined">
                                    {{ $i == $currentEpisodeNumber ? 'play_circle' : 'play_arrow' }}
                                </span>
                            </div>
                        </a>
                    @endfor
                </div>
            </div>
        </aside>
    </div>
@endsection