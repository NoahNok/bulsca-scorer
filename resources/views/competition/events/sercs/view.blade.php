@extends('layout')

@section('title')
{{ $serc->name }} | {{ $comp->name }}
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events', $comp) }}">Events</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}">{{ $serc->name }}</a>
</div>

@endsection

@section('content')
<div class="grid grid-cols-2 gap-6">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">{{ $serc->name }}</h2>
            <a href="{{ route('comps.view.events.sercs.edit', [$comp, $serc]) }}" class="btn">Edit SERC Setup</a>
        </div>

    </div>

    <div class="flex flex-col space-y-4">
        <h2 class="mb-0">Options</h2>
        <div class="card">
            <div class="flex justify-between items-center">
                <strong>Delete SERC</strong>
                <form action="{{ route('comps.view.events.sercs.delete', [$comp, $serc]) }}" onsubmit="return confirm('Are you sure you want to delete this SERC!')" method="post">
                    <input type="hidden" name="sid" value="{{ $serc->id }}">
                    {{ method_field('DELETE') }}
                    @csrf
                    <button class="btn btn-danger">Delete SERC</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection