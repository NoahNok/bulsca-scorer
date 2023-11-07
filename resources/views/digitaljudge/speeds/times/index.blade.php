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


        @for ($heat = 1; $heat <= $comp->getMaxHeats(); $heat++)
            @php
                $heatTeams = $comp
                    ->getHeatEntries()
                    ->where('heat', $heat)
                    ->get();

                $missingResult = false;

                // Code that checks if each team has a reuslt for the event
                foreach ($heatTeams as $team) {
                    $sr = App\Models\SpeedResult::where('competition_team', $team->team)
                        ->where('event', $speed->id)
                        ->first();

                    if ($sr->result == null) {
                        $missingResult = true;
                        break;
                    }
                }

            @endphp

            @if ($missingResult)
                <a href="{{ route('dj.speeds.times.judge', [$speed, $heat]) }}" class="btn btn-primary">Heat
                    {{ $heat }}</a>
            @elseif ($head)
                <a href="{{ route('dj.speeds.times.judge', [$speed, $heat]) }}" class="btn btn-success">Heat
                    {{ $heat }}</a>
            @else
                <button class="btn btn-success cursor-not-allowed">Heat
                    {{ $heat }}</button>
            @endif
        @endfor








    </div>
@endsection
