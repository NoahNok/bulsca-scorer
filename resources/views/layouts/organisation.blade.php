@extends('layouts.core')

@section('core-title')
    @hasSection('title')
        @yield('title') |
    @endif
    {{ $org->name }}
@endsection

@section('core-meta')
    <link rel="icon" type="image/png" href="{{ $org->getLogo() }}">
@endsection

@section('core-content')
    <div class="flex  items-center justify-between">
        <div class="flex flex-col-reverse md:flex-row md:items-center justify-between gap-2 md:gap-0 w-full -mb-2">
            <h1 class="">{{ $org->name }}</h1>
            <img src="{{ $org->getLogo() }}" alt="" class="size-14 rounded-full">
        </div>




    </div>


    <div class="  tabbed-bar mt-2 mb-4 ">

        @can('access', [$org, '*'])
            <a href="{{ route('orgs.show', $org->name) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'orgs.show')) active @endif">Overview</a>
        @endcan

        @can('access', [$org, 'admin'])
            <a href="{{ route('orgs.championships', $org->name) }}"
                class="@if (Str::startsWith(Route::currentRouteName(), 'orgs.championship')) active @endif">Championships</a>


            <a href="{{ route('orgs.accounts', $org->name) }}"
                class="@if (Str::startsWith(Route::currentRouteName(), 'orgs.accounts')) active @endif">Accounts</a>

            <a href="{{ route('orgs.scoring', $org->name) }}"
                class="@if (Str::startsWith(Route::currentRouteName(), 'orgs.scoring')) active @endif">Scoring</a>


            <a href="{{ route('orgs.infractions', $org->name) }}"
                class="@if (Str::startsWith(Route::currentRouteName(), 'orgs.infractions')) active @endif">Infractions</a>

            <a href="{{ route('orgs.edit', $org->name) }}" class="@if (Str::startsWith(Route::currentRouteName(), 'orgs.edit')) active @endif">Settings</a>
        @endcan



    </div>



    @yield('content')
@endsection
