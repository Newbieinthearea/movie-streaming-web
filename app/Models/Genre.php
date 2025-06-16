<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import this

class Genre extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The movies that belong to the genre.
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'genre_movie');
    }

    /**
     * The TV shows that belong to the genre.
     */
    public function tvShows(): BelongsToMany
    {
        return $this->belongsToMany(TVShow::class, 'genre_tv_show');
    }
}