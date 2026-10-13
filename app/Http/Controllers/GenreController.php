<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GenreController extends Controller
{
    public function index()
    {
        $records = Genre::withCount('movies')->orderBy('name')->paginate(10);

        return view('resources.index', [
            'heading' => 'Genres',
            'description' => 'Organize your movie library by genre.',
            'createRoute' => 'genres.create',
            'resource' => 'genres',
            'columns' => ['Genre', 'Movies', 'Description'],
            'rows' => $records->through(fn (Genre $genre) => [
                'id' => $genre->id,
                'primary' => $genre->name,
                'cells' => [$genre->movies_count, $genre->description ?: '—'],
            ]),
            'records' => $records,
        ]);
    }

    public function create()
    {
        return view('resources.form', [
            'heading' => 'Add genre',
            'resource' => 'genres',
            'action' => route('genres.store'),
            'method' => 'POST',
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request)
    {
        $genre = Genre::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:genres,name'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]));

        return redirect()->route('genres.show', $genre)->with('success', 'Genre created successfully.');
    }

    public function show(Genre $genre)
    {
        $genre->load('movies');

        return view('resources.show', [
            'heading' => $genre->name,
            'resource' => 'genres',
            'editRoute' => route('genres.edit', $genre),
            'details' => [
                'Description' => $genre->description ?: 'No description added.',
                'Movies in genre' => (string) $genre->movies->count(),
            ],
            'relatedTitle' => 'Movies in this genre',
            'related' => $genre->movies->map(fn ($movie) => [
                'title' => $movie->title,
                'subtitle' => $movie->release_year.' · '.ucfirst($movie->status),
                'url' => route('movies.show', $movie),
            ]),
            'deleteRoute' => route('genres.destroy', $genre),
        ]);
    }

    public function edit(Genre $genre)
    {
        return view('resources.form', [
            'heading' => 'Edit genre',
            'resource' => 'genres',
            'action' => route('genres.update', $genre),
            'method' => 'PUT',
            'fields' => $this->fields(),
            'item' => $genre,
        ]);
    }

    public function update(Request $request, Genre $genre)
    {
        $genre->update($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('genres', 'name')->ignore($genre->id)],
            'description' => ['nullable', 'string', 'max:2000'],
        ]));

        return redirect()->route('genres.show', $genre)->with('success', 'Genre updated successfully.');
    }

    public function destroy(Genre $genre)
    {
        if ($genre->movies()->exists()) {
            return back()->with('error', 'This genre still has movies. Reassign them before deleting it.');
        }

        $genre->delete();

        return redirect()->route('genres.index')->with('success', 'Genre deleted successfully.');
    }

    private function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Genre name', 'required' => true, 'placeholder' => 'e.g. Science Fiction'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'required' => false],
        ];
    }
}
