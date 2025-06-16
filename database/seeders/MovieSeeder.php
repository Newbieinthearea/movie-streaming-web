<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\Genre;

class MovieSeeder extends Seeder
{
    public function run(): void
    {
        // Sample data from TMDB's popular list
        $inception = Movie::create([
            'title' => 'Inception',
            'description' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O., but his tragic past may doom the project and his team to disaster.',
            'release_date' => '2010-07-15',
            'duration' => 148,
            'tmdb_id' => '27205',
            'imdb_id' => 'tt1375666',
            'poster_url' => 'https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq27gAkg7JS2zPpoq.jpg',
        ]);

        $interstellar = Movie::create([
            'title' => 'Interstellar',
            'description' => 'When Earth becomes uninhabitable in the future, a former NASA pilot, Joseph Cooper, is tasked to pilot a spacecraft, along with a team of researchers, to find a new planet for humans.',
            'release_date' => '2014-11-05',
            'duration' => 169,
            'tmdb_id' => '157336',
            'imdb_id' => 'tt0816692',
            'poster_url' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
        ]);

        // Find genres from your database
        $scifi = Genre::where('slug', 'science-fiction')->first();
        $drama = Genre::where('slug', 'drama')->first();
        $action = Genre::where('slug', 'action')->first();

        // Attach genres to the movies
        if ($scifi && $action && $drama) {
            $inception->genres()->attach([$action->id, $scifi->id]);
            $interstellar->genres()->attach([$drama->id, $scifi->id]);
        }
    }
}