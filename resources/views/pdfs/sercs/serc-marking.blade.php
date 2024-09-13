<!DOCTYPE html>
<html lang="en" class="bg-gray-100 w-screen h-screen flex items-center justify-center">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <title>Document</title>

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
        <h1>Initiative Marking Pack</h1>
        <h3>{{ $comp->name }}</h3>
        <p>{{ $location }}<br>{{ $comp->when->format('jS F Y') }}</p>
        <br>
        <br>
        <ol class="list ">
            @foreach ($events as $event)
                <li>{{ count($tanks) }}x {{ $event->getName() }} Marking Sheets (Over
                    {{ count($tanks->groupBy('serc_tank')) }} tanks)</li>
            @endforeach
        </ol>
        <br>
        <p class="  "><span class="font-semibold">Pages with a black strip at the top mark the begining of a new
                tank!</span><br><span class=" print:hidden">(Ensure 'Background graphics' is ticked when
                printing)</span></p>
    </div>


    @foreach ($events as $event)
        @forelse ($tanks->groupBy('serc_tank') as $key => $tank)
            @foreach ($tank as $draw => $competitior)
                <div class="min-h-[297mm] min-w-[210mm] bg-white p-5 flex flex-col grow-0 relative">
                    @if ($loop->index == 0)
                        <div class="absolute top-0 left-0 w-full h-2 bg-black">&nbsp;</div>
                    @endif

                    <div class="flex w-full justify-between items-center">
                        <h2 class="hmb-0">{{ $event->getName() }}</h2>
                        <p class=" font-semibold text-right">{{ $comp->name }} -
                            {{ $comp->when->format('jS F') }}<br><small>{{ $location }}</small></p>
                    </div>

                    <div class="flex w-full justify-between items-center">
                        <p>Tank {{ $key }} | Draw {{ $draw + 1 }}</p>
                        <p class=" font-semibold text-right">Initiative Judge</p>
                    </div>


                    <br>
                    <div class="flex justify-between items-center">
                        <h4>{{ $competitior->team }}</h4>
                        <p class="text-small text-gray-500">{{ $competitior->region }} | {{ $competitior->league }}
                        </p>
                    </div>



                    <table class=" table-fixed text-left ">
                        <thead class="border-b border-black">

                            <tr>
                                <th class="w-full">Criteria</th>
                                <th class="  min-w-[5.5rem] text-right">Mark (/10)</th>


                            </tr>
                        </thead>
                        <tbody class="">

                            @foreach ($event->getJudges as $judge)
                                <tr class="border-b border-black">
                                    <td class="py-1 bg-gray-200 " colspan="2">{{ $judge->name }}</td>

                                </tr>
                                @foreach ($judge->getMarkingPoints as $mp)
                                    <tr class="border-b border-black">
                                        <td class="py-2 border-r border-black indent-3 ">{{ $mp->name }}</td>
                                        <td></td>

                                    </tr>
                                @endforeach
                            @endforeach


                        </tbody>

                    </table>

                    <p><strong>Comments:</strong></p>

                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
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
                            preg_match_all('/\b\w/', $event->getName(), $matches);
                            $firstLetters = implode('', $matches[0]);

                        @endphp
                        <p class="text-sm text-right">IJ -
                            {{ $firstLetters }}
                            -
                            T{{ $key }} -
                            D{{ $draw + 1 }}</p>
                        {{-- ROLE - EVENT - TANK - DRAW --}}
                    </div>

                </div>
            @endforeach

        @empty
            <div></div>
        @endforelse
    @endforeach




</body>

</html>
