@extends('layouts.public')

@section('title', 'Watching: ' . $tvShow['name'] . ' - S' . $seasonNumber . 'E' . $currentEpisodeNumber)

@section('content')
<div class="space-y-8" 
     x-data="{
        tvId: {{ $tvShow['id'] }},
        currentSeasonForHighlight: {{ $seasonNumber }},
        currentEpisodeForHighlight: {{ $currentEpisodeNumber }},
        selectedSeasonNumber: {{ $seasonNumber }},
        episodes: {},
        loading: false,

        fetchEpisodes(seasonNum) {
            if (!seasonNum || this.episodes[seasonNum]) {
                return; // Do not fetch if season is not selected or already loaded
            }
            this.loading = true;
            fetch(`/api/tv/${this.tvId}/season/${seasonNum}`)
                .then(res => res.json())
                .then(data => {
                    this.episodes[seasonNum] = data;
                    this.loading = false;
                })
                .catch(err => {
                    console.error('Error fetching episodes:', err);
                    this.loading = false;
                });
        }
     }" 
     x-init="
        fetchEpisodes(selectedSeasonNumber); // Fetch episodes for the initial season on load
        $watch('selectedSeasonNumber', newSeasonNumber => { // Watch for changes in the dropdown
            fetchEpisodes(newSeasonNumber);
        });
     ">
    {{-- Main Grid for Player and Episode List --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

        {{-- Video Player --}}
        <div class="lg:col-span-3">
            <div class="aspect-video overflow-hidden rounded-lg bg-black shadow-2xl">
                <iframe class="h-full w-full" src="{{ $embedUrl }}" title="Video player for {{ $tvShow['name'] }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>

        {{-- Episode List Sidebar --}}
        <aside class="lg:col-span-1">
            <div class="flex h-full flex-col rounded-lg bg-black/30 p-4 backdrop-blur-md">
                {{-- Season Picker Dropdown --}}
                <div class="mb-4 flex-shrink-0">
                    <label for="season-picker" class="text-sm font-semibold text-gray-300">Season</label>
                    <select id="season-picker" x-model="selectedSeasonNumber" class="mt-1 pl-1 block w-full h-2/3 rounded-md border-gray-600 bg-purple-600/50 text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        @foreach ($tvShow['seasons'] as $season)
                            @if ($season['season_number'] > 0)
                                <option value="{{ $season['season_number'] }}">{{ $season['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                
                {{-- Episode List Container --}}
                <div class="custom-scrollbar flex-grow space-y-2 overflow-y-auto" style="height: 45vh;">
                    <div x-show="loading" class="flex h-full items-center justify-center">
                        <p class="text-sm text-gray-400">Loading...</p>
                    </div>
                    <div x-show="!loading" class="space-y-2">
                        <template x-for="episode in episodes[selectedSeasonNumber] || []" :key="episode.id">
                            <a :href="`/tv-shows/${tvId}/season/${selectedSeasonNumber}/episode/${episode.episode_number}`"
                               class="flex items-center gap-3 rounded-md p-2 transition-colors"
                               :class="currentSeasonForHighlight == selectedSeasonNumber && episode.episode_number == currentEpisodeForHighlight ? 'bg-purple-600/50' : 'hover:bg-gray-700/80'">
                                <div class="w-8 text-center font-bold text-gray-400" x-text="episode.episode_number"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-white" x-text="episode.name"></p>
                                </div>
                                <div class="text-white">
                                    <span class="material-symbols-outlined">
                                        <template x-if="currentSeasonForHighlight == selectedSeasonNumber && episode.episode_number == currentEpisodeForHighlight">
                                            <span class="material-symbols-outlined">play_circle</span>
                                        </template>
                                        <template x-if="!(currentSeasonForHighlight == selectedSeasonNumber && episode.episode_number == currentEpisodeForHighlight)">
                                            <span class="material-symbols-outlined">play_arrow</span>
                                        </template>
                                    </span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </aside>

    </div>

    {{-- Details Section --}}
    <section class="rounded-lg bg-gray-800/50 p-6">
        <h3 class="text-lg font-semibold text-purple-400">{{ $tvShow['name'] }}</h3>
        <h1 class="mt-1 text-3xl font-extrabold text-white md:text-4xl">
            Season {{ $seasonNumber }} &middot; Episode {{ $currentEpisodeNumber }}
        </h1>
         <p class="mt-4 max-w-3xl leading-relaxed text-gray-300">
            {{ $currentSeason['overview'] ?: $tvShow['overview'] }}
        </p>
    </section>

    {{-- Recommendations Section --}}
    @if(!empty($recommendations))
    <section>
        <h2 class="mb-4 text-2xl font-semibold text-white">You Might Also Like</h2>
        <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6 lg:mx-0 lg:px-0">
            @foreach ($recommendations as $item)
                <div class="w-40 flex-shrink-0 sm:w-48">
                    <x-api-content-card :item="$item" />
                </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection