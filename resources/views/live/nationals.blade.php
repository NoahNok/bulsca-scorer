<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">


    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <title>{{ $comp->name }} | Live | RLSS</title>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>

    </style>
</head>

<body class="overflow-x-hidden  w-screen h-screen">


    <div class="w-full h-48 bg-rlss-blue flex flex-col justify-center px-12 overflow-x-hidden "
        style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

        <div class="container mx-auto">
            <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
            <p class=" font-ariel text-rlss-yellow font-semibold">John Charles Centre for Sport</p>
        </div>


    </div>

    <div class="container mx-auto py-6 overflow-x-hidden">

        <div class="w-full max-w-full flex  px-2">
            <a href="#tanks" class="btn !bg-rlss-blue mx-2 grow  ">Jump to tanks</a>
        </div>


        <br>

        <h2 class="font-astoria text-rlss-blue font-extrabold">Event Order - Diving Pit Side</h2>

        @php
            $entries = collect(
                DB::select(
                    'SELECT h.id, h.heat, h.lane, ct.team, l.name AS league, c.name AS club, c.region FROM heats h INNER JOIN competition_teams ct ON ct.id=h.team INNER JOIN leagues l ON l.id=ct.league INNER JOIN clubs c ON c.id=ct.club WHERE h.competition = ? ORDER BY heat, lane;',
                    [$comp->id],
                ),
            );
            $heats = $entries->sortBy(['heat', 'lane'])->groupBy('heat');
        @endphp

        <div class="w-full overflow-x-auto  ">
            <table class="table-auto font-greycliff">
                <thead>
                    <tr class=" text-rlss-blue font-bold  ">
                        <th class="bg-rlss-blue py-2 px-3 border border-rlss-blue sticky left-0 "></th>
                        <th class=" bg-rlss-blue bg-opacity-40 py-2 px-3 border border-rlss-blue w-48  ">
                            Category</th>
                        <th class="border border-rlss-blue w-48">Lane 1</th>
                        <th class="border border-rlss-blue w-48">Lane 2</th>
                        <th class="border border-rlss-blue w-48">Lane 3</th>
                        <th class="border border-rlss-blue w-48">Lane 4</th>
                        <th class="border border-rlss-blue w-48">Lane 5</th>
                        <th class="border border-rlss-blue w-48">Lane 6</th>
                        <th class="border border-rlss-blue w-48">Lane 7</th>
                        <th class="border border-rlss-blue w-48">Lane 8</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($heats->nth(2) as $key => $heat)
                        <tr class="relative">
                            <td
                                class=" bg-rlss-blue text-white  text-center py-2 px-3 border border-rlss-blue sticky left-0 ">
                                {{ $heat->first()->heat }}</td>
                            <td
                                class="py-2 px-3 bg-rlss-blue bg-opacity-40 text-rlss-blue border border-rlss-blue text-center  ">
                                {{ $heat->where('lane', 4)->first()->league }}
                            </td>

                            @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                @php
                                    $lane = $heat->where('lane', $l)->first();
                                @endphp


                                <td class="py-2 px-3 text-center border border-rlss-blue">
                                    @if ($lane)
                                        {{ $lane->team }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td>No heats available yet!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <br>
        <h2 class="font-astoria text-rlss-blue font-extrabold ">Event Order - Scoreboard Side </h2>
        <div class="w-full overflow-x-auto ">
            <table class="table-auto font-greycliff">
                <thead>
                    <tr class=" text-rlss-blue font-bold sticky top-0 left-0">
                        <th class="bg-rlss-blue py-2 px-3 border border-rlss-blue sticky left-0"></th>
                        <th class=" bg-rlss-blue bg-opacity-40 py-2 px-3 border border-rlss-blue w-48 ">
                            Category</th>
                        <th class="border border-rlss-blue w-48 ">Lane 1</th>
                        <th class="border border-rlss-blue w-48">Lane 2</th>
                        <th class="border border-rlss-blue w-48">Lane 3</th>
                        <th class="border border-rlss-blue w-48">Lane 4</th>
                        <th class="border border-rlss-blue w-48">Lane 5</th>
                        <th class="border border-rlss-blue w-48">Lane 6</th>
                        <th class="border border-rlss-blue w-48">Lane 7</th>
                        <th class="border border-rlss-blue w-48">Lane 8</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($heats->nth(2,1) as $key => $heat)
                        <tr>
                            <td
                                class=" bg-rlss-blue text-white  text-center py-2 px-3 border border-rlss-blue sticky left-0">
                                {{ $heat->first()->heat }}</td>
                            <td
                                class="py-2 px-3 bg-rlss-blue bg-opacity-40 text-rlss-blue border border-rlss-blue text-center">
                                {{ $heat->where('lane', 4)->first()->league }}
                            </td>

                            @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                @php
                                    $lane = $heat->where('lane', $l)->first();
                                @endphp


                                <td class="py-2 px-3 text-center border border-rlss-blue">
                                    @if ($lane)
                                        {{ $lane->team }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td>No heats available yet!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <br>
        <h2 class="font-astoria text-rlss-blue font-extrabold " id="tanks">Initiative Tanks </h2>

        <div class="grid-4">

            @foreach ($comp->getSercTanks()->groupBy('serc_tank')->sortKeys() as $tankNo => $tank)
                <div>
                    <h4 class=" text-rlss-red font-astoria">Tank {{ $tankNo }}</h4>

                    <table class="table-auto font-greycliff">
                        <thead>
                            <tr class=" text-rlss-blue font-bold">
                                <th class="bg-rlss-blue py-2 px-3 border border-rlss-blue"></th>
                                <th class=" bg-rlss-blue bg-opacity-40 py-2 px-3 border border-rlss-blue w-48">Category
                                </th>
                                <th class="border border-rlss-blue w-48 ">Region</th>
                                <th class="border border-rlss-blue w-48">Competitor</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tank as $heatNo => $competitor)
                                <tr>
                                    <td
                                        class=" bg-rlss-blue text-white  text-center py-2 px-3 border border-rlss-blue ">
                                        {{ $heatNo + 1 }}</td>
                                    <td
                                        class="py-2 px-3 bg-rlss-blue bg-opacity-40 text-rlss-blue border border-rlss-blue text-center">
                                        {{ $competitor->league }}
                                    </td>

                                    <td class="py-2 px-3 text-center border border-rlss-blue">
                                        {{ $competitor->region }}
                                    </td>
                                    <td class="py-2 px-3 text-center border border-rlss-blue">
                                        {{ $competitor->team }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td>No heats available yet!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach




        </div>


    </div>
    <br>


</body>

</html>
