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


        <div class="w-screen h-36 bg-rlss-blue flex  items-center justify-start px-6 overflow-x-hidden space-x-6"
            style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

            <div class="">
                <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
                <p class=" font-ariel text-rlss-yellow font-semibold">{{ $comp->where }}</p>
            </div>

            <div class="w-1 h-12  border-r border-white"></div>

            <div class="">
                <h3 class=" text-white font-astoria hmb-0">{{ $schema->name }}</h3>

            </div>

            <div class=" flex items-center justify-center space-x-3 pl-12">
                <p class="text-white font-ariel text-xs -mt-1">Result breakdowns availalbe online</p>
                {!! QrCode::size(75)->style('round')->generate(\App\Helpers\RouteHelpers::externalRoute('results', 'public.results.comp', $comp->resultsSlug())) !!}
            </div>

            <div class="!ml-auto   ">
                <img src="{{ $comp->getBrand->getLogo() }}" class=" w-20 h-20" alt="">
            </div>


        </div>

        <table class="w-full text-sm shadow-md  rounded-lg text-left text-gray-500 ">
            <thead class="text-xs text-rlss-blue ">
                <tr class="border border-rlss-blue font-bold">
                    <th class="bg-rlss-blue border-x border-rlss-blue w-8"></th>
                    <th scope="col"
                        class="py-2 px-4 whitespace-nowrap bg-rlss-blue bg-opacity-40 border-x border-rlss-blue ">
                        Competitors
                    </th>

                    @foreach ($results['eventOrder'] as $event)
                        <th scope="col" class="py-2 px-4 whitespace-nowrap ">
                            {{ $event }}
                        </th>
                    @endforeach
                    <th scope="col" class="py-2 px-4 whitespace-nowrap border-x border-rlss-blue">Total</th>
                    <th scope="col" class="py-2 px-4 whitespace-nowrap border-x border-rlss-blue">Position</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results['results'] as $result)
                    <tr class="bg-white border-b text-left text-rlss-blue border-rlss-blue ">

                        <td
                            class="py-3 text-center text-xs whitespace-nowrap bg-rlss-blue text-white border-x  border-rlss-blue">
                            {{ $result->place }}
                        </td>

                        <td
                            class="py-3 px-4  text-xs whitespace-nowrap font-bold bg-rlss-blue bg-opacity-40  border-x border-rlss-blue">
                            {{ $result->name }}
                        </td>


                        @foreach ($result->events as $event)
                            <td class="py-3 px-4 text-black text-xs whitespace-nowrap  ">
                                {{ $event?->place ?? 16 }}
                            </td>
                        @endforeach

                        <td class="py-3 px-4 text-black text-xs whitespace-nowrap border-x border-rlss-blue">
                            {{ $result->score }}
                        </td>

                        <td class="py-3 px-4 text-black text-xs whitespace-nowrap border-x border-rlss-blue">
                            {{ $result->place }}
                        </td>





                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <script>
        window.onload = function() {
            // window.print()
        }
    </script>
</body>
