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
        Schema::create('tv_shows', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('poster_url')->nullable();
    $table->date('release_date')->nullable();
    $table->string('status')->nullable()->comment('e.g., Returning Series, Ended');
    $table->string('tmdb_id')->unique()->nullable(); // For API data, if used
    $table->timestamps();
    $table->index('title');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_shows');
    }
};
