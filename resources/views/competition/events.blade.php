@extends('layouts.competition')

@section('title')
    Events
@endsection



@section('content')
    <h2>Events</h2>
    <p>All the events associated with this competition are listed below!</p>
    <br>

    @can('access', [Session::get('ac')])
        <h3 class="mb-0">Speed Events</h3>
        <br>
        <div class="grid-4">
            @foreach ($comp->getSpeedEvents as $event)
                <a href="{{ route('comps.events.speeds.view', ['comp' => $comp, 'event' => $event]) }}" class="se-btn">
                    <p class="text-lg font-semibold">{{ $event->getName() }}</p>


                </a>
            @endforeach

            <x-add-card link="{{ route('comps.events.speeds.add', $comp) }}" text="Speed Event" />
        </div>
        <br>
    @endcan

    <h3 class="mb-0">SERCs</h3>
    <br>
    <div class="grid-4">
        @foreach ($comp->getSERCs as $event)
            <a href="{{ route('comps.events.sercs.view', ['comp' => $comp, 'serc' => $event]) }}" class="se-btn">
                <p class="text-lg font-semibold">{{ $event->name }}</p>


            </a>
        @endforeach
        <x-add-card link="{{ route('comps.events.sercs.add', $comp) }}" text="SERC" />

    </div>
@endsection
