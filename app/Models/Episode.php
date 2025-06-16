<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Episode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'episode_number',
        'title',
        'description',
        'release_date',
        'duration',
        'imdb_id',
        'tmdb_id',
        'custom_sub_url',
        'default_sub_lang',
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
     * Get the season that owns the episode.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }
    public function watchHistory(): MorphMany
    {
        return $this->morphMany(WatchHistory::class, 'watchable');
    }
}