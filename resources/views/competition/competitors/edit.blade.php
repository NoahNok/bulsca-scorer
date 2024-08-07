@extends('layout')

@section('title')
    Edit Competitors | {{ $comp->name }}
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps') }}">Competitions</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.competitors', $comp) }}">Competitors</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.competitors.edit', $comp) }}">Edit Competitors</a>
    </div>
@endsection



@section('content')
    <div class="grid-3" x-data="{
        data: {{ json_encode($data) }},
        regions: {{ json_encode($regions) }},
        selectedRegion: '',
        selectedBracket: '',
        csrf: '{{ csrf_token() }}',
        hasChanges: false,
    
        save() {
            let fd = new FormData()
            fd.append('json', JSON.stringify(this.regions))
            fd.append('_token', this.csrf)
            fetch('{{ route('comps.view.competitors.save', $comp) }}', {
                method: 'POST',
                body: fd
            }).then(res => {
                if (res.ok) {
                    showSuccess('Saved teams')
                    setTimeout(() => location.href = `{{ route('comps.view.competitors', $comp) }}`, 500)
                } else {
                    showAlert(`Failed to save teams. Check your inputs and try again!`)
                }
            })
        },
    
        getAvailRegions() {
            // get all the region names from te active regions in regions where {name: 'name', brackets: []}
            var selected = this.regions.map(region => region.name)
    
            return this.data.availRegions.filter(region => !selected.includes(region))
        },
    
        addRegion() {
            if (this.selectedRegion === '') return
            this.regions.push({
                name: this.selectedRegion,
                brackets: [],
                fresh: true,
            })
            this.selectedRegion = ''
    
    
        },
    
        // This expects to be passed a region object not a region name
        getAvailRegionBrackets(region) {
    
            console.log(region)
    
            var currentBrackets = region.brackets.map(bracket => bracket.name)
    
            return this.data.availBrackets.filter(bracket => !currentBrackets.includes(bracket.name))
        },
    
        addBracket(region) {
    
    
    
            console.log(region.brackets)
    
            if (this.selectedBracket === '') return
            region.brackets.push({
                name: this.selectedBracket,
                id: this.data.availBrackets.find(b => b.name === this.selectedBracket).id,
                competitors: {
                    club: '',
                    swimmers: this.isPair({ name: this.selectedBracket }) ? [{ name: '' }, { name: '' }] : [{ name: '' }]
                }
            })
            this.selectedBracket = ''
        },
    
        isPair(bracket) {
            return this.data.availBrackets.find(b => b.name === bracket.name).pairs
        },
    
    
    
    }" @change="hasChanges = true">
        <div class="flex flex-col space-y-4 col-span-2">

            <div class="flex justify-between">
                <h2 class="mb-0">Edit Competitors</h2>
                <button @click="save()" class="btn">Save</button>
            </div>
            <p>Editable cells are white!
            </p>


            <div class="space-y-4">

                <template x-for="region in regions" :key="region.name">
                    <div class="card space-y-3" x-data="{
                        collapsed: true,
                    }">
                        <div class="flex items-center justify-between" @click="collapsed=!collapsed">
                            <h3 class="mb-0 text-3xl text-bulsca font-semibold  capitalize" x-text="region.name">

                            </h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 transition-transform"
                                :class="!collapsed ? 'rotate-180' : ''">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>


                        </div>

                        <div class="grid-3" x-show="!collapsed" x-collapse x-init="collapsed = !region?.fresh"> <template
                                x-for="bracket in region.brackets">
                                <div x-show="!bracket.hide">
                                    <div class="flex items-center justify-between">
                                        <h5 x-text="bracket.name"></h5>
                                        <div class="hover:text-bulsca_red transition-colors cursor-pointer"
                                            @click="() => {bracket.competitors.club = ''; bracket.hide = true}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </div>


                                    </div>

                                    <div class="grid grid-cols-2 gap-1 ">

                                        <div class="form-input imb-0 col-span-full">

                                            <input class="input" placeholder="Club" x-model="bracket.competitors.club" />
                                        </div>

                                        <div class="form-input imb-0" :class="!isPair(bracket) ? 'col-span-full' : ''">

                                            <input class="input" placeholder="Swimmer Name"
                                                x-model="bracket.competitors.swimmers[0].name" />
                                        </div>

                                        <template x-if="isPair(bracket)">

                                            <div class="form-input imb-0">

                                                <input class="input" placeholder="Swimmer Name"
                                                    x-model="bracket.competitors.swimmers[1].name" />
                                            </div>
                                        </template>


                                    </div>
                                </div>



                            </template>
                            <div class="card space-y-3">

                                <h3 class="mb-0">Add Bracket</h3>

                                <div class="form-input">
                                    <label for="" class="">Name</label>
                                    <select class="input" x-model="selectedBracket">
                                        <option value="">Please select a bracket</option>
                                        <template x-for="bracket in getAvailRegionBrackets(region)">
                                            <option x-text="bracket.name">
                                        </template>
                                    </select>
                                </div>

                                <button class="btn" x-on:click="addBracket(region)">Add</button>

                            </div>
                        </div>





                    </div>
                </template>

                <div class="card space-y-3">

                    <h3 class="mb-0">Add Region</h3>

                    <div class="form-input">
                        <label for="" class="">Name</label>
                        <select class="input" x-model="selectedRegion">
                            <option value="">Please select a region</option>
                            <template x-for="region in getAvailRegions()">
                                <option x-text="region">
                            </template>
                        </select>
                    </div>

                    <button class="btn" x-on:click="addRegion()">Add</button>

                </div>


            </div>
            <div class=" row-start-1 md:row-start-auto">
                <div class="alert-box alert-warning">
                    <h1>Heat & SERC Order</h1>
                    <p>You will need to <strong>regenerate</strong> the Heat and SERC Order after adding any
                        <strong>new</strong> teams.
                        <br>
                        <strong>Tip:</strong> Only generate the heats and SERC Order after adding all your teams!
                    </p>
                </div>
                <br>
                <div class="alert-box" x-show="hasChanges" style="display: none">
                    <h1>Unsaved Changes</h1>
                    <p>You have <strong>unsaved</strong> changes. You need to click the save button to keep your current
                        changes!
                    </p>
                </div>
            </div>
        </div>
    @endsection
