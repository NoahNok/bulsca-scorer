@extends('layouts.core')

@section('core-title')
    @yield('title') | {{ $comp->name }}
@endsection

@section('core-content')
    <div class="flex flex-col-reverse md:flex-row md:items-center justify-between gap-2 md:gap-0">
        <div>
            <h1 class="-mb-2" x-text="global_state.competition_name ?? '{{ $comp->name }}'">{{ $comp->name }}</h1>
            <small class="text-gray-500">Scoring v{{ $comp->scoring_version }}</small>
        </div>

        @if ($comp->getOrganisation)
            <a href="{{ route('orgs.show', $comp->getOrganisation->name) }}">
                <img src="{{ $comp->getOrganisation->getLogo() }}" alt="{{ $comp->getOrganisation->name }}'s logo"
                    class="size-14 min-w-14 rounded-full">
            </a>
        @endif




    </div>


    <div class="  tabbed-bar mt-2 mb-4 ">

        @can('access', [$comp, 'view'])
            <a href="{{ route('comps.view', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.view')) active @endif">Overview</a>
        @endcan

        @can('access', [$comp, 'teams'])
            @if (\App\Helpers\ScoringHelper::getCompetitionScoringDetails($comp)['use_competitors'])
                <a href="{{ route('comps.competitors', $comp) }}"
                    class="@if (Str::startsWith(Route::currentRouteName(), 'comps.competitor')) active @endif">Competitors</a>
            @else
                <a href="{{ route('comps.teams', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.team')) active @endif">Teams</a>
            @endif
        @endcan

        @can('access', [$comp, 'heats_and_draws'])
            <a href="{{ route('comps.heats', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.heats')) active @endif">Heats &
                Draws</a>
        @endcan

        @can('access', [$comp, 'printables'])
            <a href="{{ route('comps.printables', $comp) }}"
                class="@if (Str::startsWith(Route::currentRouteName(), 'comps.printables')) active @endif">Printables</a>
        @endcan

        @can('access', [$comp, ['serc', 'speed', 'serc_writer']])
            <a href="{{ route('comps.events', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.events')) active @endif">Events</a>
        @endcan

        @can('access', [$comp, 'results'])
            <a href="{{ route('comps.results', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.results')) active @endif">Results</a>
        @endcan

    </div>



    @yield('content')
@endsection
