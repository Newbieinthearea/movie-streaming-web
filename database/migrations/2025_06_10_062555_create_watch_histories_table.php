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
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();

            // Link to the user who watched
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // These two columns create the polymorphic relationship
            $table->unsignedBigInteger('watchable_id'); // The ID of the movie OR episode
            $table->string('watchable_type'); // The model name ('App\Models\Movie' or 'App\Models\Episode')

            // Optional: To track progress
            $table->unsignedInteger('progress')->nullable()->comment('Progress in seconds');

            // Timestamp of when it was last watched
            $table->timestamp('watched_at');

            $table->timestamps();

            // Add an index for faster lookups
            $table->index(['watchable_id', 'watchable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};