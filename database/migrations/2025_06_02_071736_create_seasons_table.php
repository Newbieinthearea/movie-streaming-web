<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tv_show_id')->constrained('tv_shows')->onDelete('cascade');
    $table->integer('season_number');
    $table->string('title')->nullable();
    $table->text('description')->nullable();
    $table->date('release_date')->nullable();
    $table->string('poster_url')->nullable();
    $table->string('tmdb_id')->unique()->nullable(); // TMDB ID for the season
    $table->timestamps();
    $table->unique(['tv_show_id', 'season_number']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
