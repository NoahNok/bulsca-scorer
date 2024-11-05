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
    <title>{{ $event->getName() }} | {{ $comp->name }} | Results | RLSS</title>


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script src="{{ asset('js/sorttable.js') }}?{{ config('version.hash') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        :root {
            --brand-primary: {{ $brand->primary_color }};
            --brand-secondary: {{ $brand->secondary_color }};
        }
    </style>
</head>

<body class="overflow-x-hidden  w-screen h-screen" x-data="{
    showModal: false,

    modalUrl: '',
    modalBaseUrl: '{{ route('public.results.dq-pen', [$comp->id, 'T', 'C']) }}',

    showDqPen(code, teamId) {
        this.showModal = true;

        this.modalUrl = this.modalBaseUrl.replace('T', teamId).replace('C', code);

    },
    hideDqPen() {
        this.showModal = false;

    }
}">


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
        <h2 class="font-astoria text-rlss-blue font-extrabold">{{ $event->getName() }} Results</h2>
        <p>You may need to scroll/drag on the table to see more results.</p>
        <br>
        <div id="table-wrapper"
            class="  relative overflow-x-auto w-full  lg:max-w-[80vw] h-[90vh] lg:h-[80vh] resize-y bg-white ">
            <p class="link hidden text-center" id="cfs">Close Fullscreen</p>
            <table id="table"
                class="table-highlight text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse  relative "
                sortable>

                <thead class="text-xs text-gray-700 text-right uppercase sticky top-0 z-50 ">
                    <tr class="">
                        <th class=""></th>
                        <th class=""></th>
                        @foreach ($fsd['judges'] as $judge => $mps)
                            <th colspan="{{ count($mps) + 1 }}" class="py-3 px-6 border-x text-center sticky top-0 ">
                                {{ $judge }}</th>
                        @endforeach
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr data-sortable-row class="">
                        <th scope="col" class="py-3 px-6 text-left z-20  max-w-[40vw] break-words" data-sortable>
                            Competitor - Club (Region) - League
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Notes
                        </th>

                        @foreach ($fsd['judges'] as $judge => $mpIds)
                            @foreach ($mpIds as $markingPoint)
                                <th scope="col"
                                    class="py-3 px-6  @if ($loop->first) border-l @endif group  lg:whitespace-nowrap hover:whitespace-normal"
                                    style="writing-mode: vertical-rl; " title="{{ $markingPoint['name'] }}"
                                    data-sortable>

                                    <span
                                        class="block max-h-52 overflow-hidden text-ellipsis">{{ $markingPoint['name'] }}</span>
                                    <!--<p class="text-center">{{ number_format($markingPoint['weight'], 1) }}</p>-->

                                </th>
                            @endforeach
                            <th scope="col"
                                class="py-3 px-6 border-r    group max-h-52 lg:whitespace-nowrap overflow-hidden text-ellipsis hover:whitespace-normal"
                                style="writing-mode: vertical-rl; " data-sortable>
                                TOTAL


                            </th>
                        @endforeach

                        <th scope="col" class="py-3 px-6">
                            DQ
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Mark
                        </th>

                        <th scope="col" class="py-3 px-6" data-sortable>
                            Place
                        </th>


                    </tr>
                    <tr class="">
                        <th class=""></th>
                        <th class=""></th>
                        @foreach ($fsd['judges'] as $judge => $mpIds)
                            @foreach ($mpIds as $markingPoint)
                                <th
                                    class="py-3 px-6 text-center sticky top-0   @if ($loop->first) border-l @endif">
                                    {{ $markingPoint['weight'] }}</th>
                            @endforeach
                            <th class="py-3 px-6 text-center sticky top-0   border-r ">
                                -</th>
                        @endforeach
                        <th></th>
                        <th>Max: {{ round($event->getMaxMark(), 1) }}</th>
                        <th></th>
                        <th></th>
                    </tr>

                </thead>
                <tbody id="table-body">

                    @forelse ($fsd['results'] as $tid => $team)
                        <tr class="bg-white border-b text-right  place-{{ $team['place'] }} ">
                            <th scope="row"
                                class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap max-w-[40vw] overflow-x-auto ">
                                {{ $team['team'] }}
                                @php
                                    $pair = \App\Models\CompetitionTeam::find($team['tid'])
                                        ->getClub->getTeams->where('id', '!=', $team['tid'])
                                        ->first();
                                @endphp
                                @if ($pair)
                                    & {{ $pair->team }}
                                @endif

                            </th>
                            <td class="py-4 px-6 ">
                                <a href="{{ route('public.results.serc.team-notes', [$comp->resultsSlug(), $event, $team['tid']]) }}"
                                    class="link">Notes</a>
                            </td>

                            @foreach ($fsd['judges'] as $judge => $mpIds)
                                @php
                                    $localTotal = 0;
                                @endphp
                                @foreach ($mpIds as $mpId => $markingPoint)
                                    @php

                                        $localTotal += ($team['results'][$mpId] ?? 0) * $markingPoint['weight'];
                                    @endphp
                                    <td class="py-3 px-6 text-center">
                                        {{ round($team['results'][$mpId] ?? 0) }}
                                    </td>
                                @endforeach
                                <td class="py-3 px-6 text-center border-x font-semibold" data-total-row>
                                    {{ $localTotal }}</td>
                            @endforeach


                            <td class="py-4 px-6"
                                @click="showDqPen('{{ $team['disqualification'] }}', {{ $team['tid'] }})"
                                title="{{ $team['disqualification'] ?: '-' }}">
                                {{ $team['disqualification'] ?: '-' }}

                            </td>
                            <td class="py-4 px-6 font-semibold ">
                                {{ round($team['raw']) }}
                            </td>

                            <td class="py-4 px-6 ">
                                {{ $team['place'] }}
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








        <div class="mt-2 flex flex-col md:flex-row space-y-4 md:space-y-0 items-center ">
            <div class="flex flex-col ">
                <div class="form-input" style="margin-bottom: 0px !important;"><input placeholder="Search"
                        id="team-filter" style="margin-bottom: 0 !important" type="text"></div>

            </div>

            <div class="flex items-center justify-center space-x-2 ml-8">
                <label for="anal">Analysis</label>
                <input type="checkbox" name="" id="anal">
            </div>

            <div class="md:ml-auto">
                <p class="link" id="fs">Fullscreen</p>
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
    <div class="modal" x-show="showModal" x-transition style="display: none">
        <div class="modal-content " @click.outside="showModal=false">
            <iframe :src="modalUrl" frameborder="0" scrolling="no" class="w-full"
                onload="this.height=this.contentWindow.document.body.scrollHeight;"></iframe>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/analysis.js') }}"></script>
    <script>
        function initTable() {
            let table = document.getElementById("table");
            let tableBody = document.getElementById("table-body");
            let tableRows = tableBody.querySelectorAll("tr")

            let tableWrapper = document.getElementById("table-wrapper")
            let fs = document.getElementById("fs")
            let cfs = document.getElementById("cfs")

            fs.onclick = () => {
                tableWrapper.requestFullscreen()
                tableWrapper.classList.add("h-screen")
                table.classList.add("h-screen")
                fs.classList.add("hidden")
                cfs.classList.remove("hidden")
            }

            cfs.onclick = () => {
                document.exitFullscreen()
                tableWrapper.classList.remove("h-screen")
                table.classList.remove("h-screen")
                fs.classList.remove("hidden")
                cfs.classList.add("hidden")
            }


            analyze(tableRows)
            document.getElementById("anal").onchange = v => {
                tableBody.classList.toggle("analysis")
            }


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

            // Team search
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

        window.onload = function() {
            initTable()
            Sorttable()
        }
    </script>



</body>

</html>
