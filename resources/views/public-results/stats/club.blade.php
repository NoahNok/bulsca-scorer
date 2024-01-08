<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden flex w-full h-full justify-center">
    <div class=" w-[75%] my-28 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">
        <h1 class="font-bold ">{{ $club->name }}</h1>


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
                <table class="table-auto">
                    <thead class="text-left">
                        <tr>
                            <th>Event</th>
                            <th>Time</th>
                            <th>Team</th>
                            <th>Competition</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($club->getClubRecords() as $record)
                            <tr>
                                @php

                                @endphp
                                <td>{{ $record['se'] }}</td>
                                <td>{{ $record['result'] == 99999999999999999 ? '-' : App\Models\SpeedResult::getPrettyTime($record['result']) }}
                                </td>
                                <td>{{ $record['team'] ?? '-' }}</td>
                                <td>{{ $record['comp_name'] ?? '-' }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                <h3>Best Serc</h3>
                @php
                    $bestSerc = $club->getBestSerc()[0];
                @endphp
                <p><strong>{{ $bestSerc->serc_name }}</strong> at {{ $bestSerc->name }}</p>
                <p>{{ round($bestSerc->total) }}/{{ round($bestSerc->max) }}
                    (<strong>{{ round(($bestSerc->total / $bestSerc->max) * 100, 2) }}%</strong>) by
                    {{ $club->name }} <strong>{{ $bestSerc->team }}</strong></p>
            </div>
        </div>

    </div>



    </div>

</body>

</html>
