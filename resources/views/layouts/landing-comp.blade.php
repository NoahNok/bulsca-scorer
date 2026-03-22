@extends('layouts.core')

@section('core-title')
    @yield('title') | {{ $comp->name }}
@endsection

@section('core-meta')
    <script src="{{ asset('js/sorttable.js') }}?{{ config('version.hash') }}"></script>
@endsection


@section('core-content')
    <div class="flex flex-col-reverse md:flex-row md:items-center justify-between gap-2 md:gap-0 pt-8  ">

        <div>
            <h1 class="xl:text-5xl!  ">{{ $comp->name }}</h1>
            <p class="font-archivo text-sm! text-gray-700! uppercase -mb-1 ">{{ $comp->when->format('M jS Y') }}</p>
        </div>

        @if ($comp->getOrganisation)
            <a href="{{ route('landing.organisation', $comp->getOrganisation->name) }}" class="mb-4 md:mb-0">
                <img src="{{ $comp->getOrganisation->getLogo() }}" alt="" class="size-16 rounded-full">
            </a>
        @endif
    </div>


    <div class="  tabbed-bar mt-2 mb-4 ">

        @if ($comp->can_be_live)
            <a href="{{ route('live') }}?comp={{ $comp->id }}" target="_blank"
                class="text-se! font-semibold! animate-pulse!">Live</a>
        @endif

        <a href="{{ route('landing.competition', $comp->getSlug()) }}"
            class="@if (Route::currentRouteName() == 'landing.competition') active @endif">Info</a>

        <a href="{{ route('landing.competition.heats-draws', $comp->getSlug()) }}"
            class="@if (Str::startsWith(Route::currentRouteName(), 'landing.competition.heats-draws')) active @endif">Heats & Draws</a>

        <a href="{{ route('landing.competition.results', $comp->getSlug()) }}"
            class="@if (Str::startsWith(Route::currentRouteName(), 'landing.competition.results')) active @endif">Results</a>




    </div>

    @if ($comp->status_message)
        <div class="flex items-center  space-x-3 mb-4 mt-2">
            <div class="text-blue-500 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>

            </div>
            <div>


                {!! $comp->status_message !!}
            </div>

        </div>
    @endif

    @yield('content')
@endsection
