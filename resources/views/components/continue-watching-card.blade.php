@props(['historyItem'])

@php
    // THIS BLOCK WAS ACCIDENTALLY REMOVED. IT'S NOW RESTORED.
    $item = $historyItem->watchable;
    $watchUrl = '#'; // Default fallback URL
    $isMovie = false; // Default to false

    if ($item) { // Ensure the watchable item still exists
        $isMovie = ($historyItem->watchable_type === 'App\Models\Movie');
        if ($isMovie) {
            // Use the TMDB ID for the watch route
            $watchUrl = route('movies.watch', $item->tmdb_id);
        } else {
            // It's an Episode
            $watchUrl = route('episodes.watch', [
                'tv_id' => $item->season->tvShow->tmdb_id,
                'season_number' => $item->season->season_number,
                'episode_number' => $item->episode_number,
            ]);
        }
    }
@endphp

{{-- Add x-data for state management and x-show for the fade-out effect --}}
<div x-data="{ visible: true }" x-show="visible"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     class="rounded-lg shadow-lg overflow-hidden group relative">
    
    <a href="{{ $watchUrl }}">
        <div class="relative aspect-[2/3] bg-gray-800">
            <img src="{{ $isMovie ? $item->poster_url : $item->season->tvShow->poster_url ?? 'https://placehold.co/300x450/2A2A2A/E0E0E0?text=No+Poster' }}"
                 alt="Poster"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-6xl">play_circle</span>
            </div>
        </div>
    </a>
    
    {{-- UPDATED: Remove button form now uses Alpine.js --}}
    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <form
            x-ref="deleteForm"
            @submit.prevent="
                fetch('{{ route('watch-history.destroy', $historyItem) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: new FormData($refs.deleteForm)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        visible = false; // This will trigger the fade-out transition
                    } else {
                        alert('Could not remove item. Please try again.');
                    }
                })
                .catch(() => alert('An error occurred. Please try again.'))
            "
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="p-1.5 bg-black/50 hover:bg-red-600 rounded-full" title="Remove from history">
                <span class="material-symbols-outlined text-white text-base">close</span>
            </button>
        </form>
    </div>

    {{-- Info section below the poster --}}
    <div class="p-3 bg-gray-800">
        @if($isMovie)
            <h3 class="text-sm font-semibold text-white truncate" title="{{ $item->title }}">
                {{ $item->title }}
            </h3>
            <p class="text-xs text-gray-400">Movie</p>
        @else
            <h3 class="text-sm font-semibold text-white truncate" title="{{ $item->season->tvShow->title }}">
                {{ $item->season->tvShow->title }}
            </h3>
            <p class="text-xs text-purple-400 font-semibold">
                S{{ $item->season->season_number }} E{{ $item->episode_number }}: {{ $item->title }}
            </p>
        @endif
    </div>
</div>