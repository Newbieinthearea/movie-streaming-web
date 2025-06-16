<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Return minimal data to test
        return view('welcome', [
            'trendingSlides' => [],
            'latestMovies' => [],
            'popularMovies' => [],
            'topRatedMovies' => [],
            'latestTvShows' => [],
            'popularTvShows' => [],
            'topRatedTvShows' => [],
            'watchHistory' => collect(),
        ]);
    }
}