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
            <a href="{{ route('comps.entities', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.entities') ||
                    Str::startsWith(Route::currentRouteName(), 'comps.league')) active @endif">Entries &
                Leagues</a>
        @endcan

        @can('access', [$comp, ['serc', 'speed', 'serc_writer']])
            <a href="{{ route('comps.events', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.events')) active @endif">Events</a>
        @endcan

        @can('access', [$comp, 'heats_and_draws'])
            <a href="{{ route('comps.heats_and_draws', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.heats_and_draws')) active @endif">Heats
                &
                Draws</a>
        @endcan

        @can('access', [$comp, 'printables'])
            <a href="{{ route('comps.printables', $comp) }}"
                class="@if (Str::startsWith(Route::currentRouteName(), 'comps.printables')) active @endif">Printables</a>
        @endcan



        @can('access', [$comp, 'results'])
            <a href="{{ route('comps.results', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.results')) active @endif">Results</a>
        @endcan

    </div>

    @if ($comp->statusMessages)
        <div class="flex  space-x-3">
            <div class="text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>

            </div>
            <div>
                <p class="text-gray-800 text-sm font-semibold font-archivo prose">
                    {{ $statusMessage->updated_at->format('h:s d/m/y') }}</p>

                <p>{!! $statusMessage->message !!}</p>
            </div>

        </div>
    @endif

    @yield('content')
@endsection
