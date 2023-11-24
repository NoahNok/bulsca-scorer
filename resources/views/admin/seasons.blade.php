@extends('layout')

@section('title')
    Seasons | Admin
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
        <a href="{{ route('admin.seasons') }}">Seasons</a>
    </div>
@endsection

@section('content')
    <h2 class="mb-0">Seasons</h2>
    <br>
    <div class="grid-4">
        @php
            $seasons = \App\Models\Season::all();
        @endphp
        @foreach ($seasons as $season)
            <a href="{{ route('admin.seasons.edit', ['season' => $season->id]) }}"
                class="flex flex-row items-center shadow-md hover:shadow-lg bg-white p-4 rounded-md border hover:border-black transition-all">
                <h5 class="mb-0">{{ $season->name }}</h5>
            </a>
        @endforeach
        <x-add-card text="Season" link="{{ route('admin.seasons.create') }}"></x-add-card>


    </div>
@endsection
