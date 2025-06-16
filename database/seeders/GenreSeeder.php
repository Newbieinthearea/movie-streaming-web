<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre; // Import the Genre model
use Illuminate\Support\Str; // Import the Str facade for slugs

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define an array of sample genres
        $genres = [
            'Action',
            'Comedy',
            'Drama',
            'Science Fiction',
            'Horror',
            'Thriller',
            'Romance',
            'Animation',
            'Documentary',
            'Fantasy',
        ];

        // Loop through the array and create each genre
        foreach ($genres as $genreName) {
            Genre::create([
                'name' => $genreName,
                'slug' => Str::slug($genreName), // Auto-generate the slug from the name
            ]);
        }

        // You can also create specific genres one by one if you prefer more control
        // Genre::create(['name' => 'Adventure', 'slug' => 'adventure']);
        // Genre::create(['name' => 'Mystery', 'slug' => 'mystery']);
    }
}