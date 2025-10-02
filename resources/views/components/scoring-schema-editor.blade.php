 @props(['type', 'route', 'schema' => null, 'org' => null])

 <div class="flex flex-col space-y-4 col-span-2 mt-2" x-data="{
     type: '{{ $type ?? 'event' }}',
 
     data: null,
     errors: {},
     schemas: {{ $org
         ? $org->scoringSchemas->map(function ($schema) {
             return [
                 'id' => $schema->id,
                 'name' => $schema->name,
                 'schema' => $schema->schema,
             ];
         })
         : '[]' }},
 
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
 
     init() {
         this.data = {{ json_encode($schema->schema ?? null) }};
         console.log({{ json_encode($schema->schema ?? null) }})
 
 
 
 
         if (!this.data) {
             this.createNew()
             return
         }
 
         this.data.name = '{{ $schema->name ?? '' }}'
 
         this.stage = 2
 
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
 
         if (!('rank_higher' in this.data)) {
             this.data['rank_higher'] = true
         }
 
         if (!this.data['rank_equation']) {
             this.data['rank_equation'] = ''
         }
 
         if (!('allow_disqualified_to_rank' in this.data)) {
             this.data['allow_disqualified_to_rank'] = false
         }
 
         if (!('group_by' in this.data)) {
             this.data['group_by'] = []
         }
 
         if (!('force_penalty' in this.data)) {
             this.data['force_penalty'] = false
         }
 
 
     },
 
     createNew() {
         this.data = {
             name: '',
             equation: '',
             penalty_func: '',
             global_variables: [],
             local_variables: [],
             auto_penalties: [],
             auto_disqualifications: [],
             rank_higher: true,
             rank_equation: '',
             allow_disqualified_to_rank: false,
             group_by: [],
             force_penalty: false
         }
     },
 
     stage: 0,
 
     selectOrganisation() {
         this.type = 'event'
         this.stage = 1
     },
 
     selectCustom() {
         this.type = 'event'
         this.stage = 2
         this.createNew()
     },
 
     selectSchema(id) {
         let schema = this.schemas.find(s => s.id === id)
         if (!schema) {
             return
         }
 
         this.data = schema.schema
         this.data.name = schema.name
         this.stage = 2
 
         if (!('rank_higher' in this.data)) {
             this.data['rank_higher'] = true
         }
 
         if (!this.data['rank_equation']) {
             this.data['rank_equation'] = ''
         }
 
         if (!('allow_disqualified_to_rank' in this.data)) {
             this.data['allow_disqualified_to_rank'] = false
         }
 
         if (!('group_by' in this.data)) {
             this.data['group_by'] = []
         }
 
         if (!('force_penalty' in this.data)) {
             this.data['force_penalty'] = false
         }
     },
 
     toggleGrouping(item) {
         if (this.data.group_by.includes(item)) {
             // remove it
             this.data.group_by = this.data.group_by.filter(i => i !== item);
         } else {
             // add it
             this.data.group_by.push(item)
         }
     },
 
 
 }">

     <p x-cloak x-show="stage == 0">Select an option to start/restart the scoring setup for this event, you're changes
         will not be saved until
         you click <strong>Save</strong>.</p>
     <div class="grid-2" x-cloak x-show="stage == 0">

         <div class="se-card se-card-hover se-card-body" :class="stage === 1 ? 'se-card-active' : ''"
             @click="selectOrganisation()">
             <div class="flex items-center justify-between h-full">
                 <div>
                     <h4>Clone from Organisation</h3>
                         <p>This will clone the socring setup from one of your organisations defaults</p>
                 </div>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-8">
                     <path stroke-linecap="round" stroke-linejoin="round"
                         d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                 </svg>

             </div>
         </div>
         <div class="se-card se-card-hover se-card-body" @click="selectCustom()">

             <div class="flex items-center justify-between space-x-6">
                 <div>
                     <h4>Custom</h4>
                     <p>Create a custom scoring schema from scratch</p>
                 </div>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-8">
                     <path stroke-linecap="round" stroke-linejoin="round"
                         d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                 </svg>


             </div>

         </div>
     </div>

     <hr class="spacer !mb-6 !mt-4" x-cloak x-show="stage == 0">

     <div x-show="stage != 0" class="flex items-center space-x-4">
         <p>You will not loose any current changes until you save after clicking restart. (Refresh the page to remove
             any changes)</p>
         <button x-cloak class="se-btn se-btn-danger ml-auto" @click="stage = 0">Restart</button>
     </div>


     <div x-cloak x-show="stage == 1">
         <h3>Please select a Schema to clone</h3>

         <div class="grid-4 mt-2">

             <template x-for="schema in schemas">
                 <div class="se-card se-card-hover se-card-body" @click="selectSchema(schema.id)">
                     <h4 x-text="schema.name"></h4>
                 </div>
             </template>







         </div>


     </div>

     <div class="se-form-input mt-2" x-cloak x-show="data != null && type == 'org' && stage == 2">
         <label for="name">Name</label>
         <input type="text" x-model="data.name" placeholder="Scoring Schema Name">
         <small x-show="errors['name']" x-text="errors['name']"></small>
     </div>


     <div class="flex flex-col space-y-4 " x-cloak x-show="data != null && stage == 2 ">
         <h3>Auto Penalty</h3>
         <p class="-mt-4">Automatically apply the given <strong>penalty</strong> code based on the condition.
             Evaluated
             once per row
         </p>

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
         <p class="-mt-4">Automatically apply the given <strong>disqualification</strong> code based on the
             condition.
             Evaluated
             once per row
         </p>

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
         <p class="-mt-4">Global variables are generated once per event, and are usable in both local variables and
             the
             result equation. See <a class="link" href="#">here</a> for available functions.
         </p>

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
         <p class="-mt-4">Local variables are generated once per row and are only available in the result equation.
             See
             <a class="link" href="#">here</a> for available functions.
         </p>

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
         <p class="-mt-4">This equation is evaluated once per row and produces the final points for the row, which
             is
             then passed to ranking (defaults to highest points wins). You can use the above local and global
             variables
             here. See <a class="link" href="#">here</a> for
             available functions.
         </p>
         <div class="se-form-input">
             <input type="text" x-model="data.equation" placeholder="equation">
             <small x-show="errors['equation']" x-text="errors['equation']"></small>
         </div>

         <br>

         <h3>
             Penalty Function
         </h3>
         <p class="-mt-4">This is applied once per row to apply the effect of penalties, it is run before the result
             equation. This expects you to include the original result within the function e.g. <code>result -
                 (penalties
                 * 5)</code>. See <a class="link" href="#">here</a> for
             available functions.
         </p>
         <div class="se-form-input">
             <input type="text" x-model="data.penalty_func" placeholder="equation">
         </div>

         <div class="se-form-input checkbox">
             <label for="rank_higher">Always Run</label>
             <input type="checkbox" id="rank_higher" x-model="data.force_penalty">
             <p>If enabled, the penatly function will run regardless of if the result has a penalty.</p>

         </div>

         <br>

         <h3>
             Grouping
         </h3>
         <p class="-mt-4">Group results by one or more of the below or none at all.
         </p>
         <div class="flex items-center space-x-6">
             <div class="se-form-input checkbox">
                 <label for="grouping_league">League</label>
                 <input type="checkbox" id="grouping_league" @change="toggleGrouping('league')"
                     :checked="data.group_by.includes('league')">

             </div>

             <div class="se-form-input checkbox">
                 <label for="grouping_club">Club</label>
                 <input type="checkbox" id="grouping_club" @change="toggleGrouping('club')"
                     :checked="data.group_by.includes('club')">
             </div>

             <div class="se-form-input checkbox">
                 <label for="grouping_team">Team</label>
                 <input type="checkbox" id="grouping_team" @change="toggleGrouping('team')"
                     :checked="data.group_by.includes('team')">
             </div>
         </div>

         <br>

         <hr class="spacer">
         <h2>Ranking Options</h2>
         <p class="-mt-4">
             The following options apply to ranking, and occur when ranking each item based on its calculated points
         </p>
         <div class="se-form-input">
             <input type="text" x-model="data.rank_equation" placeholder="rank equation">
         </div>

         <div class="flex items-center space-x-6">
             <div class="se-form-input checkbox">
                 <label for="rank_higher">Highest Score Wins</label>
                 <input type="checkbox" id="rank_higher" x-model="data.rank_higher">

             </div>

             <div class="se-form-input checkbox">
                 <label for="rank_disqualified">Rank Disqualified Items</label>
                 <input type="checkbox" id="rank_disqualified" x-model="data.allow_disqualified_to_rank">
             </div>
         </div>

         <button class="se-btn se-btn-success" @click="save">Save</button>


     </div>



 </div>
