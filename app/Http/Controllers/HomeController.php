<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function index()
    {
        // Cache homepage data for 3 hours to reduce API calls
        $cacheDuration = now()->addHours(3);

        // --- NEW: Fetch trending movies for the hero slideshow ---
        $trendingSlides = Cache::remember('trending_movies_slides', $cacheDuration, function () {
            // We fetch more to ensure we have enough high-quality slides after filtering
            return $this->tmdbService->getTrendingMovies(10)['results'] ?? [];
        });

        $latestMovies = Cache::remember('latest_movies', $cacheDuration, function () {
            return $this->tmdbService->getLatestMovies()['results'] ?? [];
        });

        $popularMovies = Cache::remember('popular_movies', $cacheDuration, function () {
            return $this->tmdbService->getPopularMovies()['results'] ?? [];
        });

        $topRatedMovies = Cache::remember('top_rated_movies', $cacheDuration, function () {
            return $this->tmdbService->getTopRatedMovies()['results'] ?? [];
        });

        $latestTvShows = Cache::remember('latest_tv_shows', $cacheDuration, function () {
            return $this->tmdbService->getLatestTvShows()['results'] ?? [];
        });

        $popularTvShows = Cache::remember('popular_tv_shows', $cacheDuration, function () {
            return $this->tmdbService->getPopularTvShows()['results'] ?? [];
        });
        
        $topRatedTvShows = Cache::remember('top_rated_tv_shows', $cacheDuration, function () {
            return $this->tmdbService->getTopRatedTvShows()['results'] ?? [];
        });

        $watchHistory = collect();
        if (Auth::check()) {
            $watchHistory = Auth::user()->watchHistory()
                                ->with('watchable.season.tvShow', 'watchable.genres')
                                ->take(12) // Limit history on homepage
                                ->get();
        }

        return view('welcome', [
            'trendingSlides' => $trendingSlides, // Pass slides to the view
            'latestMovies' => $latestMovies,
            'popularMovies' => $popularMovies,
            'topRatedMovies' => $topRatedMovies,
            'latestTvShows' => $latestTvShows,
            'popularTvShows' => $popularTvShows,
            'topRatedTvShows' => $topRatedTvShows,
            'watchHistory' => $watchHistory,
        ]);
    }
}