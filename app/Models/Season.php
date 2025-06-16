<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import this
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import this

class Season extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tv_show_id',
        'season_number',
        'title',
        'description',
        'release_date',
        'poster_url',
        'tmdb_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'release_date' => 'date',
    ];

    /**
     * Get the TV show that owns the season.
     */
    public function tvShow(): BelongsTo // Note: singular 'tvShow'
    {
        return $this->belongsTo(TVShow::class, 'tv_show_id');
    }

    /**
     * Get all of the episodes for the Season.
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class, 'season_id');
    }
}