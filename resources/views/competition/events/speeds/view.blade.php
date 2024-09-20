@extends('layout')

@section('title')
    {{ $event->getName() }} | {{ $comp->name }}
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps') }}">Competitions</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3 ">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.events', $comp) }}">Events</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.events.speeds.view', [$comp, $event]) }}">{{ $event->getName() }}</a>
    </div>
@endsection

@section('content')
    <div class="grid-2">
        <div class="flex flex-col space-y-4" x-data="{
            search: '',
        }">
            <div class="flex justify-between">
                <h2 class="mb-0 mt-0">{{ $event->getName() }}</h2>
                <a href="{{ route('comps.view.events.speeds.edit', [$comp, $event]) }}" class="btn">Edit</a>
            </div>


            <div class="  relative w-full overflow-x-auto  ">
                <div class="form-input imb-0 ">
                    <input type="text" table-search placeholder="Search teams" x-model="search">
                </div>

                <br>
                @include('competition.events.speeds.table_templates.' . $comp->scoring_type)
            </div>

        </div>

        <div class="flex flex-col space-y-4">
            <h2 class="mb-0">Options</h2>
            <div class="card space-y-4">
                <div class="flex justify-between items-center">
                    <strong>Delete event</strong>
                    <form action="{{ route('comps.view.events.speeds.delete', [$comp, $event]) }}"
                        onsubmit="return confirm('Are you sure you want to delete this event!')" method="post">
                        <input type="hidden" name="eid" value="{{ $event->id }}">
                        {{ method_field('DELETE') }}
                        @csrf
                        <button class="btn btn-danger">Delete Event</button>
                    </form>
                </div>


                <div class="flex justify-between items-center">
                    <strong>DigitalJudge @if ($event->digitalJudgeEnabled)
                            (Enabled)
                        @endif
                    </strong>
                    @if ($event->digitalJudgeEnabled)
                        <a href="{{ route('dj.speedToggle', [$comp, $event]) }}" class="btn btn-danger">Disable</a>
                    @else
                        <a href="{{ route('dj.speedToggle', [$comp, $event]) }}" class="btn">Enable</a>
                    @endif
                </div>

                @if ($event->viewable)
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <strong>Hide from Results</strong>
                            <small>This will make this event hidden on the public results area!</small>
                        </div>

                        <div>
                            <a href="{{ route('comps.view.speeds.hide', [$comp, $event]) }}" class="btn btn-danger">Hide
                                Event</a>
                        </div>

                    </div>
                @else
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <strong>Unhide from Results</strong>
                            <small>This will make this event visible on the public results area!</small>
                        </div>

                        <div>
                            <a href="{{ route('comps.view.speeds.hide', [$comp, $event]) }}" class="btn ">Unhide
                                Event</a>

                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
