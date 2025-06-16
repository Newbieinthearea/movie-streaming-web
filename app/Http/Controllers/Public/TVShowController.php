<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\TmdbService;
use App\Models\TVShow; // Import the TVShow model directly
use Illuminate\Http\Request;

class TVShowController extends Controller
{
    /**
     * Note: The 'index' route points to the main browse page.
     * This logic is handled by the BrowseController.
     * This method can be removed or kept for future 'all shows' pages.
     */
    public function index(Request $request, TmdbService $tmdbService)
    {
        // This logic is currently handled by BrowseController, but we keep it here for reference
        $filters = $request->only(['genre', 'year', 'sort', 'page']);
        $response = $tmdbService->getDiscover('tv', $filters);
        
        return view('browse.index', [
            'results' => $response['results'] ?? [],
            'paginationData' => [
                'current_page' => $response['page'] ?? 1,
                'total_pages' => $response['total_pages'] ?? 1,
            ],
            'genres' => $tmdbService->getTvGenres(),
            'years' => range(date('Y'), 1950),
            'currentType' => 'tv_show',
        ]);
    }
    
    /**
     * Display the specified TV Show.
     */
    public function show(int $id, TmdbService $tmdbService)
    {
        $tvShow = $tmdbService->getTvShowDetails($id);

        if (empty($tvShow) || (isset($tvShow['success']) && $tvShow['success'] === false)) {
             return redirect()->route('home')->with('error', 'Sorry, that TV show could not be found.');
        }

        // Find the local representation of the TV show to get season IDs
        // Use the imported TVShow model directly instead of an alias
        $localTvShow = TVShow::where('tmdb_id', $id)->with('seasons')->first();
        
        // Create a key-value pair of season_number => season_model for easy lookup in the view
        $localSeasons = $localTvShow ? $localTvShow->seasons->keyBy('season_number') : collect();
        
        return view('tv_shows.show', compact('tvShow', 'localSeasons'));
    }
}