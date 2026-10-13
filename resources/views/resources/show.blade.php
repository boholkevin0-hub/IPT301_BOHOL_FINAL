@extends('layouts.app')

@section('title', $heading)
@section('breadcrumb', $heading)

@section('content')
    <section class="page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-dot"></span> CATALOG RECORD</div>
            <h1>{{ $heading }}</h1>
            <p class="page-subtitle">Record details and connected catalog items.</p>
        </div>
        <div class="heading-actions">
            <a href="{{ $editRoute }}" class="btn btn-light app-secondary-btn">Edit record</a>
            <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('Are you sure you want to delete this record? This action cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger delete-btn">Delete</button>
            </form>
        </div>
    </section>

    <div class="detail-grid">
        <section class="panel-card detail-panel">
            <div class="panel-heading"><div><h2>Overview</h2><p>Information saved for this record</p></div><span class="record-chip">{{ ucfirst(\Illuminate\Support\Str::singular($resource)) }}</span></div>
            <dl class="detail-list">
                @foreach ($details as $label => $value)
                    <div class="detail-item">
                        <dt>{{ $label }}</dt>
                        <dd>{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
        <section class="panel-card related-panel">
            <div class="panel-heading"><div><h2>{{ $relatedTitle }}</h2><p>{{ $related->count() }} connected {{ \Illuminate\Support\Str::plural('record', $related->count()) }}</p></div></div>
            @forelse ($related as $record)
                <a class="related-row" href="{{ $record['url'] }}">
                    <span class="related-mark">▣</span>
                    <span><strong>{{ $record['title'] }}</strong><small>{{ $record['subtitle'] }}</small></span>
                    <b>↗</b>
                </a>
            @empty
                <div class="related-empty">Nothing connected yet.</div>
            @endforelse
        </section>
    </div>
@endsection
