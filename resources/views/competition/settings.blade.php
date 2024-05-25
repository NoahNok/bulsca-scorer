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
        <a href="{{ route('dj.settings', $comp) }}">Settings</a>
    </div>
@endsection

@section('content')
    <form method="POST">
        <div class="grid-3">
            <h2 class="mb-0">Settings</h2>
            <div class="flex items-center justify-center col-start-3">
                <button class="btn ml-auto">Save</button>
            </div>
        </div>
        <br>
        @csrf
        <div class="grid-3">
            <div class="card">
                <h4>Heats</h4>
                <x-form-input id="lanes" title="Lanes" type="number" defaultValue="{{ $comp->max_lanes }}" />

            </div>

            <div class="card">
                <h4>Other</h4>
                @php
                    $sercStart = $comp->serc_start_time;
                    $sercStart?->setTimezone('BST');
                @endphp
                <x-form-input id="serc_start_time" title="SERC Start Time" required="false" type="datetime-local"
                    defaultValue="{{ $sercStart }}"></x-form-input>
                <div class="flex space-x-2">
                    <input type="checkbox" name="can_be_live" @if ($comp->can_be_live) checked @endif
                        id="can_be_live">
                    <label for="can_be_live">Viewable Live</label>
                </div>

            </div>
        </div>
    </form>
@endsection
