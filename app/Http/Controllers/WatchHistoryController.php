<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Subscription;
use App\Models\WatchHistory;
use Illuminate\Http\Request;

class WatchHistoryController extends Controller
{
    public function index()
    {
        $records = WatchHistory::with(['movie', 'subscription'])->latest('watched_at')->paginate(10);

        return view('resources.index', [
            'heading' => 'Watch History',
            'description' => 'Review the movies watched by each subscriber.',
            'createRoute' => 'watch-histories.create',
            'resource' => 'watch-histories',
            'columns' => ['Movie', 'Subscriber', 'Watched at', 'Minutes', 'Completed'],
            'rows' => $records->through(fn (WatchHistory $history) => [
                'id' => $history->id,
                'primary' => $history->movie->title,
                'cells' => [
                    $history->subscription->subscriber_name,
                    $history->watched_at->format('M j, Y g:i A'),
                    $history->minutes_watched.' min',
                    $history->completed ? 'Yes' : 'No',
                ],
            ]),
            'records' => $records,
        ]);
    }

    public function create()
    {
        return view('resources.form', [
            'heading' => 'Record a watch session',
            'resource' => 'watch-histories',
            'action' => route('watch-histories.store'),
            'method' => 'POST',
            'fields' => $this->fields(),
            'options' => $this->options(),
        ]);
    }

    public function store(Request $request)
    {
        $history = WatchHistory::create($request->validate($this->rules()));

        return redirect()->route('watch-histories.show', $history)->with('success', 'Watch session recorded successfully.');
    }

    public function show(WatchHistory $watchHistory)
    {
        $watchHistory->load(['movie', 'subscription']);

        return view('resources.show', [
            'heading' => 'Watch session · '.$watchHistory->movie->title,
            'resource' => 'watch-histories',
            'editRoute' => route('watch-histories.edit', $watchHistory),
            'details' => [
                'Subscriber' => $watchHistory->subscription->subscriber_name,
                'Email' => $watchHistory->subscription->email,
                'Movie' => $watchHistory->movie->title,
                'Watched at' => $watchHistory->watched_at->format('F j, Y g:i A'),
                'Minutes watched' => (string) $watchHistory->minutes_watched,
                'Completed' => $watchHistory->completed ? 'Yes' : 'No',
                'Device' => $watchHistory->device ?: 'Not specified',
            ],
            'relatedTitle' => 'Related records',
            'related' => collect([
                [
                    'title' => $watchHistory->movie->title,
                    'subtitle' => 'Movie · '.$watchHistory->movie->genre->name,
                    'url' => route('movies.show', $watchHistory->movie),
                ],
                [
                    'title' => $watchHistory->subscription->subscriber_name,
                    'subtitle' => $watchHistory->subscription->plan.' subscription',
                    'url' => route('subscriptions.show', $watchHistory->subscription),
                ],
            ]),
            'deleteRoute' => route('watch-histories.destroy', $watchHistory),
        ]);
    }

    public function edit(WatchHistory $watchHistory)
    {
        return view('resources.form', [
            'heading' => 'Edit watch session',
            'resource' => 'watch-histories',
            'action' => route('watch-histories.update', $watchHistory),
            'method' => 'PUT',
            'fields' => $this->fields(),
            'options' => $this->options(),
            'item' => $watchHistory,
        ]);
    }

    public function update(Request $request, WatchHistory $watchHistory)
    {
        $watchHistory->update($request->validate($this->rules()));

        return redirect()->route('watch-histories.show', $watchHistory)->with('success', 'Watch session updated successfully.');
    }

    public function destroy(WatchHistory $watchHistory)
    {
        $watchHistory->delete();

        return redirect()->route('watch-histories.index')->with('success', 'Watch session deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'subscription_id' => ['required', 'integer', 'exists:subscriptions,id'],
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'watched_at' => ['required', 'date'],
            'minutes_watched' => ['required', 'integer', 'min:0', 'max:999'],
            'completed' => ['required', 'boolean'],
            'device' => ['nullable', 'string', 'max:80'],
        ];
    }

    private function fields(): array
    {
        return [
            ['name' => 'subscription_id', 'label' => 'Subscriber', 'type' => 'select', 'required' => true, 'options' => 'subscriptions'],
            ['name' => 'movie_id', 'label' => 'Movie', 'type' => 'select', 'required' => true, 'options' => 'movies'],
            ['name' => 'watched_at', 'label' => 'Watched at', 'type' => 'datetime-local', 'required' => true],
            ['name' => 'minutes_watched', 'label' => 'Minutes watched', 'type' => 'number', 'required' => true, 'min' => 0, 'max' => 999],
            ['name' => 'completed', 'label' => 'Completed', 'type' => 'select', 'required' => true, 'choices' => ['0' => 'No', '1' => 'Yes']],
            ['name' => 'device', 'label' => 'Device', 'required' => false, 'placeholder' => 'e.g. Smart TV'],
        ];
    }

    private function options(): array
    {
        return [
            'subscriptions' => Subscription::orderBy('subscriber_name')
                ->get()->mapWithKeys(fn (Subscription $subscription) => [
                    $subscription->id => $subscription->subscriber_name.' · '.$subscription->email,
                ]),
            'movies' => Movie::orderBy('title')->pluck('title', 'id'),
        ];
    }
}
