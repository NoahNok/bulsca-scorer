<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $comp->name }} - Editor | WhatIf | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>

<body>
    <div class="w-screen h-screen p-3 flex flex-col overflow-x-hidden" x-data="start()">
        <div class="w-full flex space-x-3 py-2 px-4  items-center">
            <h1 class=" text-[2rem] text-transparent bg-clip-text bg-gradient-to-r from-bulsca via-purple-500 to-bulsca_red"
                style="margin-bottom: 0 !important">
                WhatIf</h1>

            <div class="flex space-x-2 text-xs items-center justify-center self-end">
                <a href="#" class="hover:underline" @click="optionsOpen=true">Options</a>
                <span style="width: 3px; height: 3px;" class=" bg-black rounded-full"></span>
                <a href="{{ route('whatif.logout') }}" class="hover:underline">Leave</a>
            </div>
            <div class="flex-grow"></div>
            <h1 class="text-[1.5rem]" style="margin-bottom: 0 !important">{{ $comp->name }}</h1>
        </div>
        <div
            class="w-full flex flex-col md:flex-row space-x-3 px-2  border rounded-md md:rounded-full bg-gray-100 items-center">

            <div class="py-1 px-3 hidden md:block">Events</div>

            <div class="pill-select">
                @foreach ($comp->getSERCs as $serc)
                    <div class=" pill-select-option" @click="switchPill('event','se:{{ $serc->id }}')"
                        :class="pillActive('event', 'se:{{ $serc->id }}')">
                        {{ $serc->getName() }}</div>
                @endforeach
                @foreach ($comp->getSpeedEvents as $speed)
                    <div class=" pill-select-option" @click="switchPill('event','sp:{{ $speed->id }}')"
                        :class="pillActive('event', 'sp:{{ $speed->id }}')">
                        {{ $speed->getName() }}</div>
                @endforeach

            </div>


            <div class="py-1 px-3 hidden md:block" style="margin-left: auto">Results</div>

            <div class="pill-select">

                @foreach ($comp->getResultSchemas as $schema)
                    <div class=" pill-select-option"
                        @click="() => {switchPill('schema','{{ $schema->id }}'); updateResultsFrame()}"
                        :class="pillActive('schema', '{{ $schema->id }}')">
                        {{ $schema->name }}</div>
                @endforeach


            </div>

        </div>



        <div class="w-full h-full p-5 flex flex-col md:flex-row  ">
            <p style="display: none" class="w-[70%]" x-show="pills['event'] == null">Please select an event.</p>

            <div style="display: none" class="w-full md:w-[70%] z-10 mb-1" x-show="pills['event'] != null">

                @foreach ($comp->getSERCs as $serc)
                    <div class="" style="display: none" x-show="pills['event'] == 'se:{{ $serc->id }}'">

                        <div class="flex flex-col md:flex-row space-x-5" x-data="{
                            sdata: {{ json_encode($serc->getDataAsJson()) }},
                            rdata: null,
                            void: 0,
                        
                            loadResults() {
                                this.rdata = null
                                fetch('{{ route('whatif.editor.sercs', $serc->id) }}').then(res => res.json()).then(data => {
                        
                                    this.rdata = data
                                })
                            },
                        
                        
                            onlyMps() {
                                let mps = [];
                                this.sdata.judges.forEach(j => {
                                    mps.push(...j.marking_points)
                                })
                                return mps;
                        
                        
                            },
                        
                            onChange(newValue, srId, mp, team) {
                        
                                if (newValue > 10) {
                                    newValue = 10
                                    this.sdata.data[mp][team].result = 10
                                } else if (newValue < 0) {
                                    newValue = 0
                                    this.sdata.data[mp][team].result = 0
                                }
                        
                        
                        
                                let fd = new FormData();
                                fd.append('_token', '{{ csrf_token() }}')
                                fd.append('id', srId)
                                fd.append('result', newValue)
                        
                                fetch('{{ route('whatif.userc') }}', {
                                    method: 'POST',
                                    body: fd
                                }).then(res => res.json()).then(data => {
                        
                                    if (data.success) {
                                        this.refreshResults()
                                        this.loadResults()
                                    }
                                })
                            },
                        
                        
                        
                            init() {
                                this.loadResults()
                            }
                        }">

                            <div class="flex-grow  max-w-[80%] overflow-auto">
                                <h2>{{ $serc->getName() }}</h2>

                                <div class="  relative w-full overflow-x-auto max-h-[85vh] ">
                                    <table class="table text-sm">
                                        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100">
                                            <tr>
                                                <th
                                                    class="py-3 px-6 text-left sticky left-0 top-0 z-50 bg-gray-100 border-r ">
                                                    Team
                                                </th>
                                                <template x-for="judge in sdata.judges" :key="judge.id">
                                                    <th class="p-2 text-center border-r  last-of-type:border-r-0 sticky top-0 bg-gray-100 z-40"
                                                        :colspan="judge.marking_points.length" x-text="judge.name">
                                                    </th>
                                                </template>
                                            </tr>
                                            <tr>
                                                <th
                                                    class="border-r text-left  px-6 sticky left-0 top-10 bg-gray-100 z-50 ">

                                                </th>
                                                <template x-for="mp in (onlyMps())" :key="mp.id">
                                                    <th class="px-2 border-r border-t last-of-type:border-r-0 text-center sticky top-10  z-40 bg-gray-100"
                                                        x-text=" mp.name"></th>
                                                </template>


                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="team in sdata.teams" :key="team.id">
                                                <tr class="hover:bg-gray-100">
                                                    <td class="border-r border-b py-3 text-left px-4 font-medium text-gray-900 whitespace-nowrap sticky left-0 top-0 bg-white z-40"
                                                        x-text="team.name"></td>
                                                    <template x-for="mp in (onlyMps())" :key="mp.id">
                                                        <td class=" border-r border-b last-of-type:border-r-0 hover:bg-gray-300  p-0 cursor-text"
                                                            x-data="" x-on:click="$refs.i.select()">
                                                            <input
                                                                class=" w-full text-center bg-inherit  h-full inline-block relative outline-none p-0 m-0 hide-number-arrows"
                                                                type="number" min="0" max="10"
                                                                step="1" x-ref="i"
                                                                x-on:click="$event.target.select()"
                                                                x-on:change.debounce="onChange($event.target.value, sdata.data[mp.id][team.id].id, mp.id, team.id )"
                                                                x-model="sdata.data[mp.id][team.id].result"
                                                                x-mask:dynamic="$input.length==1 ? '9' : '99' " />
                                                        </td>

                                                    </template>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div>
                                <h2>&nbsp;</h2>

                                <div x-show="rdata == null"><x-loader></x-loader></div>

                                <div class="  relative w-full overflow-x-auto max-h-[85vh]  " x-show="rdata != null">
                                    <table class=" text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                                        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100 ">
                                            <tr>
                                                <th scope="col" class="py-3 px-6 text-left sticky top-0 bg-gray-100">
                                                    Team
                                                </th>
                                                <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-100">
                                                    Points
                                                </th>
                                                <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-100">
                                                    Place
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <template x-for="team  in rdata" x-key="team.id">

                                                <tr class="bg-white border-b text-right hover:bg-gray-100">
                                                    <th scope="row"
                                                        class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap border-r "
                                                        x-text="team.team">
                                                    </th>

                                                    <td class="py-3 px-4   border-r " x-text="Math.round(team.points)">
                                                    </td>
                                                    <td class="py-3 px-4 text-center   border-r " x-text="team.place">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                        </div>

                    </div>
                @endforeach


                @foreach ($comp->getSpeedEvents as $speed)
                    <div class="flex-grow  " style="display: none"
                        x-show="pills['event'] == 'sp:{{ $speed->id }}'">

                        <div class="flex flex-row space-x-5" x-data="{
                            sdata: {{ json_encode($speed->getDataAsJson()) }},
                            rdata: null,
                        
                            loadResults() {
                                this.rdata = null
                                fetch('{{ route('whatif.editor.speeds', $speed->id) }}').then(res => res.json()).then(data => {
                        
                                    this.rdata = data
                                })
                            },
                        
                        
                            onDqChange(newValue, srId, index) {
                                if (newValue.length < 3) {
                                    this.sdata[index].disqualification = ''
                                    newValue = ''
                                }
                        
                        
                                return this.onChange(newValue, srId, 'dq')
                        
                            },
                        
                            onChange(newValue, srId, type = 'result') {
                        
                        
                        
                                let fd = new FormData();
                                fd.append('_token', '{{ csrf_token() }}')
                                fd.append('id', srId)
                                fd.append('value', newValue)
                                fd.append('type', type)
                        
                                fetch('{{ route('whatif.uspeed') }}', {
                                    method: 'POST',
                                    body: fd
                                }).then(res => res.json()).then(data => {
                        
                                    if (data.success) {
                                        this.refreshResults()
                                        this.loadResults()
                                    }
                                })
                            },
                        
                            init() {
                                this.loadResults()
                            }
                        }">


                            <div class=" flex-grow">
                                <h2>{{ $speed->getName() }}</h2>


                                <div class="  relative w-full overflow-x-auto max-h-[85vh] ">
                                    <table class=" text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                                        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100 ">
                                            <tr>
                                                <th scope="col"
                                                    class="py-3 px-6 text-left sticky top-0 z-10 bg-gray-100">
                                                    Team
                                                </th>

                                                <th scope="col" class="py-3 px-6 sticky top-0 z-10 bg-gray-100">
                                                    @if ($speed->getName() == 'Rope Throw')
                                                        Ropes/Time
                                                    @else
                                                        Time
                                                    @endif
                                                </th>

                                                <th scope="col" class="py-3 px-6 sticky top-0 z-10 bg-gray-100">
                                                    DQ
                                                </th>

                                                @if ($speed->hasPenalties())
                                                    <th scope="col"
                                                        class="py-3 px-6 sticky top-0 z-10 bg-gray-100">
                                                        Penalties
                                                    </th>
                                                @endif


                                            </tr>
                                        </thead>
                                        <tbody>

                                            <template x-for="(team, index) in sdata" x-key="team.id">

                                                <tr class="bg-white border-b text-right hover:bg-gray-100">
                                                    <th scope="row"
                                                        class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap border-r "
                                                        x-text="team.name">
                                                    </th>

                                                    <td class="py-4  hover:bg-gray-300 border-r ">
                                                        <input type="text"
                                                            class=" px-4 text-right w-full bg-inherit outline-none"
                                                            x-mask="99:99.999" x-model="team.result"
                                                            x-on:change.debounce="onChange($event.target.value, team.id)"
                                                            class="w-auto">



                                                    </td>


                                                    <td
                                                        class="py-4  hover:bg-gray-300 border-r last-of-type:border-r-0 ">
                                                        <input type="text"
                                                            class="text-right px-4 w-full bg-inherit outline-none"
                                                            x-mask="DQ999" x-model="team.disqualification"
                                                            x-on:change.debounce="  onDqChange($event.target.value, team.id, index)"
                                                            class="w-auto">
                                                    </td>

                                                    @if ($speed->hasPenalties())
                                                        <td class="py-4  hover:bg-gray-300">
                                                            <input type="text"
                                                                class=" text-right px-4 w-full bg-inherit outline-none"
                                                                x-model="team.penalties"
                                                                x-on:change.debounce="onChange($event.target.value, team.id, 'pen')"
                                                                class="w-auto">

                                                        </td>
                                                    @endif


                                                </tr>



                                            </template>


                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div>
                                <h2>&nbsp;</h2>

                                <div x-show="rdata == null"><x-loader></x-loader></div>

                                <div class="  relative w-full overflow-x-auto max-h-[85vh]  " x-show="rdata != null">
                                    <table class=" text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                                        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100 ">
                                            <tr>
                                                <th scope="col"
                                                    class="py-3 px-6 text-left sticky top-0 bg-gray-100">
                                                    Team
                                                </th>
                                                <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-100">
                                                    Points
                                                </th>
                                                <th scope="col" class="py-3 px-6 sticky top-0 bg-gray-100">
                                                    Place
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <template x-for="team  in rdata" x-key="team.id">

                                                <tr class="bg-white border-b text-right hover:bg-gray-100">
                                                    <th scope="row"
                                                        class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap border-r "
                                                        x-text="team.team">
                                                    </th>

                                                    <td class="py-3 px-4   border-r "
                                                        x-text="Math.round(team.points)">
                                                    </td>
                                                    <td class="py-3 px-4 text-center   border-r " x-text="team.place">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>




                        </div>

                    </div>
                @endforeach




            </div>

            <div class="bg-white px-2 z-50 hidden md:block" id="resize-drag"
                :class="pills['event'] == null ? 'cursor-not-allowed' : 'cursor-col-resize'">
                <div class="bg-gray-100 h-full  rounded-full flex items-center  ">
                    <div class="flex flex-col space-y-1 p-1 ">
                        <div class="w-1 h-1 rounded-full bg-black"></div>
                        <div class="w-1 h-1 rounded-full bg-black"></div>
                        <div class="w-1 h-1 rounded-full bg-black"></div>
                    </div>

                </div>
            </div>


            <div class="grow  h-full z-50 bg-white " x-show="showResults">
                <div class="flex flex-row  items-center space-x-5 w-full">
                    <h2>Results</h2>
                    <div class="pill-select space-x-1">
                        <div class=" pill-select-option bg-gray-100"
                            @click="() => {switchPill('result-style','simple'); updateResultsFrame()}"
                            :class="pillActive('result-style', 'simple')">
                            Simple</div>

                        <div class=" pill-select-option bg-gray-100"
                            @click="() => {switchPill('result-style','full'); updateResultsFrame()}"
                            :class="pillActive('result-style', 'full')">
                            Full</div>
                        <div class=" pill-select-option bg-gray-100" @click="() => {toggleResults()}"
                            :class="pillActive('result-style', 'close')">
                            Close</div>
                    </div>
                </div>


                <iframe x-ref="resultsFrame"
                    src="{{ route('whatif.editor.results', $comp->getResultSchemas->first()) }}" frameborder="0"
                    class=" w-full h-full max-h-[85vh] overflow-hidden"></iframe>
            </div>


        </div>
        <div class="modal" x-show="optionsOpen" x-transition style="display: none">
            <div class="modal-content" @click.outside="optionsOpen=false">
                <div class="flex justify-between items-center">
                    <h3>Options</h3>
                    <svg @click="optionsOpen=false" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" data-slot="icon"
                        class="w-6 h-6 cursor-pointer transition-all transform hover:rotate-90">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </div>
                <p class="text-sm">Either open a previous editing session, start a new one, or delete the current one.
                    Please note that
                    editors older than 30 days are automatically deleted!</p>
                <br>
                <h5 class="mb-0">Previous Editors</h5>
                <small>Shows the editor name, and when it was last <strong>used</strong></small>
                <div class="w-full mt-2">

                    @forelse (auth()->user()->getWhatIfEditors()->where('id', '!=', $comp->id)->orderBy('updated_at', 'desc')->get() as $editor)
                        <a href="{{ route('whatif.switch', $editor->id) }}"
                            class="flex justify-between items-center group hover:text-bulsca hover:font-semibold transition-all">
                            <p>{{ $editor->name }} <small>({{ $editor->updated_at->format('d/m/Y @ H:i') }})</small>
                            </p>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                data-slot="icon" class="w-4 h-4 group-hover:animate-pulse  ">
                                <path fill-rule="evenodd"
                                    d="M2 10a.75.75 0 0 1 .75-.75h12.59l-2.1-1.95a.75.75 0 1 1 1.02-1.1l3.5 3.25a.75.75 0 0 1 0 1.1l-3.5 3.25a.75.75 0 1 1-1.02-1.1l2.1-1.95H2.75A.75.75 0 0 1 2 10Z"
                                    clip-rule="evenodd" />
                            </svg>

                        </a>
                    @empty
                        <small>You don't have any open editor sessions.</small>
                    @endforelse


                </div>
                <br>
                <h5>New Editor</h5>
                <form action="{{ route('whatif.internalCas') }}" method="POST" class="w-full flex flex-col"
                    x-on:submit="() => {optionsOpen = false; loader.message = 'Please wait while we generate your editor session...'; loader.show = true}">
                    @csrf
                    <div class="form-input">


                        <select name="competition" id="competition" class="input"
                            style="padding-top: 0.65em; padding-bottom: 0.65em; margin-bottom: 0px !important">

                            @php
                                Config::set('database.default', 'mysql');
                            @endphp

                            @foreach (\App\Models\Season::all() as $season)
                                <option value="null">Please select a competition</option>
                                <optgroup label="{{ $season->name }}">
                                    @foreach ($season->getCompetitions as $competition)
                                        <option value="{{ $competition->id }}">{{ $competition->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                            @php
                                Config::set('database.default', 'whatif');
                            @endphp
                        </select>


                    </div>
                    <button class="btn ml-auto">Start</button>
                </form>
                <br>
                <h5>Delete/Reset Editor</h5>
                <p class="text-sm">This will delete/reset the current editor, and all of its data. This action cannot
                    be
                    undone.
                </p>
                <div class="flex space-x-3">
                    <a href="#" @click="resetCompetition()" class=" ml-auto btn btn-danger">Reset</a>
                    <form action="{{ route('whatif.delete', $comp) }}" method="POST" class=" "
                        onsubmit="return confirm('Are you sure you want to delete this editor? This action cannot be undone!')">
                        @csrf
                        <button class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="w-full h-full bg-gray-300 bg-opacity-50 flex items-center justify-center z-50 fixed top-0 left-0"
            x-show="loader.show" x-transition>
            <div class="card items-center">
                <x-loader size=12 />
                <p class="text-sm" x-text="loader.message">Please wait while we generate your editor session...</p>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 right-0 font-semibold text-white bg-bulsca_red p-1 px-3 rounded-tl z-50">BETA</div>


    <div class="alert-banner z-50" id="alert">Test</div>


    <div
        class="bg-white z-50 lg:hidden fixed w-screen h-screen top-0 left-0 flex flex-col items-center justify-center">
        <div>
            <h3 class="-mb-6">BULSCA</h3>
            <h1 class=" text-[7rem] text-transparent bg-clip-text bg-gradient-to-r from-bulsca via-purple-500 to-bulsca_red"
                style="margin-bottom: 0 !important">
                WhatIf</h1>
            <p class="text-center">WhatIf is not available on mobile devices!</p>
        </div>
    </div>




    <script src="{{ asset('js/alert.js') }}"></script>


    <script>
        const frame = document.getElementById('resultsFrame')

        function start() {
            return {
                pills: {
                    event: null,
                    schema: '{{ $comp->getResultSchemas->first()->id }}',
                    'result-style': 'simple'
                },
                loader: {
                    show: true,
                    message: 'Loading...'
                },
                fullResults: false,
                optionsOpen: false,
                showResults: true,


                switchPill(pill, value) {
                    this.pills[pill] = value


                    let params = new URLSearchParams(window.location.search)
                    params.set(pill, value)



                    let newUrl = window.location.origin + window.location.pathname + '?' + decodeURIComponent(params
                        .toString())
                    window.history.pushState({}, '', newUrl)

                    if (pill == 'schema') {
                        this.showResults = true
                    }
                },

                pillActive(pill, value) {
                    return this.pills[pill] == value ? 'selected' : ''
                },

                updateResultsFrame() {
                    this.$refs.resultsFrame.contentDocument.location = "{{ route('whatif.editor.results', '') }}/" + this
                        .pills['schema'] + (this.pills['result-style'] == 'full' ? '?full=yes' : '')
                },

                resetCompetition() {

                    if (!confirm("Are you sure you want to reset this editor? This action cannot be undone!")) return

                    this.optionsOpen = false
                    this.loader.show = true
                    this.loader.message = 'Resetting editor...'

                    fetch('{{ route('whatif.reset') }}').then(res => res.json()).then(data => {
                        if (data.success) {
                            this.loader.show = false
                            window.location.reload()
                        } else {
                            showAlert('Error resetting editor. Please try again later.')
                        }
                    })
                },

                toggleResults() {
                    this.showResults = !this.showResults

                    if (!this.showResults) {
                        this.pills['schema'] = null
                        leftSide.style.width = `100%`;

                    }
                },

                init() {
                    let urlParams = new URLSearchParams(window.location.search);

                    if (urlParams.has('event')) {
                        this.pills['event'] = urlParams.get('event')
                    }
                    let shouldUpdateResults = false
                    if (urlParams.has('schema')) {
                        this.pills['schema'] = urlParams.get('schema')
                        shouldUpdateResults = true
                    }
                    if (urlParams.has('full')) {
                        this.fullResults = true
                        shouldUpdateResults = true
                    }
                    if (urlParams.has('result-style')) {
                        this.pills['result-style'] = urlParams.get('result-style')
                    }

                    if (shouldUpdateResults) {
                        this.updateResultsFrame()
                    }

                    this.loader.show = false



                },

                refreshResults() {
                    this.$refs.resultsFrame.contentDocument.location.reload(true)
                }
            }
        }
    </script>
    <script>
        // Resize and drag code
        const resizer = document.getElementById('resize-drag');
        const leftSide = resizer.previousElementSibling;
        const rightSide = resizer.nextElementSibling;

        let x = 0
        let y = 0

        let leftWidth = 0



        const mouseMoveHandler = function(e) {
            // How far the mouse has been moved
            const dx = e.clientX - x;
            const dy = e.clientY - y;

            const newLeftWidth = ((leftWidth + dx) * 100) / resizer.parentNode.getBoundingClientRect().width;
            leftSide.style.width = `${newLeftWidth}%`;
            resizer.style.cursor = 'col-resize';
            document.body.style.cursor = 'col-resize';
            leftSide.style.userSelect = 'none';
            leftSide.style.pointerEvents = 'none';

            rightSide.style.userSelect = 'none';
            rightSide.style.pointerEvents = 'none';
        };

        const mouseUpHandler = function() {
            resizer.style.removeProperty('cursor');
            document.body.style.removeProperty('cursor');

            leftSide.style.removeProperty('user-select');
            leftSide.style.removeProperty('pointer-events');

            rightSide.style.removeProperty('user-select');
            rightSide.style.removeProperty('pointer-events');

            // Remove the handlers of `mousemove` and `mouseup`
            document.removeEventListener('mousemove', mouseMoveHandler);
            document.removeEventListener('mouseup', mouseUpHandler);
        };



        // Handle the mousedown event
        // that's triggered when user drags the resizer
        const mouseDownHandler = function(e) {
            // Get the current mouse position
            x = e.clientX;
            y = e.clientY;
            leftWidth = leftSide.getBoundingClientRect().width;

            // Attach the listeners to `document`
            document.addEventListener('mousemove', mouseMoveHandler);
            document.addEventListener('mouseup', mouseUpHandler);
        };

        // Attach the handler
        resizer.addEventListener('mousedown', mouseDownHandler);
    </script>
    <script>
        console.log('WhatIf Editor v1.0.0')
        console.log('Having a look around are we ;)')

        console.log('⠀⠄⠠⠀⠄⠠⠀⠄⠠⠀⠄⠠⠀⠄⠠⠀⠄⣀⣤⣴⣶⡿⢿⡻⢟⡻⣛⡟⡟⣷⢶⣤⣄⡀⠀⢀⠀⠠⢀⠀⢰⣿⣿⣿⣿⣿⣿')
        console.log('⠠⠈⢀⠐⠀⠂⠐⠀⠂⠐⠀⠂⢀⢂⣤⣶⣿⣿⢿⢯⣷⣹⢧⣻⣭⣳⣝⣾⣱⣏⡾⣭⣟⣿⣷⣤⡀⠂⢀⠠⢸⣿⣿⣿⣿⣿⣿')
        console.log('⠀⡐⠀⠠⠈⠀⠂⠁⠐⠈⠀⢰⣴⣿⣿⣿⣯⣿⣯⣿⣾⣽⣿⣷⣿⣷⣿⣾⣷⣯⣿⣳⣟⣾⣽⣟⣿⣦⠀⠀⣾⣿⣿⣿⣿⣿⣿')
        console.log('⠀⡀⠐⠀⡀⠁⠠⠈⠀⢠⣵⣿⣿⣿⣿⣿⣿⣷⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣷⣿⣿⣯⣿⢿⣿⣿⣷⡀⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⢀⠐⠀⠀⠐⠀⠀⣱⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⡀⠀⠁⠀⠀⣱⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣧⠙⢿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⢀⠀⠁⢀⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠟⠉⠁⣀⡉⠛⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⠀⠀⠈⠛⠿⣿')
        console.log('⠀⠀⠁⠀⠀⠀⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣏⠀⠠⣾⣿⣿⣷⢸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡏⠀⠀⠀⠀⠀⢾')
        console.log('⠀⠀⠀⠀⠀⢰⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡀⠀⠻⠿⠿⠋⣼⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⡿⠁⠀⠀⠀⠀⠀⢸')
        console.log('⠀⠀⠀⠀⠀⠸⣿⡿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣶⣤⣤⣴⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣟⢯⣛⢷⣲⣾')
        console.log('⠀⠀⠀⠀⠀⢰⡏⢠⣍⢿⣿⣿⣿⣿⣿⣿⣿⣿⢿⡿⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣞⣷⣯⣿')
        console.log('⠀⠀⠀⠀⠀⠈⢳⣸⣿⡞⣿⣿⣿⣿⡟⢯⠹⡘⠦⡉⢖⡡⢏⡿⣻⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣯⣷⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⢳⡛⢃⣿⣿⠿⣭⡙⢆⢣⡙⠴⣉⢦⡹⣎⣷⣻⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠹⣿⣿⢏⡿⣴⡹⣎⢦⣝⣮⣳⢯⣷⣻⢾⣽⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠘⢿⢯⢾⣵⣻⣽⣻⣞⡷⣯⣟⡾⣽⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢘⣯⢟⣼⣳⣳⢯⢾⣝⣳⣾⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠘⠿⢶⣭⡷⠯⠟⠚⢛⣿⣿⣿⣿⣿⣿⠿⠛⠛⠛⠛⠛⠿⠿⢿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠁⠀⠀⠀⢀⣾⣿⣿⣿⣿⡟⠁⡀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠉⠙⠻⢿⣿⣿⣿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣸⣿⣿⣿⣿⣿⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⠛⠿⣿⣿⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣿⣿⣿⣿⣿⡏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠻⣿⣿')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣸⣿⣿⣿⣿⣿⡇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈⢻')
        console.log('⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣿⣿⣿⣿⣿⣿⣷⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠈')
        console.log('   NOOT')
        console.log('      NOOT')
    </script>
</body>

</html>
