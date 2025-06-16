<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Season;
use App\Models\TVShow;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EpisodeController extends Controller
{
    public function watch(int $tv_id, int $season_number, int $episode_number, TmdbService $tmdbService)
    {
        // --- Watch History Logic (with on-the-fly creation) ---
        if (Auth::check()) {
            // Step 1: Find or Create the TV Show
            $tvShowDetails = $tmdbService->getTvShowDetails($tv_id);
            $localTvShow = TVShow::firstOrCreate(
                ['tmdb_id' => $tv_id],
                [
                    'title' => $tvShowDetails['name'],
                    'poster_url' => $tvShowDetails['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $tvShowDetails['poster_path'] : null,
                    'release_date' => $tvShowDetails['first_air_date'] ?? null,
                    'status' => $tvShowDetails['status'] ?? null,
                ]
            );

            // Step 2: Find or Create the Season
            // We need to find the specific season details from the API response
            $seasonDetails = collect($tvShowDetails['seasons'])->firstWhere('season_number', $season_number);
            $localSeason = Season::firstOrCreate(
                ['tv_show_id' => $localTvShow->id, 'season_number' => $season_number],
                [
                    'title' => $seasonDetails['name'] ?? "Season {$season_number}",
                    'release_date' => $seasonDetails['air_date'] ?? null,
                    'poster_url' => $seasonDetails['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $seasonDetails['poster_path'] : null,
                    'tmdb_id' => $seasonDetails['id'] ?? null,
                ]
            );

            // Step 3: Find or Create the Episode (This requires another API call)
            // Note: The free TMDB details endpoint doesn't include episode details.
            // A full implementation would require another API call to get episode details here.
            // For now, we'll create a placeholder if it doesn't exist.
            $localEpisode = Episode::firstOrCreate(
                ['season_id' => $localSeason->id, 'episode_number' => $episode_number],
                [
                    'title' => "Episode {$episode_number}", // Placeholder title
                ]
            );

            // Step 4: Save the history
            if ($localEpisode) {
                Auth::user()->watchHistory()->updateOrCreate(
                    ['watchable_type' => Episode::class, 'watchable_id' => $localEpisode->id],
                    ['watched_at' => now()]
                );
            }
        }
        // --- End of Watch History Logic ---


        // Fetch TMDB data for the view
        $embedUrl = "https://vidsrc.xyz/embed/tv?tmdb={$tv_id}&season={$season_number}&episode={$episode_number}";
        $currentSeason = collect($tvShowDetails['seasons'])->firstWhere('season_number', $season_number);
        
        return view('episodes.watch', [
            'tvShow' => $tvShowDetails ?? $tmdbService->getTvShowDetails($tv_id),
            'seasonNumber' => $season_number,
            'episodeNumber' => $episode_number,
            'embedUrl' => $embedUrl,
        ]);
    }
}