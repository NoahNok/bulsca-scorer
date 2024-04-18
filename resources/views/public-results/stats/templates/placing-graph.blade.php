@php
    $order = [];
    $compNames = [];

    foreach ($data as $comp) {
        $order[$comp->league][$comp->team][] = $comp;

        if (!in_array($comp->competition, $compNames)) {
            $compNames[] = $comp->competition;
        }
    }

    $reform = [];

    if (isset($order['O'])) {
        $reform['Overall'] = $order['O'];
    }

    if (isset($order['A'])) {
        $reform['A'] = $order['A'];
    }

    if (isset($order['B'])) {
        $reform['B'] = $order['B'];
    }

@endphp

@foreach ($reform as $league => $league_data)
    @php

        ksort($league_data);

        $maxPoints = 0;
        $totalPoints = 0;
        $totalEntries = 0;

        $maxPointsTeam = '';
        $maxPointsComp = [];

        $avgPlace = 0;

        foreach ($league_data as $team => $data) {
            foreach ($data as $comp) {
                $totalPoints += $comp->points;
                $totalEntries++;
                if ($comp->points > $maxPoints) {
                    $maxPoints = $comp->points;
                    $maxPointsTeam = $team;
                    $maxPointsComp = $comp;
                }
                $avgPlace += $comp->place;
            }
        }

        $avgPoints = $totalPoints / $totalEntries;
        $avgPlace = $avgPlace / $totalEntries;

    @endphp




    <div class="card col-span-full lg:col-span-4 3xl:col-span-2 z-30" x-data="{ show: 1 }">

        <div class="flex space-x-2 items-center justify-between">
            <h3>{{ $league }}</h3>
            <div class="flex space-x-2">
                <div class=" transition-colors rounded-full py-1 px-4 text-xs  cursor-pointer hover:bg-bulsca hover:text-white"
                    :class="show == 1 ? 'bg-bulsca text-white' : 'bg-gray-200'" @click="show=1">Graph</div>
                <div class="  transition-colors rounded-full py-1 px-4 text-xs  cursor-pointer hover:bg-bulsca hover:text-white"
                    :class="show == 2 ? 'bg-bulsca text-white' : 'bg-gray-200'" @click="show=2">Table</div>
            </div>
        </div>
        <canvas id="placingChart-{{ $club->name }}-{{ $team ?? ''}}-{{ $league }}" x-league="{{ $league }}" x-show="show==1"
            class="mt-auto mb-auto"></canvas>



        <div class="w-full h-full overflow-x-auto" x-show="show==2" style="display:none">
            <table class="table-auto ">
                <thead class="text-left">
                    <tr>
                        <th class="px-2 whitespace-nowrap ">Team</th>
                        @foreach ($compNames as $name)
                            <th class="px-2 whitespace-nowrap ">{{ $name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $nf = new NumberFormatter('en-GB', NumberFormatter::ORDINAL);

                    @endphp


                    @foreach ($league_data as $key => $data)
                        <tr>
                            <td class="px-2 ">
                                {{ $key }}
                            </td>
                            @foreach ($data as $comp)
                                <td class="px-2 ">
                                    {{ $nf->format($comp->place) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach





                </tbody>
            </table>
        </div>


    </div>

    <div class="grid grid-rows-3 col-span-full lg:col-span-2  3xl:col-span-1 gap-y-2 z-20">

        <div class="card  min-w-full  relative justify-center ">

            <div class="absolute w-6 h-1 bg-bulsca_red -left-6 hidden lg:block "></div>

            <div class="absolute w-1 h-6 bg-bulsca_red -top-6 lg:hidden "></div>

            <div class="absolute w-1 h-3 bg-bulsca -bottom-3 "></div>


            <div class="flex ">
                <p class="font-semibold  ">Best Score</p>
                <a class="link ml-auto"
                    href="{{ route('public.results.comp', $maxPointsComp->competition . '.' . $maxPointsComp->competition_id) }}"><span
                        class="text-sm">{{ $maxPointsComp->competition }}</span></a>
            </div>
            <h3 class="hmb-0">
                {{ round($maxPoints) }} pts
            </h3>
            <p class=" text-sm flex">

                <span>{{ $club->name }} {{ $maxPointsTeam }} </span>

            </p>
        </div>

        <div class="card  min-w-full  snap-center z-30 relative justify-center ">

            <div class="absolute w-1 h-3 bg-bulsca_red -bottom-3 "></div>

            <div class="flex ">
                <p class="font-semibold  ">Avg Score</p>

            </div>
            <h3 class="hmb-0">
                {{ round($avgPoints) }} pts
            </h3>
            <p class=" text-sm flex">

                <span>Over {{ $totalEntries }} {{ $totalEntries > 1 ? 'entries' : 'entry' }} </span>

            </p>

        </div>
        @php
            $nf = new NumberFormatter('en-GB', NumberFormatter::ORDINAL);

        @endphp
        <div class="card  min-w-full  snap-center z-40 justify-center ">

            <div class="flex ">
                <p class="font-semibold  ">Avg Place</p>

            </div>
            <h3 class="hmb-0">
                {{ $nf->format(round($avgPlace)) }}
            </h3>
            <p class=" text-sm flex">

                <span>Over {{ $totalEntries }} {{ $totalEntries > 1 ? 'entries' : 'entry' }} </span>

            </p>

        </div>



    </div>

    <script>
        var ctx = document.getElementById("placingChart-{{ $club->name }}-{{ $team ?? '' }}-{{ $league }}").getContext('2d');




        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @foreach ($compNames as $name)
                        '{{ $name }}',
                    @endforeach
                ],
                datasets: [

                    @foreach ($league_data as $key => $data)
                        {
                            label: '{{ $key }}',
                            data: [
                                @foreach ($data as $comp)
                                    {{ $comp->place }},
                                @endforeach
                            ],

                            fill: false,
                            tension: 0.1,
                            spanGaps: true,
                        },
                    @endforeach



                ]
            },
            options: {
                scales: {
                    y: {

                        reverse: true,

                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            callback: function(value, index, values) {
                                return addSuffix(value);
                            },

                        }
                    }
                }
            }
        });
    </script>
@endforeach
