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

        <div>Competitions</div>


    </div>



    @yield('content')
@endsection
