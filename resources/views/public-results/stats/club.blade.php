<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $club->name }} | Stats | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden flex w-full h-full justify-center">
    <div class=" w-full md:w-[75%] m-6 md:my-28 md:mx-0 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">

        <a href="{{ route('public.results.stats.clubs') }}"
            class="link flex items-center space-x-1  z-50 cursor-pointer">All Clubs</a>

        <h1 class="font-bold  " style="font">{{ $club->name }}</h1>



        <div class="flex divide-x ">Teams:
            @foreach ($club->getDistinctTeams() as $team)
                <a href="" class="link px-2">
                    {{ $team->team }}
                </a>
            @endforeach
        </div>
        <br>
        <div class="grid-4">
            <div class="card">
                <h3>Club Records</h3>
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
                            @foreach ($club->getClubRecords() as $record)
                                <tr class=" space">

                                    <td class="px-2 pl-0 whitespace-nowrap">{{ $record['se'] }}</td>
                                    <td class="px-2">
                                        {{ $record['result'] == 99999999999999999 ? '-' : App\Models\SpeedResult::getPrettyTime($record['result']) }}
                                    </td>
                                    <td class="px-2">{{ $record['team'] ?? '-' }}</td>
                                    <td class="px-2">{{ $record['comp_name'] ?? '-' }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <h3>Competed at</h3>
                <ol class=" columns-2">
                    @foreach ($club->getCompetitionsCompetedAt() as $comp)
                        <li><a href="{{ route('public.results.comp', $comp->name . '.' . $comp->id) }}"
                                class="link  whitespace-nowrap">
                                {{ $comp->name }}
                            </a></li>
                    @endforeach
            </div>
            <div class="card">
                <h3>Best SERCs</h3>
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
                            @foreach ($club->getBestSercs() as $serc)
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
        </div>

    </div>



    </div>

</body>

</html>
