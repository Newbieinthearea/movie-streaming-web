@extends('layouts.public')

@section('title', 'Watching: ' . $movie['title'])

@section('content')
    <div class="space-y-12">
        {{-- Player Section --}}
        <section>
            <div class="aspect-video overflow-hidden rounded-lg bg-black shadow-2xl">
                <iframe class="h-full w-full" src="{{ $embedUrl }}" title="Video player for {{ $movie['title'] }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </section>

        {{-- Details Section --}}
        <section class="rounded-lg bg-gray-800/50 p-6">
            <h1 class="text-3xl font-extrabold text-white md:text-4xl">{{ $movie['title'] }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-medium text-gray-400">
                @if(!empty($movie['release_date']))
                    <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}</span>
                    <span class="opacity-50">•</span>
                @endif
                @if(!empty($movie['runtime']))
                    <span>{{ floor($movie['runtime'] / 60) }}h {{ $movie['runtime'] % 60 }}m</span>
                @endif
            </div>
            @if(!empty($movie['genres']))
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($movie['genres'] as $genre)
                        <a href="{{ route('browse.index', ['genre' => $genre['id'], 'type' => 'movie']) }}" class="rounded-full bg-gray-700/80 px-3 py-1 text-xs font-semibold text-gray-300 transition-colors hover:bg-purple-600 hover:text-white">
                            {{ $genre['name'] }}
                        </a>
                    @endforeach
                </div>
            @endif
            <p class="mt-4 max-w-3xl leading-relaxed text-gray-300">{{ $movie['overview'] }}</p>
        </section>

        {{-- Recommendations Section --}}
        @if(!empty($recommendations))
        <section>
            <h2 class="mb-4 text-2xl font-semibold text-white">You Might Also Like</h2>
            <div class="custom-scrollbar -mx-4 flex space-x-4 overflow-x-auto px-4 pb-4 md:space-x-6">
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