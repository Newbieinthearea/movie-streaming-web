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

        // Fetch genres for the dropdowns
        $movieGenres = $this->tmdbService->getMovieGenres();
        $tvGenres = $this->tmdbService->getTvGenres();
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
            $apiResponse = $this->tmdbService->searchMulti($query, $page);
            
            if ($type && $type !== 'multi') {
                $mediaType = $type === 'tv_show' ? 'tv' : $type;
                $apiResponse['results'] = collect($apiResponse['results'] ?? [])
                    ->filter(fn($item) => ($item['media_type'] ?? '') === $mediaType)
                    ->values()->all();
            }
            
            if ($yearInput) {
                $apiResponse['results'] = collect($apiResponse['results'] ?? [])
                    ->filter(function($item) use ($yearInput) {
                        $releaseDate = $item['release_date'] ?? $item['first_air_date'] ?? '';
                        return str_starts_with($releaseDate, $yearInput);
                    })->values()->all();
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
            'movieGenres' => $movieGenres, // Pass movie genres
            'tvGenres' => $tvGenres,       // Pass TV show genres
            'years' => $availableYears,
            'currentType' => $type,
            'currentGenre' => $genreInput,
            'currentYear' => $yearInput,
            'currentSort' => $sortInput,
        ]);
    }
}