@extends('layouts.public')

@section('title', $movie['title'])

@section('content')
@php
        $trailer = collect($movie['videos']['results'] ?? [])->firstWhere('type', 'Trailer');
    @endphp
    <div>
        <section class="relative min-h-[50vh] bg-cover bg-center bg-no-repeat -mx-4 -mt-8 sm:-mx-6 lg:-mx-8" style="background-image: linear-gradient(rgba(18,18,18,0.9), rgba(18,18,18,0.9)), url('https://image.tmdb.org/t/p/w1280{{ $movie['backdrop_path'] }}');">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center py-16">
                
                {{-- Use flexbox that stacks on mobile and is a row on desktop --}}
                <div class="flex flex-col md:flex-row gap-8 text-white">
                    
                    <div class="flex-shrink-0 w-48 sm:w-64 mx-auto">
                        <img src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }} Poster" class="w-full h-auto rounded-lg shadow-2xl">
                    </div>

                    <div class="flex-grow text-center md:text-left">
                        <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">{{ $movie['title'] }}</h1>

                        <div class="flex items-center justify-center md:justify-start flex-wrap gap-x-4 gap-y-2 text-gray-300 font-medium mb-4">
                            @if(!empty($movie['release_date']))
                                <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}</span>
                                <span class="opacity-50">•</span>
                            @endif
                            @if(!empty($movie['runtime']))
                                <span>{{ floor($movie['runtime'] / 60) }}h {{ $movie['runtime'] % 60 }}m</span>
                                <span class="opacity-50">•</span>
                            @endif
                            <span class="flex items-center">
                                <span class="material-symbols-outlined text-yellow-400 text-base mr-1">star</span>
                                <span>{{ number_format($movie['vote_average'], 1) }} / 10</span>
                            </span>
                        </div>

                        @if(!empty($movie['genres']))
                        <div class="flex flex-wrap gap-2 mb-6 justify-center md:justify-start">
                            @foreach($movie['genres'] as $genre)
                                <a href="{{ route('browse.index', ['genre' => $genre['id'], 'type' => 'movie']) }}" class="px-3 py-1 bg-gray-700/80 hover:bg-purple-600 text-gray-300 hover:text-white text-xs font-semibold rounded-full transition-colors">
                                    {{ $genre['name'] }}
                                </a>
                            @endforeach
                        </div>
                        @endif

                        <p class="text-gray-300 leading-relaxed mb-8 max-w-2xl mx-auto md:mx-0">{{ $movie['overview'] }}</p>

                        <div class="flex items-center space-x-4 justify-center md:justify-start">
                            <a href="{{ route('movies.watch', $movie['id']) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center">
                                <span class="material-symbols-outlined mr-2">play_arrow</span>
                                Watch Now
                            </a>
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

        @if(!empty($movie['credits']['cast']))
        <section class="mt-12">
            <h2 class="text-2xl font-semibold text-white mb-4">Top Billed Cast</h2>
            <div class="flex overflow-x-auto pb-4 -mx-2 px-2 no-scrollbar">
                @foreach(collect($movie['credits']['cast'])->take(10) as $cast)
                <div class="flex-shrink-0 w-32 text-center mr-4">
                    <img src="{{ $cast['profile_path'] ? 'https://image.tmdb.org/t/p/w185'.$cast['profile_path'] : 'https://placehold.co/185x278/2A2A2A/E0E0E0?text=No+Image' }}" alt="{{ $cast['name'] }}" class="rounded-lg shadow-md mb-2">
                    <p class="text-sm font-semibold text-white">{{ $cast['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $cast['character'] }}</p>
                </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
@endsection