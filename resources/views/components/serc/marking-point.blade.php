<div class="flex flex-col space-y-2 border-b pb-4" id="mpcontainer-sample" x-data="{
    half_open: false,

    settings: {
        mode: 'choice',

        min: 0,
        max: 10,
        step: 0.5,
        use_toggle_for_half: true,

        choice: [
            { value: 1, label: 'Poor' },
            { value: 3, label: 'Bad' },
            { value: 5, label: 'Good' },
            { value: 7, label: 'Very Good' },
            { value: 10, label: 'Excellent' },
            // ... more choices based on settings
        ],
    },

    loops: {
        main: [],
        secondary: [],
    },

    generateLoopValues() {
        const values = [];
        const toggled = [];
        for (let i = this.settings.min; i <= this.settings.max; i += this.settings.step) {
            if (i !== 0) {

                if (this.settings.use_toggle_for_half && i % 1 !== 0) {
                    toggled.push(i);
                } else {
                    values.push(i);
                }
            }
        }

        this.loops.main = values;
        this.loops.secondary = toggled;

        return values;
    },

    initialize() {
        // check for a editor parent alpine element that is already lodead and in scope via editor.settings and use those settings instead of the defaults
        if (editor && editor.settings) {
            this.settings = editor.settings;

            // watch for changes in editor.settings and update the local settings accordingly
            this.$watch('editor.settings', () => {
                this.generateLoopValues();
            }, { deep: true });
        }


        this.generateLoopValues();
    }

}" x-init="initialize()">
    <div class="flex justify-between items-center ">
        <p>Sample Name</p>
        <div class="flex items-center justify-center"
            x-show="settings.mode == 'default' && settings.min <= 0 && settings.max >= 0">
            <input type="radio" required class="w-0 h-0 peer" value="0" name="mp-sample" id="mp-sample-0">
            <label for="mp-sample-0"
                class="  flex items-center justify-center px-4 py-0.5 font-semibold  rounded-xs bg-gray-200 text-xs peer-checked:bg-bulsca_red peer-checked:text-white ">
                ZERO
            </label>
        </div>
    </div>


    <div x-show="settings.mode == 'default'">
        <div class="grid grid-cols-5 gap-2 gap-y-4" x-show="!settings.use_toggle_for_half || !half_open">
            <template x-for="i in loops.main" :key="i">
                <div class="flex items-center justify-center">
                    <input type="radio" required class="w-0 h-0 peer" :value="i" name="mp-sample"
                        :id="'mp-sample-' + i">
                    <label :for="'mp-sample-' + i"
                        class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white "
                        x-text="i">

                    </label>
                </div>
            </template>
        </div>

        <div class="grid grid-cols-5 gap-2 gap-y-4" x-show="!settings.use_toggle_for_half || half_open">
            <template x-for="i in loops.secondary" :key="i">
                <div class="flex items-center justify-center">
                    <input type="radio" required class="w-0 h-0 peer" :value="i" name="mp-sample"
                        :id="'mp-sample-' + i">
                    <label :for="'mp-sample-' + i"
                        class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white "
                        x-text="i">

                    </label>
                </div>
            </template>
        </div>






        <div class="flex items-center justify-center mt-2" x-show="settings.use_toggle_for_half">
            <button type="button" class="badge  font-mono! text-black!"
                :class="half_open ? 'bg-bulsca! text-white!' : 'bg-gray-200!'" @click="half_open = !half_open">Toggle
                Half
                Marks</button>
        </div>



        <div class="text-gray-500 pt-2 flex justify-between">
            <small>Min: 0</small><small>Avg: 5</small><small>Max: 10</small>
        </div>
    </div>

    <div x-show="settings.mode == 'choice'" class="grid grid-cols-5 gap-2 gap-y-4">
        <template x-for="(choice, index) in settings.choice" :key="index">
            <div class="flex items-center justify-center">

                <div class="flex items-center justify-center">
                    <input type="radio" required class="w-0 h-0 peer" :value="choice.value" name="mp-sample"
                        :id="'mp-sample-choice-' + index">
                    <label :for="'mp-sample-choice-' + index"
                        class=" h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white "
                        x-text="choice.label">

                    </label>
                </div>
            </div>
        </template>
    </div>
