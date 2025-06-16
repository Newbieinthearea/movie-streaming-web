<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('poster_url')->nullable();
            $table->date('release_date')->nullable();
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->string('imdb_id')->unique()->nullable();
            $table->string('tmdb_id')->unique()->nullable();
            $table->string('custom_sub_url')->nullable()->comment('URL to .srt or .vtt subtitle file');
            $table->string('default_sub_lang', 10)->nullable()->comment('ISO 639 language code');
            $table->timestamps();
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};