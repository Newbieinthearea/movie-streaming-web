<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Profile') }}
            </h2>
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-colors">
                Edit Profile
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Profile Information
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Name: <span class="font-semibold">{{ $user->name }}</span>
                    </p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Email: <span class="font-semibold">{{ $user->email }}</span>
                    </p>
                     <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Account Role: <span class="font-semibold capitalize">{{ $user->role }}</span>
                    </p>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Joined: <span class="font-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                    </p>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-full">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Watch History
                    </h3>

                    @if($watchHistory->isNotEmpty())
                        <ul class="space-y-4">
                            @foreach($watchHistory as $history)
                                @if($history->watchable) {{-- Check if the watchable item still exists --}}
                                    <li class="flex items-center space-x-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50">
                                        @if($history->watchable_type === 'App\Models\Movie')
                                            <a href="{{ route('movies.show', $history->watchable) }}">
                                                <img src="{{ $history->watchable->poster_url ?? 'https://placehold.co/80x120/2A2A2A/E0E0E0?text=Movie' }}" alt="{{ $history->watchable->title }} Poster" class="w-16 h-auto rounded">
                                            </a>
                                            <div class="flex-grow">
                                                <a href="{{ route('movies.show', $history->watchable) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-400">
                                                    {{ $history->watchable->title }}
                                                </a>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Movie</p>
                                            </div>
                                        @elseif($history->watchable_type === 'App\Models\Episode')
                                            <a href="{{ route('tv-shows.show', $history->watchable->season->tvShow) }}">
                                                <img src="{{ $history->watchable->season->tvShow->poster_url ?? 'https://placehold.co/80x120/2A2A2A/E0E0E0?text=Show' }}" alt="{{ $history->watchable->season->tvShow->title }} Poster" class="w-16 h-auto rounded">
                                            </a>
                                            <div class="flex-grow">
                                                <a href="{{ route('tv-shows.show', $history->watchable->season->tvShow) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-400">
                                                    {{ $history->watchable->season->tvShow->title }}
                                                </a>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    S{{ $history->watchable->season->season_number }} E{{ $history->watchable->episode_number }} - {{ $history->watchable->title }}
                                                </p>
                                            </div>
                                        @endif
                                        <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                            <p>Last Watched</p>
                                            <p>{{ $history->watched_at->diffForHumans() }}</p>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <div class="mt-6">
                            {{ $watchHistory->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">You have no watch history yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>