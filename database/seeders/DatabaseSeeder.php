<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->call([
            // Add other seeders here
            GenreSeeder::class,
            MovieSeeder::class,
            TVShowSeeder::class,   // Depends on Genres
            SeasonSeeder::class,   // Depends on TVShows
            EpisodeSeeder::class,
             // Ensure this is the correct path to your GenreSeeder
            // MovieSeeder::class, // Uncomment if you have a MovieSeeder
            // TVShowSeeder::class, // Uncomment if you have a TVShowSeeder
        ]);
    }
}
