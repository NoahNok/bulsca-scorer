@extends('layout')

@section('title')
    SERC Tanks | Heats and Orders | {{ $comp->name }}
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
        <a href="{{ route('comps.view.heats', $comp) }}">Heats and Orders</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="#">Edit SERC Tanks</a>
    </div>
@endsection

@section('content')
    <div x-data="{
        tanks: {{ json_encode($comp->getTanks()) }},
        brackets: {{ json_encode($comp->getCompetitorsPerLeague()) }},
    
        selected: [],
    
        get selectedIds() {
            return this.selected.map(b => b.league)
        },
    
        selectBracket(bracket) {
    
            if (this.selected.includes(bracket)) {
                this.selected = this.selected.filter(b => b.league != bracket.league)
                return
            }
            this.selected.push(bracket)
        },
    
        moveToTank(index) {
            if (this.selected.length === 0) return
    
    
    
            this.selected.forEach(s => {
    
                for (var i = 0; i < this.tanks.length; i++) {
                    this.tanks[i] = this.tanks[i].filter(b => !this.selectedIds.includes(b.league))
                }
    
    
    
            })
    
            this.selected.forEach(s => {
                this.tanks[index].push(s)
            })
    
            this.selected = []
        },
    
        get getTankedBrackets() {
            let tanked = []
    
            this.tanks.forEach(tank => {
                tank.forEach(bracket => {
                    tanked.push(bracket.league)
                })
            })
    
            return tanked
        },
    
        addTank() {
            this.tanks.push([])
        },
    
        removeTank(index) {
            this.tanks.splice(index, 1)
        },
    
        save() {
            let assigned = this.tanks.reduce((a, t) => a + t.reduce((a1, b) => a1 + 1, 0), 0)
    
            if (assigned != this.brackets.length) {
                showAlert('Please add all brackets to tanks first!')
                return;
            }
    
            fetch('{{ route('comps.view.serc-order.editTanksPost', $comp) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
    
                },
                body: JSON.stringify(this.tanks)
            }).then(resp => resp.json()).then(
                window.location.href = '{{ route('comps.view.heats', $comp) }}')
        }
    }">

        <div class="flex items-center justify-between">
            <h1>Tanks</h1>
            <button class="btn btn-save" @click="save">Save</button>
        </div>
        <p>Select one or more brackets and then select a tank to add them to it. Click 'Add Tank' to add aditional tanks!
        </p>

        <br>

        <div class=" grid grid-cols-8 gap-3">
            <template x-for="bracket in brackets">
                <div class="card card-extrathin card-row items-center justify-between hover:bg-gray-300 cursor-pointer"
                    x-key="bracket.league" :class="selectedIds.includes(bracket.league) ? '!bg-bulsca text-white' : ''"
                    @click="selectBracket(bracket)" x-show="getTankedBrackets.includes(bracket.league) == false">
                    <p x-text="bracket.name"></p>
                    <p x-text="bracket.count"></p>
                </div>
            </template>

        </div>

        <p>Selected #: <span x-text="selected.reduce((a1, b) => a1 + b.count, 0)"></span></p>

        <br>

        <div class="grid-4">

            <template x-for="(tank, ind) in tanks">
                <div class="card" @click="moveToTank(ind)">
                    <div class="flex  justify-between">
                        <div class="flex flex-col">
                            <h4 class="hmb-0">Tank <span x-text="ind+1"></span></h4>
                            <p class="text-gray-500 text-xs">Total #: <span
                                    x-text="tank.reduce((a1, b) => a1 + b.count, 0)"></span></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-8 h-8 cross" @click="removeTank(ind)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <template x-for="bracket in tank">
                            <div class="card card-extrathin card-row items-center justify-between hover:bg-gray-300 cursor-pointer"
                                x-key="bracket.league"
                                :class="selectedIds.includes(bracket.league) ? '!bg-bulsca text-white' : ''"
                                @click.stop="selectBracket(bracket)">
                                <p x-text="bracket.name"></p>
                                <p x-text="bracket.count"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div class="card items-center justify-center shadow-xl hover:bg-gray-300 cursor-pointer hover:text-white group transition-colors ease-in-out"
                @click="addTank()">

                <p class="text-2xl font-semibold">Add Tank</p>


            </div>

        </div>

        <br>
        <button class="btn btn-danger" @click="tanks=[]">Unassign All</button>


    </div>
@endsection
