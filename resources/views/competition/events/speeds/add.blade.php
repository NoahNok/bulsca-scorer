@extends('layout')

@section('title')
Add Speeds Event | {{ $comp->name }}
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
    <a href="{{ route('comps.view.events.speeds.add', $comp) }}">Add Speeds Event</a>
</div>

@endsection

@section('content')
<h2 class="mb-0">Add Speeds Event</h2>
<br>
<form action="{{ route('comps.view.events.speeds.addPost', $comp) }}" method="post">
    @csrf
    <div class="grid-4">
        <x-form-select id="event" title="Event" :options="App\Models\SpeedEvent::get()" required></x-form-select>
        <input type="number" name="weight" value="1" required class="hidden">
        <input type="text" name="record" value="00:00.000" required class="hidden">


    </div>
    <button type="submit" class="btn">Add</button>
</form>
@endsection