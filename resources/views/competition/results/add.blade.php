@extends('layouts.competition')

@section('title')
    Add Results Sheet
@endsection


@section('content')
    <div class="flex flex-col space-y-4" x-data="{
        name: '',
        group_on: 'team',
        events: {{ $comp->getSpeedEvents->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->getName(),
                    'type' => 'speed',
                    'selected' => false,
                    'weight' => 1,
                    'break_ties' => null,
                ];
            })->merge(
                $comp->getSERCs->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'name' => $event->getName(),
                        'type' => 'serc',
                        'selected' => false,
                        'weight' => 2,
                        'break_ties' => null,
                    ];
                }),
            ) }},
        schema_templates: {{ $comp->getOrganisation?->resultSchemas->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'schema' => $i->schema]) }},
        league: null,
        rank_higher: true,
        ignore_disqualified: false,
        repeat_for_all_leagues: false,
        formula: null,
        formula_data: {
            equation: '',
            global_variables: []
        },
    
        onFormulaChange() {
            let splt = this.formula.split(':')
    
            switch (splt[0]) {
                case 'bi':
                    let parts = splt[1].split('-')
                    let isLow = parts.length > 1
    
                    this.rank_higher = !isLow
    
                    if (parts[0] == 'point') {
                        this.formula_data.equation = 'item.points'
                    } else if (parts[0] == 'position') {
                        this.formula_data.equation = 'item.position'
                    } else if (parts[0] == 'result') {
                        this.formula_data.equation = 'item.resolvedResult'
                    } else {
                        showAlert('Unknown builtin')
                    }
    
                    break
    
                case 'org':
                    let id = splt[1]
    
                    let target_schema = this.schema_templates.find(s => s.id == id)
    
                    this.formula_data = target_schema.schema
    
                    this.formula = 'custom'
                    break
    
    
            }
    
        },
    
        toggleTieBreak(event) {
    
            let maxBreakTie = Math.max(...this.events.map(obj => obj.break_ties));
    
            if (event.break_ties) {
    
    
    
    
    
    
                this.events.filter(evt => evt.break_ties > event.break_ties).forEach(e => {
                    e.break_ties = e.break_ties - 1
                })
    
                event.break_ties = null
    
                return
            }
    
    
    
            if (!maxBreakTie) {
                maxBreakTie = 0
            }
    
            maxBreakTie++
    
            event.break_ties = maxBreakTie
    
        },
    
    
        async submit() {
            selected_events = this.events.filter(event => event.selected)
    
            if (!this.league) {
                showAlert('Please select a league')
                return
            }
    
            if (selected_events.length == 0) {
                showAlert('Please select atleast one event.')
                return
            }
    
            if (!this.formula) {
                showAlert('You must select a formula')
                return
            }
    
    
    
            data = {
                name: this.name,
                league: this.league,
                group_on: this.group_on,
                events: selected_events,
                rank_higher: this.rank_higher,
                ignore_disqualified: this.ignore_disqualified,
                repeat_for_all_leagues: this.repeat_for_all_leagues,
                equation: this.formula_data.equation,
                global_variables: this.formula_data.global_variables,
            }
    
    
    
            let response = await fetch('{{ route('comps.results.addPost', [$comp]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
    
            if (response.ok) {
                let d = await response.json()
    
                window.location.href = d['url']
            } else {
                showAlert('Failed to create sheet(s)')
            }
    
    
        }
    
    }">

        <div class="flex justify-between">
            <h2 class="mb-0">Add Results Sheet</h2>

        </div>
        <p>You may leave the name blank and it will use the name of the League instead</p>

        <div class="grid-4">
            <div class="se-form-input col-span-2">
                <label for="">Name</label>
                <input type="text" placeholder="Name" x-model="name">
            </div>

            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">League</label>

                <select style="margin-bottom: 0 !important" name="target_entity" x-model="league">
                    <option value="null" selected disabled>Please select a league</option>
                    <option value="all">All</option>
                    @foreach ($comp->getLeagues as $league)
                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">Group On</label>

                <select style="margin-bottom: 0 !important" name="target_entity" x-model="group_on">
                    <option value="club">Clubs</option>
                    <option value="team">Teams</option>
                    <option value="competitor">Competitiors</option>
                </select>
            </div>
        </div>


        <h3 class="mb-0!">Events</h3>
        <p>Select events to include, they will become green when selected. You can also specify an event weighting.
            (Defaults to 1 for speed events, and 2 for SERCs)</p>
        <div class="grid-4">
            <template x-for="event in events">
                <div class="se-card se-card-body se-card-hover" :class="event.selected ? 'se-card-success' : ''"
                    @click="event.selected = !event.selected">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-3 items-center">
                            <h4 x-text="event.name"></h4>
                            <span class="badge badge-info" x-show="event.type == 'serc'">SERC</span>
                        </div>

                        <div class="text-xs! font-semibold font-archivo" x-show="event.selected" @click.stop>
                            <label for="">WEIGHT</label>
                            &nbsp;
                            <input type="number"
                                class="w-5 text-right border-b border-gray-500 hover:border-gray-800! outline-none!"
                                name="" min="1" x-model="event.weight" id="">
                        </div>
                    </div>
                </div>

            </template>



        </div>

        <hr class="spacer mb-6! mt-4!">

        <h3 class="mb-0!">Formula</h3>
        <p>Use a simple pre-made formula, or create your own</p>




        <div class="grid-4">
            <div class="se-form-input" style="margin-bottom: 0 !important">


                <select style="margin-bottom: 0 !important" name="target_entity" x-model="formula"
                    @change="onFormulaChange">
                    <option value="null" selected disabled>Please select a formula</option>

                    <option value="custom">Custom</option>

                    <optgroup label="Clone from Organisation">
                        <template x-for="schema in schema_templates">
                            <option :value="`org:${schema.id}`" x-text="schema.name"></option>
                        </template>

                    </optgroup>

                    <optgroup label="Built-in">
                        <option value="bi:point">Highest Point Sum Wins</option>
                        <option value="bi:point-low">Lowest Points Sum Wins</option>
                        <option value="bi:position">Highest Position Sum Wins</option>
                        <option value="bi:position-low">Lowest Position Sum Wins</option>
                        <option value="bi:result">Highest Result Sum Wins</option>
                        <option value="bi:result-low">Lowset Result Sum Wins</option>
                    </optgroup>


                </select>
            </div>


            <div class="se-checkbox">
                <div>
                    <input type="checkbox" id="rank_higher" x-model="rank_higher">
                    <label for="rank_higher">Rank Higher</label>
                </div>
                <p>Highest wins</p>
            </div>

            <div class="se-checkbox">
                <div>
                    <input type="checkbox" id="ignore_disqualified" x-model="ignore_disqualified">
                    <label for="ignore_disqualified">Ignore Disqualified</label>
                </div>
                <p>Includes disqualified results</p>
            </div>


            <div class="se-checkbox">
                <div>
                    <input type="checkbox" id="repeat_for_all_leagues" x-model="repeat_for_all_leagues">
                    <label for="repeat_for_all_leagues">Repeat for All Leagues</label>
                </div>
                <p>If checked, this result sheet will be duplicated for each league in the competition</p>
            </div>







        </div>

        <div class="flex flex-col space-y-4 se-card se-card-body se-card-hover" x-cloak x-show="formula == 'custom'"
            x-data="{
                addGlobal() {
                    this.formula_data.global_variables.push({
                        order: Math.max(...this.formula_data.global_variables.map(v => v.order)),
                        name: '',
                        expression: ''
                    })
                },
            }">
            <h2>Custom Formula</h2>
            <h3>Global Variables</h3>
            <p class="-mt-4">Global variables are generated once per event, and are usable in both local variables and
                the
                result equation. See <a class="link" href="#">here</a> for available functions.
            </p>

            <div class="flex flex-col space-y-2">
                <template x-for="gvar in formula_data.global_variables">
                    <div class="flex flex-row items-center gap-1 ">

                        <input rows="1" cols="2" x-init="dynamicInput($el)" class="badge badge-gray"
                            x-model="gvar.name" placeholder="var"></input>

                        <span class="badge">=</span>

                        <input class="badge badge-info" x-init="dynamicInput($el)" x-model="gvar.expression"
                            placeholder="score * 2">
                    </div>
                </template>

                <button class="se-btn" @click="addGlobal">Add</button>
            </div>


            <br>

            <h3>
                Result Equation
            </h3>
            <p class="-mt-4">This equation is evaluated once per row and produces the final points for the row, which
                is
                then passed to ranking (defaults to highest points wins). You can use the above local and global
                variables
                here. See <a class="link" href="#">here</a> for
                available functions.
            </p>
            <div class="se-form-input">
                <input type="text" x-model="formula_data.equation" placeholder="equation">

            </div>

        </div>

        <br>
        <div>
            <h4>Break Ties</h4>
            <p>Optionally select which events should be used to break ties. If none are selected, ties are allowed</p>
            <div class="grid-4 mt-2">

                <template x-for="event in events.filter(event => event.selected)">
                    <div class="se-card se-card-body px-2! py-1! " :class="event.break_ties ? 'se-card-success' : ''"
                        @click="toggleTieBreak(event)">
                        <div class="flex items-center justify-between">
                            <h4 x-text="event.name"></h4>

                            <p class="text-lg font-bold font-archivo" x-text="event.break_ties"></p>
                        </div>
                    </div>
                </template>
                <small x-show="events.filter(event => event.selected).length == 0" class="col-span-2 text-red-500">
                    Please select one or more events above to allow for tie breaking
                </small>
            </div>
        </div>

        <br>
        <button class=" ml-auto se-btn se-btn-success" @click="submit">Create</button>

    </div>
@endsection
