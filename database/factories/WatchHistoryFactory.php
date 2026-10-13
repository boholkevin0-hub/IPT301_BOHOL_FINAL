<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Subscription;
use App\Models\WatchHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<WatchHistory> */
class WatchHistoryFactory extends Factory
{
    protected $model = WatchHistory::class;

    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'movie_id' => Movie::factory(),
            'watched_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'minutes_watched' => fake()->numberBetween(1, 180),
            'completed' => fake()->boolean(40),
            'device' => fake()->randomElement(['Smart TV', 'Mobile', 'Tablet', 'Web browser']),
        ];
    }
}
