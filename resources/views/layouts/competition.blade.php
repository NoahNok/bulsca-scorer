@extends('layouts.core')



@section('core-content')
    <div class="flex  items-center justify-between">
        <div>
            <h1 class="-mb-2">{{ $comp->name }}</h1>
            <small class="text-gray-500">Scoring v{{ $comp->scoring_version }}</small>
        </div>



        @if ($comp->brand)
            <img src="{{ $comp->getBrand->getLogo() }}" alt="{{ $comp->getBrand->name }}" class="w-[50px] h-[50px] ">
        @endif

    </div>


    <div class="  tabbed-bar mt-2 mb-4 ">

        <a href="{{ route('comps.view', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.view')) active @endif">Overview</a>
        <a href="{{ route('comps.teams', $comp) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'comps.team')) active @endif">Teams</a>
        <div>Heats & Orders</div>
        <div>Printables</div>
        <div>Events</div>
        <div>Results</div>
    </div>



    @yield('content')
@endsection
