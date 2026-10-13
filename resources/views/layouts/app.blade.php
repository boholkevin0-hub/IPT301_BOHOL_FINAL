<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Overview') · Reelbase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-mark">▶</span>
            <span>REELBASE<small>STREAMING STUDIO</small></span>
        </a>
        <div class="sidebar-label">WORKSPACE</div>
        <nav class="side-nav" aria-label="Main navigation">
            <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <span class="nav-icon">⌂</span> Overview
            </a>
            <a class="nav-item {{ request()->routeIs('movies.*') ? 'active' : '' }}" href="{{ route('movies.index') }}">
                <span class="nav-icon">▣</span> Movies <span class="nav-count">{{ \App\Models\Movie::count() }}</span>
            </a>
            <a class="nav-item {{ request()->routeIs('genres.*') ? 'active' : '' }}" href="{{ route('genres.index') }}">
                <span class="nav-icon">◈</span> Genres
            </a>
            <a class="nav-item {{ request()->routeIs('actors.*') ? 'active' : '' }}" href="{{ route('actors.index') }}">
                <span class="nav-icon">♙</span> Actors
            </a>
            <a class="nav-item {{ request()->routeIs('subscriptions.*') ? 'active' : '' }}" href="{{ route('subscriptions.index') }}">
                <span class="nav-icon">▤</span> Subscriptions
            </a>
            <a class="nav-item {{ request()->routeIs('watch-histories.*') ? 'active' : '' }}" href="{{ route('watch-histories.index') }}">
                <span class="nav-icon">◷</span> Watch History
            </a>
        </nav>
        <div class="sidebar-bottom">
            <div class="help-card">
                <span class="help-icon">✦</span>
                <strong>Your library, in sync.</strong>
                <span>Manage movies, subscribers, casts, and watch activity.</span>
            </div>
            <div class="profile-row">
                <div class="avatar">RS</div>
                <div class="profile-copy"><strong>Studio Admin</strong><span>Content manager</span></div>
                <span class="profile-menu">···</span>
            </div>
        </div>
    </aside>
    <main class="main-panel">
        <header class="topbar">
            <div class="breadcrumb-label">STUDIO <span>/</span> @yield('breadcrumb', 'Overview')</div>
            <div class="topbar-right">
                <span class="live-indicator"><i></i> Streaming content workspace</span>
                <div class="top-avatar">RS</div>
            </div>
        </header>
        <div class="page-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show app-alert" role="alert">
                    <span class="alert-check">✓</span> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show app-alert" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
