@extends('layouts.admin')

@section('title')
    Activity Monitor | Admin
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.index') }}">Admin</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.activity') }}">Activity Monitor</a>
    </div>
@endsection

@section('content')
    <h2>Activity Monitor</h2>

    <div class="se-table">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th class="text-left">Type</th>
                    <th>Description</th>
                    <th>By</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($activities as $activity)
                    <tr class="cursor-pointer">
                        <th class=" font-normal!">{{ $activity->created_at }}</th>
                        <th>{{ $activity->activity }}</th>
                        <td>{{ $activity->description ?? 'N/A' }}</td>

                        <td>{{ $activity->user->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
