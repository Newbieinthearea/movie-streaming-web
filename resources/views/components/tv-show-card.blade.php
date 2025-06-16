@props(['item'])

<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden group">
    <a href="{{ route('tv-shows.show', $item) }}">
        <div class="relative aspect-[2/3]">
            <img src="{{ $item->poster_url ?? 'https://placehold.co/300x450/2A2A2A/E0E0E0?text=No+Poster' }}"
                 alt="{{ $item->title }} Poster"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <span class="material-symbols-outlined text-white text-6xl">play_circle</span>
            </div>
        </div>
        <div class="p-3">
            <h3 class="text-sm font-semibold text-white truncate group-hover:text-purple-400" title="{{ $item->title }}">
                {{ $item->title }}
            </h3>
            <p class="text-xs text-gray-400">{{ $item->release_date ? $item->release_date->format('Y') : 'N/A' }}</p>
        </div>
    </a>
</div>