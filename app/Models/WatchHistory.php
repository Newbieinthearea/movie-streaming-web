<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'watchable_id',
        'watchable_type',
        'progress',
        'watched_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'watched_at' => 'datetime',
    ];

    /**
     * Get the parent watchable model (movie or episode).
     */
    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the watch history record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}