<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\BlockedContent;
use Carbon\Carbon;

class TmdbService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected array $blockedIds;
    protected int $minVoteCount = 50; // Increased for better quality
    protected float $minVoteAverage = 3.0; // Minimum rating for quality content

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
        $this->baseUrl = 'https://api.themoviedb.org/3';

        $this->blockedIds = Cache::remember('blocked_content_ids', now()->addHour(), function () {
            return BlockedContent::pluck('tmdb_id')->all();
        });
    }

    private function get(string $endpoint, array $params = []): array
    {
        $response = Http::get("{$this->baseUrl}/{$endpoint}", array_merge([
            'api_key' => $this->apiKey,
            'language' => 'en-US',
        ], $params));

        return $response->json() ?? [];
    }

    /**
     * Enhanced filtering for high-quality content
     */
    private function filterResults(array $response, bool $strictQuality = false): array
    {
        if (isset($response['results'])) {
            $response['results'] = collect($response['results'])->filter(function ($item) use ($strictQuality) {
                // Basic filters
                $hasValidPoster = !empty($item['poster_path']);
                $notBlocked = !in_array($item['id'], $this->blockedIds);
                $hasMinVotes = ($item['vote_count'] ?? 0) >= $this->minVoteCount;
                
                // Strict quality filters
                if ($strictQuality) {
                    $hasGoodRating = ($item['vote_average'] ?? 0) >= $this->minVoteAverage;
                    $hasOverview = !empty($item['overview']);
                    return $hasValidPoster && $notBlocked && $hasMinVotes && $hasGoodRating && $hasOverview;
                }
                
                return $hasValidPoster && $notBlocked && $hasMinVotes;
            })->values()->all();
        }
        return $response;
    }

    /**
     * Get high-quality content using discover endpoint with multiple pages if needed
     */
    private function getQualityContent(string $type, array $params = [], int $targetCount = 20): array
    {
        $allResults = [];
        $page = 1;
        $maxPages = 5; // Limit to prevent infinite loops
        
        while (count($allResults) < $targetCount && $page <= $maxPages) {
            $response = $this->get("discover/{$type}", array_merge($params, ['page' => $page]));
            $filtered = $this->filterResults($response, true);
            
            if (empty($filtered['results'])) {
                break;
            }
            
            $allResults = array_merge($allResults, $filtered['results']);
            $page++;
        }
        
        return [
            'results' => array_slice($allResults, 0, $targetCount),
            'page' => 1,
            'total_pages' => $page - 1,
            'total_results' => count($allResults)
        ];
    }

    // --- MOVIES ---
    public function getLatestMovies(int $count = 20): array
    {
        return $this->getQualityContent('movie', [
            'sort_by' => 'primary_release_date.desc',
            'primary_release_date.lte' => Carbon::now()->format('Y-m-d'),
            'primary_release_date.gte' => Carbon::now()->subMonths(12)->format('Y-m-d'), // Last 6 months
            'vote_count.gte' => 10,
        ], $count);
    }

    public function getPopularMovies(int $count = 20): array
    {
        return $this->getQualityContent('movie', [
            'sort_by' => 'popularity.desc',
            'vote_count.gte' => $this->minVoteCount,
            'vote_average.gte' => 6.0,
        ], $count);
    }

    public function getTopRatedMovies(int $count = 20): array
    {
        return $this->getQualityContent('movie', [
            'sort_by' => 'vote_average.desc',
            'vote_count.gte' => 1000, // Higher threshold for top rated
            'vote_average.gte' => 7.0,
        ], $count);
    }

    // --- TV SHOWS ---
    public function getLatestTvShows(int $count = 20): array
    {
        return $this->getQualityContent('tv', [
            'sort_by' => 'first_air_date.desc',
            'first_air_date.lte' => Carbon::now()->format('Y-m-d'),
            'first_air_date.gte' => Carbon::now()->subMonths(12)->format('Y-m-d'),
            'vote_count.gte' => 10,
        ], $count);
    }

    public function getPopularTvShows(int $count = 20): array
    {
        return $this->getQualityContent('tv', [
            'sort_by' => 'popularity.desc',
            'vote_count.gte' => $this->minVoteCount,
            'vote_average.gte' => 6.0,
        ], $count);
    }

    public function getTopRatedTvShows(int $count = 20): array
    {
        return $this->getQualityContent('tv', [
            'sort_by' => 'vote_average.desc',
            'vote_count.gte' => 500,
            'vote_average.gte' => 7.5,
        ], $count);
    }

    // --- TRENDING (Using trending endpoint for better results) ---
    public function getTrendingMovies(int $count = 20): array
    {
        $allResults = [];
        $page = 1;
        $maxPages = 3;
        
        while (count($allResults) < $count && $page <= $maxPages) {
            $response = $this->get('trending/movie/week', ['page' => $page]);
            $filtered = $this->filterResults($response, true);
            
            if (empty($filtered['results'])) {
                break;
            }
            
            $allResults = array_merge($allResults, $filtered['results']);
            $page++;
        }
        
        return [
            'results' => array_slice($allResults, 0, $count),
            'page' => 1,
            'total_pages' => $page - 1,
            'total_results' => count($allResults)
        ];
    }

    // Keep your existing methods for compatibility
    public function getMovieDetails(int $id): array
    {
        return $this->get("movie/{$id}", ['append_to_response' => 'credits,videos,images']);
    }

    public function getTvShowDetails(int $id): array
    {
        return $this->get("tv/{$id}", ['append_to_response' => 'credits,videos,images']);
    }
    
    public function getMovieGenres(): array
    {
        return $this->get('genre/movie/list')['genres'] ?? [];
    }

    public function getTvGenres(): array
    {
        return $this->get('genre/tv/list')['genres'] ?? [];
    }

    public function searchMulti(string $query, int $page = 1): array
    {
        $params = [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
        ];
        // Temporarily remove filtering to test
        return $this->get('search/multi', $params);
        // return $this->filterResults($this->get('search/multi', $params));
    }

    public function searchMovies(string $query, int $page = 1, ?int $year = null): array
    {
        $params = [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
        ];
        if ($year) {
            $params['primary_release_year'] = $year;
        }
        // Temporarily remove filtering to test
        return $this->get('search/movie', $params);
        // return $this->filterResults($this->get('search/movie', $params));
    }

    public function searchTvShows(string $query, int $page = 1, ?int $year = null): array
    {
        $params = [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
        ];
        if ($year) {
            $params['first_air_date_year'] = $year;
        }
        // Temporarily remove filtering to test
        return $this->get('search/tv', $params);
        // return $this->filterResults($this->get('search/tv', $params));
    }

    // Add other existing methods you might have...
    public function getDiscover(string $type, array $filters = [])
    {
        // Normalize the type parameter
        $normalizedType = ($type === 'tv_show') ? 'tv' : $type;

        $endpoint = $normalizedType === 'tv' ? 'discover/tv' : 'discover/movie';

        $params = [
            'sort_by' => $this->getSortByValue($filters['sort'] ?? 'latest', $normalizedType),
            'with_genres' => $filters['genre'] ?? null,
            'page' => $filters['page'] ?? 1,
        ];

        // Add different vote_count minimums based on sort type
        if (($filters['sort'] ?? '') === 'top_rated') {
            $params['vote_count.gte'] = 200;
        } elseif (($filters['sort'] ?? '') === 'popular') {
            $params['vote_count.gte'] = 50;
        } else { // latest
            $params['vote_count.gte'] = 10; // This was missing in your original!
        }

        if ($normalizedType === 'movie') {
            $params['primary_release_year'] = $filters['year'] ?? null;
            if (empty($filters['year'])) {
                $params['primary_release_date.lte'] = Carbon::now()->format('Y-m-d');
                // Add date range for latest content
                if (($filters['sort'] ?? 'latest') === 'latest') {
                    $params['primary_release_date.gte'] = Carbon::now()->subYears(3)->format('Y-m-d');
                }
            }
        } else {
            $params['first_air_date_year'] = $filters['year'] ?? null;
            if (empty($filters['year'])) {
                $params['first_air_date.lte'] = Carbon::now()->format('Y-m-d');
                // Add date range for latest content
                if (($filters['sort'] ?? 'latest') === 'latest') {
                    $params['first_air_date.gte'] = Carbon::now()->subYears(3)->format('Y-m-d');
                }
            }
        }

        // Use less strict filtering for latest content
        $useStrictFiltering = ($filters['sort'] ?? 'latest') !== 'latest';

        return $this->filterResults($this->get($endpoint, array_filter($params)), $useStrictFiltering);
    }

    private function getSortByValue(string $sort, string $type): string
    {
        $normalizedType = ($type === 'tv_show') ? 'tv' : $type;
        $releaseDate = $normalizedType === 'tv' ? 'first_air_date.desc' : 'primary_release_date.desc';
        
        return match ($sort) {
            'popular' => 'popularity.desc',
            'top_rated' => 'vote_average.desc',
            default => $releaseDate,
        };
    }

    public function getGenres(): array
    {
        $movieGenres = $this->getMovieGenres();
        $tvGenres = $this->getTvGenres();
        
        // Merge and remove duplicates by ID
        $allGenres = collect($movieGenres)->merge($tvGenres);
        return $allGenres->unique('id')->values()->all();
    }
}