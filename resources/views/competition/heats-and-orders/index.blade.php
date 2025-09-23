@extends('layouts/competition')

@section('title')
    Heats and Draws
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
        <a href="{{ route('comps.heats_and_draws', $comp) }}">Heats and Orders</a>
    </div>
@endsection

@section('content')
    <div class="">
        <div class="flex flex-col space-y-4">



            <div class="flex justify-between">
                <h2 class="mb-0">Heats</h2>

            </div>

            @if ($comp->getCompetitionTeams->count() == 0)
                <div class=" grid grid-cols-3">
                    <div class="alert-box ">
                        <h1>No Teams</h1>
                        <p>You need to <strong>add</strong> some <a class=""
                                href="{{ route('comps.teams.edit', $comp) }}">teams</a> before you can generate heats
                        </p>
                    </div>
                </div>
            @else
                @foreach ($comp->getHeats() as $heatevent)
                    @if ($comp->heats_per_event)
                        <h3>{{ $heatevent['event']->getName() }} Heats</h3>
                    @endif
                    <a href="{{ route('comps.heats_and_draws.heats.edit', [$comp, $heatevent['event']]) }}"
                        id="edit-heats-kill" class="se-btn">Edit
                        Heats</a>
                    <div class="grid grid-flow-col auto-cols-max gap-4 flex-wrap">

                        <div class="flex flex-col  py-2">
                            <h3 class="font-bold  mb-2">L</h3>
                            <div class="space-y-2">

                                @for ($i = 0; $i < $comp->max_lanes; $i++)
                                    <div class="border border-transparent font-semibold py-2  rounded">
                                        {{ $i + 1 }}
                                    </div>
                                @endfor


                            </div>
                        </div>

                        @foreach ($heatevent['heats'] as $heat_no => $lanes)
                            <div class="flex flex-col  py-2">
                                <h3 class="font-bold  mb-2">Heat {{ $heat_no }}</h3>
                                <div class="space-y-2">

                                    @for ($i = 0; $i < $comp->max_lanes; $i++)
                                        @php
                                            $lane = $lanes->where('lane', $i + 1)->first();
                                        @endphp
                                        <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded">
                                            {{ $lane?->entity->getName() ?? '-' }}
                                        </div>
                                    @endfor


                                </div>
                            </div>
                        @endforeach


                    </div>
                @endforeach
            @endif

            <br>

            <div class="flex justify-between">
                <h2 class="mb-0">SERC Order</h2>

                <a href="{{ route('comps.heats_and_draws.draws.edit', $comp) }}" class="se-btn">Edit SERC Order</a>


            </div>




        </div>
    </div>
@endsection
