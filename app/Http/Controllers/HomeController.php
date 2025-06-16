<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Movie;
use App\Models\Episode;

class HomeController extends Controller
{
    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function index()
    {
        $cacheDuration = now()->addHours(3);
        $itemLimit = 20;

        $trendingSlides = Cache::remember('trending_all_slides', $cacheDuration, function () {
            return $this->tmdbService->getTrendingAll(7)['results'] ?? [];
        });

        $latestMovies = Cache::remember('latest_movies', $cacheDuration, function () use ($itemLimit) {
            return $this->tmdbService->getLatestMovies($itemLimit)['results'] ?? [];
        });
        
        $popularMovies = Cache::remember('popular_movies', $cacheDuration, function () use ($itemLimit) {
            return $this->tmdbService->getPopularMovies($itemLimit)['results'] ?? [];
        });

        $topRatedMovies = Cache::remember('top_rated_movies', $cacheDuration, function () use ($itemLimit) {
            return $this->tmdbService->getTopRatedMovies($itemLimit)['results'] ?? [];
        });

        $latestTvShows = Cache::remember('latest_tv_shows', $cacheDuration, function () use ($itemLimit) {
            return $this->tmdbService->getLatestTvShows($itemLimit)['results'] ?? [];
        });

        $popularTvShows = Cache::remember('popular_tv_shows', $cacheDuration, function () use ($itemLimit) {
            return $this->tmdbService->getPopularTvShows($itemLimit)['results'] ?? [];
        });
        
        $topRatedTvShows = Cache::remember('top_rated_tv_shows', $cacheDuration, function () use ($itemLimit) {
            return $this->tmdbService->getTopRatedTvShows($itemLimit)['results'] ?? [];
        });
        
        $watchHistory = collect();
        if (Auth::check()) {
            $user = Auth::user();
            // 1. Get all history, ordered by latest first
            $allHistory = $user->watchHistory()->with('watchable')->latest('watched_at')->get();
            
            // 2. Eager load nested relationships for episodes
            $allHistory->loadMorph('watchable', [
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

            // 4. From the episodes, get only the latest one for each unique TV show
            $latestEpisodeHistory = $episodeHistory->unique(function ($item) {
                return $item->watchable->season->tv_show_id;
            });

            // 5. Merge, sort, and take the top 12 for the homepage
            $watchHistory = $movieHistory->merge($latestEpisodeHistory)
                                ->sortByDesc('watched_at')
                                ->take(12);
        }

        return view('welcome', [
            'trendingSlides' => $trendingSlides,
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