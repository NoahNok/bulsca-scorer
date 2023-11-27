@extends('layout')

@section('title')
    {{ $comp->name }}
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
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('dj.settings', $comp) }}">DigitalJudge Settings</a>
    </div>
@endsection

@section('content')
    <form method="POST">
        <div class="grid-3">
            <h2 class="mb-0">DigitalJudge Settings</h2>
            <div class="flex items-center justify-center col-start-3">
                <button class="btn ml-auto">Save</button>
            </div>
        </div>
        <br>
        @csrf
        <div class="grid-3">
            <div class="card">
                <h4>Enabled Events</h4>
                <p>Please check all the events below that you want to enable DigitalJudge for:</p>
                <div class="ml-3 mt-1 mb-6">
                    <h5>SERCs</h5>
                    @forelse ($comp->getSERCs as $event)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="se:{{ $event->id }}"
                                @if ($event->digitalJudgeEnabled) checked @endif id="se:{{ $event->id }}">
                            <label for="se:{{ $event->id }}">{{ $event->getName() }}</label>
                        </div>
                    @empty
                        <p>No SERCs found!</p>
                    @endforelse
                    <br>
                    <h5>Speeds</h5>
                    @forelse ($comp->getSpeedEvents as $event)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="sp:{{ $event->id }}"
                                @if ($event->digitalJudgeEnabled) checked @endif id="sp:{{ $event->id }}">
                            <label for="sp:{{ $event->id }}">{{ $event->getName() }}</label>
                        </div>
                    @empty
                        <p>No Speed Events found!</p>
                    @endforelse
                </div>


            </div>

            <div class="card">
                <h4>Heats</h4>
                <x-form-input id="lanes" title="Lanes" type="number" defaultValue="{{ $comp->max_lanes }}" />

            </div>

            <div class="card">
                <h4>Other</h4>
                <x-form-input id="serc_start_time" title="SERC Start Time" required="false" type="datetime-local"
                    defaultValue="{{ $comp->serc_start_time }}"></x-form-input>
                <div class="flex space-x-2">
                    <input type="checkbox" name="can_be_live" @if ($comp->can_be_live) checked @endif
                        id="can_be_live">
                    <label for="can_be_live">Viewable Live</label>
                </div>
            </div>
        </div>
    </form>
@endsection
