@extends('layouts.competition')

@section('title')
    Entries & Leagues
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

                <div class="ml-auto flex">
                    @php
                        $ec = $league->entityCount();
                    @endphp


                    @if ($ec['teams'] > 0)
                        <div class="flex items-center justify-center gap-1  mr-3 text-sm" title="Teams">
                            {{ $league->entityCount()['teams'] }}


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>

                        </div>
                    @endif

                    @if ($ec['competitors'] > 0)
                        <div class="flex items-center justify-center gap-1  mr-3 text-sm" title="Competitors">
                            {{ $league->entityCount()['competitors'] }}

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>


                        </div>
                    @endif
                </div>




                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class=" size-4 group-hover:text-se transition-all group-hover:stroke-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>


            </a>
        @endforeach


        <x-add-card link="{{ route('comps.leagues.create', $comp) }}" />




    </div>


    <hr class="spacer my-5! mt-7!">


    <div class="flex justify-between ">
        <h2 class="mb-0">Entries</h2>
        <a href="{{ route('comps.entities.edit', $comp) }}" class="se-btn  flex items-center ">Edit Entries</a>
    </div>



    <div class="flex space-x-3 mb-2 font-archivo uppercase font-semibold ">
        <small>{{ $comp->getClubs->count() }} Clubs</small>
        <small class="border-x px-3">{{ $comp->getCompetitionTeams->count() }} Teams</small>
        <small>{{ $comp->getCompetitors->count() }} Competitors</small>
    </div>


    @php
        $seed_events = $comp->getSeedableEvents();
        $first_speed_id = $comp->getSpeedEvents->first()?->id;
    @endphp

    @if (!$first_speed_id && $comp->use_seeds)
        <div class="alert-box alert-warning mb-3">
            <h1>Seeds</h1>
            <p>You will be unable to add seed times until you have added a speed event!</p>
        </div>
    @endif


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

                @foreach ($comp->getClubs()->with('getTeams.getCompetitors.leagues', 'getTeams.getSeeds', 'getTeams.getCompetitors.getSeeds')->get() as $club)
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
                                        {{ $team->leagues->pluck('name')->join(', ') ?? '-' }}
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
                                            {{ $competitor->leagues->pluck('name')->join(', ') ?? '-' }}
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


                @foreach ($comp->getCompetitionTeams()->whereNull('club')->with('getCompetitors.leagues', 'getSeeds', 'getCompetitors.getSeeds')->get() as $team)
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
                                {{ $team->leagues->pluck('name')->join(', ') ?? '-' }}
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
                                    {{ $competitor->leagues->pluck('name')->join(', ') ?? '-' }}
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

                @foreach ($comp->getCompetitors()->whereNull('team')->with('leagues', 'getSeeds')->get() as $competitor)
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
                            {{ $competitor->leagues->pluck('name')->join(', ') ?? '-' }}
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
