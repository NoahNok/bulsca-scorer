<head>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <title>
        Rough Judge Sheets | {{ $serc->getName() }}
    </title>
    <style>
        @media print {
            .pagebreak {
                page-break-before: always;
            }

            /* page-break-after works, as well */
        }

        @page {
            margin: 10px;
        }
    </style>
</head>

<body class="">
    <div class="  " id="raw_data">




        @foreach ($serc->getJudges as $judge)
            <div class="pagebreak">
                <h5>
                    {{ $judge->name }} | {{ $serc->getName() }}
                </h5>

                <table class=" text-sm   rounded-lg text-left text-gray-500 ">
                    <thead class="text-xs border text-gray-700 text-right uppercase bg-gray-50 ">
                        <tr>


                            <th scope="col" class="py-2 px-2 whitespace-nowrap">
                                Team
                            </th>
                            @foreach ($judge->getMarkingPoints as $mp)
                                <th scope="col" class="py-2 px-2  border  ">
                                    <div class=" table-caption text-left">{{ $mp->name }}</div>
                                </th>
                            @endforeach



                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($comp->getCompetitionTeams as $team)
                            <tr class=" border text-right ">

                                <td class="py-2 px-2 border text-black text-sm whitespace-nowrap">

                                    <span class="font-semibold">{{ $team->getFullname() }}</span>


                                </td>
                                @foreach ($judge->getMarkingPoints as $mp)
                                    <td class="py-2 px-2 border text-black text-sm whitespace-nowrap">




                                    </td>
                                @endforeach





                            </tr>
                        @endforeach








                    </tbody>
                </table>
            </div>
        @endforeach





    </div>
    <script>
        window.onload = function() {
            window.print()
        }
    </script>
</body>
