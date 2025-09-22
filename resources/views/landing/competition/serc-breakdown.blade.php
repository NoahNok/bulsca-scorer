@extends('layouts.landing-comp')

@section('title')
    Breakdown | {{ $event->getName() }}
@endsection

@section('content')

    <div class="overflow-x-hidden" x-data="{
        showModal: false,
        showNotesModal: false,
    
        modalUrl: '',
        modalBaseUrl: '{{ route('public.results.dq-pen', [$comp->id, 'T', 'C']) }}',
        notesModalBaseUrl: '{{ route('public.results.serc.team-notes', [$comp->id, $event->id, 'T']) }}',
    
        notesData: null,
    
        showDqPen(code, teamId) {
            this.showModal = true;
    
            this.modalUrl = this.modalBaseUrl.replace('T', teamId).replace('C', code);
    
        },
        hideDqPen() {
            this.showModal = false;
    
        },
        loadTeamNotes(teamId) {
            this.showNotesModal = true;
            this.notesData = null;
    
            fetch(this.notesModalBaseUrl.replace('T', teamId))
                .then(response => response.json())
                .then(data => {
                    this.notesData = data;
                })
    
        },
    }">

        <div class=" ">


            <div class="">
                @if ($comp->areResultsProvisional())
                    <div class="p-2 text-center text-lg">
                        <p>These results are provisional! <strong>They are subject to change</strong> and should not be
                            considered accurate or final!</p>
                    </div>
                @endif



                @if (count($overallJudgeNotes) > 0)
                    <div x-data="{
                        open: true,
                    }">
                        <div class="flex justify-between items-center mx-3 lg:mx-0 cursor-pointer" @click="open = !open">

                            <h3>Overall feedback</h3>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6 transition-transform"
                                :class="{ 'transform rotate-180': open }">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>


                        </div>

                        <div class="mx-3 grid sm:grid-cols-2 xl:grid-cols-3 3xl:grid-cols-4  gap-2 " x-cloak x-show="open">
                            @foreach ($overallJudgeNotes as $note)
                                <div class="flex flex-col ">
                                    <strong>{{ $note->judge }}</strong>
                                    <p class="ml-6">{{ $note->note }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <br>
                    <hr>
                    <br>
                @endif







                <div id="table-wrapper" class="se-table   ">
                    <p class="link hidden text-center" id="cfs">Close Fullscreen</p>
                    <table id="table"
                        class="table-highlight text-sm w-full shadow-md rounded-lg top-0 text-left  border-collapse  relative "
                        sortable>

                        <thead class="text-xs  text-right uppercase sticky top-0 z-50 ">
                            <tr class="">
                                <th class=""></th>
                                <th class=""></th>
                                @foreach ($fsd['judges'] as $judge => $mps)
                                    <th colspan="{{ count($mps) + 1 }}"
                                        class="py-3 px-6 border-x text-center sticky top-0 ">
                                        {{ $judge }}</th>
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
                                <tr class="bg-white border-b text-right  place-{{ $team['place'] }} group ">
                                    <th scope="row" class=" group-hover:bg-gray-200! transition-colors  ">
                                        {{ $team['team'] }}
                                    </th>
                                    <td class="py-4 px-6 group-hover:bg-gray-200! ">
                                        <a href="#notes" class="link"
                                            @click="loadTeamNotes({{ $team['tid'] }})">Notes</a>
                                    </td>

                                    @foreach ($fsd['judges'] as $judge => $mpIds)
                                        @php
                                            $localTotal = 0;
                                        @endphp
                                        @foreach ($mpIds as $mpId => $markingPoint)
                                            @php
                                                $localTotal += ($team['results'][$mpId] ?? 0) * $markingPoint['weight'];
                                            @endphp
                                            <td class="py-3 px-6 text-center group-hover:bg-gray-200! hover:bg-gray-300!">
                                                {{ round($team['results'][$mpId] ?? 0) }}
                                            </td>
                                        @endforeach
                                        <td class="py-3 px-6 text-center border-x font-semibold group-hover:bg-gray-200!"
                                            data-total-row>
                                            {{ $localTotal }}</td>
                                    @endforeach


                                    <td class="py-4 px-6 group-hover:bg-gray-200!"
                                        @click="showDqPen('{{ $team['disqualification'] }}', {{ $team['tid'] }})"
                                        title="{{ $team['disqualification'] ?: '-' }}">
                                        {{ $team['disqualification'] ?: '-' }}

                                    </td>
                                    <td class="py-4 px-6 font-semibold group-hover:bg-gray-200!">
                                        {{ round($team['raw']) }}
                                    </td>
                                    <td class="py-4 px-6 font-bold group-hover:bg-gray-200!">
                                        {{ round($team['points']) }}
                                    </td>
                                    <td class="py-4 px-6 group-hover:bg-gray-200! ">
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
                        <div class="form-input" style="margin-bottom: 0px !important;"><input placeholder="Filter"
                                id="team-filter" style="margin-bottom: 0 !important" type="text"></div>
                        <small class="text-gray-600">team:x, league:x</small>
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



            <div class=" pb-16 text-center">
                <small>
                    &copy;
                    Noah Hollowell, Scoring.Events, BULSCA
                    2022-{{ date('Y') }}

                </small>
            </div>





        </div>

        <div class="modal" x-show="showModal" x-transition style="display: none">
            <div class="modal-content " @click.outside="showModal=false">
                <iframe :src="modalUrl" frameborder="0" scrolling="no" class="w-full"
                    onload="this.height=this.contentWindow.document.body.scrollHeight;"></iframe>
            </div>
        </div>

        <div class="modal" x-show="showNotesModal" x-transition style="display: none">
            <div class="modal-content " @click.outside="showNotesModal=false">
                <div class="flex flex-col max-h-[70vh] overflow-y-auto ">
                    <h4 class="font-bold">Notes for <span x-text="notesData?.name"></span></h4>
                    <div class="flex flex-col space-y-3">

                        <template x-for="note in notesData.notes">
                            <div class="flex flex-col ">
                                <strong x-text="note.judge"></strong>
                                <p class="ml-6 " x-text="note.note"></p>
                            </div>
                        </template>

                        <div x-show="!notesData.notes.length">
                            <p>No notes were given for this team</p>
                        </div>

                        <template x-if="!notesData">
                            <p>Loading...</p>
                        </template>
                    </div>
                </div>
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


            initTable()
            Sorttable()
        </script>

    </div>

@endsection
