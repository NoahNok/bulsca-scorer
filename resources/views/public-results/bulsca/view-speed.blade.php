<!DOCTYPE html>
<html lang="en">

@php
    if ($comp->getBrand != null) {
        $brand = $comp->getBrand;
    }
@endphp

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">



    @if (isset($brand))
        <link rel="icon" type="image/png" href="{{ $brand->getLogo() }}" />
        <title>
            @if ($comp->areResultsProvisional())
                (PROVISIONAL)
            @endif{{ $event->getName() }} | {{ $comp->name }} | Results | {{ $brand->name }}
        </title>
    @else
        <title>
            @if ($comp->areResultsProvisional())
                (PROVISIONAL)
            @endif{{ $event->getName() }} | {{ $comp->name }} | Results | BULSCA
        </title>
        <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    @endif

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

</head>

<body class="overflow-x-hidden" x-data="{
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
    @isset($brand)
        <style>
            :root {
                --brand-primary: {{ $brand->primary_color }};
                --brand-secondary: {{ $brand->secondary_color }};
            }
        </style>
    @endisset
    <div class="flex flex-col items-center w-screen h-screen p-8 space-y-6 ">
        <div class="flex flex-row space-x-6 items-center">
            <img src="@if (isset($brand)) {{ $brand->getLogo() }}@else https://www.bulsca.co.uk/storage/logo/blogo.png @endif"
                class="w-32 h-32" alt="">
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
            <div>
                <p class="text-center text-sm">Hovering over/clicking a Penalty/DQ will tell you more about it.
                </p>
            </div>
            <div class="flex justify-between items-center mx-3 lg:mx-0">
                <h3>Results</h3>
                <a class="link"
                    href="{{ route('public.results.comp', $comp->resultsSlug()) }}"><small>Back</small></a>
            </div>
            <div class="  relative overflow-x-auto w-screen lg:w-auto  ">
                <table id="table"
                    class="table-highlight text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50  ">
                        <tr class="">
                            <th scope="col" class="py-3 px-6 text-left sticky left-0 bg-gray-50">
                                Team

                            </th>
                            <th scope="col" class="py-3 px-6">
                                @if ($event->getName() == 'Rope Throw')
                                    Ropes/Time
                                @else
                                    Time
                                @endif
                            </th>
                            <th scope="col" class="py-3 px-6">
                                DQ
                            </th>

                            @if ($event->hasPenalties())
                                <th scope="col" class="py-3 px-6">
                                    Penalties
                                </th>
                            @endif
                            <th scope="col" class="py-3 px-6">
                                Points
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Position
                            </th>

                        </tr>
                    </thead>
                    <tbody id="table-body">

                        @forelse ($event->getResults() as $result)
                            <tr class="bg-white border-b text-right place-{{ $result->place }}">
                                <th scope="row"
                                    class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap sticky left-0 bg-white ">
                                    {{ $result->team }}
                                </th>
                                <td class="py-4 px-6">
                                    @php
                                        $actualResult =
                                            $event->getName() == 'Rope Throw'
                                                ? $result->result_penalties
                                                : $result->result;
                                    @endphp

                                    {{ App\Models\SpeedResult::prettyTime($actualResult) }}

                                    @if ($actualResult != $result->base_result)
                                        <br>
                                        <small>
                                            Was {{ App\Models\SpeedResult::prettyTime((int) $result->base_result) }}
                                        </small>
                                    @endif


                                </td>
                                <td class="py-4 px-6 hover:underline cursor-pointer"
                                    title="{{ $result->disqualification ? App\Models\DQCode::message($result->disqualification) : '' }}"
                                    @click="showDqPen('{{ $result->disqualification }}', {{ $result->tid }})">
                                    {{ App\Models\SpeedResult::remapDq($result->disqualification) ?: '-' }}
                                </td>

                                @if ($event->hasPenalties())
                                    <td class="py-3 px-6 ">

                                        @php
                                            $blank = true;
                                        @endphp
                                        @if ($result->penalties != 0)
                                            @foreach (App\Models\Penalty::where('speed_result', $result->id)->get('code') as $penalty)
                                                <span class="hover:underline cursor-pointer"
                                                    title="{{ App\Models\PenaltyCode::message($penalty->code) }}"
                                                    @click="showDqPen('{{ $penalty->code }}', {{ $result->tid }})">{{ $penalty->code . ($loop->last ? '' : ',') }}</span>
                                                @php
                                                    $blank = false;
                                                @endphp
                                            @endforeach
                                        @endif
                                        @if ($event->getName() == 'Swim & Tow' && $result->{'900_penalties'} != 0)
                                            <span class="hover:underline"
                                                title="{{ App\Models\PenaltyCode::message('P900') }}">
                                                (P900
                                                x{{ $result->{'900_penalties'} }})
                                            </span>
                                            @php
                                                $blank = false;
                                            @endphp
                                        @endif
                                        @if ($blank)
                                            -
                                        @endif
                                    </td>
                                @endif
                                <td class="py-4 px-6">
                                    {{ round($result->points) }}
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
                    <div class="form-input" style="margin-bottom: 0px !important;"><input placeholder="Filter"
                            id="team-filter" style="margin-bottom: 0 !important" type="text"></div>
                    <small class="text-gray-600">team:x, league:x</small>
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


        <div class=" pt-8 pb-16 text-center ">
            <small>
                &copy;
                Noah Hollowell, BULSCA
                2022-{{ date('Y') }}
                @if (isset($brand))
                    <br>Other logos, styles and assets are the property of their respective owners
                    ({{ $brand->name }})
                @endif
            </small>

        </div>





    </div>

    <div class="modal" x-show="showModal" x-transition style="display: none">
        <div class="modal-content " @click.outside="showModal=false">
            <iframe :src="modalUrl" frameborder="0" scrolling="no" class="w-full"
                onload="this.height=this.contentWindow.document.body.scrollHeight;"></iframe>
        </div>
    </div>

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
