@extends('layout')

@section('title')
    Heats and Orders | {{ $comp->name }}
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
        <a href="{{ route('comps.view.heats', $comp) }}">Heats and Orders</a>
    </div>
@endsection

@section('content')
    <div class="">
        <div class="flex flex-col space-y-4">

            <div class="flex justify-between">
                <h2 class="mb-0">Heats</h2>
                <a href="{{ route('comps.view.heats.edit', $comp) }}" class="btn">Edit Heats</a>
            </div>

            @if ($comp->getCompetitionTeams->count() == 0)
                <div class=" grid grid-cols-3">
                    <div class="alert-box ">
                        <h1>No Teams</h1>
                        <p>You need to <strong>add</strong> some <a class=""
                                href="{{ route('comps.view.teams.edit', $comp) }}">teams</a> before you can generate heats
                        </p>
                    </div>
                </div>
            @else
                @include('competition.heats-and-orders.heat_list_templates.' . $comp->scoring_type)
            @endif

            <br>

            <div class="flex justify-between">
                <h2 class="mb-0">SERC Order</h2>
                @if (!$comp->needsToRegenerateSERCDraw())
                    <a href="{{ route('comps.view.serc-order.edit', $comp) }}" class="btn">Edit SERC Order</a>
                @endif

            </div>

            <div class="grid grid-rows-6 gap-3 md:grid-flow-col">
                @if ($comp->needsToRegenerateSERCDraw())
                    <div>
                        <a href="{{ route('comps.view.serc-order.regen', $comp) }}" class="btn ">Generate SERC Order</a>
                    </div>
                @elseif ($comp->getCompetitionTeams->count() == 0)
                    <div class=" grid grid-cols-3">
                        <div class="alert-box ">
                            <h1>No Teams</h1>
                            <p>You need to <strong>add</strong> some <a class=""
                                    href="{{ route('comps.view.teams.edit', $comp) }}">teams</a> before you can generate a
                                SERC order
                            </p>
                        </div>
                    </div>
                @else
                    @include('competition.heats-and-orders.serc_list_templates.' . $comp->scoring_type)
                @endif

            </div>


        </div>
    </div>
@endsection
