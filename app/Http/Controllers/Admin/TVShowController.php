<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TVShow;
use Illuminate\Http\Request;
use App\Models\Genre; // Import Genre model for genre management
use Illuminate\Support\Str; // If you need to handle file storage
use Illuminate\Validation\Rule; // For validation rules

class TVShowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $tv_shows = TVShow::with('genres')->orderBy('title')->paginate(10);
        return view('admin.tv_shows.index', compact('tv_shows'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $genres = Genre::orderBy('name')->get(); // Get all genres for selection
        return view('admin.tv_shows.create', compact('genres'));
       
        //
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
            'status' => 'nullable|string|max:50', // Consider using an Enum or specific validation rule if statuses are fixed
            'tmdb_id' => 'nullable|string|max:20|unique:tv_shows,tmdb_id', // Must be unique if provided
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);

        // 2. Create the TV Show (excluding genres for now)
        $tv_show = TVShow::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'poster_url' => $validatedData['poster_url'],
            'release_date' => $validatedData['release_date'],
            'status' => $validatedData['status'],
            'tmdb_id' => $validatedData['tmdb_id'],
        ]);

        // 3. Attach selected genres to the TV Show (if any are selected)
        if ($request->has('genres')) {
            $tv_show->genres()->attach($validatedData['genres']);
        }

        // 4. Redirect back to the TV Show index page with a success message
        return redirect()->route('admin.tv-shows.index')
                         ->with('success', 'TV Show created successfully!');
    
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TVShow $tv_show)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TVShow $tv_show)
    {
        $genres = Genre::orderBy('name')->get(); // Get all genres for selection
        // Get IDs of genres currently associated with this TV show
        $selectedGenreIds = $tv_show->genres->pluck('id')->toArray();

        return view('admin.tv_shows.edit', compact('tv_show', 'genres', 'selectedGenreIds'));
        
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TVShow $tv_show)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster_url' => 'nullable|url|max:2048',
            'release_date' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'tmdb_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('tv_shows', 'tmdb_id')->ignore($tv_show->id), // Ignore current TV show
            ],
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);

        // 2. Update the TV show's attributes
        $tv_showData = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'poster_url' => $validatedData['poster_url'],
            'release_date' => $validatedData['release_date'],
            'status' => $validatedData['status'],
            'tmdb_id' => $validatedData['tmdb_id'],
        ];

        $tv_show->update($tv_showData);

        // 3. Sync selected genres
        if ($request->has('genres')) {
            $tv_show->genres()->sync($validatedData['genres']);
        } else {
            // If no genres were submitted (e.g., all checkboxes unchecked), detach all existing genres
            $tv_show->genres()->detach();
        }

        // 4. Redirect back to the TV Show index page with a success message
        return redirect()->route('admin.tv-shows.index')
                         ->with('success', 'TV Show updated successfully!');
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TVShow $tv_show)
    {

        try {
            // Important Consideration: Seasons and Episodes
            // If your 'seasons' table has a foreign key 'tv_show_id' with onDelete('cascade')
            // and your 'episodes' table has a foreign key 'season_id' with onDelete('cascade'),
            // then deleting the TVShow will automatically trigger the deletion of its seasons,
            // and deleting seasons will trigger the deletion of their episodes at the database level.

            // Eloquent's delete() method for a model with belongsToMany relationships (like genres)
            // will typically also detach records from the pivot table (genre_tv_show).

            // If cascading deletes are NOT set up at the database level for seasons/episodes,
            // you would need to delete them manually here first, e.g.:
            // foreach ($tv_show->seasons as $season) {
            //     $season->episodes()->delete(); // Delete episodes of the season
            //     $season->delete(); // Delete the season
            // }
            // However, it's best to rely on database-level cascading deletes if possible.
            // Let's assume cascading deletes are set up for seasons and episodes.

            $tv_showTitle = $tv_show->title; // Get title for the success message before deleting

            // Detach genres first (optional, as delete() on model might handle pivot records, but explicit is fine)
            $tv_show->genres()->detach();

            // Then delete the TV show itself
            $tv_show->delete();

            return redirect()->route('admin.tv-shows.index')
                             ->with('success', 'TV Show "' . $tv_showTitle . '" and its associated seasons/episodes deleted successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            // This might catch issues if cascading deletes are not set up and there are still
            // existing seasons/episodes referencing this TV show with restrictive foreign keys.
            \Log::error('Error deleting TV Show: ' . $e->getMessage()); // Log the actual error
            return redirect()->route('admin.tv-shows.index')
                             ->with('error', 'Could not delete TV Show. It might still have related data that prevents deletion, or a database error occurred.');
        } catch (\Exception $e) {
            \Log::error('Unexpected error deleting TV Show: ' . $e->getMessage()); // Log the actual error
            return redirect()->route('admin.tv-shows.index')
                             ->with('error', 'An unexpected error occurred while trying to delete the TV Show.');
        }
        //
    }
}
