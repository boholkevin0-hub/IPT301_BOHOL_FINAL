<?php

namespace App\Models;

use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'subscriber_name',
        'email',
        'plan',
        'monthly_price',
        'started_at',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'expires_at' => 'date',
            'monthly_price' => 'decimal:2',
        ];
    }

    public function watchHistory(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }
}
