<div x-data="{


    name: '{{ $serc?->getName() ?? '' }}',
    type: '{{ $serc?->type ?? 'DRY' }}',
    target: '{{ $serc?->scorable_entity ?? 'team' }}',

    judges: {{ json_encode($configuration) }},

    marking_point_templates: {{ $templates->toJson() }},


    active_marking_point: {
        id: null,
        description: '',
        weight: 1,
        hint: '',
        template_id: null,
    },

    openMarkingPointSettings(judge_index, mp_index) {
        // For now, just open the modal. In the future, we can populate it with the MP's data.
        this.active_marking_point = this.judges[judge_index].marking_points[mp_index];
        this.modals.mp_settings = true;
    },

    createQuill(element, judge_index) {
        const quill = new Quill(element, {
            theme: 'snow',
            placeholder: 'Enter marking hints/help/specification here.',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered', }, { list: 'bullet', }, ],
                ],
            },
        });

        // Set initial content if it exists
        if (this.judges[judge_index].hint) {
            quill.root.innerHTML = this.judges[judge_index].hint;
        }

        quill.on('text-change', () => {
            this.judges[judge_index].hint = quill.root.innerHTML;
            //this.has_changes = true;
        });


    },

    deleted: {
        judges: [],
        marking_points: [],
    },

    async deleteJudge(index) {
        if (!await askConfirm(`Are you sure you want to delete '${this.judges[index].name || 'this judge'}'?`)) {
            return;
        }

        this.deleted.judges.push(this.judges[index].id);

        this.judges.splice(index, 1);
    },

    async deleteMarkingPoint(judge_index, mp_index) {
        if (!await askConfirm(`Are you sure you want to delete '${this.judges[judge_index].marking_points[mp_index].description || 'this marking point'}'?`)) {
            return;
        }

        this.deleted.marking_points.push(this.judges[judge_index].marking_points[mp_index].id);

        this.judges[judge_index].marking_points.splice(mp_index, 1);
    },

    errors: null,

    async save() {

        let payload = {
            name: this.name,
            type: this.type,
            target_entity: this.target,
            judges: this.judges,
            deleted: this.deleted,

        };

        let response = await fetch('{{ $edit ? route('comps.events.sercs.editPost', ['comp' => $serc->competition, 'serc' => $serc->id]) : route('comps.events.sercs.addPost', ['comp' => $comp]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(payload),
        });


        if (response.ok) {

            let data = await response.json();

            this.has_changes = false;

            @if($edit)
            data.id = {{ $serc->id }};
            @endif


            window.location.href = '{{ route('comps.events.sercs.view', ['comp' => $comp, 'serc' => '__serc__']) }}'.replace('__serc__', data.id);

        } else {
            let errorData = await response.json();

            if (errorData?.errors) {
                this.errors = errorData.errors;
                console.error('Validation errors:', this.errors);
            } else {
                showAlert('An error occurred while saving. Please try again.');
            }
        }
    },

    isMarkingPointInvalid(mp) {
        return !mp.description || mp.weight === null || mp.weight === undefined || mp.template_id === null;
    },

    isJudgeInvalid(judge) {
        if (!judge.name) {
            return true;
        }

        for (let mp of judge.marking_points) {
            if (this.isMarkingPointInvalid(mp)) {
                return true;
            }
        }

        return false;
    },

    has_changes: false,

    form: null,

    init() {
        window.addEventListener('beforeunload', (event) => {
            if (this.has_changes) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
    }
}" @change="has_changes=true">


    <div class="flex flex-row justify-between items-center mb-1">
        <h2 class="mb-0">{{ $edit ? 'Edit - ' . $serc->name : 'Create SERC' }}</h2>

        <div class="sticky top-4 flex space-x-2 ">
            <a href="#" class="se-btn">Back</a>
            <button class="se-btn se-btn-success" @click="save">{{ $edit ? 'Update' : 'Create' }}</button>
        </div>
    </div>

    <div class="se-alert-box alert-info alert-thin my-2">
        <p>
            Items outlined in <strong class="text-red-500 font-semibold">red</strong>
            are
            not
            complete,
            click the
            settings icon to edit.
        </p>



    </div>

    <div class="se-alert-box  my-4" x-show="has_changes" x-cloak>
        <h1>Unsaved Changes</h1>
        <p>You have unsaved changes.</p>
    </div>

    <div class="se-alert-box animate-pulse my-4" x-show="errors" x-cloak>
        <h1>Error</h1>
        <ol class=" list-decimal list-inside">

            <template x-for="(errorMessages, field) in errors" :key="field">
                <template x-for="(message, index) in errorMessages" :key="index">
                    <li x-text="message"></li>
                </template>
            </template>


        </ol>
    </div>



    <div class="grid-4">
        <div class="se-form-input col-span-2">
            <label for="">Name</label>
            <input type="text" class="input" placeholder="Name" x-model="name" value="{{ $serc?->name ?? '' }}"
                :class="{ 'border-red-500!  border-dashed': !name }" required>
        </div>

        <div class="se-form-input">
            <label for="">Type</label>
            <select x-model="type">
                <option value="DRY">Dry</option>
                <option value="WET">Wet</option>
            </select>
        </div>
        <div class="se-form-input" style="margin-bottom: 0 !important">
            <label for="">Target</label>

            <select style="margin-bottom: 0 !important" name="target_entity" x-model="target">
                <option value="club">Clubs</option>
                <option value="team">Teams</option>
                <option value="competitor">Competitiors</option>
            </select>
        </div>
    </div>

    <h3>Casualties/Objectives</h3>
    <div class="grid-3 gap-8! mt-3">

        <template x-for="(judge, index) in judges" :key="judge.id || judge.sort_id">
            <div class="border border-black/25 p-4 rounded-md"
                :class="{ 'border-red-500 border-2 border-dashed': isJudgeInvalid(judge) }">
                <div class="flex justify-between items-center">
                    <h4>Casualty/Objective</h4>
                    <div title="Delete Judge" class="flex items-center justify-center  h-full"
                        @click="deleteJudge(index)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 cross">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>

                <x-se-input label="Name" name="name" x-model.text="judge.name" placeholder="Casualty/Objective X"
                    required />

                <div class="se-form-input  mt-3" x-init="createQuill($refs.editor, index)">
                    <label for="">Marking Hints</label>
                    <div id="editor" x-ref="editor"></div>
                </div>

                <br>
                <h4>Marking Points</h4>
                <div class="flex flex-col space-y-2">
                    <template x-for="(mp, mpIndex) in judge.marking_points" :key="mp.id || mp.sort_id">
                        <div class="flex flex-row space-x-2 justify-between items-center rounded-md "
                            :class="{ 'border-2 border-dashed border-red-500 ': isMarkingPointInvalid(mp) }">
                            <div class="w-[75%]">
                                <x-se-input placeholder="Description" name="description" x-model.text="mp.description"
                                    x-bind:placeholder="'Marking Point ' + (mpIndex + 1)" class="" required />
                            </div>
                            <div class="w-[4rem]">
                                <x-se-input placeholder="Weight" name="weight" type="number" min="0"
                                    step="0.5" x-model.number="mp.weight" placeholder="1" class="w-[20cw]"
                                    required />
                            </div>
                            <div class="w-[5%] flex items-center justify-center" title="Marking Point Settings">
                                <div class=" transition-transform cursor-pointer hover:rotate-90 hover:text-se"
                                    @click="openMarkingPointSettings(index, mpIndex)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6"> ">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="w-[5%] flex items-center justify-center" title="Delete Marking Point"
                                @click="deleteMarkingPoint(index, mpIndex)">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cross">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                    </template>
                    <button class="se-btn se-btn-outline-success w-full"
                        @click="judge.marking_points.push({ id: null, description: '', weight: 1, template_id: null, sort_id: Date.now() }); openMarkingPointSettings(index, judge.marking_points.length - 1)">Add
                        Marking
                        Point</button>

                </div>
            </div>
        </template>

        <x-add-card title="Add Casualty/Objective"
            @click="judges.push({
            id: null,
            name: '',
            marking_points: [],
            sort_id: Date.now(),
        })" />







    </div>

    <x-s-e-modal id="mp_settings" title="Marking Point Settings">
        <div x-effect="title = active_marking_point?.description || 'Marking Point Settings'"
            class="flex flex-col space-y-3">


            <div class="flex flex-row space-x-2 justify-between items-center">
                <div class="w-[75%]">
                    <x-se-input placeholder="Description" label="Description" name="description"
                        x-model.text="active_marking_point.description" placeholder="Description" class=""
                        required />
                </div>
                <div class="">
                    <x-se-input placeholder="Weight" label="Weight" name="weight" type="number" min="0"
                        step="0.5" x-model.number="active_marking_point.weight" placeholder="1" class="w-[20cw]"
                        required />
                </div>
            </div>


            <x-se-input x-model.important="active_marking_point.template_id" label="Marking Point Template"
                name="template" type="select" required>

                <option>Please select a template</option>
                <template x-for="template in marking_point_templates" :key="template.id">

                    <option :value="template.id" x-text="template.name"></option>
                </template>
            </x-se-input>


        </div>

    </x-s-e-modal>

</div>
