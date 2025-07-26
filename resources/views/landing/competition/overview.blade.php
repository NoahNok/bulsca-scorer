@extends('layouts.landing-comp')

@section('title', 'Overview')

@section('content')

    <h2>Register</h2>
    <div class="grid-3 mt-2">
        <a href="" class="se-btn">Competitiors</a>
        <a href="" class="se-btn">Officials</a>
        <a href="" class="se-btn">Helpers</a>
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
                    <button class="se-btn">
                        <p class="text-lg font-semibold">{{ $event->getName() }}</p>


                    </button>
                @endforeach
                @foreach ($comp->getSERCs as $event)
                    <button class="se-btn flex items-center justify-center space-x-2">
                        <span class="badge badge-sm">SERC</span>
                        <p class="text-lg font-semibold">{{ $event->name }}</p>




                    </button>
                @endforeach

            </div>
        </div>

        <div>
            <h2>Details</h2>
            <p>{{ $comp->when->format('M jS Y') }} at {{ $comp->where }}</p>

            <iframe width="100%" height="450" style="border:0" loading="lazy" allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCC_QTIqaeEJifTEugGosP03tcXrGu5cw8&q={{ $comp->where }}&zoom=15&maptype=roadmap">
            </iframe>
        </div>


    </div>






    <br>
    <hr class="spacer mt-2!">
    <br>












@endsection
