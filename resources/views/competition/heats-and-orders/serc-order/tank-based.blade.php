@extends('layouts.competition')

@section('title')
    Edit SERC Tanks | Heats and Draws
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
                data => {
                    window.location.href = '{{ route('comps.heats', $comp) }}'
                })
        }
    }">

        <div class="flex items-center justify-between">
            <h1>Tanks</h1>
            <button class="se-btn se-btn-success" @click="save">Save</button>
        </div>
        <p>Select one or more brackets and then select a tank to add them to it. Click 'Add Tank' to add aditional tanks!
        </p>

        <br>

        <div class=" grid grid-cols-8 gap-3">
            <template x-for="bracket in brackets">
                <div class=" se-btn flex items-center justify-between" x-key="bracket.league"
                    :class="selectedIds.includes(bracket.league) ? 'se-btn-primary' : ''" @click="selectBracket(bracket)"
                    x-show="getTankedBrackets.includes(bracket.league) == false">
                    <span x-text="bracket.name"></span>
                    <div class="flex items-center "><span x-text="bracket.count"></span><svg
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-[0.875rem]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>

                    </div>
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
                            <p class="text-gray-500 text-xs mb-1">Total #: <span
                                    x-text="tank.reduce((a1, b) => a1 + b.count, 0)"></span></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 cross" @click="removeTank(ind)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <template x-for="bracket in tank">
                            <div class="se-btn flex items-center justify-between" x-key="bracket.league"
                                :class="selectedIds.includes(bracket.league) ? 'se-btn-primary' : ''"
                                @click.stop="selectBracket(bracket)">
                                <p x-text="bracket.name"></p>
                                <div class="flex items-center "><span x-text="bracket.count"></span><svg
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-[0.875rem]">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <div class="se-btn border-green-500! text-green-500 hover:bg-green-500 flex  items-center justify-center"
                @click="addTank()">

                <p class="text-2xl font-semibold">Add Tank</p>


            </div>

        </div>

        <br>
        <button class="btn btn-danger" @click="tanks=[]">Unassign All</button>


    </div>
@endsection
