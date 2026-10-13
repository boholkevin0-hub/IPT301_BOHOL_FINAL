<?php

namespace Database\Factories;

use App\Models\Actor;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Actor> */
class ActorFactory extends Factory
{
    protected $model = Actor::class;

    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'name' => fake()->name(),
            'character_name' => fake()->firstName(),
            'nationality' => fake()->country(),
            'date_of_birth' => fake()->date(),
        ];
    }
}
