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
    protected int $minVoteCount = 50; // For popular/top-rated content
    protected float $minVoteAverage = 3.0; // For popular/top-rated content

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

    private function filterResults(array $response, bool $strictQuality = false): array
    {
        if (isset($response['results'])) {
            $response['results'] = collect($response['results'])->filter(function ($item) use ($strictQuality) {
                // Base requirements for all content
                $notBlocked = !in_array($item['id'], $this->blockedIds);
                if (!$notBlocked) return false;

                $hasValidPoster = !empty($item['poster_path']);
                if (!$hasValidPoster) return false;


                if ($strictQuality) {
                    // Strict filters for popular and top-rated content
                    $hasMinVotes = ($item['vote_count'] ?? 0) >= $this->minVoteCount;
                    $hasGoodRating = ($item['vote_average'] ?? 0) >= $this->minVoteAverage;
                    return $hasMinVotes && $hasGoodRating;
                }
                
                // **NEW:** A balanced, lenient filter for "Latest" content
                // It now requires at least a handful of votes to be shown.
                $hasSomeVotes = ($item['vote_count'] ?? 0) >= 5; // At least 5 votes for lenient filtering
                return $hasSomeVotes;

            })->values()->all();
        }
        return $response;
    }

    private function getQualityContent(string $type, array $params = [], int $targetCount = 20, bool $strict = true): array
    {
        $allResults = [];
        $page = 1;
        $maxPages = 5;
        
        while (count($allResults) < $targetCount && $page <= $maxPages) {
            $response = $this->get("discover/{$type}", array_merge($params, ['page' => $page]));
            $filtered = $this->filterResults($response, $strict);
            
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
            'vote_count.gte' => 10,
        ], $count, false);
    }

    
    public function getPopularMovies(int $count = 20): array
    {
        return $this->getQualityContent('movie', ['sort_by' => 'popularity.desc'], $count, true);
    }

    public function getTopRatedMovies(int $count = 20): array
    {
        return $this->getQualityContent('movie', ['sort_by' => 'vote_average.desc', 'vote_count.gte' => 1000], $count, true);
    }

    // --- TV SHOWS ---
    public function getLatestTvShows(int $count = 20): array
    {
        return $this->getQualityContent('tv', [
            'sort_by' => 'first_air_date.desc',
            'first_air_date.lte' => Carbon::now()->format('Y-m-d'),
            'vote_count.gte' => 10,
        ], $count, false);
    }

    public function getPopularTvShows(int $count = 20): array
    {
        return $this->getQualityContent('tv', ['sort_by' => 'popularity.desc'], $count, true);
    }

    public function getTopRatedTvShows(int $count = 20): array
    {
        return $this->getQualityContent('tv', ['sort_by' => 'vote_average.desc', 'vote_count.gte' => 500], $count, true);
    }

    // --- TRENDING ---
    public function getTrendingAll(int $count = 10): array
    {
        $allResults = [];
        $page = 1;
        $maxPages = 3;
        
        while (count($allResults) < $count && $page <= $maxPages) {
            $response = $this->get('trending/all/week', ['page' => $page]);
            $filtered = $this->filterResults($response, true);
            
            if (empty($filtered['results'])) {
                break;
            }
            
            $allResults = array_merge($allResults, $filtered['results']);
            $page++;
        }
        
        return [
            'results' => array_slice(collect($allResults)->whereIn('media_type', ['movie', 'tv'])->all(), 0, $count),
            'page' => 1,
            'total_pages' => $page - 1,
            'total_results' => count($allResults)
        ];
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

public function getMovieRecommendations(int $movieId, int $count = 12): array
    {
        // 1. Try recommendations
        $response = $this->get("movie/{$movieId}/recommendations");
        $results = $response['results'] ?? [];

        // 2. Fallback to similar movies
        if (empty($results)) {
            $response = $this->get("movie/{$movieId}/similar");
            $results = $response['results'] ?? [];
        }

        // 3. Fallback to popular movies with a lenient filter if still empty
        if (empty($results)) {
            $response = $this->get("discover/movie", ['sort_by' => 'popularity.desc']);
            $results = $response['results'] ?? [];
        }

        // Filter out the original movie, items with no poster, and take the desired count
        return collect($results)->filter(function ($item) use ($movieId) {
            return !empty($item['poster_path']) && $item['id'] != $movieId;
        })->take($count)->all();
    }

    /**
     * Get TV show recommendations with a more robust fallback system.
     */
    public function getTvShowRecommendations(int $tvShowId, int $count = 12): array
    {
        $response = $this->get("tv/{$tvShowId}/recommendations");
        $results = $response['results'] ?? [];

        // Fallback to similar shows
        if (empty($results)) {
            $response = $this->get("tv/{$tvShowId}/similar");
            $results = $response['results'] ?? [];
        }

        // Fallback to popular TV shows with a lenient filter if still empty
        if (empty($results)) {
            $response = $this->get("discover/tv", ['sort_by' => 'popularity.desc']);
            $results = $response['results'] ?? [];
        }

        // Filter out the original show, items with no poster, and take the desired count
        return collect($results)->filter(function ($item) use ($tvShowId) {
            return !empty($item['poster_path']) && $item['id'] != $tvShowId;
        })->take($count)->all();
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
    
    public function getSeasonDetails(int $tvShowId, int $seasonNumber): array
    {
        return $this->get("tv/{$tvShowId}/season/{$seasonNumber}");
    }
    public function getEpisodeDetails(int $tvShowId, int $seasonNumber, int $episodeNumber): array
    {
        return $this->get("tv/{$tvShowId}/season/{$seasonNumber}/episode/{$episodeNumber}");
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
        return $this->get('search/multi', $params);
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
        return $this->get('search/movie', $params);
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
        return $this->get('search/tv', $params);
    }

    public function getDiscover(string $type, array $filters = [])
    {
        $normalizedType = ($type === 'tv_show') ? 'tv' : $type;
        $endpoint = $normalizedType === 'tv' ? 'discover/tv' : 'discover/movie';

        $params = [
            'sort_by' => $this->getSortByValue($filters['sort'] ?? 'latest', $normalizedType),
            'with_genres' => $filters['genre'] ?? null,
            'page' => $filters['page'] ?? 1,
        ];

        if (($filters['sort'] ?? '') === 'top_rated') {
            $params['vote_count.gte'] = 200;
        } elseif (($filters['sort'] ?? '') === 'popular') {
            $params['vote_count.gte'] = 50;
        } else {
            $params['vote_count.gte'] = 10;
        }

        if ($normalizedType === 'movie') {
            $params['primary_release_year'] = $filters['year'] ?? null;
            if (empty($filters['year'])) {
                $params['primary_release_date.lte'] = Carbon::now()->format('Y-m-d');
                if (($filters['sort'] ?? 'latest') === 'latest') {
                    $params['primary_release_date.gte'] = Carbon::now()->subYears(3)->format('Y-m-d');
                }
            }
        } else {
            $params['first_air_date_year'] = $filters['year'] ?? null;
            if (empty($filters['year'])) {
                $params['first_air_date.lte'] = Carbon::now()->format('Y-m-d');
                if (($filters['sort'] ?? 'latest') === 'latest') {
                    $params['first_air_date.gte'] = Carbon::now()->subYears(3)->format('Y-m-d');
                }
            }
        }

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
        
        $allGenres = collect($movieGenres)->merge($tvGenres);
        return $allGenres->unique('id')->values()->all();
    }
}