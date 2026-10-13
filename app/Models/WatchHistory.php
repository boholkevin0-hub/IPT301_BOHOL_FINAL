<?php

namespace App\Models;

use Database\Factories\WatchHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    /** @use HasFactory<WatchHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'movie_id',
        'watched_at',
        'minutes_watched',
        'completed',
        'device',
    ];

    protected function casts(): array
    {
        return [
            'watched_at' => 'datetime',
            'completed' => 'boolean',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
