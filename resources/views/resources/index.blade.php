@extends('layouts.app')

@section('title', $heading)
@section('breadcrumb', $heading)

@section('content')
    <section class="page-heading">
        <div>
            <div class="eyebrow"><span class="eyebrow-dot"></span> CATALOG MANAGEMENT</div>
            <h1>{{ $heading }}</h1>
            <p class="page-subtitle">{{ $description }}</p>
        </div>
        <a href="{{ route($createRoute) }}" class="btn btn-primary app-primary-btn"><span>＋</span> Add {{ \Illuminate\Support\Str::singular(strtolower($heading)) }}</a>
    </section>

    <section class="panel-card list-panel">
        <div class="list-toolbar">
            <div><strong>{{ $records->total() }} {{ \Illuminate\Support\Str::plural(strtolower(\Illuminate\Support\Str::singular($heading)), $records->total()) }}</strong><span> in your workspace</span></div>
            <span class="list-filter">All records <b>⌄</b></span>
        </div>
        @if ($rows->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">▣</span>
                <strong>No {{ strtolower($heading) }} yet</strong>
                <span>Create your first record to start organizing your catalog.</span>
                <a href="{{ route($createRoute) }}" class="btn btn-primary app-primary-btn mt-3">Add {{ \Illuminate\Support\Str::singular(strtolower($heading)) }}</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table app-table align-middle">
                    <thead><tr>
                        <th>{{ $columns[0] }}</th>
                        @foreach (array_slice($columns, 1) as $column)<th>{{ strtoupper($column) }}</th>@endforeach
                        <th>DETAILS</th>
                    </tr></thead>
                    <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td><a class="table-title" href="{{ route($resource.'.show', $row['id']) }}">{{ $row['primary'] }}</a></td>
                            @foreach ($row['cells'] as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                            <td><a class="table-action" href="{{ route($resource.'.show', $row['id']) }}">Open record</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if ($records->hasPages())
                <div class="pagination-wrap">{{ $records->links('vendor.pagination.catalog') }}</div>
            @endif
        @endif
    </section>
@endsection
