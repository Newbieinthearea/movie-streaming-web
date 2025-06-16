<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\TVShow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // For validation rules

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TVShow $tv_show)
    {
        $seasons = $tv_show->seasons()->orderBy('season_number')->paginate(10);
        return view('admin.seasons.index', compact('tv_show', 'seasons'));
    
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TVShow $tv_show)
    {
        //
         // We pass the $tv_show to the view so we know which TV show this season belongs to
        return view('admin.seasons.create', compact('tv_show'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TVShow $tv_show)
    {
        //
        $validatedData = $request->validate([
            'season_number' => [
                'required',
                'integer',
                'min:0',
                // Ensure season_number is unique for this specific tv_show_id
                Rule::unique('seasons')->where(function ($query) use ($tv_show) {
                    return $query->where('tv_show_id', $tv_show->id);
                }),
            ],
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'poster_url' => 'nullable|url|max:2048',
            'tmdb_id' => 'nullable|string|max:20|unique:seasons,tmdb_id', // TMDB ID for season should be unique across all seasons
        ]);

        // 2. Add the tv_show_id to the validated data before creation
        $dataToStore = $validatedData;
        $dataToStore['tv_show_id'] = $tv_show->id;

        // 3. Create the new Season
        Season::create($dataToStore);

        // 4. Redirect back to the seasons index page for this TV Show with a success message
        return redirect()->route('admin.tv-shows.seasons.index', $tv_show)
                         ->with('success', 'Season created successfully for ' . $tv_show->title . '!');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(TVShow $tv_show, Season $season)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TVShow $tv_show, Season $season)
    {
        //
        return view('admin.seasons.edit', compact('tv_show', 'season'));
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TVShow $tv_show, Season $season)
    {
        //
        $validatedData = $request->validate([
            'season_number' => [
                'required',
                'integer',
                'min:0',
                // Ensure season_number is unique for this specific tv_show_id, ignoring the current season
                Rule::unique('seasons')->where(function ($query) use ($tv_show) {
                    return $query->where('tv_show_id', $tv_show->id);
                })->ignore($season->id),
            ],
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'poster_url' => 'nullable|url|max:2048',
            'tmdb_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('seasons', 'tmdb_id')->ignore($season->id), // TMDB ID for season should be unique, ignoring current season
            ],
        ]);

        // 2. Update the season's attributes
        // Note: tv_show_id should not change for an existing season, so we don't update it from the form.
        $season->update($validatedData);

        // 3. Redirect back to the seasons index page for this TV Show with a success message
        return redirect()->route('admin.tv-shows.seasons.index', $tv_show)
                         ->with('success', 'Season updated successfully for ' . $tv_show->title . '!');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TVShow $tv_show, Season $season)
    {
        //
        try {
            // Important Consideration: Episodes
            // If your 'episodes' table has a foreign key 'season_id' with onDelete('cascade'),
            // then deleting the Season will automatically trigger the deletion of its episodes
            // at the database level. This is the preferred method.

            // If cascading deletes are NOT set up at the database level for episodes,
            // you would need to delete them manually here first, e.g.:
            // $season->episodes()->delete(); // Delete all episodes belonging to this season

            // Let's assume cascading deletes are set up for episodes.

            $seasonNumber = $season->season_number; // Get info for the success message
            $season->delete();

            return redirect()->route('admin.tv-shows.seasons.index', $tv_show)
                             ->with('success', 'Season ' . $seasonNumber . ' of "' . $tv_show->title . '" and its episodes deleted successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            // This might catch issues if cascading deletes are not set up and there are still
            // existing episodes referencing this season with restrictive foreign keys.
            \Log::error('Error deleting Season: ' . $e->getMessage());
            return redirect()->route('admin.tv-shows.seasons.index', $tv_show)
                             ->with('error', 'Could not delete season. It might still have related episodes that prevent deletion, or a database error occurred.');
        } catch (\Exception $e) {
            \Log::error('Unexpected error deleting Season: ' . $e->getMessage());
            return redirect()->route('admin.tv-shows.seasons.index', $tv_show)
                             ->with('error', 'An unexpected error occurred while trying to delete the season.');
        }
    }
}
