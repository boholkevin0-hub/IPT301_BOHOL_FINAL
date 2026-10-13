<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Subscription;
use App\Models\WatchHistory;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'movieCount' => Movie::count(),
            'publishedCount' => Movie::where('status', 'published')->count(),
            'subscriptionCount' => Subscription::where('status', 'active')->count(),
            'actorCount' => Actor::count(),
            'genreCount' => Genre::count(),
            'watchHistoryCount' => WatchHistory::count(),
            'recentMovies' => Movie::with('genre')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
