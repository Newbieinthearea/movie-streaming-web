<?php

namespace App\Http\Controllers;

use App\Models\Movie;   // Add this use statement
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View; // Import the View contract
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page with their watch history.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // Step 1: Get the paginated history, loading just the top-level 'watchable' item.
        $watchHistory = $user->watchHistory()->with('watchable')->paginate(10);

        // Step 2: Eager load the nested relationships for the items we just fetched.
        // This tells Laravel: "For the items that are Movies, load their genres.
        // For the items that are Episodes, load their season and the season's TV show."
        $watchHistory->loadMorph('watchable', [
            Movie::class => ['genres'],
            Episode::class => ['season.tvShow'],
        ]);

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