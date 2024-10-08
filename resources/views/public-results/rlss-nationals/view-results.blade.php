<!DOCTYPE html>
<html lang="en">
@php
    if ($comp->getBrand != null) {
        $brand = $comp->getBrand;
    }
@endphp

<head>
    <meta charset="UTF-8">


    <link rel="icon" type="image/png" href="{{ $brand->getLogo() }}" />
    <title>{{ $schema->name }} | {{ $comp->name }} | Results | RLSS</title>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        :root {
            --brand-primary: {{ $brand->primary_color }};
            --brand-secondary: {{ $brand->secondary_color }};
        }
    </style>
</head>

<body class="overflow-x-hidden  w-screen h-screen">


    <div class="w-full h-48 bg-rlss-blue flex  items-center px-12 overflow-x-hidden "
        style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

        <div class="container mx-auto flex flex-row items-center">
            <div>
                <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
                <p class=" font-ariel text-rlss-yellow font-semibold">{{ $comp->where }}</p>
            </div>

            <div class="!ml-auto   ">
                <img src="{{ $brand->getLogo() }}" class=" w-20 h-20" alt="">
            </div>
        </div>



    </div>



    <div class="container mx-auto py-6 overflow-x-hidden">
        <a class="link" href="{{ route('public.results.comp', [$comp->resultsSlug()]) }}"><small>Back</small></a>

        <h2 class="font-astoria text-rlss-blue font-extrabold hmb-0">{{ $schema->name }}
        </h2>
        <p>You may need to scroll/drag on the table to see more results.</p>
        <br>
        <div class="  relative overflow-x-auto w-full  lg:max-w-[80vw] max-h-[90vh] lg:max-h-[80vh]  ">
            @php
                $overalls = str_starts_with($schema->league, 'O');
            @endphp
            <table id="table"
                class="table-highlight text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse relative">
                <thead class="text-xs text-gray-700  uppercase bg-gray-50 ">
                    <tr>



                        <th scope="col" class="py-3 px-6  whitespace-nowrap  max-w-[40vw] break-words ">
                            Competitior(s)
                        </th>
                        @foreach ($results['eventOrder'] as $event)
                            @if (!$overalls)
                                <th scope="col" class="py-2 px-4 whitespace-nowra text-right">
                                    {{ $event }} Result
                                </th>
                                <th scope="col" class="py-2 px-4 whitespace-nowrap text-center">
                                    Pos
                                </th>
                            @else
                                <th scope="col" class="py-2 px-4 whitespace-nowra text-right">
                                    {{ $event }}
                                </th>
                            @endif
                        @endforeach
                        <th scope="col" class="py-3 px-6  whitespace-nowrap ">
                            Total
                        </th>
                        <th scope="col" class="py-3 px-6  whitespace-nowrap relative  top-0 md:right-0">
                            Place
                        </th>



                    </tr>
                </thead>

                <tbody id="table-body">

                    @forelse ($results['results'] as $result)
                        <tr class="bg-white border-b  place-{{ $result->place }} ">


                            <td
                                class="py-3 px-6 text-black text-sm whitespace-nowrap bg-white max-w-[40vw] overflow-x-auto">

                                @php
                                    $nameGroup = collect($result->events)
                                        ->where('type', 'speed')
                                        ->first();

                                @endphp

                                @if ($nameGroup && property_exists($nameGroup, 'pair'))
                                    @if ($nameGroup->team > $nameGroup->pair->name)
                                        {{ $nameGroup->team }}
                                        <br>
                                        {{ $nameGroup->pair->name }}
                                    @else
                                        {{ $nameGroup->pair->name }}
                                        <br>
                                        {{ $nameGroup->team }}
                                    @endif
                                @else
                                    {{ $result->name }}
                                @endif






                            </td>


                            @foreach ($result->events as $event)
                                @php
                                    $pair = property_exists($event, 'pair');
                                @endphp

                                @if (!$overalls)
                                    <td class="py-2 px-4 text-black text-xs whitespace-nowrap">

                                        <div class="flex items-center  justify-end ">
                                            @if ($event->type == 'speed')
                                                @if ($pair)
                                                    <div class="flex items-center justify-end">
                                                        <div class="border-r pr-2">

                                                            @if ($event->team > $event->pair->name)
                                                                @if ($event->disqualification)
                                                                    {{ App\Models\SpeedResult::remapDq($event->disqualification) }}
                                                                @else
                                                                    {{ App\Models\SpeedResult::prettyTime($event->base_result) }}
                                                                @endif


                                                                <br>
                                                                @if ($event->pair->disqualification)
                                                                    {{ App\Models\SpeedResult::remapDq($event->pair->disqualification) }}
                                                                @else
                                                                    {{ App\Models\SpeedResult::prettyTime($event->pair->base_result) }}
                                                                @endif
                                                            @else
                                                                @if ($event->pair->disqualification)
                                                                    {{ App\Models\SpeedResult::remapDq($event->pair->disqualification) }}
                                                                @else
                                                                    {{ App\Models\SpeedResult::prettyTime($event->pair->base_result) }}
                                                                @endif
                                                                <br>
                                                                @if ($event->disqualification)
                                                                    {{ App\Models\SpeedResult::remapDq($event->disqualification) }}
                                                                @else
                                                                    {{ App\Models\SpeedResult::prettyTime($event->base_result) }}
                                                                @endif
                                                            @endif




                                                        </div>
                                                        <div class="ml-2">
                                                            {{ App\Models\SpeedResult::prettyTime($event->result) }}
                                                        </div>

                                                    </div>
                                                @else
                                                    {{ App\Models\SpeedResult::prettyTime($event->result) }}

                                                    @if ($event->result != $event->base_result)
                                                        <br>
                                                        <small>
                                                            Was
                                                            {{ App\Models\SpeedResult::prettyTime((int) $event->base_result) }}
                                                        </small>
                                                    @endif
                                                @endif
                                            @else
                                                <div>
                                                    {{ round($event->score, 1) }}
                                                </div>
                                            @endif


                                        </div>
                                    </td>
                                @endif
                                <td class="py-2 px-4 text-black text-xs text-center whitespace-nowrap">
                                    {{ ($event?->place ?? 16) * ($event?->weight ?? 1) }}
                                </td>
                            @endforeach

                            <td class="py-3 px-6 text-black text-sm whitespace-nowrap ">
                                {{ round($result->score) }}

                            </td>

                            <td class="py-3 px-6 text-black text-sm whitespace-nowrap  md:sticky top-0 right-0 ">
                                {{ $result->place }}

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

        <div class="mt-2 flex items-center justify-between">
            <div class="flex flex-col  ">
                <div class="form-input" style="margin-bottom: 0px !important;"><input placeholder="Search"
                        id="team-filter" style="margin-bottom: 0 !important" type="text"></div>

            </div>

        </div>









    </div>
    <br>
    <script>
        function initTable() {
            let table = document.getElementById("table");
            let tableBody = document.getElementById("table-body");
            let tableRows = tableBody.querySelectorAll("tr")

            table.onmouseover = (e) => {
                let type = e.target;
                if (type.nodeName != "TD") return
                //console.log(type.innerHTML)
                let index = Array.from(type.parentNode.children).indexOf(type)


                tableBody.querySelectorAll("tr").forEach(tr => {
                    tr.children[index].classList.add('bg-gray-200')
                })
            }


            table.onmouseout = (e) => {
                let type = e.target;
                if (type.nodeName != "TD") return
                //console.log(type.innerHTML)
                let index = Array.from(type.parentNode.children).indexOf(type)


                tableBody.querySelectorAll("tr").forEach(tr => {
                    tr.children[index].classList.remove('bg-gray-200')
                })
            }

            let filter = document.getElementById("team-filter")

            function search(team) {

                tableRows.forEach(row => {
                    let teamCol = row.children[0];
                    let teamName = teamCol.innerHTML.trim().toLowerCase();

                    team = team.toLowerCase();
                    let hide = false;



                    if (team.startsWith("team:")) {
                        hide = !teamName.endsWith(team.substr(5));
                    } else if (team.startsWith("league:")) {
                        let targetLeague = team.substr(7, 1);
                        if (targetLeague == "a") {
                            hide = !teamName.endsWith(targetLeague);
                        } else {
                            hide = teamName.endsWith("a")
                        }
                    } else {
                        hide = !teamName.includes(team);
                    }

                    row.hidden = hide;


                })

            }
            filter.onkeyup = (e) => {
                search(e.target.value)
            }


        }

        window.onload = initTable()
    </script>

</body>

</html>
