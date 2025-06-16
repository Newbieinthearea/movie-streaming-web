<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genre_movie', function (Blueprint $table) {
            $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
            $table->foreignId('movie_id')->constrained('movies')->onDelete('cascade');
            $table->primary(['genre_id', 'movie_id']);
            // $table->timestamps(); // Optional, usually not needed for basic pivot
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genre_movie');
    }
};