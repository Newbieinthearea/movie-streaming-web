<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // For validation rules

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Season $season)
    {
         $episodes = $season->episodes()->orderBy('episode_number')->paginate(10);
        return view('admin.episodes.index', compact('season', 'episodes'));
    
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Season $season)
    {
         return view('admin.episodes.create', compact('season'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Season $season)
    {
        $validatedData = $request->validate([
            'episode_number' => [
                'required',
                'integer',
                'min:0',
                // Ensure episode_number is unique for this specific season_id
                Rule::unique('episodes')->where(function ($query) use ($season) {
                    return $query->where('season_id', $season->id);
                }),
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'imdb_id' => 'nullable|string|max:20|unique:episodes,imdb_id', // IMDB ID for episode should be unique across all episodes
            'tmdb_id' => 'nullable|string|max:20|unique:episodes,tmdb_id', // TMDB ID for episode should be unique across all episodes
            'custom_sub_url' => 'nullable|url|max:2048',
            'default_sub_lang' => 'nullable|string|max:10',
        ]);

        // 2. Add the season_id to the validated data before creation
        $dataToStore = $validatedData;
        $dataToStore['season_id'] = $season->id;

        // 3. Create the new Episode
        Episode::create($dataToStore);

        // 4. Redirect back to the episodes index page for this Season with a success message
        return redirect()->route('admin.seasons.episodes.index', $season)
                         ->with('success', 'Episode created successfully for Season ' . $season->season_number . ' of ' . $season->tvShow->title . '!');
    
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Season $season, Episode $episode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Season $season, Episode $episode)
    {
         // Get the parent season for context (e.g., for breadcrumbs, cancel links)
        $season = $episode->season; // Assuming the 'season' relationship exists on the Episode model

        return view('admin.episodes.edit', compact('season', 'episode'));
        
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Season $season, Episode $episode)
    {
        $validatedData = $request->validate([
            'episode_number' => [
                'required',
                'integer',
                'min:0',
                // Ensure episode_number is unique for this specific season_id, ignoring current episode
                Rule::unique('episodes')->where(function ($query) use ($episode) {
                    return $query->where('season_id', $episode->season_id);
                })->ignore($episode->id),
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'duration' => 'nullable|integer|min:0',
            'imdb_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('episodes', 'imdb_id')->ignore($episode->id), // IMDB ID unique, ignore current
            ],
            'tmdb_id' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('episodes', 'tmdb_id')->ignore($episode->id), // TMDB ID unique, ignore current
            ],
            'custom_sub_url' => 'nullable|url|max:2048',
            'default_sub_lang' => 'nullable|string|max:10',
        ]);

        // 2. Update the episode's attributes
        // Note: season_id should not change for an existing episode from this form.
        $episode->update($validatedData);

        // 3. Redirect back to the episodes index page for the episode's season
        return redirect()->route('admin.seasons.episodes.index', $episode->season_id) // Use $episode->season_id or $episode->season
                         ->with('success', 'Episode updated successfully!');
    
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Season $season, Episode $episode)
    {
        //

        try {
            // Store the parent season_id for redirection before deleting the episode
            $seasonId = $episode->season_id;
            // Or, if you want to pass the whole season object to the route later:
            // $season = $episode->season;

            $episodeTitle = $episode->title; // Get info for the success message
            $episode->delete();

            // Redirect back to the episodes index page for the parent season
            return redirect()->route('admin.seasons.episodes.index', $seasonId) // or $season
                             ->with('success', 'Episode "' . $episodeTitle . '" deleted successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Error deleting Episode: ' . $e->getMessage());
            // It's unlikely to have foreign key issues deleting an episode unless other tables reference it directly.
            // We need to ensure we can redirect back to the correct season's episode list.
            // If $episode is deleted, $episode->season_id might still be accessible if fetched before delete,
            // but it's safer to get it before the delete operation.
            // If $seasonId couldn't be reliably fetched, redirect to a more general admin page.
            return redirect()->back() // Or a more general admin page like admin TV show list
                             ->with('error', 'Could not delete episode due to a database error.');
        } catch (\Exception $e) {
            \Log::error('Unexpected error deleting Episode: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'An unexpected error occurred while trying to delete the episode.');
        }
    }
}
