<!DOCTYPE html>
<html lang="en">
@php
    if ($comp->getBrand != null) {
        $brand = $comp->getBrand;
    }
@endphp

<head>
    <meta charset="UTF-8">


    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <title>{{ $event->getName() }} | {{ $comp->name }} | Live | RLSS</title>


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


    <div class="w-full h-48 bg-rlss-blue flex flex-col justify-center px-12 overflow-x-hidden "
        style="background-image: url('/rlss-transparent.svg'); background-position-y: center; background-position-x: -100px; background-repeat: no-repeat;">

        <div class="container mx-auto">
            <h1 class="text-white font-astoria hmb-0">{{ $comp->name }}</h1>
            <p class=" font-ariel text-rlss-yellow font-semibold">John Charles Centre for Sport</p>
        </div>


    </div>

    <div class="container mx-auto py-6 overflow-x-hidden">
        <a class="link" href="{{ route('public.results.comp', [$comp->resultsSlug()]) }}"><small>Back</small></a>

        <h2 class="font-astoria text-rlss-blue font-extrabold">{{ $event->getName() }} Results</h2>
        <br>
        <div class="  relative overflow-x-auto w-full lg:w-auto  ">
            <table id="table" class="table-highlight text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50  ">
                    <tr class="">
                        <th scope="col" class="py-3 px-6 text-left sticky left-0 bg-gray-50">
                            Competitor - Club (Region) - League

                        </th>
                        <th scope="col" class="py-3 px-6">
                            Time
                        </th>
                        <th scope="col" class="py-3 px-6">
                            DQ
                        </th>



                        <th scope="col" class="py-3 px-6">
                            Position
                        </th>

                    </tr>
                </thead>
                <tbody id="table-body">

                    @forelse ($event->getResults() as $result)
                        @php
                            $pair = property_exists($result, 'pair');
                        @endphp
                        <tr class="bg-white border-b text-right place-{{ $result->place }} ">
                            <th scope="row"
                                class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap sticky left-0 bg-white ">
                                {{ $result->team }} - {{ $result->league }}

                                @if ($pair)
                                    <br>
                                    {{ $result->pair->name }} - {{ $result->league }}
                                @endif
                            </th>
                            <td class="py-4 px-6">
                                @if ($pair)
                                    <div class="flex items-center justify-end">
                                        <div class="border-r pr-2">
                                            {{ App\Models\SpeedResult::prettyTime($result->base_result) }}
                                            <br>
                                            {{ App\Models\SpeedResult::prettyTime($result->pair->base_result) }}

                                        </div>
                                        <div class="ml-2">
                                            {{ App\Models\SpeedResult::prettyTime($result->result) }}
                                        </div>

                                    </div>
                                @else
                                    {{ App\Models\SpeedResult::prettyTime($result->result) }}

                                    @if ($result->result != $result->base_result)
                                        <br>
                                        <small>
                                            Was {{ App\Models\SpeedResult::prettyTime((int) $result->base_result) }}
                                        </small>
                                    @endif
                                @endif



                            </td>
                            <td class="py-4 px-6 hover:underline cursor-pointer">
                                {{ $result->disqualification ?: '-' }}
                                @if ($pair)
                                    <br>
                                    {{ $result->pair->disqualification ?: '-' }}
                                @endif
                            </td>



                            <td class="py-4 px-6">
                                {{ $result->place }}
                            </td>

                        </tr>
                    @empty
                        <tr class="bg-white border-b text-right ">
                            <th colspan="100" scope="row"
                                class="py-4 px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                                None
                            </th>
                        </tr>
                    @endforelse



                </tbody>
            </table>

        </div>
        <div class="mt-2 flex flex-col md:flex-row space-y-4 md:space-y-0 items-center justify-between">
            <div class="flex flex-col ">
                <div class="form-input" style="margin-bottom: 0px !important;"><input placeholder="Search"
                        id="team-filter" style="margin-bottom: 0 !important" type="text"></div>

            </div>
            @if (!$comp->areResultsProvisional())
                <div class="md:ml-auto flex flex-col items-center md:items-end">
                    <div>
                        <a href="{{ route('whatif') }}" class="link ">Edit in WhatIf</a>
                        or
                        <a href="{{ Request::url() }}?dlCSV" class="link ">Download as CSV</a>
                    </div>
                    <small>WhatIf lets you explore how changes would effect overall results!</small>
                </div>
            @endif
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
