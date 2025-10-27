<head>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">
    <title>
        Print All Results | {{ $event->getName() }} | {{ $comp->name }} | Scorer
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
            size: A4 landscape;
        }
    </style>
</head>

<body class=" overflow-x-hidden">
    @foreach ($data as $d)
        @php
            $league = $d['league'];
            $results = $d['results'];

        @endphp


        @if (count($results) == 0)
            @continue
        @endif

        <div class="  pagebreak " id="raw_data">
            <h2>
                @if ($comp->areResultsProvisional())
                    (PROVISIONAL)
                @endif{{ $league }} | {{ $event->getName() }} | {{ $comp->name }}
            </h2>



            <div class="se-table">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                                Team
                            </th>

                            <th scope="col">
                                @if ($event->getName() == 'Rope Throw')
                                    Ropes/Time
                                @else
                                    Time
                                @endif
                            </th>


                            <th scope="col">
                                Points
                            </th>
                            <th scope="col">
                                Position
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $eventHeats = $event->getHeats;
                        @endphp

                        @forelse ($results as $result)
                            @if ($result->isCombined())
                                <tr x-data="{ name: `{{ $result->entity->getName($comp) }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                                    <th scope="row">
                                        {!! $result->combined->map(fn($item) => $item->entity->getName($comp))->implode('<br>') !!}


                                    </th>

                                    <td>
                                        <div class="flex justify-end items-center ">
                                            <div class="border-r-2 pr-2">
                                                {!! $result->combined->map(fn($item) => App\Models\SpeedResult::prettyTime($item->result))->implode('<br>') !!}
                                            </div>
                                            <div class="pl-2">
                                                {{ App\Models\SpeedResult::prettyTime($result->resolvedResult) }}
                                            </div>
                                        </div>


                                    </td>



                                    <td>
                                        {{ $result->isDisqualified() && !$show_dq_points ? 'DQ' : (round($result->points, 1) ?: '-') }}
                                    </td>
                                    <td>
                                        {{ $result->position }}
                                    </td>

                                </tr>
                            @else
                                <tr x-data="{ name: `{{ $result->entity->getName($comp) }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                                    <th scope="row">
                                        {{ $result->entity->getName($comp) }}
                                    </th>

                                    <td>



                                        {{ App\Models\SpeedResult::prettyTime($result->resolvedResult) }}

                                        @if ($result->resolvedResult != $result->result)
                                            <br>
                                            <small>
                                                Was {{ App\Models\SpeedResult::prettyTime($result->result) }}
                                            </small>
                                        @endif


                                    </td>



                                    <td>
                                        {{ $result->isDisqualified() && !$show_dq_points ? 'DQ' : (round($result->points, 1) ?: '-') }}
                                    </td>
                                    <td>
                                        {{ $result->position }}
                                    </td>

                                </tr>
                            @endif


                        @empty
                            <tr class="empty ">
                                <th colspan="100" scope="row">
                                    None
                                </th>
                            </tr>
                        @endforelse



                    </tbody>
                </table>
            </div>


        </div>
    @endforeach
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
