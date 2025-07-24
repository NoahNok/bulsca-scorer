@extends('layouts.landing-comp')

@section('title', 'Results')

@section('content')

    @php
        $sercs = $comp->getSERCs->where('viewable', true);
        $speeds = $comp->getSpeedEvents->where('viewable', true);
        $schemas = $comp->getResultSchemas->where('viewable', true);
    @endphp


    <div class="flex flex-col space-y-4">
        @if (count($sercs) == 0 && count($speeds) == 0)
            <div class="alert-box mt-2">
                <h1>No events</h1>
                <p>This competition has no events available</p>
            </div>
        @endif


        @if (count($sercs) > 0)
            <div>
                <h3>SERCs</h3>
                <div class="grid-4 mt-2">

                    @foreach ($sercs as $serc)
                        <a href="#" class="se-card se-card-body se-card-hover">
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
                        <a href="#" class="se-card se-card-body se-card-hover">
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
                    <a href="" class="se-card se-card-hover">
                        <div class="se-card-body flex items-center justify-between">
                            <h3 class="-mb-1!">{{ $schema->name }}</h3>


                        </div>

                        <div class="bg-gray-200 px-4 py-2 ">
                            <p class="font-archivo text-xs! text-gray-700! uppercase -mb-1">
                                {{ $schema->getLeagueName() }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="alert-box mt-2">
            <h1>No results</h1>
            <p>This competition has no results available</p>
        </div>
    @endif



@endsection
