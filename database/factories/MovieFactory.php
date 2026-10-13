<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Movie> */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'genre_id' => Genre::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'release_year' => fake()->numberBetween(1950, (int) date('Y')),
            'duration_minutes' => fake()->numberBetween(75, 180),
            'rating' => fake()->randomElement(['G', 'PG', 'PG-13', 'R']),
            'status' => fake()->randomElement(['published', 'coming_soon', 'draft']),
            'poster_url' => null,
        ];
    }
}
