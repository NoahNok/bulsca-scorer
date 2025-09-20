<head>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">
    <title>
        @if ($comp->areResultsProvisional())
            (PROVISIONAL)
        @endif{{ $schema->name }} | {{ $comp->name }}
    </title>
</head>

<body class="">
    <div class="  " id="raw_data">
        <h2>
            @if ($comp->areResultsProvisional())
                (PROVISIONAL)
            @endif{{ $schema->name }} | {{ $comp->name }}
        </h2>

        <table class=" text-sm   rounded-lg text-left text-gray-500 ">
            <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                <tr>

                    <th scope="col" class="py-2 px-4 whitespace-nowrap">
                        Team
                    </th>

                    @foreach ($results[0]->events as $event)
                        <th scope="col" class="py-2 px-4 whitespace-nowrap">
                            {{ $event->event->getName() }}
                        </th>
                    @endforeach

                    <th scope="col" class="py-2 px-4 whitespace-nowrap">
                        Total
                    </th>

                    <th scope="col" class="py-2 px-4 whitespace-nowrap">
                        Position
                    </th>


                </tr>
            </thead>
            <tbody>

                @forelse ($results as $result)
                    <tr class=" border-b text-right ">

                        <td class="py-2 px-4 text-black text-sm whitespace-nowrap">
                            {{ $result->entity->getName() }}

                        </td>

                        @foreach ($result->events as $event)
                            <td scope="col" class="py-2 px-4 whitespace-nowrap">
                                {{ round($event->adjustedPoints) }} ({{ $event->position }})
                            </td>
                        @endforeach

                        <td class="py-2 px-4 text-black text-sm whitespace-nowrap">
                            {{ round($result->totalPoints) }}

                        </td>

                        <td class="py-2 px-4 text-black text-sm whitespace-nowrap">
                            {{ $result->position }}

                        </td>




                    </tr>
                @empty
                    <tr class="bg-white border-b text-right ">
                        <th colspan="100" scope="row"
                            class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                            None
                        </th>
                    </tr>
                @endforelse



            </tbody>
        </table>

    </div>
    <small>Values have been rounded for presentation</small>
    <script>
        window.onload = function() {
            //window.print()
        }
    </script>
</body>
