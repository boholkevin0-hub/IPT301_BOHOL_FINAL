<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MovieController extends Controller
{
    public function index()
    {
        $records = Movie::with('genre')->latest()->paginate(10);

        return view('resources.index', [
            'heading' => 'Movies',
            'description' => 'Manage movie titles, release details, and availability.',
            'createRoute' => 'movies.create',
            'resource' => 'movies',
            'columns' => ['Movie', 'Genre', 'Year', 'Runtime', 'Rating', 'Status'],
            'rows' => $records->through(fn (Movie $movie) => [
                'id' => $movie->id,
                'primary' => $movie->title,
                'cells' => [
                    $movie->genre->name,
                    $movie->release_year,
                    $movie->duration_minutes.' min',
                    $movie->rating,
                    ucfirst(str_replace('_', ' ', $movie->status)),
                ],
            ]),
            'records' => $records,
        ]);
    }

    public function create()
    {
        return view('resources.form', [
            'heading' => 'Add movie',
            'resource' => 'movies',
            'action' => route('movies.store'),
            'method' => 'POST',
            'fields' => $this->fields(),
            'options' => $this->options(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['slug'] = $this->uniqueSlug($data['title']);
        $movie = Movie::create($data);

        return redirect()->route('movies.show', $movie)->with('success', 'Movie added to your catalog.');
    }

    public function show(Movie $movie)
    {
        $movie->load(['genre', 'actors', 'watchHistory.subscription']);

        return view('resources.show', [
            'heading' => $movie->title,
            'resource' => 'movies',
            'editRoute' => route('movies.edit', $movie),
            'details' => [
                'Genre' => $movie->genre->name,
                'Release year' => (string) $movie->release_year,
                'Runtime' => $movie->duration_minutes.' minutes',
                'Rating' => $movie->rating,
                'Status' => ucfirst(str_replace('_', ' ', $movie->status)),
                'Description' => $movie->description ?: 'No description added.',
                'Watch sessions' => (string) $movie->watchHistory->count(),
            ],
            'relatedTitle' => 'Cast',
            'related' => $movie->actors->map(fn ($actor) => [
                'title' => $actor->name,
                'subtitle' => $actor->character_name ? 'as '.$actor->character_name : ($actor->nationality ?: 'Cast member'),
                'url' => route('actors.show', $actor),
            ]),
            'deleteRoute' => route('movies.destroy', $movie),
        ]);
    }

    public function edit(Movie $movie)
    {
        return view('resources.form', [
            'heading' => 'Edit movie',
            'resource' => 'movies',
            'action' => route('movies.update', $movie),
            'method' => 'PUT',
            'fields' => $this->fields(),
            'options' => $this->options(),
            'item' => $movie,
        ]);
    }

    public function update(Request $request, Movie $movie)
    {
        $data = $request->validate($this->rules());
        if ($movie->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $movie->id);
        }
        $movie->update($data);

        return redirect()->route('movies.show', $movie)->with('success', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie)
    {
        if ($movie->watchHistory()->exists()) {
            return back()->with('error', 'This movie has watch history. Remove those records before deleting it.');
        }

        $movie->delete();

        return redirect()->route('movies.index')->with('success', 'Movie deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'release_year' => ['required', 'integer', 'between:1888,'.(date('Y') + 5)],
            'duration_minutes' => ['required', 'integer', 'between:1,999'],
            'rating' => ['required', Rule::in(['G', 'PG', 'PG-13', 'R', 'NC-17'])],
            'status' => ['required', Rule::in(['published', 'coming_soon', 'draft'])],
            'poster_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    private function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Movie title', 'required' => true, 'placeholder' => 'e.g. The Last Signal'],
            ['name' => 'genre_id', 'label' => 'Genre', 'type' => 'select', 'required' => true, 'options' => 'genres'],
            ['name' => 'release_year', 'label' => 'Release year', 'type' => 'number', 'required' => true, 'min' => 1888, 'max' => date('Y') + 5],
            ['name' => 'duration_minutes', 'label' => 'Runtime (minutes)', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 999],
            ['name' => 'rating', 'label' => 'Content rating', 'type' => 'select', 'required' => true, 'choices' => ['G' => 'G', 'PG' => 'PG', 'PG-13' => 'PG-13', 'R' => 'R', 'NC-17' => 'NC-17']],
            ['name' => 'status', 'label' => 'Catalog status', 'type' => 'select', 'required' => true, 'choices' => ['draft' => 'Draft', 'coming_soon' => 'Coming soon', 'published' => 'Published']],
            ['name' => 'poster_url', 'label' => 'Poster image URL', 'type' => 'url', 'required' => false, 'placeholder' => 'https://example.com/poster.jpg'],
            ['name' => 'description', 'label' => 'Synopsis', 'type' => 'textarea', 'required' => false],
        ];
    }

    private function options(): array
    {
        return ['genres' => Genre::orderBy('name')->pluck('name', 'id')];
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'movie';
        $slug = $base;
        $number = 2;

        while (Movie::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$number++;
        }

        return $slug;
    }
}
