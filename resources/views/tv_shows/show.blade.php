@extends('layouts.public')

@section('title', $tvShow['name'])

@section('content')
    @php
        $trailer = collect($tvShow['videos']['results'] ?? [])->firstWhere('type', 'Trailer');
    @endphp
    <div>
        {{-- Hero Section --}}
        <section class="relative h-[60vh] bg-cover bg-center bg-no-repeat md:h-[80vh]" style="background-image: linear-gradient(rgba(18,18,18,0.7), rgba(18,18,18,1)), url('https://image.tmdb.org/t/p/w1280{{ $tvShow['backdrop_path'] }}');">
            {{-- ... existing hero content from previous steps ... --}}
        </section>

        {{-- Seasons & Episodes Section --}}
        <section class="mt-12">
            <h2 class="mb-4 text-2xl font-semibold text-white">Seasons & Episodes</h2>
            <div class="space-y-3" x-data="{ 
                openSeason: null,
                episodes: {},
                visibleEpisodeCounts: {},
                loading: {},
                initialLoadSize: 10,
                loadMoreSize: 20,

                fetchEpisodes(tvId, seasonNumber) {
                    if (this.episodes[seasonNumber]) return;
                    this.loading[seasonNumber] = true;
                    fetch(`/api/tv/${tvId}/season/${seasonNumber}`)
                        .then(res => res.json())
                        .then(data => {
                            this.episodes[seasonNumber] = data;
                            this.visibleEpisodeCounts[seasonNumber] = Math.min(this.initialLoadSize, data.length);
                            this.loading[seasonNumber] = false;
                        })
                        .catch(err => {
                            console.error('Error fetching episodes:', err);
                            this.loading[seasonNumber] = false;
                        });
                },

                showMore(seasonNumber) {
                    const currentCount = this.visibleEpisodeCounts[seasonNumber] || 0;
                    const totalCount = this.episodes[seasonNumber].length;
                    this.visibleEpisodeCounts[seasonNumber] = Math.min(currentCount + this.loadMoreSize, totalCount);
                }
            }">
                @forelse($tvShow['seasons'] as $season)
                    @if($season['season_number'] > 0)
                    <div class="overflow-hidden rounded-lg bg-gray-800/70">
                        <button 
                            @click="
                                openSeason = (openSeason === {{ $season['season_number'] }} ? null : {{ $season['season_number'] }});
                                if (openSeason === {{ $season['season_number'] }}) {
                                    fetchEpisodes({{ $tvShow['id'] }}, {{ $season['season_number'] }});
                                }
                            "
                            class="flex w-full items-center justify-between p-4 text-left hover:bg-gray-700 focus:outline-none">
                            <h3 class="text-lg font-semibold text-white">
                                {{ $season['name'] }}
                                <span class="ml-2 text-sm font-normal text-gray-400">({{ $season['episode_count'] }} Episodes)</span>
                            </h3>
                            <span class="material-symbols-outlined text-white transition-transform" :class="openSeason === {{ $season['season_number'] }} ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        
                        <div x-show="openSeason === {{ $season['season_number'] }}" x-transition.duration.300ms class="border-t border-gray-700 p-4">
                            <div x-show="loading[{{ $season['season_number'] }}]">
                                <p class="text-sm text-gray-400">Loading episodes...</p>
                            </div>
                            <div x-show="!loading[{{ $season['season_number'] }}] && episodes[{{ $season['season_number'] }}]">
                                <div class="space-y-2">
                                    {{-- The loop now iterates over a slice of the full array --}}
                                    <template x-for="episode in episodes[{{ $season['season_number'] }}].slice(0, visibleEpisodeCounts[{{ $season['season_number'] }}] || 0)" :key="episode.id">
                                        <a :href="`/tv-shows/{{ $tvShow['id'] }}/season/{{ $season['season_number'] }}/episode/${episode.episode_number}`" 
                                           class="flex items-start gap-4 rounded-lg p-3 transition-colors hover:bg-gray-700/50">
                                            <div class="font-semibold text-gray-400" x-text="episode.episode_number"></div>
                                            <div class="flex-1">
                                                <p class="font-semibold text-white" x-text="episode.name"></p>
                                                <p class="mt-1 text-sm text-gray-400" x-text="episode.overview"></p>
                                            </div>
                                            <div class="text-sm text-gray-500" x-text="episode.runtime ? `${episode.runtime} min` : ''"></div>
                                        </a>
                                    </template>
                                </div>
                                
                                {{-- "Show More" Button --}}
                                <div x-show="visibleEpisodeCounts[{{ $season['season_number'] }}] < (episodes[{{ $season['season_number'] }}]?.length || 0)" class="mt-4">
                                    <button @click="showMore({{ $season['season_number'] }})" class="w-full rounded-lg bg-gray-700 py-2 text-center font-semibold text-white transition hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                                        Show More
                                    </button>
                                </div>
                            </div>
                            <div x-show="!loading[{{ $season['season_number'] }}] && !episodes[{{ $season['season_number'] }}] && openSeason === {{ $season['season_number'] }}">
                                <p class="text-sm text-gray-400">Could not load episodes. Please ensure they are added to the database.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="rounded-lg bg-gray-800 p-6 text-center text-gray-500">
                        <p>No season information available for this TV Show.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection