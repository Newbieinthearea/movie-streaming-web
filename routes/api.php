<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\Api\EpisodeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/browse', [BrowseController::class, 'getFilteredContent']);

// The API route now accepts the TMDB ID and season number directly
Route::get('/tv/{tmdb_id}/season/{season_number}', [EpisodeController::class, 'index'])->name('api.episodes.index');