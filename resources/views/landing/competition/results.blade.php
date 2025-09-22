@extends('layouts.landing-comp')

@section('title', 'Results')

@section('content')

    <div x-data="{
        leagues: {{ $comp->getLeagues }},
        events: {{ $comp->getAllEvents()->map(fn($event) => ['id' => $event->id, 'name' => $event->getName(), 'type' => $event->getType()])->toJson() }},
    
    
        selected: {
            league: null,
            event: null
        },
    
        violationData: null,
    
        table: null,
    
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
        }
    }">
        <div class="grid mt-2 ">


            <div class="row-start-1 col-start-1 grid-4 " x-show="selected.event == null" x-transition:enter.duration.500ms>

                <template x-for="event in events" :key="event.id">
                    <a href="#" class="se-card se-card-body se-card-hover" @click.prevent="onEventChange(event)"
                        :class="selected.event && selected.event.id === event.id ? 'se-card-active' : ''">
                        <h4 class="-mb-1 flex items-center"><span x-text="event.name"></span><span
                                class="ml-2  badge badge-info badge-sm" x-show="event.type == 'serc'">SERC</span></h4>

                    </a>
                </template>
            </div>


            <div x-show="selected.event != null" x-transition class="space-y-4 row-start-1 col-start-1  overflow-x-hidden">


                <div class="flex flex-col md:flex-row md:items-center  md:space-x-3">
                    <div class="flex items-center space-x-3 ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 hover:text-se transition-color cursor-pointer"
                            @click="selected.event = null; table = null;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>

                        <h1 x-text="selected.event?.name"></h1><span class="ml-2  badge badge-info "
                            x-show="selected.event?.type == 'serc'">SERC</span>



                    </div>


                    <div class="se-form-input text-sm!  w-auto! imb-0">
                        <select name="" id="" class="py-0! h-6! px-1!"
                            @change="onLeagueChange(leagues.find(l => l.id == $event.target.value))">

                            <template x-for="league in leagues" :key="league.id">
                                <option :value="league.id" x-text="league.name"></option>
                            </template>
                        </select>
                    </div>

                    <a class="se-btn se-btn-blue" target="_blank"
                        :href="breakdownUrl.replace('_serc_id', selected.event?.id)"
                        x-show="selected.event?.type == 'serc' && error == null">Breakdowns</a>
                </div>



                <div class="flex items-center justify-center"
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

                <div class="se-table" x-show="table != null" x-transition>
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

                                            <template x-if="type == 'violation'">
                                                <div>
                                                    <template x-for="(violation, indx) in data">
                                                        <span @click="loadViolation(violation['id'], violation['type'])"
                                                            x-text="violation['display'] + (indx === data.length - 1 ? '' : ', ')"
                                                            class="hover:font-semibold cursor-pointer"></span>
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
            <div>
                <h2 x-text="violationData?.code"></h2>
                <p x-text="violationData?.for"></p>
            </div>
        </x-s-e-modal>

        <hr class="spacer my-8! mt-10!">

    </div>


    <div x-data="{
        schemas: {{ $comp->getResultSchemas->pluck('name', 'id') }},
    
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
    
    
    }">








        <div>

            <div class="grid-4 mt-2" x-show="selected.schema == null" x-transition:enter.duration.500ms>

                <template x-for="(schema_name, schema_id) in schemas" :key="schema_id">
                    <div class="se-card se-card-hover" @click.prevent="onSchemaChange({id: schema_id, name: schema_name})">
                        <div class="se-card-body flex items-center justify-between">
                            <h4 class="-mb-1!" x-text="schema_name"></h4>


                        </div>


                    </div>
                </template>
            </div>

            <div x-show="selected.schema != null" x-transition class="space-y-4 row-start-1 col-start-1  overflow-x-hidden">




                <div class="flex flex-col md:flex-row md:items-center  md:space-x-3">
                    <div class="flex items-center space-x-3 ">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 hover:text-se transition-color cursor-pointer"
                            @click="selected.schema = null; table = null;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>

                        <h1 x-text="selected.schema?.name"></h1>

                    </div>


                    <div class="se-form-input text-sm!  w-auto! imb-0">
                        <select name="" id="" class="py-0! h-6! px-1!"
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
    </div>







@endsection
