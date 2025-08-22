@extends('layouts.competition')

@section('title')
    {{ $event->getName() }}
@endsection

@section('breadcrumbs')


@section('content')

    <div class="grid-3 ">
        <div class="flex flex-col space-y-4 col-span-2" x-data="{
        
        
            data: {
                global_variables: [{
                    order: 0,
                    name: 'test',
                    expression: 'score * 2'
                }],
        
                local_variables: [],
        
                equation: '',
        
                penalty_func: ''
            },
        
            addGlobal() {
                this.data.global_variables.push({
                    order: Math.max(...this.data.global_variables.map(v => v.order)),
                    name: '',
                    expression: ''
                })
            },
        
            addLocal() {
                this.data.local_variables.push({
                    order: Math.max(...this.data.local_variables.map(v => v.order)),
                    name: '',
                    expression: ''
                })
            },
        
            addAutoPen() {
                this.data.auto_penalties.push({
                    code: '',
                    condition: '',
                    amount: ''
                })
            },
        
            addAutoDq() {
                this.data.auto_disqualifications.push({
                    code: '',
                    condition: '',
                    amount: ''
                })
            },
        
        
        
            save() {
        
                this.data.local_variables = this.data.local_variables.filter(v => v.name.trim() != '' && v.expression.trim() != '')
                this.data.global_variables = this.data.global_variables.filter(v => v.name.trim() != '' && v.expression.trim() != '')
        
                this.data.auto_penalties = this.data.auto_penalties.filter(v => v.code.trim() != '' && v.condition.trim() != '' && v.amount.trim() != '')
                this.data.auto_disqualifications = this.data.auto_disqualifications.filter(v => v.code.trim() != '' && v.condition.trim() != '' && v.amount.trim() != '')
        
        
                fetch('{{ $route }}', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.data)
                }).then(resp => resp.json()).then(data => {
                    showSuccess('Saved scoring settings')
                })
            },
        
            init() {
                this.data = {{ json_encode($event->scoringSchema?->schema ?? []) }}
        
                if (!this.data['auto_disqualifications']) {
        
                    this.data['auto_disqualifications'] = []
                }
        
                if (!this.data['auto_penalties']) {
        
                    this.data['auto_penalties'] = []
                }
        
                if (!this.data['local_variables']) {
        
                    this.data['local_variables'] = []
                }
        
                if (!this.data['global_variables']) {
        
                    this.data['global_variables'] = []
                }
        
        
        
        
            },
        
        
        }">
            <div class="flex justify-between">
                <h2 class="mb-0 mt-0">Scoring Settings | {{ $event->getName() }}</h2>

            </div>

            <h3>Auto Penalty</h3>

            <div class="flex flex-col space-y-2">
                <template x-for="apen in data.auto_penalties">
                    <div class="flex flex-row items-center flex-wrap gap-1 ">


                        <input x-init="dynamicInput($el)" class="badge badge-orange   focus:outline-0" x-model="apen.code"
                            placeholder="code"></input>

                        <div>
                            <span class="badge">x</span>

                            <input x-model="apen.amount" x-init="dynamicInput($el)" class="badge badge-info"
                                placeholder="amount">

                        </div>
                        <div>
                            <span class="badge">if</span>

                            <input x-model="apen.condition" x-init="dynamicInput($el)" class="badge badge-info"
                                placeholder="condition">
                        </div>
                    </div>
                </template>

                <button class="se-btn" @click="addAutoPen">Add</button>
            </div>

            <br>

            <h3>Auto Disqualify</h3>

            <div class="flex flex-col space-y-2">
                <template x-for="adq in data.auto_disqualifications">
                    <div class="flex flex-row items-center flex-wrap gap-1 ">


                        <input x-init="dynamicInput($el)" class="badge badge-danger   focus:outline-0" x-model="adq.code"
                            placeholder="code"></input>

                        <div>
                            <span class="badge">x</span>

                            <input x-model="adq.amount" x-init="dynamicInput($el)" class="badge badge-info"
                                placeholder="amount">

                        </div>
                        <div>
                            <span class="badge">if</span>

                            <input x-model="adq.condition" x-init="dynamicInput($el)" class="badge badge-info"
                                placeholder="condition">
                        </div>
                    </div>
                </template>

                <button class="se-btn" @click="addAutoDq">Add</button>
            </div>

            <br>


            <h3>Global Variables</h3>

            <div class="flex flex-col space-y-2">
                <template x-for="gvar in data.global_variables">
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

            <h3>Local Variables</h3>

            <div class="flex flex-col space-y-2">
                <template x-for="lvar in data.local_variables">
                    <div class="flex flex-row items-center gap-1 ">

                        <input rows="1" cols="2" x-init="dynamicInput($el)" class="badge badge-gray"
                            x-model="lvar.name" placeholder="var"></input>

                        <span class="badge">=</span>

                        <input class="badge badge-info" x-init="dynamicInput($el)" x-model="lvar.expression"
                            placeholder="score * 2">
                    </div>
                </template>

                <button class="se-btn" @click="addLocal">Add</button>
            </div>

            <br>

            <h3>
                Result Equation
            </h3>
            <div class="se-form-input">
                <input type="text" x-model="data.equation" placeholder="equation">
            </div>

            <br>

            <h3>
                Penalty Function
            </h3>
            <div class="se-form-input">
                <input type="text" x-model="data.penalty_func" placeholder="equation">
            </div>

            <br>

            <button class="se-btn se-btn-success" @click="save">Save</button>



        </div>

        <div class="flex flex-col space-y-4">

            <div class="sticky top-4">



                <a href="{{ $returnRoute }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Back to event</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </a>




            </div>


        </div>


    </div>



@endsection
