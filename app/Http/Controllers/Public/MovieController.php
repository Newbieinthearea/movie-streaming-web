<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function show(int $id, TmdbService $tmdbService)
    {
        $movie = $tmdbService->getMovieDetails($id);
        return view('movies.show', compact('movie'));
    }

    public function watch(int $id, TmdbService $tmdbService)
    {
        // --- Watch History Logic (with on-the-fly creation) ---
        if (Auth::check()) {
            // Use firstOrCreate: Find a movie with this tmdb_id, or create it if it doesn't exist.
            $localMovie = Movie::firstOrCreate(
                ['tmdb_id' => $id],
                [
                    // Data to use if creating a new record. We fetch it from TMDB.
                    // Note: In a production app, you might want to handle the case where getMovieDetails fails.
                    'title' => $tmdbService->getMovieDetails($id)['title'] ?? 'Unknown Title',
                    'slug' => Str::slug($tmdbService->getMovieDetails($id)['title'] ?? 'Unknown Title') . '-' . $id,
                    'release_date' => $tmdbService->getMovieDetails($id)['release_date'] ?? null,
                    'poster_url' => $tmdbService->getMovieDetails($id)['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $tmdbService->getMovieDetails($id)['poster_path'] : null,
                ]
            );

            // Now that we're guaranteed to have a localMovie record, save the history.
            Auth::user()->watchHistory()->updateOrCreate(
                [
                    'watchable_type' => Movie::class,
                    'watchable_id' => $localMovie->id,
                ],
                [
                    'watched_at' => now(),
                ]
            );
        }
        // --- End of Watch History Logic ---


        // Fetch TMDB data for the view
        $embedUrl = "https://vidsrc.xyz/embed/movie?tmdb=" . $id;
        $movie = $tmdbService->getMovieDetails($id);

        if (empty($movie) || isset($movie['success']) && $movie['success'] === false) {
             return redirect()->route('home')->with('error', 'Sorry, that movie could not be found.');
        }

        return view('movies.watch', [
            'movie' => $movie,
            'embedUrl' => $embedUrl,
        ]);
    }
}