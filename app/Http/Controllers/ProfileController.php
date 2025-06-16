<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\LengthAwarePaginator;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page with their paginated watch history.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // 1. Get all history, ordered by latest first
        $allHistory = $user->watchHistory()->with('watchable')->latest('watched_at')->get();

        // 2. Eager load nested relationships correctly
        $allHistory->loadMorph('watchable', [
            Movie::class => ['genres'],
            Episode::class => ['season.tvShow'],
        ]);

        // 3. Separate movies and valid episodes
        $movieHistory = $allHistory->where('watchable_type', Movie::class);
        $episodeHistory = $allHistory->filter(function ($item) {
            return $item->watchable_type === Episode::class
                && $item->watchable
                && $item->watchable->season
                && $item->watchable->season->tvShow; // Ensure full relationship exists
        });

        // 4. Get the latest episode per TV show
        $latestEpisodeHistory = $episodeHistory->unique(function ($item) {
            return $item->watchable->season->tv_show_id;
        });

        // 5. Merge and sort the final list
        $filteredHistory = $movieHistory->merge($latestEpisodeHistory)->sortByDesc('watched_at');

        // 6. Manually paginate the filtered collection
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $currentPageItems = $filteredHistory->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $watchHistory = new LengthAwarePaginator(
            $currentPageItems,
            $filteredHistory->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('profile.show', [
            'user' => $user,
            'watchHistory' => $watchHistory,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}