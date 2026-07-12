@extends('layouts.core')

@section('core-title')
    @yield('title') | {{ $comp->name }}
@endsection

@section('core-content')
    <div class="flex flex-col-reverse md:flex-row md:items-center justify-between gap-2 md:gap-0">
        <div class="flex space-x-3 items-center justify-center -mb-2">
            <div>
                <h1 class="" x-text="global_state.competition_name ?? '{{ $comp->name }}'">{{ $comp->name }}</h1>
                <p class="font-archivo text-sm! text-gray-700! uppercase -mb-1 ">{{ $comp->when->format('M jS Y') }}
                    @if ($comp->championship_id)
                        | <a href="{{ route('orgs.championship.view', [$comp->getOrganisation->name, $comp->championship->slug()]) }}"
                            class="hover:underline">{{ $comp->championship->name }}</a>
                    @endif
                </p>
            </div>
            @if (!$comp->show_competition)
                <div title="This competition is private">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </div>
            @endif


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
