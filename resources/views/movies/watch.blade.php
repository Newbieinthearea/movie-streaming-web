@extends('layouts.public')

@section('title', 'Watching: ' . $movie['title'])

@section('content')
    <div class="space-y-6">
        <section>
            <div class="aspect-video bg-black rounded-lg overflow-hidden shadow-2xl">
                <iframe class="w-full h-full" src="{{ $embedUrl }}" title="Video player for {{ $movie['title'] }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>

            <div class="mt-4 p-4 bg-gray-800/50 rounded-lg">
                <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $movie['title'] }}</h1>
                <div class="flex items-center space-x-4 text-gray-400 font-medium mt-2 text-sm">
                    @if(!empty($movie['release_date']))
                        <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}</span>
                        <span class="opacity-50">•</span>
                    @endif
                    @if(!empty($movie['runtime']))
                        <span>{{ floor($movie['runtime'] / 60) }}h {{ $movie['runtime'] % 60 }}m</span>
                    @endif
                </div>
                <p class="text-gray-300 leading-relaxed mt-4 max-w-3xl text-sm">
                    {{ $movie['overview'] }}
                </p>
            </div>
        </section>
    </div>
@endsection