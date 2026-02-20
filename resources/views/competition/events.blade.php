@extends('layouts.competition')

@section('title')
    Events
@endsection



@section('content')
    <h2>Events</h2>
    <p>All the events associated with this competition are listed below!</p>
    <br>

    @can('access', [$comp, 'speed'])
        <h3 class="mb-0">Speed Events</h3>
        <br>
        <div class="grid-4">
            @foreach ($comp->getSpeedEvents as $event)
                <a href="{{ route('comps.events.speeds.view', ['comp' => $comp, 'event' => $event]) }}"
                    class="se-btn se-btn-inline">
                    <p class="text-lg font-semibold">{{ $event->getName() }}</p>

                    @if ($event->isComplete(true))
                        <span class="tooltip" title="Event Complete">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-green-500" title="ahh">
                                <path fill-rule="evenodd"
                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <span class="tooltip" title="Event Incomplete">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-red-500">
                                <path fill-rule="evenodd"
                                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                    clip-rule="evenodd" />
                            </svg>


                        </span>
                    @endif


                </a>
            @endforeach

            <x-add-card link="{{ route('comps.events.speeds.add', $comp) }}" text="Speed Event" />
        </div>
        <br>
    @endcan



    @can('access', [$comp, ['serc', 'serc_writer']])
        <h3 class="mb-0">SERCs</h3>
        <br>
        <div class="grid-4">
            @foreach ($comp->getSERCs as $event)
                <a href="{{ route('comps.events.sercs.view', ['comp' => $comp, 'serc' => $event]) }}"
                    class="se-btn se-btn-inline">
                    <p class="text-lg font-semibold">{{ $event->name }}</p>

                    @if ($event->isComplete(true))
                        <span class="tooltip" title="Event Complete">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-green-500" title="ahh">
                                <path fill-rule="evenodd"
                                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <span class="tooltip" title="Event Incomplete">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6 text-red-500">
                                <path fill-rule="evenodd"
                                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                    clip-rule="evenodd" />
                            </svg>


                        </span>
                    @endif
                </a>
            @endforeach
            <x-add-card link="{{ route('comps.events.sercs.add', $comp) }}" text="SERC" />

        </div>
    @endcan
@endsection
