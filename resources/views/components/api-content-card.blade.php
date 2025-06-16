@props(['item'])

@php
    // Determine the type and set up common variables
    $type = isset($item['first_air_date']) ? 'tv' : 'movie';
    $title = $item['title'] ?? $item['name'];
    $id = $item['id'];
    $poster = 'https://image.tmdb.org/t/p/w500' . ($item['poster_path'] ?? null);
    if (!($item['poster_path'] ?? null)) {
        $poster = 'https://placehold.co/300x450/2A2A2A/E0E0E0?text=No+Poster';
    }
    $year = isset($item['release_date']) && $item['release_date'] 
            ? \Carbon\Carbon::parse($item['release_date'])->format('Y') 
            : (isset($item['first_air_date']) && $item['first_air_date'] ? \Carbon\Carbon::parse($item['first_air_date'])->format('Y') : 'N/A');
    
    // Define the appropriate detail page route based on type
    $detailUrl = $type === 'tv'
        ? route('tv-shows.show', $id)
        : route('movies.show', $id);

    // Define the appropriate watch page route based on type
    $watchUrl = $type === 'movie' ? route('movies.watch', $id) : route('tv-shows.show', $id);
@endphp

<div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden group relative">
    <a href="{{ $detailUrl }}">
        <div class="relative aspect-[2/3]">
            <img src="{{ $poster }}" alt="{{ $title }} Poster" class="w-full h-full object-cover">
        </div>
    </a>

    {{-- This overlay will be shown on hover for mouse users, and always for touch users --}}
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3 touch-overlay">
        
        <div class="space-y-2">
             <div>
                <a href="{{ $detailUrl }}">
                    <h3 class="font-bold text-white group-hover:text-purple-400 transition-colors" title="{{ $title }}">{{ \Illuminate\Support\Str::limit($title, 25) }}</h3>
                </a>
                <p class="text-xs text-gray-400">{{ $year }}</p>
            </div>
            <a href="{{ $watchUrl }}" class="flex items-center justify-center w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-3 rounded-md text-xs transition-colors">
                <span class="material-symbols-outlined mr-1 text-sm">play_arrow</span>
                Watch Now
            </a>
             <a href="{{ $detailUrl }}" class="flex items-center justify-center w-full bg-gray-600/80 hover:bg-gray-500/80 text-white font-semibold py-2 px-3 rounded-md text-xs transition-colors">
                <span class="material-symbols-outlined mr-1 text-sm">info</span>
                Details
            </a>
        </div>
    </div>
</div>