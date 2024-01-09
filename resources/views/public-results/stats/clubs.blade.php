<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clubs | Stats | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden flex w-full h-full justify-center">
    <div class=" w-[75%] my-28 ">
        <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class=" w-40 mb-2 " alt="">
        <h1 class="font-bold ">Clubs</h1>

        <br>

        <div class="grid-4">
            <p>
                @foreach (App\Models\Club::getStatableClubs() as $club)
                    <a href="{{ route('public.results.stats.club', $club->name) }}" class="link ">
                        {{ $club->name }}
                    </a>
                    <br>
                @endforeach
            </p>

            <div class="card">
                <h3>Records</h3>
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
                            @foreach (App\Stats\Stats::getGlobalSpeedEventRecords() as $record)
                                <tr class=" space">

                                    <td class="px-2 pl-0 whitespace-nowrap">{{ $record->event_name }}</td>
                                    <td class="px-2">
                                        {{ App\Models\SpeedResult::getPrettyTime($record->record) }}
                                    </td>
                                    <td class="px-2">{{ $record->name }}{{ $record->team }}</td>
                                    <td class="px-2">{{ $record->comp_name ?? '-' }}</td>

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
