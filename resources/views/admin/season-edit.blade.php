@extends('layout')

@section('title')
    Edit Season | Admin
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
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.seasons.edit', ['season' => $season]) }}">Edit Season</a>
    </div>
@endsection

@section('content')
    <h2 class="mb-0">Edit Season: {{ $season->name }}</h2>
    <br>
    <form action="{{ route('admin.seasons.edit.post', ['season' => $season]) }}" method="post">
        @csrf
        <div class="grid-4">
            <x-form-input id="name" title="Name" required placeholder="20XX-20YY"
                defaultValue="{{ $season->name }}"></x-form-input>

        </div>
        <button type="submit" class="btn">Save</button>
    </form>
@endsection
