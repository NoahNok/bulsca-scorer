<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name }} | Stats | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>

<body class="overflow-x-hidden flex w-full h-full justify-center">
    <div class=" w-full md:w-[75%] m-6 md:my-28 md:mx-0 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">

        <a href="{{ route('public.results.stats.clubs') }}"
            class="link flex items-center space-x-1  z-50 cursor-pointer">All Clubs</a>

        <h1 class="font-bold  " style="font">{{ $club->name }}</h1>



        <div class="flex divide-x ">Teams:
            @foreach ($distinctTeams as $team)
                <a href="" class="link px-2">
                    {{ $team->team }}
                </a>
            @endforeach
        </div>
        <br>
        <div class="grid-3">
            <div class="card">
                <h3>Speed Records</h3>
                <div class="w-full h-full overflow-x-auto">
                    <table class="table-auto">
                        <thead class="text-left">
                            <tr class="gap-1">
                                <th class="px-2 pl-0">Event</th>
                                <th class="px-2">Time</th>
                                <th class="px-2">Team</th>
                                <th class="px-2">Competition</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($speedRecords as $record)
                                <tr class=" space">

                                    <td class="px-2 pl-0 whitespace-nowrap">{{ $record['se'] }}</td>
                                    <td class="px-2">
                                        {{ $record['result'] == 99999999999999999 ? '-' : App\Models\SpeedResult::getPrettyTime($record['result']) }}
                                    </td>
                                    <td class="px-2">{{ $record['team'] ?? '-' }}</td>
                                    <td class="px-2"><a class="link"
                                            href="{{ route('public.results.comp', ($record['comp_name'] ?? '') . '.' . ($record['comp_id'] ?? '')) }}">{{ $record['comp_name'] ?? '-' }}</a>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <h3>SERC Records</h3>
                <div class="w-full h-full overflow-x-auto">
                    <table class="table-auto">
                        <thead class="text-left">
                            <tr>
                                <th class="px-2 pl-0">SERC</th>
                                <th class="px-2">Points</th>
                                <th class="px-2">Team</th>
                                <th class="px-2">Competition</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sercRecords as $serc)
                                <tr>
                                    @php

                                    @endphp
                                    <td class="px-2 pl-0"><a
                                            href="{{ route('public.results.serc', [$serc->comp_name . '.' . $serc->comp_id, $serc->serc_id]) }}"
                                            class="link">{{ $serc->serc_name }}</a>
                                    </td>
                                    <td class="px-2 whitespace-nowrap">
                                        {{ round($serc->total) }}/{{ round($serc->max) }}
                                        (<strong>{{ round(($serc->total / $serc->max) * 100, 2) }}%</strong>)
                                    </td>
                                    <td class="px-2">{{ $serc->team }}</td>
                                    <td class="px-2"><a
                                            href="{{ route('public.results.comp', $serc->comp_name . '.' . $serc->comp_id) }}"
                                            class="link  whitespace-nowrap">
                                            {{ $serc->comp_name }}
                                        </a></li>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <h3>Competed at</h3>
                <ol class=" columns-3">
                    @foreach ($competedAt as $comp)
                        <li><a href="{{ route('public.results.comp', $comp->name . '.' . $comp->id) }}"
                                class="link  whitespace-nowrap">
                                {{ $comp->name }}
                            </a></li>
                    @endforeach
            </div>


            @foreach ($allPlacings as $key => $placings)
                <div class="card md:row-start-2 " x-data="{ show: 1 }">

                    <div class="flex space-x-2 items-center justify-between">
                        <h3>{{ $key }}</h3>
                        <div class="flex space-x-2">
                            <div class=" transition-colors rounded-full py-1 px-4 text-xs bg-gray-200 cursor-pointer hover:bg-bulsca hover:text-white"
                                :class="show == 1 && 'bg-bulsca text-white'" @click="show=1">Graph</div>
                            <div class="  transition-colors rounded-full py-1 px-4 text-xs bg-gray-200 cursor-pointer hover:bg-bulsca hover:text-white"
                                :class="show == 2 && 'bg-bulsca text-white'" @click="show=2">Table</div>
                        </div>
                    </div>
                    <canvas id="placingChart-{{ $key }}" x-show="show==1"></canvas>



                    <div class="w-full h-full overflow-x-auto" x-show="show==2" style="display:none">
                        <table class="table-auto ">
                            <thead class="text-left">
                                <tr>
                                    <th class="px-2 pl-0">Team</th>
                                    @foreach ($competedAt as $comp)
                                        <th class="px-2 whitespace-nowrap ">{{ $comp->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $nf = new NumberFormatter('en-GB', NumberFormatter::ORDINAL);

                                @endphp

                                @foreach ($distinctTeams as $team)
                                    <tr>
                                        @if (!($placings[$team->team] ?? false))
                                            @continue
                                        @endif
                                        <td class="px-2 pl-0">{{ $team->team }}</td>
                                        @foreach ($competedAt as $comp)
                                            <td class="px-2 ">
                                                {{ ($placings[$team->team] ?? false) && ($placings[$team->team][$comp->id] ?? false) ? $nf->format($placings[$team->team][$comp->id]['place']) : '-' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>


                </div>
            @endforeach


        </div>

    </div>



    </div>

    <script>
        @foreach ($allPlacings as $key => $placings)
            var ctx = document.getElementById('placingChart-{{ $key }}').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach ($competedAt as $comp)
                            '{{ $comp->name }}',
                        @endforeach
                    ],
                    datasets: [
                        @foreach ($distinctTeams as $team)
                            @if (!($placings[$team->team] ?? false))
                                @continue
                            @endif {
                                label: '{{ $team->team }}',
                                data: [
                                    @foreach ($competedAt as $comp)
                                        {{ ($placings[$team->team] ?? false) && ($placings[$team->team][$comp->id] ?? false) ? $placings[$team->team][$comp->id]['place'] : 'null' }},
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
                                callback: function(value, index, values) {
                                    return addSuffix(value);
                                },

                            }
                        }
                    }
                }
            });
        @endforeach

        function addSuffix(i) {
            var a = i % 10,
                b = i % 100;

            if (i == 0) {
                return "0"
            }

            if (a == 1 && b != 11) {
                return i + "st";
            } else if (a == 2 && b != 12) {
                return i + "nd";
            } else if (a == 3 && b != 13) {
                return i + "rd";
            } else {
                return i + "th";
            }
        }
    </script>

</body>

</html>
