@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Create Result Scoring Schema</h2>
    <p>Create a new scoring schema for caculating overall results with</p>

    <br>


    <div class="flex flex-col space-y-4 " x-data="{
        errors: {},
        formula_data: {
            name: '',
            equation: '',
            global_variables: []
        },
        addGlobal() {
            this.formula_data.global_variables.push({
                order: Math.max(...this.formula_data.global_variables.map(v => v.order)),
                name: '',
                expression: ''
            })
        },
    
        save() {
    
    
            this.formula_data.global_variables = this.formula_data.global_variables.filter(v => v.name.trim() != '' && v.expression.trim() != '')
    
            if (this.formula_data.name.trim() == '') {
                showAlert('Please enter an name')
                return
            }
    
            if (this.formula_data.equation.trim() == '') {
                showAlert('Please enter an equation')
                return
            }
    
    
    
    
            fetch('{{ route('orgs.scoring.result-schema.create.post', $org) }}', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.formula_data)
            }).then(async resp => {
                if (!resp.ok) {
                    let data = await resp.json()
    
                    if (data.errors) {
                        this.errors = data.errors
                    }
                }
                return resp.json()
            }).then(data => {
    
                if (data.url) {
                    window.location.href = data.url
                }
    
                showSuccess('Saved scoring settings')
    
            })
        },
    }">

        <div class="se-form-input mt-2">
            <label for="name">Name</label>
            <input type="text" x-model="formula_data.name" placeholder="Scoring Schema Name">
            <small x-show="errors['name']" x-text="errors['name']"></small>
        </div>


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

        <button class="se-btn se-btn-success" @click="save">Create</button>

    </div>
@endsection
