<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TmdbService;

class SearchController extends Controller
{
    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Handle the incoming search request using the TMDB API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);

        $moviesResponse = [];
        $tvShowsResponse = [];

        if ($query) {
            $moviesResponse = $this->tmdbService->searchMovies($query, $page);
            $tvShowsResponse = $this->tmdbService->searchTvShows($query, $page);
        }
        
        // For simplicity, we'll paginate one or the other, or combine.
        // Let's combine and manually create a simple paginator if needed,
        // or just show top results without pagination for now.
        // The API returns paginated results, but combining them is complex.
        // We'll show top movies and top TV shows on the same page.

        return view('search.index', [
            'query' => $query,
            'movies' => $moviesResponse['results'] ?? [],
            'tvShows' => $tvShowsResponse['results'] ?? [],
        ]);
    }
}