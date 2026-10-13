<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Subscription> */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'subscriber_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'plan' => fake()->randomElement(['Basic', 'Standard', 'Premium']),
            'monthly_price' => fake()->randomElement([5.99, 9.99, 14.99]),
            'started_at' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'expires_at' => null,
            'status' => 'active',
        ];
    }
}
