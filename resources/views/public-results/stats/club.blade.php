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
                <h3>Best SERCs</h3>
                <table class="table-auto">
                    <thead class="text-left">
                        <tr>
                            <th>SERC</th>
                            <th>Points</th>
                            <th>Team</th>
                            <th>Competition</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($club->getBestSercs() as $serc)
                            <tr>
                                @php

                                @endphp
                                <td><a href="{{ route('public.results.serc', [$serc->comp_name . '.' . $serc->comp_id, $serc->serc_id]) }}"
                                        class="link">{{ $serc->serc_name }}</a>
                                </td>
                                <td>{{ round($serc->total) }}/{{ round($serc->max) }}
                                    (<strong>{{ round(($serc->total / $serc->max) * 100, 2) }}%</strong>)
                                </td>
                                <td>{{ $serc->team }}</td>
                                <td><a href="{{ route('public.results.comp', $serc->comp_name . '.' . $serc->comp_id) }}"
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

</body>

</html>
