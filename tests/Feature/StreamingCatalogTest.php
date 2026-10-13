<?php

namespace Tests\Feature;

use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Subscription;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StreamingCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_five_topic_tables_and_catalog_pages_are_available(): void
    {
        foreach (['movies', 'genres', 'actors', 'subscriptions', 'watch_histories'] as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }

        WatchHistory::factory()->create();

        $this->get('/')->assertOk()->assertSee('Your streaming library');
        $this->get(route('movies.index'))->assertOk()->assertSee('Movies');
        $this->get(route('genres.index'))->assertOk()->assertSee('Genres');
        $this->get(route('actors.index'))->assertOk()->assertSee('Actors');
        $this->get(route('subscriptions.index'))->assertOk()->assertSee('Subscriptions');
        $this->get(route('watch-histories.index'))
            ->assertOk()
            ->assertSee('Watch History')
            ->assertSee('DETAILS');

        $this->get(route('movies.create'))->assertOk();
        $this->get(route('genres.create'))->assertOk();
        $this->get(route('actors.create'))->assertOk();
        $this->get(route('subscriptions.create'))->assertOk();
        $this->get(route('watch-histories.create'))->assertOk();
    }

    public function test_related_movies_cast_subscriptions_and_watch_history_can_be_created(): void
    {
        $genre = Genre::factory()->create(['name' => 'Mystery']);
        $this->post(route('movies.store'), [
            'genre_id' => $genre->id,
            'title' => 'The Hidden Archive',
            'description' => 'A mystery movie.',
            'release_year' => 2025,
            'duration_minutes' => 108,
            'rating' => 'PG-13',
            'status' => 'published',
            'poster_url' => '',
        ])->assertRedirect();

        $movie = Movie::where('title', 'The Hidden Archive')->firstOrFail();
        $this->assertSame('the-hidden-archive', $movie->slug);
        $this->assertSame($genre->id, $movie->genre->id);

        $this->post(route('actors.store'), [
            'movie_id' => $movie->id,
            'name' => 'Jordan Example',
            'character_name' => 'Detective Lane',
            'nationality' => 'Canadian',
            'date_of_birth' => '1992-04-11',
        ])->assertRedirect();

        $actor = Actor::where('name', 'Jordan Example')->firstOrFail();
        $subscription = Subscription::factory()->create();

        $this->post(route('watch-histories.store'), [
            'subscription_id' => $subscription->id,
            'movie_id' => $movie->id,
            'watched_at' => '2025-04-01T20:30',
            'minutes_watched' => 108,
            'completed' => 1,
            'device' => 'Smart TV',
        ])->assertRedirect();

        $history = WatchHistory::firstOrFail();
        $this->assertSame($movie->id, $history->movie->id);
        $this->assertSame($subscription->id, $history->subscription->id);
        $this->assertSame($movie->id, $actor->movie->id);

        $this->get(route('movies.show', $movie))->assertOk()->assertSee('Detective Lane');
        $this->get(route('actors.show', $actor))->assertOk()->assertSee('The Hidden Archive');
        $this->get(route('subscriptions.show', $subscription))->assertOk()->assertSee('The Hidden Archive');
        $this->get(route('watch-histories.show', $history))->assertOk()->assertSee('Smart TV');
        $this->get(route('movies.edit', $movie))->assertOk();
        $this->get(route('actors.edit', $actor))->assertOk();
        $this->get(route('subscriptions.edit', $subscription))->assertOk();
        $this->get(route('watch-histories.edit', $history))->assertOk();
    }

    public function test_invalid_relationships_are_rejected(): void
    {
        $this->from(route('movies.create'))
            ->post(route('movies.store'), [
                'genre_id' => 999,
                'title' => 'Orphan Movie',
                'release_year' => 2025,
                'duration_minutes' => 90,
                'rating' => 'PG',
                'status' => 'draft',
            ])
            ->assertRedirect(route('movies.create'))
            ->assertSessionHasErrors('genre_id');

        $this->assertDatabaseMissing('movies', ['title' => 'Orphan Movie']);
    }

    public function test_genres_movies_and_subscriptions_with_history_cannot_be_deleted(): void
    {
        $genre = Genre::factory()->create();
        $movie = Movie::factory()->create(['genre_id' => $genre->id]);
        $subscription = Subscription::factory()->create();
        $history = WatchHistory::factory()->create([
            'movie_id' => $movie->id,
            'subscription_id' => $subscription->id,
        ]);

        $this->from(route('genres.show', $genre))
            ->delete(route('genres.destroy', $genre))
            ->assertRedirect(route('genres.show', $genre))
            ->assertSessionHas('error');

        $this->from(route('movies.show', $movie))
            ->delete(route('movies.destroy', $movie))
            ->assertRedirect(route('movies.show', $movie))
            ->assertSessionHas('error');

        $this->from(route('subscriptions.show', $subscription))
            ->delete(route('subscriptions.destroy', $subscription))
            ->assertRedirect(route('subscriptions.show', $subscription))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('genres', ['id' => $genre->id]);
        $this->assertDatabaseHas('movies', ['id' => $movie->id]);
        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id]);
        $this->assertDatabaseHas('watch_histories', ['id' => $history->id]);
    }

    public function test_genres_can_be_updated_and_deleted_when_unused(): void
    {
        $genre = Genre::factory()->create(['name' => 'Old name']);

        $this->put(route('genres.update', $genre), [
            'name' => 'New name',
            'description' => 'Updated genre description.',
        ])->assertRedirect(route('genres.show', $genre));

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'New name',
            'description' => 'Updated genre description.',
        ]);

        $this->delete(route('genres.destroy', $genre))->assertRedirect(route('genres.index'));
        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }

    public function test_watch_history_table_uses_clear_actions_and_pagination_labels(): void
    {
        $movie = Movie::factory()->create();
        $subscription = Subscription::factory()->create();
        WatchHistory::factory(11)->create([
            'movie_id' => $movie->id,
            'subscription_id' => $subscription->id,
        ]);

        $response = $this->get(route('watch-histories.index'))
            ->assertOk()
            ->assertSee('Open record')
            ->assertSee('Page 1 of 2')
            ->assertSee('11 total records')
            ->assertSee('Previous')
            ->assertSee('Next')
            ->assertDontSee('Showing 1 to 10 of 11 results')
            ->assertDontSee('View details')
            ->assertDontSee('→');
    }
}
