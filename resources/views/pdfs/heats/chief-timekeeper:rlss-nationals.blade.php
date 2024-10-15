<!DOCTYPE html>
<html lang="en" class="bg-gray-100 w-screen h-screen flex items-center justify-center">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <title>Chief Timekeeper Pack</title>

    <style>
        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

        }
    </style>

    @if ($brand)
        <style>
            :root {
                --brand-primary: {{ $brand->primary_color }};
                --brand-secondary: {{ $brand->secondary_color }};
            }
        </style>
    @endif
</head>



<body class="w-screen h-screen  flex flex-col space-y-12 print:space-y-0 items-center overflow-x-hidden ">


    <div class="min-h-[297mm] min-w-[210mm] bg-white p-5 flex flex-col  items-center justify-center text-center">
        <h1>Chief Timekeeper Pack</h1>
        <h3>{{ $comp->name }}</h3>
        <p>{{ $location }}<br>{{ $comp->when->format('jS F Y') }}</p>
        <br>
        <br>
        <ol class="lis list-disc">
            @foreach ($eventNames as $eventName)
                <li>{{ count($heats[$eventName]->groupBy('heat')) }}x {{ $eventName }} Heats</li>
            @endforeach
        </ol>
    </div>


    @foreach ($eventNames as $eventName)
        @forelse ($heats[$eventName]->sortBy(['heat','lane'])->groupBy('heat') as $key => $heat)
            <div class="min-h-[297mm] min-w-[210mm] bg-white p-5 flex flex-col grow-0 ">
                <div class="flex w-full justify-between items-center">
                    <h2 class="hmb-0">{{ $eventName }}</h2>
                    <p class=" font-semibold text-right">{{ $comp->name }} -
                        {{ $comp->when->format('jS F') }}<br><small>{{ $location }}</small></p>
                </div>

                <div class="flex w-full justify-between items-center">
                    <p>{{ $poolNames[($key - 1) % count($poolNames)] }}</p>
                    <p class=" font-semibold text-right">CHIEF TIMEKEEPER</p>
                </div>


                <br>

                <h3>Heat {{ $heat->first()->heat }}</h3>

                <table class=" table-fixed text-left ">
                    <thead class="border-b border-black">
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-center" colspan="3">Time</th>

                        </tr>
                        <tr>
                            <th class="text-center">Lane</th>
                            <th>Bracket</th>
                            <th>Region</th>
                            <th>Competitor</th>
                            <th class="w-14 text-center">DQ</th>
                            <th class="w-14 text-center">Mins</th>
                            <th class="w-14 text-center">Secs</th>
                            <th class="w-14 text-center">100th</th>

                        </tr>
                    </thead>
                    <tbody class="">

                        @for ($l = 1; $l <= $comp->max_lanes; $l++)
                            @php
                                $lane = $heat->where('lane', $l)->first();
                            @endphp


                            @if ($lane)
                                <tr class="border-b border-black">
                                    <td class="py-2 text-center">{{ $l }}</td>
                                    <td>{{ $lane->league }}</td>
                                    <td>{{ $lane->region }}</td>
                                    <td>{{ $lane->team }}</td>
                                    <td></td>
                                    <td class="border-x border-black"></td>
                                    <td class="border-x border-black"></td>
                                    <td class=" border-black"></td>
                                </tr>
                            @endif
                        @endfor



                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-sm"><strong>{{ count($heat) }}
                                    lane{{ count($heat) == 1 ? '' : 's' }}</strong></td>
                        </tr>
                    </tfoot>
                </table>


                <div class="flex flex-col items-center justify-center">
                    <p class="text-sm font-semibold">Order of finish</p>
                    <div class="border border-black divide-x divide-black flex items-center">
                        @foreach ($heat as $h)
                            <div class=" size-10"></div>
                        @endforeach
                    </div>
                </div>

                <br>
                <br>

                <div>
                    <p><strong>Time:</strong>
                        <br>
                        <strong>Signature:</strong>
                    </p>
                </div>

                <div class="mt-auto">
                    @php
                        preg_match_all('/\b\w/', $eventName, $matches);
                        $firstLetters = implode('', $matches[0]);

                        preg_match_all('/\b\w/', $poolNames[($key - 1) % count($poolNames)], $matches);
                        $poolShort = implode('', $matches[0]);
                    @endphp
                    <p class="text-sm text-right">CT -
                        {{ $firstLetters }}
                        -
                        H{{ $key }} -
                        {{ $poolShort }}</p>
                    {{-- ROLE - EVENT - HEAT - POOL --}}
                </div>

            </div>
        @empty
            <div></div>
        @endforelse
    @endforeach


    <script>
        window.onload = function() {
            window.print()
        }
    </script>

</body>

</html>
