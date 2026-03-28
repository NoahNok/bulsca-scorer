@extends('layouts.core')

@section('core-title')
    @yield('title')
@endsection

@section('core-content')
    <div class="flex  items-center justify-between">
        <div>
            <h1 class="-mb-2">Admin</h1>

        </div>




    </div>


    <div class="  tabbed-bar mt-2 mb-4 ">


        <a href="{{ route('admin.index') }}" class="@if (Route::currentRouteName() == 'admin.index') active @endif">Admin</a>
        <a href="{{ route('admin.serc.marking-point-template.index') }}"
            class="@if (Str::startsWith(Route::currentRouteName(), 'admin.serc.marking-point-template')) active @endif">SERC
            Marking Point Templates</a>


    </div>



    @yield('content')
@endsection
