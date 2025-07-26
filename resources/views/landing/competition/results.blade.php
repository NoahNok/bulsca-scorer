@extends('layouts.landing-comp')

@section('title', 'Results')

@section('content')

    @php
        if ($comp->public_results) {
            $sercs = $comp->getSERCs->where('viewable', true);
            $speeds = $comp->getSpeedEvents->where('viewable', true);
            $schemas = $comp->getResultSchemas->where('viewable', true);
        } else {
            $sercs = [];
            $speeds = [];
            $schemas = [];
        }
    @endphp

    <h2>Events</h2>
    <div class="flex flex-col space-y-4">
        @if (count($sercs) == 0 && count($speeds) == 0)
            <div class="alert-box mt-2">
                <h1>Unavailable</h1>
                <p>No event results have been published yet.</p>
            </div>
        @endif


        @if (count($sercs) > 0)
            <div>
                <h3>SERCs</h3>
                <div class="grid-4 mt-2">

                    @foreach ($sercs as $serc)
                        <a href="{{ route('public.results.serc', [$comp->getSlug(), $serc]) }}"
                            class="se-card se-card-body se-card-hover">
                            <h4 class="-mb-1">{{ $serc->getName() }}</h4>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif


        @if (count($speeds) > 0)
            <div>
                <h3>Speeds</h3>
                <div class="grid-4 mt-2">

                    @foreach ($speeds as $speed)
                        <a href="{{ route('public.results.speed', [$comp->getSlug(), $speed]) }}"
                            class="se-card se-card-body se-card-hover">
                            <h4 class="-mb-1">{{ $speed->getName() }}</h4>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif


    </div>


    <hr class="spacer my-8! mt-10!">

    <h2>Results</h2>
    @if (count($schemas) > 0)
        <div>

            <div class="grid-4 mt-2">

                @foreach ($schemas as $schema)
                    <a href="{{ route('public.results.results', [$comp->getSlug(), $schema]) }}"
                        class="se-card se-card-hover">
                        <div class="se-card-body flex items-center justify-between">
                            <h4 class="-mb-1!">{{ $schema->name }}</h4>


                        </div>


                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="alert-box mt-2">
            <h1>Unavailable</h1>
            <p>No results sheets have been published yet.</p>
        </div>
    @endif



@endsection
