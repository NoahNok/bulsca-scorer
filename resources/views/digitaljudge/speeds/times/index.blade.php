@extends('digitaljudge.mpa-layout')

@section('title')
    {{ $speed->getName() }}
@endsection
@php
    $backlink = route('dj.home');
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
@endphp

@section('content')
    <div class="flex flex-col space-y-3  ">

        <h2 class="font-bold w-full break-words">
            Times
        </h2>

        @if ($head)
            <p>Heats will turn green once complete <strong>and</strong> maybe be edited at any time</p>
        @else
            <p>Heats will turn green once complete!</p>
        @endif


        @foreach ($speed->getHeats->sortBy('heat')->groupBy('heat') as $heat => $lanes)
            @php

                $missingResult = false;

                // Code that checks if each team has a reuslt for the event
                foreach ($lanes as $team) {
                    $sr = $team->entity->speedResults()->where('event', $speed->id)->first();

                    if ($sr->result == null) {
                        $missingResult = true;
                        break;
                    }
                }

            @endphp

            @if ($missingResult)
                <a href="{{ route('dj.speeds.times.judge', [$speed, $heat]) }}" class="se-btn se-btn-primary">Heat
                    {{ $heat }}</a>
            @elseif ($head)
                <a href="{{ route('dj.speeds.times.judge', [$speed, $heat]) }}" class="se-btn se-btn-success">Heat
                    {{ $heat }}</a>
            @else
                <button class="se-btn se-btn-success cursor-not-allowed">Heat
                    {{ $heat }}</button>
            @endif
        @endforeach








    </div>
@endsection
