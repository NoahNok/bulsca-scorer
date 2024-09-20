<head>
    <link rel="stylesheet" href="{{ asset('css/app.css?v=1.0.0') }}">
    <title>
        @if ($comp->areResultsProvisional()) (PROVISIONAL) @endif{{ $schema->name }} | {{ $comp->name }}
    </title>
</head>

<body class="">
    <div class="  " id="raw_data">
        <h2>@if ($comp->areResultsProvisional()) (PROVISIONAL) @endif{{ $schema->name }} | {{ $comp->name }}</h2>

        <table class=" text-sm   rounded-lg text-left text-gray-500 ">
            <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                <tr>


                    @foreach ($results[0] as $key => $value)

                    @if (!str_contains($key, "team") && !str_ends_with($key, "rsp") && !str_ends_with($key, "place") && !str_contains($key, "totalPoints") ) @continue

                    @endif

                    <th scope="col" class="py-2 px-4 whitespace-nowrap">
                        {{ str_replace("_", " ", preg_replace("/_[0-9]/mi", "", $key)) }}
                    </th>
                    @endforeach


                </tr>
            </thead>
            <tbody>

                @forelse ($results as $result)
                <tr class=" border-b text-right ">
                    @foreach ($result as $key => $value)
                    @if (!str_contains($key, "team") && !str_ends_with($key, "rsp") && !str_ends_with($key, "place") && !str_contains($key, "totalPoints") ) @continue

                    @endif
                    <td class="py-2 px-4 text-black text-sm whitespace-nowrap">
                        @if ($key == "team")
                        <span class="font-semibold">{{ $value }}</span>
                        @else

                        @if (str_contains($key, "rsp"))
                        ({{ $result->{$key . "_places"} }})

                        @endif

                        {{ round((float)$value) }}
                        @endif

                    </td>
                    @endforeach



                </tr>
                @empty
                <tr class="bg-white border-b text-right ">
                    <th colspan="100" scope="row" class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                        None
                    </th>
                </tr>
                @endforelse



            </tbody>
        </table>

    </div>
    <script>
        window.onload = function() {
            window.print()
        }
    </script>
</body>