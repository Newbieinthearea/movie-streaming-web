<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function show(int $id, TmdbService $tmdbService)
    {
        $movie = $tmdbService->getMovieDetails($id);
        return view('movies.show', compact('movie'));
    }

    public function watch(int $id, TmdbService $tmdbService)
    {
        $movieDetails = $tmdbService->getMovieDetails($id);

        if (empty($movieDetails) || (isset($movieDetails['success']) && $movieDetails['success'] === false)) {
             return redirect()->route('home')->with('error', 'Sorry, that movie could not be found.');
        }

        $embedUrl = "https://vidsrc.xyz/embed/movie?tmdb=" . $id;

        if (Auth::check()) {
            // Use updateOrCreate to ensure movie details are always current
            $localMovie = Movie::updateOrCreate(
                ['tmdb_id' => $id],
                [
                    'title' => $movieDetails['title'],
                    'imdb_id' => $movieDetails['imdb_id'] ?? null,
                    'release_date' => $movieDetails['release_date'] ?? null,
                    'poster_url' => $movieDetails['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $movieDetails['poster_path'] : null,
                ]
            );

            Auth::user()->watchHistory()->updateOrCreate(
                ['watchable_type' => Movie::class, 'watchable_id' => $localMovie->id],
                ['watched_at' => now()]
            );
        }

        $recommendations = $tmdbService->getMovieRecommendations($id);

        return view('movies.watch', [
            'movie' => $movieDetails,
            'embedUrl' => $embedUrl,
            'recommendations' => $recommendations,
        ]);
    }
}