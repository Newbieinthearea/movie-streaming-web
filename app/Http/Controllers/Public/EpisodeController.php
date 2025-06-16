<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Season;
use App\Models\TVShow;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EpisodeController extends Controller
{
    public function watch(int $tv_id, int $season_number, int $episode_number, TmdbService $tmdbService)
    {
        $tvShowDetails = $tmdbService->getTvShowDetails($tv_id);

        if (empty($tvShowDetails) || (isset($tvShowDetails['success']) && $tvShowDetails['success'] === false)) {
            return redirect()->route('home')->with('error', 'Sorry, that TV show could not be found.');
        }
        
        $currentSeason = collect($tvShowDetails['seasons'])->firstWhere('season_number', $season_number);
        
        if (!$currentSeason) {
            return redirect()->route('tv-shows.show', $tv_id)->with('error', 'Sorry, that season could not be found.');
        }

        $embedUrl = "https://vidsrc.xyz/embed/tv?tmdb={$tv_id}&season={$season_number}&episode={$episode_number}";
        
        if (Auth::check()) {
            // Use updateOrCreate to ensure TV show details are always current
            $localTvShow = TVShow::updateOrCreate(
                ['tmdb_id' => $tv_id],
                [
                    'title' => $tvShowDetails['name'],
                    'poster_url' => $tvShowDetails['poster_path'] ? 'https://image.tmdb.org/t/p/w500' . $tvShowDetails['poster_path'] : null,
                    'release_date' => $tvShowDetails['first_air_date'] ?? null,
                ]
            );
            
            $localSeason = Season::firstOrCreate(
                ['tv_show_id' => $localTvShow->id, 'season_number' => $season_number],
                [
                    'title' => $currentSeason['name'] ?? "Season {$season_number}",
                    'tmdb_id' => $currentSeason['id'] ?? null,
                ]
            );

            $episodeDetailsFromApi = $tmdbService->getEpisodeDetails($tv_id, $season_number, $episode_number);
            $localEpisode = Episode::firstOrCreate(
                ['season_id' => $localSeason->id, 'episode_number' => $episode_number],
                ['title' => $episodeDetailsFromApi['name'] ?? "Episode {$episode_number}"]
            );

            Auth::user()->watchHistory()->updateOrCreate(
                ['watchable_type' => Episode::class, 'watchable_id' => $localEpisode->id],
                ['watched_at' => now()]
            );
        }

        $recommendations = $tmdbService->getTvShowRecommendations($tv_id);

        return view('episodes.watch', [
            'tvShow' => $tvShowDetails,
            'currentSeason' => $currentSeason,
            'seasonNumber' => $season_number,
            'currentEpisodeNumber' => $episode_number,
            'embedUrl' => $embedUrl,
            'recommendations' => $recommendations,
        ]);
    }
}