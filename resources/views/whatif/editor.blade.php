<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editor | WhatIf | BULSCA</title>
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
            <div class="flex-grow"></div>
            <h1 class="text-[1.5rem]" style="margin-bottom: 0 !important">{{ $comp->name }}</h1>
        </div>
        <div class="w-full flex space-x-3 px-2  border rounded-full bg-gray-100 items-center">
            <div class="py-1 px-3 ">Events</div>

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

            <div class="py-1 px-3" style="margin-left: auto">Results</div>

            <div class="pill-select">

                @foreach ($comp->getResultSchemas as $schema)
                    <div class=" pill-select-option"
                        @click="() => {switchPill('schema','{{ $schema->id }}'); updateResultsFrame()}"
                        :class="pillActive('schema', '{{ $schema->id }}')">
                        {{ $schema->name }}</div>
                @endforeach

            </div>

        </div>



        <div class="w-full h-full p-5 flex  ">
            <p style="display: none" class="w-[70%]" x-show="pills['event'] == null">Please select an event.</p>

            <div style="display: none" class="w-[60%] z-10" x-show="pills['event'] != null">

                @foreach ($comp->getSERCs as $serc)
                    <div style="display: none" x-show="pills['event'] == 'se:{{ $serc->id }}'">

                        <div class="flex flex-row space-x-5" x-data="{
                            sdata: {{ json_encode($serc->getDataAsJson()) }},
                            rdata: null,
                        
                            loadResults() {
                                this.rdata = null
                                fetch('{{ route('whatif.editor.sercs', $serc->id) }}').then(res => res.json()).then(data => {
                                    console.log(data)
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
                        
                            onChange(newValue, srId) {
                                console.log(newValue, srId)
                        
                        
                                let fd = new FormData();
                                fd.append('_token', '{{ csrf_token() }}')
                                fd.append('id', srId)
                                fd.append('result', newValue)
                        
                                fetch('{{ route('whatif.userc') }}', {
                                    method: 'POST',
                                    body: fd
                                }).then(res => res.json()).then(data => {
                                    console.log(data)
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

                            <div class="flex-grow">
                                <h2>{{ $serc->getName() }}</h2>


                                <table class="table text-sm">
                                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100">
                                        <tr>
                                            <th class="py-3 px-6 text-left ">Team</th>
                                            <template x-for="judge in sdata.judges" :key="judge.id">
                                                <th class="p-2 text-center border-r  last-of-type:border-r-0"
                                                    :colspan="judge.marking_points.length" x-text="judge.name">
                                                </th>
                                            </template>
                                        </tr>
                                        <tr>
                                            <th class="border-r text-left  px-6"></th>
                                            <template x-for="mp in (onlyMps())" :key="mp.id">
                                                <th class="px-2 border-r  last-of-type:border-r-0 text-center"
                                                    x-text=" mp.name"></th>
                                            </template>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="team in sdata.teams" :key="team.id">
                                            <tr class="hover:bg-gray-100">
                                                <td class="border-r py-3 text-left px-4 font-medium text-gray-900 whitespace-nowrap"
                                                    x-text="team.name"></td>
                                                <template x-for="mp in (onlyMps())" :key="mp.id">
                                                    <td
                                                        class=" border-r  last-of-type:border-r-0 hover:bg-gray-300  p-0">
                                                        <input
                                                            class=" w-full text-center bg-inherit  h-full inline-block relative outline-none"
                                                            x-on:change.debounce="onChange($event.target.value, sdata.data[mp.id][team.id].id )"
                                                            x-model=" Math.round(sdata.data[mp.id][team.id].result)"
                                                            x-mask:dynamic="$input.length==1 ? '9' : '99' " />
                                                    </td>

                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2>&nbsp;</h2>

                                <div x-show="rdata == null"><x-loader></x-loader></div>

                                <div class="  relative w-full overflow-x-auto  " x-show="rdata != null">
                                    <table class=" text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                                        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100 ">
                                            <tr>
                                                <th scope="col" class="py-3 px-6 text-left">
                                                    Team
                                                </th>
                                                <th scope="col" class="py-3 px-6">
                                                    Points
                                                </th>
                                                <th scope="col" class="py-3 px-6">
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
                    <div class="w" style="display: none" x-show="pills['event'] == 'sp:{{ $speed->id }}'">

                        <div class="flex flex-row space-x-5" x-data="{
                            sdata: {{ json_encode($speed->getDataAsJson()) }},
                            rdata: null,
                        
                            loadResults() {
                                this.rdata = null
                                fetch('{{ route('whatif.editor.speeds', $speed->id) }}').then(res => res.json()).then(data => {
                                    console.log(data)
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
                                console.log(newValue, srId, type)
                        
                        
                                let fd = new FormData();
                                fd.append('_token', '{{ csrf_token() }}')
                                fd.append('id', srId)
                                fd.append('value', newValue)
                                fd.append('type', type)
                        
                                fetch('{{ route('whatif.uspeed') }}', {
                                    method: 'POST',
                                    body: fd
                                }).then(res => res.json()).then(data => {
                                    console.log(data)
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


                                <div class="  relative w-full overflow-x-auto  ">
                                    <table class=" text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                                        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100 ">
                                            <tr>
                                                <th scope="col" class="py-3 px-6 text-left">
                                                    Team
                                                </th>

                                                <th scope="col" class="py-3 px-6">
                                                    @if ($speed->getName() == 'Rope Throw')
                                                        Ropes/Time
                                                    @else
                                                        Time
                                                    @endif
                                                </th>

                                                <th scope="col" class="py-3 px-6">
                                                    DQ
                                                </th>

                                                @if ($speed->hasPenalties())
                                                    <th scope="col" class="py-3 px-6">
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

                                <div class="  relative w-full overflow-x-auto  " x-show="rdata != null">
                                    <table class=" text-sm w-full shadow-md rounded-lg  text-left text-gray-500 ">
                                        <thead class="text-xs text-gray-700 text-right uppercase bg-gray-100 ">
                                            <tr>
                                                <th scope="col" class="py-3 px-6 text-left">
                                                    Team
                                                </th>
                                                <th scope="col" class="py-3 px-6">
                                                    Points
                                                </th>
                                                <th scope="col" class="py-3 px-6">
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

            <div class="bg-white px-2 z-50" id="resize-drag">
                <div class="border h-full cursor-col-resize  "></div>
            </div>


            <div class="grow  h-full z-50 bg-white ">
                <h2>Results</h2>

                <iframe x-ref="resultsFrame"
                    src="{{ route('whatif.editor.results', $comp->getResultSchemas->first()) }}" frameborder="0"
                    class=" w-full h-full overflow-hidden"></iframe>
            </div>


        </div>
    </div>

    <script>
        const frame = document.getElementById('resultsFrame')

        function start() {
            return {
                pills: {
                    event: null,
                    schema: '{{ $comp->getResultSchemas->first()->id }}'
                },
                fullResults: false,


                switchPill(pill, value) {
                    this.pills[pill] = value


                    let params = new URLSearchParams(window.location.search)
                    params.set(pill, value)



                    let newUrl = window.location.origin + window.location.pathname + '?' + decodeURIComponent(params
                        .toString())
                    window.history.pushState({}, '', newUrl)
                },

                pillActive(pill, value) {
                    return this.pills[pill] == value ? 'selected' : ''
                },

                updateResultsFrame() {
                    this.$refs.resultsFrame.contentDocument.location = "{{ route('whatif.editor.results', '') }}/" + this
                        .pills['schema'] + (this.fullResults ? '?full=yes' : '')
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

                    if (shouldUpdateResults) {
                        this.updateResultsFrame()
                    }



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
</body>

</html>
