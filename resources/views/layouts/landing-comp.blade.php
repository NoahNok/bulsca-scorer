@extends('layouts.core')

@section('core-title')
    @yield('title') | {{ $comp->name }}
@endsection


@section('core-content')
    <div class="flex items-center justify-between pt-8  z-20">

        <div>
            <h1 class="text-5xl!  ">{{ $comp->name }}</h1>
            <p class="font-archivo text-sm! text-gray-700! uppercase -mb-1">{{ $comp->when->format('M jS Y') }}</p>
        </div>

        @if ($comp->getOrganisation)
            <a href="{{ route('landing.organisation', $comp->getOrganisation->name) }}">
                <img src="{{ $comp->getOrganisation->getLogo() }}" alt="" class="size-16 rounded-full">
            </a>
        @endif
    </div>


    <div class="  tabbed-bar mt-2 mb-4 ">

        <a href="{{ route('landing.competition', $comp->getSlug()) }}"
            class="@if (Route::currentRouteName() == 'landing.competition') active @endif">Live</a>

        <a href="{{ route('landing.competition.heats-draws', $comp->getSlug()) }}"
            class="@if (Str::startsWith(Route::currentRouteName(), 'landing.competition.heats-draws')) active @endif">Heats & Draws</a>

        <a href="{{ route('landing.competition.results', $comp->getSlug()) }}"
            class="@if (Str::startsWith(Route::currentRouteName(), 'landing.competition.results')) active @endif">Results</a>




    </div>
    @yield('content')
@endsection
