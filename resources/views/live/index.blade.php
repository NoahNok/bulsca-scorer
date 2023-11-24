<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $comp->name }} | Live | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden flex justify-center w-screen h-screen">
    <div class="w-[90vw] md:w-[70vw] my-12">
        <h1>{{ $comp->name }}</h1>
        <br>

        <h3>SERC Order</h3>
        <div class="grid grid-rows-12 md:grid-rows-8 2xl:grid-rows-6 gap-3 md:grid-flow-col">
            @if ($comp->getCompetitionTeams->count() == 0)
                <p>No SERC Order available yet!</p>
            @else
                @foreach ($comp->getCompetitionTeams as $team)
                    <div class="card whitespace-nowrap">
                        {{ $loop->index + 1 }}. {{ $team->getFullname() }}
                    </div>
                @endforeach
            @endif

        </div>
        <br>

        <h3>Heats</h3>
        <div class="flex space-x-2  ">
            <div class=" hidden md:block  ">
                <h5>Lane</h5>
                <ol class="space-y-2">
                    @for ($l = 1; $l <= $comp->max_lanes; $l++)
                        <li class="px-5 py-3 border border-transparent">{{ $l }}</li>
                    @endfor
                </ol>
            </div>
            <div class=" w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 3xl:grid-cols-5 gap-3   ">


                @forelse ($comp->getHeatEntries->sortBy(['heat','lane'])->groupBy('heat') as $key => $heat)
                    <div class="w-full">
                        <h5>Heat {{ $key }}</h5>
                        <ol class=" list-item space-y-2 ">
                            @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                @php
                                    $lane = $heat->where('lane', $l)->first();
                                @endphp


                                <div class="flex flex-row md:block ">
                                    <p class="px-5 py-3 border border-transparent md:hidden">{{ $l }}</p>
                                    <li class="card whitespace-nowrap flex-grow ">
                                        @if ($lane)
                                            {{ $lane->getTeam->getFullname() }}
                                            ({{ $lane->getTeam->getSwimTowTimeForDefault() }})
                                        @else
                                            &nbsp;
                                        @endif
                                    </li>
                                </div>
                            @endfor
                        </ol>
                    </div>
                @empty

                    <p>No Heats available yet!</p>
                @endforelse

            </div>
        </div>
        <br>
        <br>

</body>

</html>
