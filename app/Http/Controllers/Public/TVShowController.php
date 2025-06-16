<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\TmdbService;

class TVShowController extends Controller
{
    public function index(Request $request, TmdbService $tmdbService)
    {
        // This is the correct logic from our browse page implementation
        $filters = $request->only(['genre', 'year', 'sort', 'page']);
        $response = $tmdbService->getDiscover('tv', $filters);
        
        return view('browse.index', [
            'results' => $response['results'] ?? [],
            'paginationData' => [
                'current_page' => $response['page'] ?? 1,
                'total_pages' => $response['total_pages'] ?? 1,
            ],
            'genres' => $tmdbService->getGenres(),
            'years' => range(date('Y'), 1950),
            'resultType' => 'tv',
        ]);
    }
    
    public function show(int $id, TmdbService $tmdbService)
    {
        $tvShow = $tmdbService->getTvShowDetails($id);
        
        return view('tv_shows.show', compact('tvShow'));
    }
}