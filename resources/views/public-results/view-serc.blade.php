<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if ($comp->areResultsProvisional())
            (PROVISIONAL)
        @endif{{ $event->getName() }} | {{ $comp->name }} | Results | BULSCA
    </title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script src="{{ asset('js/sorttable.js') }}?{{ config('version.hash') }}"></script>

</head>

<body class="overflow-x-hidden">
    <div class="flex flex-col items-center w-screen h-screen p-8 space-y-6 ">
        <div class="flex flex-row space-x-6 items-center">
            <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class="w-32 h-32" alt="">
            <div class="flex flex-col">
                <h2 class="font-bold mb-0">{{ $event->getName() }}</h2>
                <h4>{{ $comp->name }}</h4>
            </div>
        </div>
        <a href="https://forms.gle/FEc8XJM3SyUma3Br6" target="_blank" rel="noopener noreferrer" class="link">Give
            Feedback</a>
        <div class="">
            @if ($comp->areResultsProvisional())
                <div class="p-2 text-center text-lg">
                    <p>These results are provisional! <strong>They are subject to change</strong> and should not be
                        considered accurate or final!</p>
                </div>
            @endif
            <div class="flex justify-between items-center mx-3 lg:mx-0">

                <h3>Results</h3>

                <a class="link"
                    href="{{ route('public.results.comp', $comp->resultsSlug()) }}"><small>Back</small></a>
            </div>
            <div class="  relative overflow-x-auto w-screen  lg:max-w-[80vw] h-[90vh] lg:h-[80vh] resize-y ">
                <table id="table"
                    class="table-highlight text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse  relative "
                    sortable>

                    <thead class="text-xs text-gray-700 text-right uppercase sticky top-0 z-50 ">
                        <tr class="">
                            <th class=""></th>
                            <th class=""></th>
                            @foreach ($event->getJudges as $judge)
                                <th colspan="{{ $judge->getMarkingPoints->count() + 1 }}"
                                    class="py-3 px-6 border-x text-center sticky top-0 ">
                                    {{ $judge->name }}</th>
                            @endforeach
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr data-sortable-row class="">
                            <th scope="col" class="py-3 px-6 text-left z-20" data-sortable>
                                Team
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Notes
                            </th>

                            @foreach ($event->getJudges as $judge)
                                @foreach ($judge->getMarkingPoints as $markingPoint)
                                    <th scope="col"
                                        class="py-3 px-6  @if ($loop->first) border-l @endif group max-h-52 lg:whitespace-nowrap overflow-hidden text-ellipsis hover:whitespace-normal"
                                        style="writing-mode: vertical-rl; " title="{{ $markingPoint->name }}"
                                        data-sortable>

                                        {{ $markingPoint->name }}
                                        <!--<p class="text-center">{{ number_format($markingPoint->weight, 1) }}</p>-->

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
                                Raw Mark
                            </th>
                            <th scope="col" class="py-3 px-6" data-sortable>
                                Points
                            </th>
                            <th scope="col" class="py-3 px-6" data-sortable>
                                Position
                            </th>


                        </tr>
                        <tr class="">
                            <th class=""></th>
                            <th class=""></th>
                            @foreach ($event->getJudges as $judge)
                                @foreach ($judge->getMarkingPoints as $mp)
                                    <th
                                        class="py-3 px-6 text-center sticky top-0   @if ($loop->first) border-l @endif">
                                        {{ $mp->weight }}</th>
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

                        @forelse ($event->getResults() as $result)
                            <tr class="bg-white border-b text-right  place-{{ $result->place }} ">
                                <th scope="row"
                                    class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap  ">
                                    {{ $result->team }}
                                </th>
                                <td class="py-4 px-6 ">
                                    <a href="{{ route('public.results.serc.team-notes', [$comp->resultsSlug(), $event, $result->tid]) }}"
                                        class="link">Notes</a>
                                </td>

                                @foreach ($event->getJudges as $judge)
                                    @php
                                        $localTotal = 0;
                                    @endphp
                                    @foreach ($judge->getMarkingPoints as $markingPoint)
                                        @php
                                            $localTotal += $markingPoint->getScoreForTeam($result->tid);
                                        @endphp
                                        <td class="py-3 px-6 text-center">
                                            {{ round($markingPoint->getScoreForTeam($result->tid)) }}
                                        </td>
                                    @endforeach
                                    <td class="py-3 px-6 text-center border-x font-semibold" data-total-row>
                                        {{ $localTotal }}</td>
                                @endforeach

                                <td class="py-4 px-6"
                                    title="{{ $event->getTeamDQ(\App\Models\CompetitionTeam::find($result->tid))?->code ? App\Models\DQCode::message($event->getTeamDQ(\App\Models\CompetitionTeam::find($result->tid))?->code) : '' }}">
                                    {{ $event->getTeamDQ(\App\Models\CompetitionTeam::find($result->tid))?->code ?: '-' }}

                                </td>
                                <td class="py-4 px-6 font-semibold ">
                                    {{ round($result->score) }}
                                </td>
                                <td class="py-4 px-6 font-bold ">
                                    {{ round($result->points) }}
                                </td>
                                <td class="py-4 px-6 ">
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
            <div class="mt-2 flex flex-col md:flex-row space-y-4 md:space-y-0 items-center ">
                <div class="flex flex-col ">
                    <div class="form-input" style="margin-bottom: 0px !important;"><input placeholder="Filter"
                            id="team-filter" style="margin-bottom: 0 !important" type="text"></div>
                    <small class="text-gray-600">team:x, league:x</small>
                </div>

                <div class="flex items-center justify-center space-x-2 ml-8">
                    <label for="anal">Analysis</label>
                    <input type="checkbox" name="" id="anal">
                </div>

                @if (!$comp->areResultsProvisional())
                    <div class="md:ml-auto flex flex-col items-center md:items-end">
                        <a href="{{ Request::url() }}?dlCSV" class="link ">Download as CSV</a>
                        <small>You can edit the CSV to see how changes effect SERC scores!</small>
                    </div>
                @endif
            </div>




        </div>

        <div class="w-screen  lg:max-w-[80vw]">
            <details>
                <summary class="text-bulsca font-semibold text-3xl" onclick="generateCharts()">Charts</summary>
                <div>
                    <canvas id="mark-dist"></canvas>
                </div>
                <br>
                <h4>Judges </h4>
                <p>The following charts show the raw and rolling average for each of the judges marking points, in the
                    order of the SERC draw</p>
                <br>
                @foreach ($event->getJudges as $judge)
                    <h5>{{ $judge->name }}</h5>
                    <div class="grid-4">
                        @foreach ($judge->getMarkingPoints as $mp)
                            <div>
                                <canvas id="rolling-mp-{{ $mp->id }}"></canvas>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </details>
        </div>

        <div class=" pb-16">
            <small>
                &copy; BULSCA 2023
            </small>
        </div>





    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/analysis.js') }}"></script>
    <script>
        function initTable() {
            let table = document.getElementById("table");
            let tableBody = document.getElementById("table-body");
            let tableRows = tableBody.querySelectorAll("tr")


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

        function charts() {
            let ctx = document.getElementById('mark-dist');
            let chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: {{ Js::from($event->getMarkDistribution()['labels']) }},
                    datasets: [{
                        label: 'Mark Distribution',
                        data: {{ Js::from($event->getMarkDistribution()['values']) }},

                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Mark Distribution'
                        }
                    }
                }
            });


            new Chart(document.getElementById('rolling-judge'), {
                type: 'line',
                data: {
                    labels: {{ Js::from($event->getRollingAverageForMP(1)['labels']) }},
                    datasets: [{
                            label: 'Raw Mark',
                            data: {{ Js::from($event->getRollingAverageForMP(1)['raw']) }},

                        },
                        {
                            label: 'Rolling Avg',
                            data: {{ Js::from($event->getRollingAverageForMP(1)['rolling']) }},
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Mark Distribution'
                        }
                    }
                }
            });



            @foreach ($event->getJudges as $judge)


                @foreach ($judge->getMarkingPoints as $mp)
                    @php
                        $cached = $event->getRollingAverageForMP($mp->id);
                    @endphp
                    new Chart(document.getElementById("rolling-mp-{{ $mp->id }}"), {
                        type: 'line',
                        data: {
                            labels: {{ Js::from($cached['labels']) }},
                            datasets: [{
                                    label: 'Raw Mark',
                                    data: {{ Js::from($cached['raw']) }},

                                },
                                {
                                    label: 'Rolling Avg',
                                    data: {{ Js::from($cached['rolling']) }},
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                },
                                title: {
                                    display: true,
                                    text: '{{ $mp->name }}'
                                }
                            }
                        }
                    });
                @endforeach
            @endforeach
        }

        let generatedCharts = false;

        function generateCharts() {
            if (generatedCharts) return;
            charts();

        }
    </script>

</body>

</html>
