@extends('layouts.public')

@section('title', 'My Profile')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 space-y-8">
    
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-white">My Profile</h1>
        <a href="{{ route('profile.edit') }}" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors">
            Edit Profile
        </a>
    </div>

    <div class="p-6 sm:p-8 bg-gray-800/50 shadow sm:rounded-lg">
        <h3 class="text-lg font-medium text-gray-100">
            Profile Information
        </h3>
        <div class="mt-4 space-y-2">
            <p class="text-sm text-gray-400">
                Name: <span class="font-semibold text-gray-200">{{ $user->name }}</span>
            </p>
            <p class="text-sm text-gray-400">
                Email: <span class="font-semibold text-gray-200">{{ $user->email }}</span>
            </p>
            <p class="text-sm text-gray-400">
                Account Role: <span class="font-semibold capitalize text-gray-200">{{ $user->role }}</span>
            </p>
            <p class="text-sm text-gray-400">
                Joined: <span class="font-semibold text-gray-200">{{ $user->created_at->format('M d, Y') }}</span>
            </p>
        </div>
    </div>

    <div class="p-6 sm:p-8 bg-gray-800/50 shadow sm:rounded-lg">
        <h3 class="text-lg font-medium text-gray-100 mb-4">
            Watch History
        </h3>

        @if($watchHistory->isNotEmpty())
            <ul class="space-y-4">
                @foreach($watchHistory as $history)
                    @if($history->watchable)
                        <li class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-700/50 transition-colors">
                            @if($history->watchable_type === 'App\Models\Movie')
                                <a href="{{ route('movies.show', $history->watchable->tmdb_id) }}">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w92' . $history->watchable->poster_url }}" alt="{{ $history->watchable->title }} Poster" class="w-16 h-auto rounded">
                                </a>
                                <div class="flex-grow">
                                    <a href="{{ route('movies.show', $history->watchable->tmdb_id) }}" class="font-semibold text-gray-100 hover:text-purple-400">
                                        {{ $history->watchable->title }}
                                    </a>
                                    <p class="text-sm text-gray-400">Movie</p>
                                </div>
                            @elseif($history->watchable_type === 'App\Models\Episode')
                                <a href="{{ route('tv-shows.show', $history->watchable->season->tvShow->tmdb_id) }}">
                                    <img src="{{ 'https://image.tmdb.org/t/p/w92' . $history->watchable->season->tvShow->poster_url }}" alt="{{ $history->watchable->season->tvShow->title }} Poster" class="w-16 h-auto rounded">
                                </a>
                                <div class="flex-grow">
                                    <a href="{{ route('tv-shows.show', $history->watchable->season->tvShow->tmdb_id) }}" class="font-semibold text-gray-100 hover:text-purple-400">
                                        {{ $history->watchable->season->tvShow->title }}
                                    </a>
                                    <p class="text-sm text-gray-400">
                                        S{{ $history->watchable->season->season_number }} E{{ $history->watchable->episode_number }} - {{ $history->watchable->title }}
                                    </p>
                                </div>
                            @endif
                            <div class="text-right text-sm text-gray-400">
                                <p>Last Watched</p>
                                <p class="text-gray-300">{{ $history->watched_at->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>

            <div class="mt-6">
                {{ $watchHistory->links() }}
            </div>
        @else
            <p class="text-gray-400">You have no watch history yet.</p>
        @endif
    </div>
</div>
@endsection