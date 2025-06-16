@props(['historyItem'])

@php
    $item = $historyItem->watchable;

    // Set default values in case the linked content has been deleted
    $watchUrl = '#';
    $posterUrl = 'https://placehold.co/300x450/1a1a1a/cccccc?text=Content+Missing';
    $title = 'Content not found';
    $subtitle = 'N/A';
    $isMovie = false;

    if ($item) {
        $isMovie = ($historyItem->watchable_type === 'App\Models\Movie');

        if ($isMovie) {
            $watchUrl = route('movies.watch', $item->tmdb_id);
            $posterUrl = $item->poster_url ?? $posterUrl;
            $title = $item->title;
            $subtitle = 'Movie';
        } else {
            // It's an Episode, so we safely access the parent TV Show
            if ($item->season && $item->season->tvShow) {
                $tvShow = $item->season->tvShow;

                $watchUrl = route('episodes.watch', [
                    'tv_id' => $tvShow->tmdb_id,
                    'season_number' => $item->season->season_number,
                    'episode_number' => $item->episode_number,
                ]);
                
                $posterUrl = $tvShow->poster_url ?? $posterUrl;
                $title = $tvShow->title;
                $subtitle = "S{$item->season->season_number} E{$item->episode_number}";
            }
        }
    }
@endphp

{{-- The new card design --}}
<div x-data="{ visible: true }" x-show="visible"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     class="group relative aspect-[2/3] w-full overflow-hidden rounded-lg bg-gray-800 shadow-lg">

    {{-- Background Poster Image --}}
    <img src="{{ $posterUrl }}" alt="Poster for {{ $title }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">

    {{-- Clickable Overlay for Play Button --}}
    <a href="{{ $watchUrl }}" class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100" title="Continue Watching">
        <span class="material-symbols-outlined text-6xl text-white">play_circle</span>
    </a>
    
    {{-- Gradient Overlay and Text Content --}}
    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 via-black/70 to-transparent p-3">
        <a href="{{ $watchUrl }}" class="hover:underline" title="{{ $title }}">
            <h3 class="truncate text-sm font-bold text-white">
                {{ $title }}
            </h3>
        </a>
        <p class="text-xs font-semibold text-purple-400">
            {{ $subtitle }}
        </p>
    </div>

    {{-- Remove from history button --}}
    <div class="absolute top-2 right-2">
        <form
            x-ref="deleteForm"
            @submit.prevent="
                fetch('{{ route('watch-history.destroy', $historyItem) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: new FormData($refs.deleteForm)
                })
                .then(res => res.json())
                .then(data => { if (data.status === 'success') { visible = false; } })
            "
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-full bg-black/50 p-1.5 opacity-0 transition-opacity hover:bg-red-600 group-hover:opacity-100" title="Remove from history">
                <span class="material-symbols-outlined text-base text-white">close</span>
            </button>
        </form>
    </div>
</div>