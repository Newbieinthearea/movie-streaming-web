<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie; // Your Movie model
use Illuminate\Http\Request;
use App\Models\Genre; // Your Genre model, if needed for future use
use Illuminate\Support\Str; // If you need to handle file storage
use Illuminate\Validation\Rule; // For validation rules

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load genres to avoid N+1 problem when displaying genres in the table
        $movies = Movie::with('genres')->orderBy('title')->paginate(10);
        return view('admin.movies.index', compact('movies'));
        // This looks for a view at resources/views/admin/movies/index.blade.php
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $genres = Genre::orderBy('name')->get(); // Get all genres to populate selection
        return view('admin.movies.create', compact('genres'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_url' => 'nullable|url|max:2048',
            'release_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'tmdb_id' => 'required|string|max:20|unique:movies,tmdb_id', // TMDB is now required and must be unique
            'imdb_id' => 'nullable|string|max:20|unique:movies,imdb_id,NULL,id', // IMDB is now optional but still unique if provided
            'custom_sub_url' => 'nullable|url|max:2048',
            'default_sub_lang' => 'nullable|string|max:10',
            'genres' => 'nullable|array', // Expect an array of genre IDs
            'genres.*' => 'exists:genres,id', // Each item in genres array must exist in genres table's id column
        ]);
        $movie = Movie::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'poster_url' => $validatedData['poster_url'],
            'release_date' => $validatedData['release_date'],
            'duration' => $validatedData['duration'],
            'imdb_id' => $validatedData['imdb_id'],
            'tmdb_id' => $validatedData['tmdb_id'],
            'custom_sub_url' => $validatedData['custom_sub_url'],
            'default_sub_lang' => $validatedData['default_sub_lang'],
        ]);
        if ($request->has('genres')) {
            $movie->genres()->attach($validatedData['genres']); // Use attach() for many-to-many
        }
        return redirect()->route('admin.movies.index')
                         ->with('success', 'Movie created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movie $movie)
    {
        $genres = Genre::orderBy('name')->get(); // Get all genres for selection
        // Get IDs of genres currently associated with this movie
        $selectedGenreIds = $movie->genres->pluck('id')->toArray();

        return view('admin.movies.edit', compact('movie', 'genres', 'selectedGenreIds'));
        
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Movie $movie) // Route-Model binding for $movie
    {
        // 1. Validate the incoming request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_url' => 'nullable|url|max:2048',
            'release_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'tmdb_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('movies', 'tmdb_id')->ignore($movie->id), // TMDB is now required and unique
            ],
            'imdb_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('movies', 'imdb_id')->ignore($movie->id), // IMDB is optional but unique
            ],
            'custom_sub_url' => 'nullable|url|max:2048',
            'default_sub_lang' => 'nullable|string|max:10',
            'genres' => 'nullable|array', // Expect an array of genre IDs
            'genres.*' => 'exists:genres,id', // Each item in genres array must exist in genres table's id column
        ]);

        // 2. Update the movie's attributes (excluding genres for now from this array)
        $movieData = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'poster_url' => $validatedData['poster_url'],
            'release_date' => $validatedData['release_date'],
            'duration' => $validatedData['duration'],
            'imdb_id' => $validatedData['imdb_id'],
            'tmdb_id' => $validatedData['tmdb_id'],
            'custom_sub_url' => $validatedData['custom_sub_url'],
            'default_sub_lang' => $validatedData['default_sub_lang'],
        ];
        
        $movie->update($movieData);

        // 3. Sync selected genres
        // sync() will detach any genres not in the list and attach new ones.
        // If 'genres' is not present in the request (e.g., all unchecked), it will detach all.
        if ($request->has('genres')) {
            $movie->genres()->sync($validatedData['genres']);
        } else {
            // If no genres were submitted (e.g., all checkboxes unchecked), detach all existing genres
            $movie->genres()->detach();
        }

        // 4. Redirect back to the movie index page with a success message
        return redirect()->route('admin.movies.index')
                         ->with('success', 'Movie updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movie $movie)
    {
        try {
            // Before deleting the movie, Eloquent will typically handle detaching
            // related records from pivot tables (like genre_movie) if the relationship
            // is defined correctly (e.g., using belongsToMany).
            // If you had onDelete('cascade') on the foreign key in the pivot table migration
            // for movie_id, the database would also handle this.
            // Explicitly detaching can also be done if necessary: $movie->genres()->detach();

            $movieTitle = $movie->title; // Get title for the success message before deleting
            $movie->delete();

            return redirect()->route('admin.movies.index')
                             ->with('success', 'Movie "' . $movieTitle . '" deleted successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle potential DB errors, though less common for simple deletes if the model exists
            // and there are no restrictive foreign key constraints preventing deletion from other tables.
            return redirect()->route('admin.movies.index')
                             ->with('error', 'Could not delete movie due to a database error. It might be referenced elsewhere in a way that prevents deletion.');
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            return redirect()->route('admin.movies.index')
                             ->with('error', 'An unexpected error occurred while trying to delete the movie.');
        }
        //
    }
}
