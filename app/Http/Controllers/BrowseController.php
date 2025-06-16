<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BrowseController extends Controller
{
    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function index(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', $query ? 'multi' : 'movie'); // Default to 'multi' when searching
        $page = $request->input('page', 1);
        $yearInput = $request->input('year');
        $genreInput = $request->input('genre');
        $sortInput = $request->input('sort', $query ? null : 'latest');

        // Fetch all genres for the dropdown
        $genres = $this->tmdbService->getGenres();
        $availableYears = range(Carbon::now()->year, 1900);

        $results = [];
        $apiResponse = [];
        $paginationData = [
            'current_page' => $page,
            'total_pages' => 0,
            'total_results' => 0,
        ];

        if ($query) {
            // --- SEARCH MODE ---
            // Use multi-search to find both movies and TV shows
            $apiResponse = $this->tmdbService->searchMulti($query, $page);
            
            // Debug: Log the raw API response
            \Log::info('Search API Response', [
                'query' => $query,
                'total_results' => $apiResponse['total_results'] ?? 0,
                'results_count' => count($apiResponse['results'] ?? []),
                'type_filter' => $type
            ]);
            
            // If type is specified and not 'multi', filter results by media_type
            if ($type && $type !== 'multi') {
                $mediaType = $type === 'tv_show' ? 'tv' : $type;
                $originalCount = count($apiResponse['results'] ?? []);
                $apiResponse['results'] = collect($apiResponse['results'] ?? [])
                    ->filter(function($item) use ($mediaType) {
                        return ($item['media_type'] ?? '') === $mediaType;
                    })
                    ->values()
                    ->all();
                
                // Debug: Log filtering results
                \Log::info('Type filtering', [
                    'original_count' => $originalCount,
                    'filtered_count' => count($apiResponse['results']),
                    'media_type_filter' => $mediaType
                ]);
            }
            
            // Apply year filter if specified
            if ($yearInput) {
                $originalCount = count($apiResponse['results'] ?? []);
                $apiResponse['results'] = collect($apiResponse['results'] ?? [])
                    ->filter(function($item) use ($yearInput) {
                        $releaseDate = $item['release_date'] ?? $item['first_air_date'] ?? '';
                        return str_starts_with($releaseDate, $yearInput);
                    })
                    ->values()
                    ->all();
                
                // Debug: Log year filtering results
                \Log::info('Year filtering', [
                    'original_count' => $originalCount,
                    'filtered_count' => count($apiResponse['results']),
                    'year_filter' => $yearInput
                ]);
            }
        } else {
            // --- DISCOVER MODE ---
            $filters = [
                'genre' => $genreInput,
                'year' => $yearInput,
                'sort' => $sortInput ?: 'latest',
                'page' => $page,
            ];
            $apiResponse = $this->tmdbService->getDiscover($type, $filters);
        }

        $results = $apiResponse['results'] ?? [];
        $paginationData = [
            'current_page' => $apiResponse['page'] ?? 1,
            'total_pages' => $apiResponse['total_pages'] ?? 1,
            'total_results' => $apiResponse['total_results'] ?? 0,
        ];

        return view('browse.index', [
            'query' => $query,
            'results' => $results,
            'paginationData' => $paginationData,
            'genres' => $genres,
            'years' => $availableYears,
            'currentType' => $type,
            'currentGenre' => $genreInput,
            'currentYear' => $yearInput,
            'currentSort' => $sortInput,
        ]);
    }
}