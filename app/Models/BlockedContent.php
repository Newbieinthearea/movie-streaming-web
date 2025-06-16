<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class BlockedContent extends Model
{
    use HasFactory;

    protected $table = 'blocked_content';
    protected $fillable = [
        'tmdb_id',
        'type',
        'reason',
    ];
}