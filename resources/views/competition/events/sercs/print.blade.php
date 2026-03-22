<head>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">
    <title>
        Print All Results | {{ $serc->getName() }} | {{ $comp->name }} | Scorer
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
                @endif{{ $league }} | {{ $serc->getName() }} | {{ $comp->name }}
            </h2>



            <div class="se-table">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                                Team
                            </th>

                            <th scope="col">
                                Raw Mark
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

                        @forelse ($results as $result)
                            @php
                                $name = $result->entity->getName($comp);
                            @endphp
                            <tr x-data="{ name: `{{ $name }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                                <th scope="row">
                                    {{ $name }}
                                </th>

                                <td>

                                    {{ $result->resolvedResult }}
                                </td>
                                <td>
                                    @php
                                        $res = round($result->points, 1);
                                    @endphp
                                    {!! $result->isDisqualified() ? "<s>{$res}</s> DQ" : $res !!}

                                </td>
                                <td>
                                    {{ $result->position }}
                                </td>


                            </tr>
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
