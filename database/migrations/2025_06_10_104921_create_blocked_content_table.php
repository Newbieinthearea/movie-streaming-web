<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocked_content', function (Blueprint $table) {
            $table->id();
            // Store the TMDB ID of the content to block
            $table->unsignedBigInteger('tmdb_id')->unique();
            // Store the type (movie or tv) for easier management
            $table->string('type');
            // Optional: A reason for blocking, for your own reference
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_content');
    }
};