<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Public\MovieController; // Import the GenreController for genre management
use App\Http\Controllers\Public\TVShowController;
use App\Http\Controllers\Public\EpisodeController; // Import the EpisodeController for episode management 
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BrowseController; 
use App\Http\Controllers\WatchHistoryController;

use App\Http\Controllers\Admin\GenreController; // <<< ADD THIS LINE to import the GenreController
use App\Http\Controllers\Admin\MovieController as AdminMovieController; // Future MovieController import, if needed
use App\Http\Controllers\Admin\TVShowController as AdminTVShowController; // Future TvShowController import, if needed
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\EpisodeController as AdminEpisodeController; 
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\HomeController; // Import the HomeController for the homepage


Route::get('/', [HomeController::class, 'index'])->name('home'); // This is your homepage
Route::get('/browse', [BrowseController::class, 'index'])->name('browse.index'); // <<< ADD THIS
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/tv-shows/{id}', [TVShowController::class, 'show'])->name('tv-shows.show');
Route::get('/movies/{id}/watch', [MovieController::class, 'watch'])->name('movies.watch');
Route::get('/tv-shows/{tv_id}/season/{season_number}/episode/{episode_number}', [EpisodeController::class, 'watch'])->name('episodes.watch');
// This is your ADMIN CMS entry point
Route::get('/dashboard', function () {
    return view('dashboard'); // This is resources/views/dashboard.blade.php
})->middleware(['auth', 'admin'])->name('dashboard');

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');



// Group for specific CMS resource management sections, like Genres, Movies, etc.
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
    Route::post('/moderation', [ModerationController::class, 'store'])->name('moderation.store');
    Route::delete('/moderation/{blockedContent}', [ModerationController::class, 'destroy'])->name('moderation.destroy');
    Route::resource('genres', GenreController::class);
    Route::resource('movies', AdminMovieController::class);
    Route::resource('tv-shows', AdminTVShowController::class);
    Route::resource('tv-shows.seasons',SeasonController::class);
    Route::resource('seasons.episodes', AdminEpisodeController::class)->shallow();

    // Future admin resources (Movies, TV Shows) will go here
});

// Standard user profile routes (accessible by any authenticated user)
Route::middleware('auth')->group(function () {
    // New route to SHOW the profile and history
    Route::delete('/watch-history/{watchHistory}', [WatchHistoryController::class, 'destroy'])->name('watch-history.destroy');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Existing route to show the EDIT form, URL changed for clarity
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Standard authentication routes (login, register, logout, etc.)
require __DIR__.'/auth.php';