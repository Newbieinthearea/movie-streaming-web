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
        Schema::create('episodes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('season_id')->constrained('seasons')->onDelete('cascade');
    $table->integer('episode_number');
    $table->string('title');
    $table->text('description')->nullable();
    $table->date('release_date')->nullable();
    $table->integer('duration')->nullable()->comment('Duration in minutes');
    $table->string('imdb_id')->unique()->nullable(); // For VidSrc API
    $table->string('tmdb_id')->unique()->nullable(); // For VidSrc API or metadata
    $table->string('custom_sub_url')->nullable();
    $table->string('default_sub_lang', 10)->nullable();
    $table->timestamps();
    $table->unique(['season_id', 'episode_number']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
