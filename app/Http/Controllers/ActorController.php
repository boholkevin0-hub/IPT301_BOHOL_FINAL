<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Movie;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    public function index()
    {
        $records = Actor::with('movie')->orderBy('name')->paginate(10);

        return view('resources.index', [
            'heading' => 'Actors',
            'description' => 'Manage cast members and the movies they appear in.',
            'createRoute' => 'actors.create',
            'resource' => 'actors',
            'columns' => ['Actor', 'Character', 'Movie', 'Nationality'],
            'rows' => $records->through(fn (Actor $actor) => [
                'id' => $actor->id,
                'primary' => $actor->name,
                'cells' => [
                    $actor->character_name ?: '—',
                    $actor->movie->title,
                    $actor->nationality ?: '—',
                ],
            ]),
            'records' => $records,
        ]);
    }

    public function create()
    {
        return view('resources.form', [
            'heading' => 'Add actor',
            'resource' => 'actors',
            'action' => route('actors.store'),
            'method' => 'POST',
            'fields' => $this->fields(),
            'options' => $this->options(),
        ]);
    }

    public function store(Request $request)
    {
        $actor = Actor::create($request->validate($this->rules()));

        return redirect()->route('actors.show', $actor)->with('success', 'Actor added to the movie cast.');
    }

    public function show(Actor $actor)
    {
        $actor->load('movie');

        return view('resources.show', [
            'heading' => $actor->name,
            'resource' => 'actors',
            'editRoute' => route('actors.edit', $actor),
            'details' => [
                'Character' => $actor->character_name ?: 'Not specified',
                'Movie' => $actor->movie->title,
                'Nationality' => $actor->nationality ?: 'Not specified',
                'Date of birth' => $actor->date_of_birth?->format('F j, Y') ?: 'Not specified',
            ],
            'relatedTitle' => 'Movie',
            'related' => collect([[
                'title' => $actor->movie->title,
                'subtitle' => $actor->movie->release_year.' · '.$actor->movie->genre->name,
                'url' => route('movies.show', $actor->movie),
            ]]),
            'deleteRoute' => route('actors.destroy', $actor),
        ]);
    }

    public function edit(Actor $actor)
    {
        return view('resources.form', [
            'heading' => 'Edit actor',
            'resource' => 'actors',
            'action' => route('actors.update', $actor),
            'method' => 'PUT',
            'fields' => $this->fields(),
            'options' => $this->options(),
            'item' => $actor,
        ]);
    }

    public function update(Request $request, Actor $actor)
    {
        $actor->update($request->validate($this->rules()));

        return redirect()->route('actors.show', $actor)->with('success', 'Actor updated successfully.');
    }

    public function destroy(Actor $actor)
    {
        $actor->delete();

        return redirect()->route('actors.index')->with('success', 'Actor removed successfully.');
    }

    private function rules(): array
    {
        return [
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'name' => ['required', 'string', 'max:255'],
            'character_name' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:80'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    private function fields(): array
    {
        return [
            ['name' => 'movie_id', 'label' => 'Movie', 'type' => 'select', 'required' => true, 'options' => 'movies'],
            ['name' => 'name', 'label' => 'Actor name', 'required' => true],
            ['name' => 'character_name', 'label' => 'Character', 'required' => false],
            ['name' => 'nationality', 'label' => 'Nationality', 'required' => false],
            ['name' => 'date_of_birth', 'label' => 'Date of birth', 'type' => 'date', 'required' => false],
        ];
    }

    private function options(): array
    {
        return ['movies' => Movie::orderBy('title')->pluck('title', 'id')];
    }
}
