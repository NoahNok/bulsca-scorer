@extends('layouts.landing-comp')

@section('title', 'Results')

@section('content')

    <div x-data="{
        leagues: {{ $comp->getLeagues }},
        events: {{ $comp->public_results ? $comp->getAllEvents()->map(fn($event) => ['id' => $event->id, 'name' => $event->getName(), 'type' => $event->getType()])->toJson() : '[]' }},
    
    
        selected: {
            league: null,
            league_id: null,
            event: null
        },
    
        violationData: null,
    
        table: null,
    
        select_league_first: {{ $comp->select_league_first ? 'true' : 'false' }},
    
        getColumns() {
            return Object.fromEntries(
                Object.entries(this.table?.columns ?? {}).filter(([key]) => !key.startsWith('_'))
            );
        },
    
        error: '',
    
        url: '{{ route('landing.competition.results.get', [$comp->getSlug(), 'league' => '_league', 'event' => '_event', 'type' => '_type']) }}',
        violationUrl: '{{ route('landing.competition.results.get.violation', [$comp->getSlug(), 'violation_id' => '_id', 'violation_type' => '_type']) }}',
        breakdownUrl: '{{ route('landing.competition.results.breakdown.serc', [$comp->getSlug(), 'serc' => '_serc_id']) }}',
    
        onLeagueChange(targetLeague) {
    
            if (this.selected.league == targetLeague) {
                this.selected.league = null;
                return;
            }
    
            this.selected.league = targetLeague;
            this.loadResults();
        },
    
        onEventChange(targetEvent) {
            this.selected.event = targetEvent;
            this.loadResults();
        },
    
        async loadResults() {
            if (!this.selected.league || !this.selected.event) {
                return;
            }
    
            this.error = null;
    
            this.table = null;
    
            let url = this.url.replace('_league', this.selected.league.id).replace('_event', this.selected.event.id).replace('_type', this.selected.event.type);
    
            let response = await fetch(url);
            if (response.ok) {
                this.table = await response.json();
    
            } else {
    
                this.error = (await response.json()).error || 'Failed to load results.';
    
            }
    
    
        },
    
        async loadViolation(violation_id, violation_type) {
            let url = this.violationUrl.replace('_id', violation_id).replace('_type', violation_type);
    
            let response = await fetch(url)
    
            if (response.ok) {
                let data = await response.json()
                this.violationData = data
    
                this.modals.violation = true
            }
        },
    
    
    
        init() {
            let all = { id: 'all', name: 'All Leagues' }
            this.leagues.unshift(all);
            this.selected.league = all;
    
            $watch('selected.league', (newVal, oldVal) => {
                // set league query param
                const url = new URL(window.location);
                if (newVal && newVal.id !== 'all') {
                    url.searchParams.set('league', newVal.id);
                } else {
                    url.searchParams.delete('league');
                }
                window.history.replaceState({}, '', url);
            });
    
            $watch('selected.event', (newVal, oldVal) => {
                // set event query param
                const url = new URL(window.location);
                if (newVal) {
                    url.searchParams.set('event', `${newVal.type}-${newVal.id}`);
                } else {
                    url.searchParams.delete('event');
                }
                window.history.replaceState({}, '', url);
            });
    
    
    
            // On load, check for league and event query params
            const params = new URLSearchParams(window.location.search);
            const leagueParam = params.get('league');
            const eventParam = params.get('event');
    
            if (leagueParam) {
                const league = this.leagues.find(l => l.id == leagueParam);
                if (league) {
                    this.selected.league = league;
    
                    setTimeout(() => {
                        this.selected.league_id = league.id;
                    }, 100);
    
                }
            }
    
            if (eventParam) {
                const [type, id] = eventParam.split('-');
                const event = this.events.find(e => e.id == id && e.type == type);
                if (event) {
                    this.selected.event = event;
                }
            }
    
            if (this.selected.league && this.selected.event) {
                this.loadResults();
            }
        }
    }">

        @if ($comp->results_provisional)
            <div class="alert-box alert-warning">
                <h1>Provisional Results</h1>
                <p>The results displayed here are provisional and may be subject to change.</p>
            </div>
            <br>
        @endif

        <div class="grid mt-2 ">


            <div class="row-start-1 col-start-1 grid-4 " x-show="selected.event == null" x-transition:enter.duration.500ms>

                <template x-for="event in events" :key="event.id">
                    <a href="#" class="se-card se-card-body se-card-hover" @click.prevent="onEventChange(event)"
                        :class="selected.event && selected.event.id === event.id ? 'se-card-active' : ''">
                        <h4 class="-mb-1 flex items-center"><span x-text="event.name"></span><span
                                class="ml-2  badge badge-info badge-sm" x-show="event.type == 'serc'">SERC</span></h4>

                    </a>
                </template>

                <template x-if="events.length == 0">
                    <div class="alert-box alert-info col-span-full">
                        <h1>Results Unavailable</h1>
                        <p>Results are not available at this time. Please check back later.</p>
                    </div>
                </template>
            </div>


            <div x-show="selected.event != null && select_league_first && selected.league_id === null" x-transition
                class="space-y-4 row-start-1 col-start-1 overflow-x-hidden">
                <div class="flex items-center justify-center flex-col space-y-4">
                    <h4>Please select a league/bracket</h4>
                    <div class="se-form-input text-sm!  w-auto! imb-0">
                        <select name="" id="" class="py-0! h-6! px-1!" x-model="selected.league_id"
                            @change="onLeagueChange(leagues.find(l => l.id == $event.target.value))">

                            <option value="null" :disabled="!select_league_first">Select League</option>

                            <template x-for="league in leagues" :key="league.id">
                                <option :value="league.id" x-text="league.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div x-show="selected.event != null && (!select_league_first || selected.league_id !== null)" x-transition
                class="space-y-4 row-start-1 col-start-1  overflow-x-hidden" x-cloak>


                <div class="flex flex-col md:flex-row md:items-center  md:space-x-3">
                    <div class="flex items-center space-x-3 ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 min-w-6 hover:text-se transition-color cursor-pointer"
                            @click="selected.event = null; table = null;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>

                        <h1 x-text="selected.event?.name"></h1><span class="ml-2  badge badge-info "
                            x-show="selected.event?.type == 'serc'">SERC</span>



                    </div>


                    <div class="se-form-input text-sm!  w-auto! imb-0">
                        <select name="" id="" class="py-0! h-6! px-1!" x-model="selected.league_id"
                            @change="onLeagueChange(leagues.find(l => l.id == $event.target.value))">



                            <template x-for="league in leagues" :key="league.id">
                                <option :value="league.id" x-text="league.name"></option>
                            </template>
                        </select>
                    </div>

                    <a class="se-btn se-btn-blue" target="_blank"
                        :href="breakdownUrl.replace('_serc_id', selected.event?.id) + `?league=${selected.league?.id}`"
                        x-show="selected.event?.type == 'serc' && error == null">Breakdowns</a>
                </div>



                <div class="flex items-center justify-center" x-cloak
                    x-show="!table && selected.league != null && selected.event != null && error == null">
                    <svg aria-hidden="true" class="w-8 h-8 animate-spin text-gray-200 fill-se" viewBox="0 0 100 101"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                </div>

                <div class="flex items-center justify-center" x-show="error">

                    <div class="alert-box alert-warning md:w-1/2">
                        <h1>Results Unavailable</h1>
                        <p x-text="error"></p>
                    </div>
                </div>

                <div class="se-table" x-show="table != null" x-transition x-cloak>
                    <table>
                        <thead>
                            <tr>

                                <template x-for="(column_name, column_label) in getColumns()">
                                    <th scope="col" x-text="column_label"></th>
                                </template>


                            </tr>
                        </thead>
                        <tbody>



                            <template x-for="row in table?.data">

                                <tr>

                                    <template x-for="([column_id, column_label], index) in Object.entries(getColumns())">
                                        <td x-data="{
                                            type: 'string',
                                            data: row.data[column_id],
                                        
                                            init() {
                                        
                                                row_type = typeof row.data[column_id]
                                        
                                                if (row_type !== 'object') {
                                                    return
                                                }
                                        
                                                // We have an object, pull its type to decide which to render below
                                        
                                                this.type = row.data[column_id]['type']
                                                this.data = row.data[column_id]['data']
                                        
                                        
                                            }
                                        }" :class="index == 0 ? 'table-th' : ''">


                                            <template x-if="type === 'string'">
                                                <span x-text="data"></span>
                                            </template>

                                            <template x-if="type === 'string-array'">
                                                <div class="">
                                                    <template x-for="item in data">

                                                        <div>
                                                            <span x-text="item"></span>
                                                        </div>

                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="type == 'violation'">
                                                <div>
                                                    <template x-for="(violation, indx) in data">
                                                        <span @click="loadViolation(violation['id'], violation['type'])"
                                                            x-text="violation['display'] + (indx === data.length - 1 ? '' : ', ')"
                                                            class="hover:font-semibold cursor-pointer"></span>
                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="type == 'violation-combined'">
                                                <div>
                                                    <template x-for="item in data">
                                                        <div>
                                                            <template x-for="(violation, indx) in item">
                                                                <span
                                                                    @click="loadViolation(violation['id'], violation['type'])"
                                                                    x-text="typeof violation == 'string' ? violation : violation['display'] + (indx === item.length - 1 ? '' : ', ')"
                                                                    class="hover:font-semibold cursor-pointer"></span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="type == 'result'">
                                                <div>
                                                    <span x-text="data['is']"></span>
                                                    <template x-if="data['is'] != data['was']">
                                                        <small><br>Was <span x-text="data['was']"></span></small>
                                                    </template>
                                                </div>
                                            </template>

                                            <template x-if="type == 'result-combined'">
                                                <div class="flex justify-end items-center ">
                                                    <div class="border-r-2 pr-2">

                                                        <template x-for="time in data['combined']['results']">
                                                            <div>
                                                                <span x-text="time"></span>
                                                            </div>
                                                        </template>


                                                    </div>
                                                    <div class="pl-2">
                                                        <span x-text="data['is']"></span>
                                                    </div>
                                                </div>
                                            </template>







                                        </td>
                                    </template>

                                </tr>
                            </template>



                        </tbody>
                    </table>
                </div>



            </div>

        </div>

        <x-s-e-modal id="violation" title="Violation">
            <div x-effect="title = violationData?.for ">
                <h2 class="text-red-500" x-text="violationData?.code"></h2>
                <p x-text="violationData?.description"></p>

            </div>

            <template x-if="violationData?.submission">

                <div class="mt-2 space-y-2">

                    <div class="flex justify-between flex-col md:flex-row  md:space-y-0">
                        <p>
                            <strong>Reporter</strong>: <span x-text="violationData.submission.name"></span> (<span
                                x-text="violationData.submission.position"></span>)
                            <br class="md:hidden">
                            <strong>Seconder</strong>: <span x-text="violationData.submission.seconder_name"></span>
                            (<span x-text="violationData.submission.seconder_position"></span>)
                        </p>

                        <p><strong>Turn</strong>: <span x-text="violationData?.submission?.turn ?? '-'"></span>
                            <strong>Length</strong>: <span x-text="violationData?.submission?.length ?? '-'"></span>
                        </p>
                    </div>

                    <p>
                        <i x-text="violationData.submission.details || 'No details provided.'"></i>
                    </p>

                </div>
            </template>
        </x-s-e-modal>

        <hr x-show="events.length > 0" class="spacer my-8! mt-10!" x-cloak>

    </div>


    <div x-data="{
        schemas: {{ $comp->public_results ? $comp->getResultSchemas->pluck('name', 'id') : '[]' }},
    
        selected: {
            schema: null,
            display: 'simple'
        },
    
        table: null,
    
        error: '',
    
        url: '{{ route('landing.competition.results.get.sheet', [$comp->getSlug(), 'schema' => '_schema']) }}',
    
        onSchemaChange(targetSchema) {
            if (this.selected.schema == targetSchema) {
                this.selected.schema = null;
                this.table = null;
                return;
            }
    
            this.selected.schema = targetSchema;
            this.loadResults();
        },
    
        async loadResults() {
            if (!this.selected.schema) {
                return;
            }
    
            this.table = null;
            this.error = null;
    
            let url = this.url.replace('_schema', this.selected.schema.id);
    
            let response = await fetch(url);
            if (response.ok) {
                this.table = await response.json();
    
            } else {
                this.error = (await response.json()).error || 'Failed to load results.';
            }
    
    
        },
    
        init() {
    
            $watch('selected.schema', (newVal, oldVal) => {
                // set schema query param
                const url = new URL(window.location);
                if (newVal) {
                    url.searchParams.set('schema', newVal.id);
                } else {
                    url.searchParams.delete('schema');
                }
                window.history.replaceState({}, '', url);
            });
    
            $watch('selected.display', (newVal, oldVal) => {
                // set display query param
                const url = new URL(window.location);
                if (newVal && newVal !== 'simple') {
                    url.searchParams.set('display', newVal);
                } else {
                    url.searchParams.delete('display');
                }
                window.history.replaceState({}, '', url);
            });
    
            // On load, check for schema query param
            const params = new URLSearchParams(window.location.search);
            const schemaParam = params.get('schema');
            const displayParam = params.get('display');
    
            if (schemaParam) {
                const schema = Object.entries(this.schemas).find(([id, name]) => id == schemaParam);
                if (schema) {
                    this.selected.schema = { id: schema[0], name: schema[1] };
                    this.loadResults();
                }
            }
    
            if (displayParam && ['simple', 'detailed'].includes(displayParam)) {
                this.selected.display = displayParam;
            }
        }
    
    
    }">


        <div>

            <div class="grid-4 mt-2" x-show="selected.schema == null" x-transition:enter.duration.500ms>

                <template x-for="(schema_name, schema_id) in schemas" :key="schema_id">
                    <div class="se-card se-card-hover"
                        @click.prevent="onSchemaChange({id: schema_id, name: schema_name})">
                        <div class="se-card-body flex items-center justify-between">
                            <h4 class="-mb-1!" x-text="schema_name"></h4>


                        </div>


                    </div>
                </template>
            </div>

            <div x-show="selected.schema != null" x-transition x-cloak
                class="space-y-4 row-start-1 col-start-1  overflow-x-hidden">




                <div class="flex flex-col md:flex-row md:items-center  md:space-x-3">
                    <div class="flex items-center space-x-3 ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 min-w-6 hover:text-se transition-color cursor-pointer"
                            @click="selected.schema = null; table = null;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>

                        <h1 x-text="selected.schema?.name"></h1>

                    </div>


                    <div class="se-form-input text-sm!  w-auto! imb-0">
                        <select name="" id="" class="py-0! h-6! px-1!" x-model="selected.display"
                            @change="selected.display = $event.target.value">

                            <option value="simple">Simple View</option>
                            <option value="detailed">Detailed View</option>

                        </select>
                    </div>
                </div>



                <div class="flex items-center justify-center" x-show="!table && selected.schema != null && error == null">
                    <svg aria-hidden="true" class="w-8 h-8 animate-spin text-gray-200 fill-se" viewBox="0 0 100 101"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                </div>

                <div class="flex items-center justify-center" x-show="error">

                    <div class="alert-box alert-warning md:w-1/2">
                        <h1>Results Unavailable</h1>
                        <p x-text="error"></p>
                    </div>
                </div>

                <div class="se-table" x-show="table != null" x-transition>
                    <table>
                        <thead>
                            <tr>

                                <template x-for="(column_label, column_id) in table?.columns" :key="column_id">
                                    <th scope="col" x-text="column_label" class=" whitespace-nowrap"
                                        x-show="column_id.includes(':') ? selected.display == 'detailed' : true"></th>
                                </template>


                            </tr>
                        </thead>
                        <tbody>



                            <template x-for="row in table?.data">

                                <tr>

                                    <template
                                        x-for="([column_id, column_label], index) in Object.entries(table?.columns ?? [])">
                                        <td x-data="{
                                            text: row[column_id],
                                            was: null,
                                        
                                        
                                        
                                            init() {
                                                if (column_id.includes(':') && typeof row[column_id] === 'object') {
                                                    this.text = `${row[column_id].position} (${row[column_id].points} pts)`;
                                        
                                        
                                        
                                                }
                                        
                                        
                                            }
                                        }" class="whitespace-nowrap"
                                            :class="index == 0 ? 'table-th' : ''"
                                            x-show="column_id.includes(':') ? selected.display == 'detailed' : true">


                                            <span x-text="text">h</span>

                                            <span x-show="was != null">
                                                <br>
                                                <small>
                                                    Was <span x-text='was'></span>
                                                </small>
                                            </span>



                                        </td>
                                    </template>

                                </tr>
                            </template>



                        </tbody>
                    </table>
                </div>



            </div>
        </div>


        <hr x-show="Object.keys(schemas).length > 0" class="spacer my-8! mt-10!" x-cloak>
    </div>





    <div x-data="{
        schemas: {{ $comp->public_results ? $comp->getMasterSchemas->pluck('name', 'id') : '[]' }},
    
        selected: {
            schema: null,
            display: 'simple'
        },
    
        table: null,
    
        error: '',
    
        url: '{{ route('landing.competition.results.get.master.sheet', [$comp->getSlug(), 'schema' => '_schema']) }}',
    
        onSchemaChange(targetSchema) {
            if (this.selected.schema == targetSchema) {
                this.selected.schema = null;
                this.table = null;
                return;
            }
    
            this.selected.schema = targetSchema;
            this.loadResults();
        },
    
        async loadResults() {
            if (!this.selected.schema) {
                return;
            }
    
            this.table = null;
            this.error = null;
    
            let url = this.url.replace('_schema', this.selected.schema.id);
    
            let response = await fetch(url);
            if (response.ok) {
                this.table = await response.json();
    
            } else {
                this.error = (await response.json()).error || 'Failed to load results.';
            }
    
    
        },
    
        init() {
    
            $watch('selected.schema', (newVal, oldVal) => {
                // set schema query param
                const url = new URL(window.location);
                if (newVal) {
                    url.searchParams.set('masterSchema', newVal.id);
                } else {
                    url.searchParams.delete('masterSchema');
                }
                window.history.replaceState({}, '', url);
            });
    
    
    
    
            // On load, check for schema query param
            const params = new URLSearchParams(window.location.search);
            const schemaParam = params.get('masterSchema');
            const displayParam = params.get('display');
    
    
            if (schemaParam) {
                const schema = Object.entries(this.schemas).find(([id, name]) => id == schemaParam);
                if (schema) {
                    this.selected.schema = { id: schema[0], name: schema[1] };
                    this.loadResults();
                }
            }
    
            if (displayParam && ['simple', 'detailed'].includes(displayParam)) {
                this.selected.display = displayParam;
            }
        }
    
    
    }">


        <div>

            <div class="grid-4 mt-2" x-show="selected.schema == null" x-transition:enter.duration.500ms>

                <template x-for="(schema_name, schema_id) in schemas" :key="schema_id">
                    <div class="se-card se-card-hover"
                        @click.prevent="onSchemaChange({id: schema_id, name: schema_name})">
                        <div class="se-card-body flex items-center justify-between">
                            <h4 class="-mb-1!" x-text="schema_name"></h4>


                        </div>


                    </div>
                </template>
            </div>

            <div x-show="selected.schema != null" x-transition x-cloak
                class="space-y-4 row-start-1 col-start-1  overflow-x-hidden">




                <div class="flex flex-col md:flex-row md:items-center  md:space-x-3">
                    <div class="flex items-center space-x-3 ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 hover:text-se transition-color cursor-pointer"
                            @click="selected.schema = null; table = null;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>

                        <h1 x-text="selected.schema?.name"></h1>

                    </div>


                    {{-- <div class="se-form-input text-sm!  w-auto! imb-0">
                        <select name="" id="" class="py-0! h-6! px-1!" x-model="selected.display"
                            @change="selected.display = $event.target.value">

                            <option value="simple">Simple View</option>
                            <option value="detailed">Detailed View</option>

                        </select>
                    </div> --}}
                </div>



                <div class="flex items-center justify-center" x-show="!table && selected.schema != null && error == null">
                    <svg aria-hidden="true" class="w-8 h-8 animate-spin text-gray-200 fill-se" viewBox="0 0 100 101"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                </div>

                <div class="flex items-center justify-center" x-show="error">

                    <div class="alert-box alert-warning md:w-1/2">
                        <h1>Results Unavailable</h1>
                        <p x-text="error"></p>
                    </div>
                </div>

                <div class="se-table" x-show="table != null" x-transition>
                    <table>
                        <thead>
                            <tr>

                                <template x-for="(column_label, column_id) in table?.columns" :key="column_id">
                                    <th scope="col" x-text="column_label" class=" whitespace-nowrap"
                                        x-show="column_id.includes(':') ? selected.display == 'detailed' : true"></th>
                                </template>


                            </tr>
                        </thead>
                        <tbody>



                            <template x-for="row in table?.data">

                                <tr>

                                    <template
                                        x-for="([column_id, column_label], index) in Object.entries(table?.columns ?? [])">
                                        <td x-data="{
                                            text: row[column_id],
                                            was: null,
                                        
                                        
                                        
                                            init() {
                                                if (column_id.includes(':') && typeof row[column_id] === 'object') {
                                                    this.text = `${row[column_id].position} (${row[column_id].points} pts)`;
                                        
                                        
                                        
                                                }
                                        
                                        
                                            }
                                        }" class="whitespace-nowrap"
                                            :class="index == 0 ? 'table-th' : ''"
                                            x-show="column_id.includes(':') ? selected.display == 'detailed' : true">


                                            <span x-text="text">h</span>

                                            <span x-show="was != null">
                                                <br>
                                                <small>
                                                    Was <span x-text='was'></span>
                                                </small>
                                            </span>



                                        </td>
                                    </template>

                                </tr>
                            </template>



                        </tbody>
                    </table>
                </div>



            </div>
        </div>
    </div>







@endsection
