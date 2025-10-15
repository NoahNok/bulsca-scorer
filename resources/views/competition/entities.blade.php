@extends('layouts.competition')

@section('title')
    Entities & Leagues
@endsection


@section('content')
    <div class=" row-start-1 md:row-start-auto">
        <div class="alert-box alert-warning">
            <h1>Heat & SERC Order</h1>
            <p>You will need to <strong>regenerate</strong> the Heat and SERC Order after adding any
                <strong>new</strong> teams.
                <br>
                <strong>Tip:</strong> Only generate the heats and SERC Order after adding all your teams!
            </p>
        </div>
    </div>

    <br>

    <h2>Leagues</h2>
    <div class="grid-3">



        @foreach ($comp->getLeagues as $league)
            <a href="{{ route('comps.leagues.view', [$comp, $league]) }}"
                class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                <p class="font-archivo flex items-center">{{ $league->name }}
                </p>



                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>


            </a>
        @endforeach


        <x-add-card link="{{ route('comps.leagues.create', $comp) }}" />




    </div>

    <br>

    <div class="flex justify-between ">
        <h2 class="mb-0">Entities</h2>
        <a href="{{ route('comps.entities.edit', $comp) }}" class="se-btn  flex items-center ">Edit</a>
    </div>

    <div class="flex space-x-3 mb-2 font-archivo uppercase font-semibold ">
        <small>{{ $comp->getClubs->count() }} Clubs</small>
        <small class="border-x px-3">{{ $comp->getCompetitionTeams->count() }} Teams</small>
        <small>{{ $comp->getCompetitors->count() }} Competitors</small>
    </div>


    @php
        $seed_events = $comp->getSeedableEvents();
        $first_speed_id = $comp->getSpeedEvents->first()->id;
    @endphp


    <div class=" se-table  md:w-2/3! ">
        <table>
            <thead>
                <tr>
                    <th scope="col">
                        Club
                    </th>
                    <th scope="col">
                        Team
                    </th>
                    <th scope="col">
                        Competitor
                    </th>
                    <th scope="col">
                        League
                    </th>
                    @if ($comp->use_seeds)
                        @if ($comp->seed_per_event)
                            @foreach ($seed_events as $event)
                                <th>
                                    {{ $event['name'] }}
                                </th>
                            @endforeach
                        @else
                            <th>Seed</th>
                        @endif
                    @endif


                </tr>
            </thead>
            <tbody>

                @foreach ($comp->getClubs()->with('getTeams.getCompetitors.getLeague', 'getTeams.getSeeds', 'getTeams.getCompetitors.getSeeds')->get() as $club)
                    @if ($club->getTeams->count() == 0)
                        <tr>
                            <th scope="row">
                                {{ $club->name }}
                            </th>
                            <td>
                                -
                            </td>

                            <td>
                                -
                            </td>
                            <td>
                                -
                            </td>
                            @if ($comp->use_seeds)
                                @foreach ($seed_events as $event)
                                    <td>
                                        {{ $club->getSeeds->where('speed_event', $event['id'])->first()?->prettySeed() ?? '-' }}
                                    </td>
                                @endforeach
                            @endif

                        </tr>
                    @else
                        @foreach ($club->getTeams as $team)
                            @if ($team->getCompetitors->count() == 0)
                                <tr>
                                    <th scope="row">
                                        {{ $club->name }}
                                    </th>
                                    <td>
                                        {{ $team->team }}
                                    </td>

                                    <td>
                                        -
                                    </td>
                                    <td>
                                        {{ $team->getLeague->name ?? '-' }}
                                    </td>
                                    @if ($comp->use_seeds)
                                        @if ($comp->seed_per_event)
                                            @foreach ($seed_events as $event)
                                                <td>
                                                    {{ $team->getSeeds->where('speed_event', $event['id'])->first()?->prettySeed() ?? '-' }}
                                                </td>
                                            @endforeach
                                        @else
                                            <td>
                                                {{ $team->getSeeds->where('speed_event', $first_speed_id)->first()?->prettySeed() ?? '-' }}
                                            </td>
                                        @endif
                                    @endif

                                </tr>
                            @else
                                @foreach ($team->getCompetitors as $competitor)
                                    <tr>
                                        <th scope="row">
                                            {{ $club->name }}
                                        </th>
                                        <td>
                                            {{ $team->team }}
                                        </td>

                                        <td>
                                            {{ $competitor->name }}
                                        </td>
                                        <td>
                                            {{ $competitor->getLeague->name ?? '-' }}
                                        </td>
                                        @if ($comp->use_seeds)
                                            @if ($comp->seed_per_event)
                                                @foreach ($seed_events as $event)
                                                    <td>
                                                        {{ $competitor->getSeeds->where('speed_event', $event['id'])->first()?->prettySeed() ?? '-' }}
                                                    </td>
                                                @endforeach
                                            @else
                                                <td>
                                                    {{ $competitor->getSeeds->where('speed_event', $first_speed_id)->first()?->prettySeed() ?? '-' }}
                                                </td>
                                            @endif
                                        @endif

                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Loose Teams --}}


                @foreach ($comp->getCompetitionTeams()->whereNull('club')->with('getCompetitors.getLeague', 'getSeeds', 'getCompetitors.getSeeds')->get() as $team)
                    @if ($team->getCompetitors->count() == 0)
                        <tr>
                            <th scope="row">
                                -
                            </th>
                            <td>
                                {{ $team->team }}
                            </td>

                            <td>
                                -
                            </td>
                            <td>
                                {{ $team->getLeague->name ?? '-' }}
                            </td>
                            @if ($comp->use_seeds)
                                @if ($comp->seed_per_event)
                                    @foreach ($seed_events as $event)
                                        <td>
                                            {{ $team->getSeeds->where('speed_event', $event['id'])->first()?->prettySeed() ?? '-' }}
                                        </td>
                                    @endforeach
                                @else
                                    <td>
                                        {{ $team->getSeeds->where('speed_event', $first_speed_id)->first()?->prettySeed() ?? '-' }}
                                    </td>
                                @endif
                            @endif

                        </tr>
                    @else
                        @foreach ($team->getCompetitors as $competitor)
                            <tr>
                                <th scope="row">
                                    -
                                </th>
                                <td>
                                    {{ $team->team }}
                                </td>

                                <td>
                                    {{ $competitor->name }}
                                </td>
                                <td>
                                    {{ $competitor->getLeague->name ?? '-' }}
                                </td>
                                @if ($comp->use_seeds)
                                    @if ($comp->seed_per_event)
                                        @foreach ($seed_events as $event)
                                            <td>
                                                {{ $competitor->getSeeds->where('speed_event', $event['id'])->first()?->prettySeed() ?? '-' }}
                                            </td>
                                        @endforeach
                                    @else
                                        <td>
                                            {{ $competitor->getSeeds->where('speed_event', $first_speed_id)->first()?->prettySeed() ?? '-' }}
                                        </td>
                                    @endif
                                @endif

                            </tr>
                        @endforeach
                    @endif
                @endforeach

                {{-- Loose Competitors --}}

                @foreach ($comp->getCompetitors()->whereNull('team')->with('getLeague', 'getSeeds')->get() as $competitor)
                    <tr>
                        <th scope="row">
                            -
                        </th>
                        <td>
                            -
                        </td>

                        <td>
                            {{ $competitor->name }}
                        </td>
                        <td>
                            {{ $competitor->getLeague->name ?? '-' }}
                        </td>
                        @if ($comp->use_seeds)
                            @if ($comp->seed_per_event)
                                @foreach ($seed_events as $event)
                                    <td>
                                        {{ $competitor->getSeeds->where('speed_event', $event['id'])->first()?->prettySeed() ?? '-' }}
                                    </td>
                                @endforeach
                            @else
                                <td>
                                    {{ $competitor->getSeeds->where('speed_event', $first_speed_id)->first()?->prettySeed() ?? '-' }}
                                </td>
                            @endif
                        @endif

                    </tr>
                @endforeach



            </tbody>
        </table>
    </div>
@endsection
