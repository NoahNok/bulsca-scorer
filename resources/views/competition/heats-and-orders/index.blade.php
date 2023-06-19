@extends('layout')

@section('title')
Heats and Orders | {{ $comp->name }}
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events', $comp) }}">Heats and Orders</a>
</div>


@endsection

@section('content')
<div class="">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">Heats</h2>
            <a href="#" class="btn">Edit Heats</a>
        </div>

        <div class=" grid grid-cols-8 " >
        
            @foreach ($heatEntries->sortBy(['heat','lane'])->groupBy('heat') as $key => $heat)
                <div >
                    <h5 >Heat {{ $key }}</h5>
                    <ol class=" list-item ">
                        @for($l = 1; $l <= $comp->max_lanes; $l++)
                            
                            @php
                                $lane = $heat->where('lane', $l)->first()
                            @endphp

                            @if ($lane)
                            <li>{{ $lane->lane }} {{ $lane->getTeam->getFullname() }}</li>  
                            @else
                            <li>{{ $l }}</li>
                            @endif

                           
                            
                        @endfor
                    </ol>
                </div>
                
            @endforeach

        </div>


    </div>


</div>

@endsection