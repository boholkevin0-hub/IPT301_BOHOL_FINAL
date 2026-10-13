@extends('layouts.app')

@section('title', 'Overview')
@section('breadcrumb', 'Overview')

@section('content')
    <section class="welcome-row dashboard-hero">
        <div class="hero-copy">
            <div class="eyebrow"><span class="eyebrow-dot"></span> {{ now()->format('l, F j, Y') }} <span class="hero-divider">/</span> YOUR CONTENT, AT A GLANCE</div>
            <h1>Your streaming library,<br><span>all in one place.</span></h1>
            <p class="page-subtitle">Manage your movies, subscribers, cast, and viewing activity.</p>
        </div>
        <div class="hero-action">
            <span class="hero-kicker">READY FOR THE NEXT RELEASE?</span>
            <a href="{{ route('movies.create') }}" class="btn btn-primary app-primary-btn"><span>＋</span> Add a movie</a>
        </div>
        <span class="hero-orbit hero-orbit-one"></span>
        <span class="hero-orbit hero-orbit-two"></span>
    </section>

    <section class="stat-grid">
        <article class="stat-card">
            <div class="stat-top"><span class="stat-label">TOTAL MOVIES</span><span class="stat-icon violet">▣</span></div>
            <div class="stat-value">{{ number_format($movieCount) }}</div>
            <div class="stat-foot"><span class="stat-note">Across your library</span><a href="{{ route('movies.index') }}">View movies <span>→</span></a></div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span class="stat-label">ACTIVE SUBSCRIPTIONS</span><span class="stat-icon green">↗</span></div>
            <div class="stat-value">{{ number_format($subscriptionCount) }}</div>
            <div class="stat-foot"><span class="status-pip"></span><span class="stat-note">Current members</span></div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span class="stat-label">CAST MEMBERS</span><span class="stat-icon orange">♙</span></div>
            <div class="stat-value">{{ number_format($actorCount) }}</div>
            <div class="stat-foot"><span class="stat-note">{{ number_format($genreCount) }} genres</span><a href="{{ route('actors.index') }}">Manage cast <span>→</span></a></div>
        </article>
        <article class="stat-card">
            <div class="stat-top"><span class="stat-label">WATCH SESSIONS</span><span class="stat-icon blue">◷</span></div>
            <div class="stat-value">{{ number_format($watchHistoryCount) }}</div>
            <div class="stat-foot"><span class="stat-note">Recorded viewing activity</span></div>
        </article>
    </section>

    <section class="dashboard-grid">
        <div class="panel-card recent-panel">
            <div class="panel-heading">
                <div><h2>Recently added movies</h2><p>Your latest catalog updates</p></div>
                <a href="{{ route('movies.index') }}" class="text-link">View all <span>→</span></a>
            </div>
            @if ($recentMovies->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">▣</span>
                    <strong>Your library is ready for its first movie</strong>
                    <span>Add a movie to start building your catalog.</span>
                    <a href="{{ route('movies.create') }}" class="btn btn-primary app-primary-btn mt-3">Add your first movie</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table app-table recent-table align-middle">
                        <thead><tr><th>MOVIE</th><th>GENRE</th><th>YEAR</th><th>STATUS</th><th></th></tr></thead>
                        <tbody>
                        @foreach ($recentMovies as $movie)
                            <tr>
                                <td><a class="table-title" href="{{ route('movies.show', $movie) }}">{{ $movie->title }}</a><span class="table-subtitle">{{ $movie->duration_minutes }} minutes · {{ $movie->rating }}</span></td>
                                <td>{{ $movie->genre->name }}</td>
                                <td>{{ $movie->release_year }}</td>
                                <td><span class="status-badge status-{{ $movie->status }}">{{ str_replace('_', ' ', $movie->status) }}</span></td>
                                <td><a class="row-arrow" href="{{ route('movies.show', $movie) }}" aria-label="View {{ $movie->title }}">↗</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="panel-card quick-panel">
            <div class="panel-heading"><div><h2>Quick actions</h2><p>Jump into your workflow</p></div></div>
            <a class="quick-link" href="{{ route('movies.create') }}"><span class="quick-icon quick-violet">＋</span><span><strong>Add a movie</strong><small>Grow your streaming library</small></span><b>→</b></a>
            <a class="quick-link" href="{{ route('subscriptions.create') }}"><span class="quick-icon quick-blue">▤</span><span><strong>New subscription</strong><small>Register a subscriber</small></span><b>→</b></a>
            <a class="quick-link" href="{{ route('watch-histories.create') }}"><span class="quick-icon quick-orange">◷</span><span><strong>Log watch history</strong><small>Record a viewing session</small></span><b>→</b></a>
            <div class="catalog-summary">
                <div class="summary-heading"><strong>Library overview</strong><span>MANAGE</span></div>
                <div class="summary-row"><span>Genres</span><a href="{{ route('genres.index') }}">{{ $genreCount }} <b>→</b></a></div>
                <div class="summary-row"><span>Watch sessions</span><a href="{{ route('watch-histories.index') }}">{{ $watchHistoryCount }} <b>→</b></a></div>
            </div>
        </div>
    </section>
    <footer class="page-footer">REELBASE CONTENT STUDIO <span>·</span> Your streaming catalog, made simple.</footer>
@endsection
