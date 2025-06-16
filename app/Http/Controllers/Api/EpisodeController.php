<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TmdbService;

class EpisodeController extends Controller
{
    /**
     * Fetch season details directly from TMDB using the TV Show TMDB ID and season number.
     *
     * @param  int  $tmdb_id
     * @param  int  $season_number
     * @param  \App\Services\TmdbService  $tmdbService
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $tmdb_id, int $season_number, TmdbService $tmdbService)
    {
        // Call the new public method instead of the private 'get' method
        $seasonDetails = $tmdbService->getSeasonDetails($tmdb_id, $season_number);

        // Return the 'episodes' array from the API response as JSON
        return response()->json($seasonDetails['episodes'] ?? []);
    }
}