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
        Schema::create('genre_tv_show', function (Blueprint $table) {
    $table->foreignId('genre_id')->constrained('genres')->onDelete('cascade');
    $table->foreignId('tv_show_id')->constrained('tv_shows')->onDelete('cascade');
    $table->primary(['genre_id', 'tv_show_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_tv_show');
    }
};
