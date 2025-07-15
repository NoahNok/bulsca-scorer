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
        <div class="flex items-center justify-between w-full -mb-2">
            <h1 class="">{{ $org->name }}</h1>
            <img src="{{ $org->getLogo() }}" alt="" class="size-14 rounded-full">
        </div>




    </div>


    <div class="  tabbed-bar mt-2 mb-4 ">

        <a href="#" class="@if (Str::startsWith(Route::currentRouteName(), 'orgs.show')) active @endif">Competitions</a>



    </div>



    @yield('content')
@endsection
