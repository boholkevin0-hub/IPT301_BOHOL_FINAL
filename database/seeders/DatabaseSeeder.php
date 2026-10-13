<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Subscription;
use App\Models\WatchHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $genres = collect(['Drama', 'Documentary', 'Comedy', 'Science Fiction'])
            ->mapWithKeys(fn (string $name) => [
                $name => Genre::firstOrCreate(
                    ['name' => $name],
                    ['description' => fake()->sentence()],
                ),
            ]);

        $movies = collect([
            ['title' => 'The Last Signal', 'genre' => 'Science Fiction', 'year' => 2025, 'duration' => 118, 'rating' => 'PG-13'],
            ['title' => 'Quiet Earth', 'genre' => 'Documentary', 'year' => 2024, 'duration' => 94, 'rating' => 'PG'],
            ['title' => 'Second Chances', 'genre' => 'Drama', 'year' => 2023, 'duration' => 106, 'rating' => 'PG-13'],
            ['title' => 'Laugh Track', 'genre' => 'Comedy', 'year' => 2025, 'duration' => 97, 'rating' => 'PG'],
            ['title' => 'The Deep Blue', 'genre' => 'Documentary', 'year' => 2022, 'duration' => 88, 'rating' => 'G'],
            ['title' => 'After the Rain', 'genre' => 'Drama', 'year' => 2024, 'duration' => 112, 'rating' => 'R'],
        ])->map(function (array $data) use ($genres) {
            $slug = Str::slug($data['title']);
            $movie = Movie::firstOrCreate(
                ['slug' => $slug],
                [
                    'genre_id' => $genres[$data['genre']]->id,
                    'title' => $data['title'],
                    'description' => fake()->paragraph(),
                    'release_year' => $data['year'],
                    'duration_minutes' => $data['duration'],
                    'rating' => $data['rating'],
                    'status' => 'published',
                ],
            );

            foreach ([
                ['name' => 'Morgan Ellis', 'character_name' => 'Alex Mercer'],
                ['name' => 'Riley Chen', 'character_name' => 'Sam Rivera'],
            ] as $cast) {
                Actor::firstOrCreate(
                    ['movie_id' => $movie->id, 'name' => $cast['name']],
                    [
                        'character_name' => $cast['character_name'],
                        'nationality' => 'United States',
                        'date_of_birth' => '1992-04-11',
                    ],
                );
            }

            return $movie;
        });

        $subscriptions = collect([
            ['name' => 'Avery Johnson', 'email' => 'avery.johnson@example.test', 'plan' => 'Premium', 'price' => 14.99],
            ['name' => 'Jordan Lee', 'email' => 'jordan.lee@example.test', 'plan' => 'Standard', 'price' => 9.99],
            ['name' => 'Taylor Morgan', 'email' => 'taylor.morgan@example.test', 'plan' => 'Basic', 'price' => 5.99],
            ['name' => 'Casey Brown', 'email' => 'casey.brown@example.test', 'plan' => 'Premium', 'price' => 14.99],
            ['name' => 'Quinn Davis', 'email' => 'quinn.davis@example.test', 'plan' => 'Standard', 'price' => 9.99],
            ['name' => 'Jamie Wilson', 'email' => 'jamie.wilson@example.test', 'plan' => 'Basic', 'price' => 5.99],
        ])->map(fn (array $data) => Subscription::firstOrCreate(
            ['email' => $data['email']],
            [
                'subscriber_name' => $data['name'],
                'plan' => $data['plan'],
                'monthly_price' => $data['price'],
                'started_at' => now()->subMonths(3)->toDateString(),
                'status' => 'active',
            ],
        ));

        foreach ($subscriptions as $subscriptionIndex => $subscription) {
            foreach (range(0, 2) as $watchIndex) {
                $movie = $movies[($subscriptionIndex + $watchIndex) % $movies->count()];
                $watchedAt = now()->subDays(($subscriptionIndex * 3) + $watchIndex + 1)->setTime(20, 15);

                WatchHistory::firstOrCreate(
                    [
                        'subscription_id' => $subscription->id,
                        'movie_id' => $movie->id,
                    ],
                    [
                        'watched_at' => $watchedAt,
                        'minutes_watched' => $movie->duration_minutes,
                        'completed' => true,
                        'device' => ['Smart TV', 'Mobile', 'Web browser'][$watchIndex],
                    ],
                );
            }
        }
    }
}
