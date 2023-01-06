@extends('layout')

@section('title')
Events | {{ $comp->name }}
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


@endsection

@section('content')
<h2>Events</h2>
<p>All the events associated with this competition are listed below!</p>
<br>

<h3 class="mb-0">Speed Events</h3>
<br>
<div class="grid-4">
    @foreach ($comp->getSpeedEvents as $event)
    <a href="{{ route('comps.view.events.speeds.view', ['comp' => $comp, 'event' => $event]) }}" class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
        <p class="text-lg font-semibold">{{ $event->getName() }}</p>


    </a>
    @endforeach
    <x-add-card link="{{ route('comps.view.events.speeds.add', $comp) }}" text="Speed Event" />
</div>
<br>

<h3 class="mb-0">SERCs</h3>
<br>
<div class="grid-4">
    @foreach ($comp->getSERCs as $event)
    <a href="{{ route('comps.view.events.sercs.view', ['comp' => $comp, 'serc' => $event]) }}" class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
        <p class="text-lg font-semibold">{{ $event->name }}</p>


    </a>
    @endforeach
    <x-add-card link="{{ route('comps.view.events.sercs.add', $comp) }}" text="SERC" />

</div>
@endsection