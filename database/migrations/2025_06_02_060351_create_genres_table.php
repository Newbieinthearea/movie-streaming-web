<?php // Make sure this is at the top if it's a new file

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
        Schema::create('genres', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing BIGINT primary key 'id'
            $table->string('name')->unique(); // Genre name, must be unique
            $table->string('slug')->unique(); // URL-friendly slug, must be unique
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};