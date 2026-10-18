@props([
    'is_creating' => false,
    'edit' => false,
    'template' => null,
])



<div x-data="{
    editor: {{ $template
        ? json_encode([
            'name' => $template->name,
            'settings' => $template->settings,
        ])
        : json_encode([
            'name' => '',
            'settings' => [
                'mode' => 'default',
                'min' => 0,
                'max' => 10,
                'step' => 0.5,
                'use_toggle_for_half' => true,
                'choice' => [
                    ['value' => 1, 'label' => 'Poor'],
                    ['value' => 3, 'label' => 'Bad'],
                    ['value' => 5, 'label' => 'Good'],
                    ['value' => 7, 'label' => 'Very Good'],
                    ['value' => 10, 'label' => 'Excellent'],
                ],
            ],
        ]) }},

    errors: null,

    async save() {
        if (this.editor.settings.mode == 'default') {
            this.editor.settings.choice = [];
        } else {
            this.editor.settings.min = 0;
            this.editor.settings.max = 10;
            this.editor.settings.step = 1;
            this.editor.settings.use_toggle_for_half = true;
        }

        let payload = {
            name: this.editor.name,
            settings: this.editor.settings,
        };

        console.log('Saving Marking Point Template with payload:', payload);

        let response = await fetch('{{ $edit ? route('admin.serc.marking-point-template.update', ['marking_point' => $template->id]) : route('admin.serc.marking-point-template.store') }}', {
            method: '{{ $edit ? 'PUT' : 'POST' }}',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(payload),
        });

        if (response.ok) {
            window.location.href = '{{ route('admin.serc.marking-point-template.index') }}';
            // Optionally, redirect or show a success message
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

    @if ($edit) async delete_() {
        if (!await askConfirm('Are you sure you want to delete this Marking Point Template? This action cannot be undone.')) {
            return;
        }

        let response = await fetch('{{ route('admin.serc.marking-point-template.destroy', ['marking_point' => $template->id]) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        });

        if (response.ok) {
            window.location.href = '{{ route('admin.serc.marking-point-template.index') }}';
        } else {
            showAlert('An error occurred while deleting. Please try again.');
        }
    }, @endif

    form: null,


}">
    <div class="flex justify-between items-center space-x-2">
        <h2>{{ $edit ? 'Edit Marking Point Template' : 'Create Marking Point Template' }}</h2>

        @if ($edit)
            <button @click="delete_" class="se-btn se-btn-danger ml-auto">Delete Template</button>
        @endif

        <button @click="save()" class="se-btn se-btn-success">{{ $edit ? 'Save Changes' : 'Create Template' }}</button>
    </div>

    <div class="se-alert-box alert-info">
        <h1>Note</h1>
        <ol class=" list-decimal list-inside">
            <li>Weights are specified when creating a SERC.</li>
            <li><kbd>Sample Name</kbd> is a placeholder for the actual marking point name.</li>
        </ol>
    </div>

    <div class="se-alert-box animate-pulse mt-4" x-show="errors" x-cloak>
        <h1>Error</h1>
        <ol class=" list-decimal list-inside">

            <template x-for="(errorMessages, field) in errors" :key="field">
                <template x-for="(message, index) in errorMessages" :key="index">
                    <li x-text="message"></li>
                </template>
            </template>


        </ol>
    </div>

    <br>
    <div>
        <h3>Settings</h3>

        <div class="grid-4">
            <div class="col-span-3">
                <x-se-input label="Marking Point Name" name="name" type="text"
                    placeholder="Marking Point Template Name" x-model.text="editor.name" class="mb-4" required />
            </div>

            <x-se-input label="Mode" name="mode" type="select" class="" x-model.text="editor.settings.mode">
                <option value="default">Default</option>
                <option value="choice">Choice</option>
            </x-se-input>

        </div>

        <br>


        <div class="grid-4" x-show="editor.settings.mode == 'default'" x-collapse x-cloak>
            <x-se-input label="Min Mark" name="min_mark" type="number" x-bind:step="editor.settings.step"
                x-model.number="editor.settings.min" required />
            <x-se-input label="Max Mark" name="max_mark" type="number" x-bind:step="editor.settings.step"
                x-model.number="editor.settings.max" required />
            <x-se-input label="Step" name="step" type="number" min="0.5" step="0.5"
                x-model.number="editor.settings.step" required />
            <x-se-input label="Use Toggle for Half Marks" name="use_toggle_for_half" type="checkbox"
                x-model.bool="editor.settings.use_toggle_for_half" />
        </div>

        <div x-show="editor.settings.mode == 'choice'" x-collapse x-cloak>
            <h3>Choices</h3>
            <template x-for="(choice, index) in editor.settings.choice" :key="index">
                <div class="flex items-center  space-x-2">
                    <x-se-input name="unused" type="text" x-model.text="choice.label" placeholder="Label"
                        required />
                    <x-se-input name="unused" type="number" x-model.number="choice.value" step="0.5" required />


                    <button type="button" class="se-btn se-btn-danger "
                        @click="editor.settings.choice.splice(index, 1)">Remove</button>

                </div>
            </template>
            <button type="button" class="btn mt-2" @click="editor.settings.choice.push({ value: 0, label: '' })">Add
                Choice</button>
        </div>



    </div>

    <br>
    <hr class="spacer">
    <br>


    <h3>Preview</h3>
    <div class="sm:max-w-[70%]  xl:max-w-[50%] 2xl:max-w-[40%] sm:mx-auto">
        <x-serc.marking-point />
    </div>

</div>
