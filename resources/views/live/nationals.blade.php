<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">


    <link rel="icon" type="image/png" href="{{ $comp->getBrand->getLogo() }}" />
    <title>{{ $comp->name }} | Live | RLSS</title>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>

    </style>
</head>

<body class="overflow-x-hidden  w-screen h-screen" x-data="{
    searchTerm: '',
}">


    <div class="w-full h-48 bg-rlss-blue flex  items-center px-12 overflow-x-hidden "
        style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

        <div class="container mx-auto flex flex-row items-center">
            <div>
                <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
                <p class=" font-ariel text-rlss-yellow font-semibold">{{ $comp->where }}</p>
            </div>

            <div class="!ml-auto   ">
                <img src="{{ $comp->getBrand->getLogo() }}" class=" w-20 h-20" alt="">
            </div>
        </div>



    </div>
    @php
        $pools = ['Diving Pit End', 'Scoreboard End'];

    @endphp

    <div class="container mx-auto py-6 overflow-x-hidden" x-data="{
        activeEvent: {{ $comp->getSpeedEvents->first()->id }}
    }">

        <div class="form-input search">
            <input type="text" id="competitor_search" placeholder="Competitior name..." x-model="searchTerm">


        </div>

        <div class="w-full max-w-full flex  px-2">
            <a href="#tanks" class="btn !bg-rlss-blue mx-2 grow  ">Jump to tanks</a>
        </div>


        <br>

        <br>
        <div class="w-full max-w-full flex  items-center justify-center md:justify-start">

            <h2 class="font-astoria text-rlss-blue font-extrabold hidden md:block hmb-0 ">Heats for:</h2>
            @foreach ($comp->getSpeedEvents as $event)
                <button class="btn  mx-2  btn-white"
                    x-bind:class="activeEvent == {{ $event->id }} ? '!bg-rlss-blue ' : ''"
                    x-bind:style="activeEvent == {{ $event->id }} ? 'color:white !important; ' : ''"
                    @click="activeEvent = {{ $event->id }}">{{ $event->getName() }}</button>
            @endforeach
        </div>
        <br>

        @foreach ($comp->getSpeedEvents as $event)
            <div x-show="activeEvent == {{ $event->id }}">
                @php
                    $entries = collect(
                        DB::select(
                            'SELECT h.id, h.heat, h.lane, ct.team, l.name AS league, c.name AS club, c.region FROM heats h INNER JOIN competition_teams ct ON ct.id=h.team INNER JOIN leagues l ON l.id=ct.league INNER JOIN clubs c ON c.id=ct.club WHERE h.competition = ? AND h.event=? ORDER BY heat, lane;',
                            [$comp->id, $event->id],
                        ),
                    );
                    $heats = $entries->sortBy(['heat', 'lane'])->groupBy('heat');
                @endphp

                <h3 class="font-astoria text-rlss-blue font-extrabold hidden md:block ">{{ $event->getName() }} -
                    Diving
                    Pit
                    End</h3>



                <div class="w-full font-greycliff flex flex-col md:hidden">
                    @foreach ($heats as $key => $heat)
                        <div
                            x-show="{{ json_encode($heat->map(function ($c) {return strtolower(strtr(utf8_decode($c->team), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));})) }}.some((el) => el.startsWith(searchTerm.trim().toLowerCase()))">
                            <div class=" bg-rlss-blue px-4 pt-3 pb-[0.375rem]">
                                <h3 class="text-3xl text-white font-astoria hmb-0 flex items-center justify-between">
                                    Heat
                                    {{ $heat->first()->heat }}
                                    <span class="text-base text-rlss-yellow">{{ $pools[($key + 1) % 2] }}</span>
                                </h3>
                            </div>



                            @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                @php
                                    $lane = $heat->where('lane', $l)->first();
                                @endphp
                                @if ($lane)
                                    <div class="flex grow"
                                        x-show="`{{ strtolower(strtr(utf8_decode($lane->team), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY')) }}`.startsWith(searchTerm.trim().toLowerCase())">
                                        <div
                                            class=" bg-rlss-blue text-white font-greycliff font-semibold py-3 w-8 text-center">
                                            {{ $l }}
                                        </div>
                                        <div class="py-2 px-3  grow">

                                            {{ $lane->team }}

                                        </div>
                                    </div>
                                @endif
                            @endfor
                        </div>
                    @endforeach
                </div>

                <div class="w-full overflow-x-auto hidden md:block ">
                    <table class="table-auto font-greycliff min-w-full">
                        <thead>
                            <tr class=" text-rlss-blue font-bold  ">
                                <th class="bg-rlss-blue py-2 px-3 border border-rlss-blue sticky left-0 top-0"></th>
                                <th
                                    class=" bg-rlss-blue bg-opacity-40 py-2 px-3 border border-rlss-blue w-48 sticky top-0  ">
                                    Category</th>
                                <th class="border border-rlss-blue w-48 sticky top-0 ">Lane 1</th>
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
                            @forelse ($heats as $key => $heat)
                                @if ($key % 2 == 0)
                                    @continue
                                @endif

                                <tr class="relative"
                                    x-show="{{ json_encode($heat->map(function ($c) {return strtolower(strtr(utf8_decode($c->team), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));})) }}.some((el) => el.startsWith(searchTerm.trim().toLowerCase()))">
                                    <td
                                        class=" bg-rlss-blue text-white  text-center py-2 px-3 border border-rlss-blue sticky left-0 w-2 ">
                                        {{ $heat->first()->heat }}</td>
                                    <td
                                        class="py-2 px-3 bg-rlss-blue bg-opacity-40 text-rlss-blue border border-rlss-blue text-center  ">
                                        {{ $heat->first()->league }}
                                    </td>

                                    @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                        @php
                                            $lane = $heat->where('lane', $l)->first();
                                        @endphp


                                        <td class="py-2 px-3 text-center border border-rlss-blue"
                                            x-bind:class="`{{ strtolower(strtr(utf8_decode($lane->team ?? '-'), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY')) }}`
                                            .startsWith(searchTerm.trim().toLowerCase()) && searchTerm.trim() != '' ?
                                                'bg-rlss-blue text-rlss-yellow' :
                                                ''">
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
                <h3 class="font-astoria text-rlss-blue font-extrabold hidden md:block ">{{ $event->getName() }} -
                    Scoreboard End </h3>
                <div class="w-full overflow-x-auto hidden md:block ">
                    <table class="table-auto font-greycliff w-full">
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
                            @forelse ($heats as $key => $heat)
                                @if ($key % 2 == 1)
                                    @continue
                                @endif
                                <tr
                                    x-show="{{ json_encode($heat->map(function ($c) {return strtolower(strtr(utf8_decode($c->team), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));})) }}.some((el) => el.startsWith(searchTerm.trim().toLowerCase()))">
                                    <td
                                        class=" bg-rlss-blue text-white  text-center py-2 px-3 border border-rlss-blue sticky left-0 w-2">
                                        {{ $heat->first()->heat }}</td>
                                    <td
                                        class="py-2 px-3 bg-rlss-blue bg-opacity-40 text-rlss-blue border border-rlss-blue text-center">
                                        {{ $heat->first()->league }}
                                    </td>

                                    @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                        @php
                                            $lane = $heat->where('lane', $l)->first();
                                        @endphp


                                        <td class="py-2 px-3 text-center border border-rlss-blue"
                                            x-bind:class="`{{ strtolower(strtr(utf8_decode($lane->team ?? '-'), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY')) }}`
                                            .startsWith(searchTerm.trim().toLowerCase()) && searchTerm.trim() != '' ?
                                                'bg-rlss-blue text-rlss-yellow' :
                                                ''">
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
            </div>
        @endforeach

        <h2 class="font-astoria text-rlss-blue font-extrabold md:hidden ">Event Order</h2>


        <br>
        <h2 class="font-astoria text-rlss-blue font-extrabold " id="tanks">Initiative Tanks </h2>

        <div class="grid-3">

            @foreach ($comp->getSercTanks()->groupBy('serc_tank')->sortKeys() as $tankNo => $tank)
                <div
                    x-show="{{ json_encode($tank->map(function ($c) {return strtolower(strtr(utf8_decode($c->team), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));})) }}.some((el) => el.startsWith(searchTerm.trim().toLowerCase()))">
                    <h4 class=" text-rlss-red font-astoria">Tank {{ $tankNo }}</h4>

                    <table class="table-auto font-greycliff">
                        <thead>
                            <tr class=" text-rlss-blue font-bold">
                                <th class="bg-rlss-blue py-2 px-2 border border-rlss-blue"></th>
                                <th class=" bg-rlss-blue bg-opacity-40 py-2 px-2 border border-rlss-blue w-48">Category
                                </th>
                                <th class="border border-rlss-blue w-48 ">Region</th>
                                <th class="border border-rlss-blue w-48">Competitor</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tank as $heatNo => $competitor)
                                <tr class="" x-data="{
                                    shouldShow() {
                                        return `{{ strtolower(strtr(utf8_decode($competitor->team), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY')) }}`.startsWith(searchTerm.trim().toLowerCase())
                                    }
                                }" x-show="shouldShow">
                                    <td
                                        class=" bg-rlss-blue text-white  text-center py-2 px-2 border border-rlss-blue ">
                                        {{ $heatNo + 1 }}</td>
                                    <td
                                        class="py-2 px-2 bg-rlss-blue bg-opacity-40 text-rlss-blue border border-rlss-blue text-center">
                                        {{ $competitor->league }}
                                    </td>

                                    <td class="py-2 px-2 text-center border border-rlss-blue">
                                        {{ $competitor->region }}
                                    </td>
                                    <td class="py-2 px-2 text-center border border-rlss-blue"
                                        x-bind:class="shouldShow && searchTerm.trim() != '' ? 'bg-rlss-blue text-rlss-yellow' : ''">
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
