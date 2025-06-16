<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Content Moderation & Blocklist') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Block New Content</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Enter the TMDB ID of a movie or TV show to block it from appearing on the site.
                    </p>

                    @if(session('success'))
                        <div class="mt-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                     @if($errors->any())
                        <div class="mt-4 text-sm text-red-600 dark:text-red-400">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.moderation.store') }}" class="mt-6 space-y-6">
                        @csrf
                        <div>
                            <label for="tmdb_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300">TMDB ID</label>
                            <input id="tmdb_id" name="tmdb_id" type="text" class="mt-1 block w-full bg-gray-900 border-gray-600 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label for="type" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Type</label>
                            <select id="type" name="type" class="mt-1 block w-full bg-gray-900 border-gray-600 rounded-md shadow-sm" required>
                                <option value="movie">Movie</option>
                                <option value="tv">TV Show</option>
                            </select>
                        </div>
                        <div>
                            <label for="reason" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Reason (Optional)</label>
                            <input id="reason" name="reason" type="text" class="mt-1 block w-full bg-gray-900 border-gray-600 rounded-md shadow-sm">
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">Block Content</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Currently Blocked Content</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">TMDB ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Reason</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($blockedContent as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $item->tmdb_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm capitalize text-gray-300">{{ $item->type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $item->reason ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <form action="{{ route('admin.moderation.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to unblock this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-green-500 hover:text-green-400">Unblock</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No content is currently blocked.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $blockedContent->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>