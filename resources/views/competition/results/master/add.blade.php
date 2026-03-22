@extends('layouts.competition')

@section('title')
    Add Master Sheet
@endsection


@section('content')
    <div class="flex flex-col space-y-4" x-data="{
        name: '',
        sum_over: 'position',
        group_on: 'club',
        default_value: 0,
        sheets: {{ $comp->getResultSchemas->map(function ($sheet) {
            return [
                'id' => $sheet->id,
                'name' => $sheet->name,
                'selected' => false,
                'weight' => 1,
            ];
        }) }},
    
        excludes: {
            clubs: {{ $comp->getClubs->map(fn($c) => ['id' => $c->id, 'name' => $c->name]) }}
        },
    
        exclude: [],
    
        toggleExclude(id) {
    
            if (this.exclude.includes(id)) {
                this.exclude = this.exclude.filter(item => item != id)
            } else {
                this.exclude.push(id)
            }
    
        },
    
        async submit() {
            selected_sheets = this.sheets.filter(event => event.selected)
    
            if (this.name == '') {
                showAlert('You must specify a name')
                return
            }
    
            if (selected_sheets.length == 0) {
                showAlert('You must select atleast one sheet')
                return
            }
    
    
    
    
    
            data = {
                name: this.name,
                sum_over: this.sum_over,
                default_value: this.default_value,
                group_on: this.group_on,
                exclude: this.exclude,
                sheets: selected_sheets,
            }
    
    
    
            let response = await fetch('{{ route('comps.results.master.addPost', [$comp]) }}', {
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
                showAlert('Failed to create master sheet(s)')
            }
    
    
        }
    
    }">

        <div class="flex justify-between">
            <h2 class="mb-0">Add Master Sheet</h2>

        </div>
        <p>Master sheets sum either the Position or Points of each item across each sheet. You should specify a suitable
            default for when the given item doesn't exist in the specified sheet.</p>

        <div class="grid-4">
            <div class="se-form-input ">
                <label for="">Name</label>
                <input type="text" placeholder="Name" x-model="name">
            </div>

            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">Group On</label>

                <select style="margin-bottom: 0 !important" name="target_entity" x-model="group_on">
                    <option value="club">Clubs</option>
                    <option value="team" disabled>Teams</option>
                    <option value="competitor" disabled>Competitiors</option>
                </select>
            </div>

            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">Sum Over</label>

                <select style="margin-bottom: 0 !important" x-model="sum_over">
                    <option value="position">Position</option>
                    <option value="totalPoints">Points</option>

                </select>
            </div>

            <div class="se-form-input">
                <label for="">Default Value</label>
                <input type="number" placeholder="0" x-model="default_value">
            </div>


        </div>


        <h3 class="mb-0!">Sheets</h3>
        <p>Select sheets to include, they will become green when selected. You can also specify a sheet weighting.
            (Defaults to 1)</p>
        <div class="grid-4">
            <template x-for="sheet in sheets">
                <div class="se-card se-card-body se-card-hover" :class="sheet.selected ? 'se-card-success' : ''"
                    @click="sheet.selected = !sheet.selected">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-3 items-center">
                            <h4 x-text="sheet.name"></h4>

                        </div>

                        <div class="text-xs! font-semibold font-archivo" x-show="sheet.selected" @click.stop>
                            <label for="">WEIGHT</label>
                            &nbsp;
                            <input type="number"
                                class="w-5 text-right border-b border-gray-500 hover:border-gray-800! outline-none!"
                                name="" min="1" x-model="sheet.weight" id="">
                        </div>
                    </div>
                </div>

            </template>



        </div>

        <div>
            <h4>Exclude</h4>
            <p>You can exclude any items from the result by clicking them here</p>
            <div class="grid-5">
                <template x-for="club in excludes.clubs">
                    <div class="se-card se-card-body se-card-hover px-2! py-2!" @click="toggleExclude(club.id)"
                        :class="exclude.includes(club.id) ? 'se-card-danger' : ''">
                        <span x-text="club.name"></span>
                    </div>

                </template>



            </div>
        </div>











        <br>
        <button class=" ml-auto se-btn se-btn-success" @click="submit">Create</button>

    </div>
@endsection
