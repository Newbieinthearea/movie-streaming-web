<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Season;
use App\Models\Episode;

class EpisodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find Breaking Bad Season 1 (assuming its TMDB ID is 3572)
        $bbS1 = Season::where('tmdb_id', '3572')->first();
        if ($bbS1) {
            Episode::create([
                'season_id' => $bbS1->id,
                'episode_number' => 1,
                'title' => 'Pilot',
                'imdb_id' => 'tt0959621',
                'release_date' => '2008-01-20',
                'duration' => 58
            ]);
            Episode::create([
                'season_id' => $bbS1->id,
                'episode_number' => 2,
                'title' => 'Cat\'s in the Bag...',
                'imdb_id' => 'tt1054724',
                'release_date' => '2008-01-27',
                'duration' => 48
            ]);
        }

        // Find Breaking Bad Season 2 (assuming its TMDB ID is 3573)
        $bbS2 = Season::where('tmdb_id', '3573')->first();
        if ($bbS2) {
            Episode::create([
                'season_id' => $bbS2->id,
                'episode_number' => 1,
                'title' => 'Seven Thirty-Seven',
                'imdb_id' => 'tt1232244',
                'release_date' => '2009-03-08',
                'duration' => 47
            ]);
        }

        // Find Stranger Things Season 1 (assuming its TMDB ID is 77099)
        $stS1 = Season::where('tmdb_id', '77099')->first();
        if ($stS1) {
            Episode::create([
                'season_id' => $stS1->id,
                'episode_number' => 1,
                'title' => 'Chapter One: The Vanishing of Will Byers',
                'imdb_id' => 'tt4574334',
                'release_date' => '2016-07-15',
                'duration' => 49
            ]);
            Episode::create([
                'season_id' => $stS1->id,
                'episode_number' => 2,
                'title' => 'Chapter Two: The Weirdo on Maple Street',
                'imdb_id' => 'tt5580186',
                'release_date' => '2016-07-15',
                'duration' => 57
            ]);
        }
    }
}