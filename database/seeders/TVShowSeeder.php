<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TVShow;
use App\Models\Genre;

class TVShowSeeder extends Seeder
{
    public function run(): void
    {
        $breakingBad = TVShow::create([
            'title' => 'Breaking Bad',
            'description' => 'A high school chemistry teacher diagnosed with inoperable lung cancer turns to manufacturing and selling methamphetamine in order to secure his family\'s future.',
            'release_date' => '2008-01-20',
            'status' => 'Ended',
            'tmdb_id' => '1396', // Real TMDB ID
            'poster_url' => 'https://image.tmdb.org/t/p/w500/ggFHVNu6YYI5L9pCfOacjizRGt.jpg',
        ]);

        $drama = Genre::where('slug', 'drama')->first();
        $thriller = Genre::where('slug', 'thriller')->first();

        if ($drama && $thriller) {
            $breakingBad->genres()->attach([$drama->id, $thriller->id]);
        }
    }
}