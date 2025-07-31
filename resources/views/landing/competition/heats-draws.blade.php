@extends('layouts.landing-comp')

@section('title', 'Heats & Draws')

@section('content')


    @if ($comp->getCompetitionTeams->count() == 0)
        <div class="alert-box">
            <h1>Unavailable</h1>
            <p>Heats and Draws are currently unavailable.</p>
        </div>
    @else
        <h2>Heats</h2>
        <div class="mt-2">
            @php
                $heatEntries = collect(
                    DB::select(
                        'SELECT h.id, h.event, h.heat, h.lane, ct.team, l.name AS league, c.name AS club, c.region FROM heats h INNER JOIN competition_teams ct ON ct.id=h.team INNER JOIN leagues l ON l.id=ct.league INNER JOIN clubs c ON c.id=ct.club WHERE h.competition = ? ORDER BY heat, lane;',
                        [$comp->id],
                    ),
                );

            @endphp
            @include('competition.heats-and-orders.heat_list_templates.' . $comp->scoring_type)
        </div>
        <br>
        <br>

        <h2 class="mb-0">SERC Draw</h2>
        @if ($comp->needsToRegenerateSERCDraw())
            <div class="alert-box mt-2">
                <h2>Unavailable</h2>
                <p>Draws are not currently available.</p>
            </div>
        @else
            <div class="grid @if ($comp->scoring_type == 'bulsca') grid-rows-6 @endif gap-3 md:grid-flow-col mt-2">


                @include($comp->drawTemplate())


            </div>
        @endif
    @endif

@endsection
