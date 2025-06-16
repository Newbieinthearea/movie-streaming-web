<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TVShow;
use App\Models\Season;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $breakingBad = TVShow::where('tmdb_id', '1396')->first(); // Find Breaking Bad
        $strangerThings = TVShow::where('tmdb_id', '66732')->first(); // Find Stranger Things

        if ($breakingBad) {
            Season::create([
                'tv_show_id' => $breakingBad->id,
                'season_number' => 1,
                'title' => 'Season 1',
                'release_date' => '2008-01-20',
                'tmdb_id' => '3572', // Season TMDB ID
                'poster_url' => 'https://image.tmdb.org/t/p/w500/1BP4xYv9ZG4ZVHkL0ocOOdK78Qc.jpg'
            ]);
            Season::create([
                'tv_show_id' => $breakingBad->id,
                'season_number' => 2,
                'title' => 'Season 2',
                'release_date' => '2009-03-08',
                'tmdb_id' => '3573',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/e6ejqfVw9t22324U1ZqXyF1N0f.jpg'
            ]);
        }

        if ($strangerThings) {
            Season::create([
                'tv_show_id' => $strangerThings->id,
                'season_number' => 1,
                'title' => 'Season 1',
                'release_date' => '2016-07-15',
                'tmdb_id' => '77099',
                'poster_url' => 'https://image.tmdb.org/t/p/w500/rbNjP7h1UhxHRDPwM1k2J22Uk2N.jpg'
            ]);
        }
    }
}