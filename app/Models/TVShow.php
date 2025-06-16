<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import this
use Illuminate\Database\Eloquent\Relations\HasMany;     // Import this

class TVShow extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tv_shows'; // Explicitly defining table name for clarity

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'poster_url',
        'release_date',
        'status',
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
     * The genres that belong to the TV show.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(
            Genre::class,           // Related Model
            'genre_tv_show',        // Pivot table name
            'tv_show_id',           // Foreign key on pivot table for TVShow (this model)
            'genre_id'              // Foreign key on pivot table for Genre (related model)
        );
    }

    /**
     * Get all of the seasons for the TVShow.
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class, 'tv_show_id');
    }
}