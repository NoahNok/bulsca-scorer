@extends('layouts.landing-comp')

@section('title', 'Overview')

@section('content')

    <h2>Register</h2>
    <div class="grid-3 mt-2">
        <a href="" class="se-btn se-btn-disabled">Competitors</a>
        <a href="" class="se-btn se-btn-disabled">Officials</a>
        <a href="" class="se-btn se-btn-disabled">Helpers</a>
    </div>
    <br>
    <hr class="spacer mt-2!">
    <br>

    <div class="grid-2">
        <div>
            <h3 class="mb-0">Events</h3>
            <br>

            <div class="grid-2">
                @foreach ($comp->getSpeedEvents as $event)
                    <button class="se-btn se-btn-disabled">
                        <p class="text-lg font-semibold">{{ $event->getName() }}</p>


                    </button>
                @endforeach
                @foreach ($comp->getSERCs as $event)
                    <button class="se-btn flex items-center justify-center space-x-2 se-btn-disabled">
                        <span class="badge badge-sm">SERC</span>
                        <p class="text-lg font-semibold">{{ $event->name }}</p>




                    </button>
                @endforeach

            </div>

        </div>

        <div>
            <h2>Details</h2>
            <p>{{ $comp->when->format('M jS Y') }} at {{ $comp->where }}</p>

            <a class="link" target="_blank" rel="noopener"
                href="https://www.google.com/maps/search/?{{ http_build_query(['api' => 1, 'query' => $comp->where]) }}">Open
                in Google Maps &nearr;</a>
        </div>


    </div>






    <br>
    <hr class="spacer mt-2!">
    <br>












@endsection
