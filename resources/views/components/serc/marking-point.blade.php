@props(['mp' => null, 'team' => null])

@php
    $containerId = $mp ? $mp->id : 'container';
    $settings = $mp ? $mp->template?->settings : null;
    $mpValue = $mp && $team ? ($mp->getScoreForTeam($team) ?: null) : null;
@endphp

<div class="flex flex-col space-y-2 border-b pb-4" id="mpcontainer-{{ $containerId }}" x-cloak x-show="ready"
    x-data="{
        half_open: {{ $mpValue ? (fmod($mpValue, 1) === 0.5 ? 'true' : 'false') : 'false' }},
    
        ready: false,
        value: {{ $mpValue ?: -1 }},
    
        settings: {
            mode: 'default',
    
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
    
            this.ready = true
    
            return values;
        },
    
        getChoiceColsClass() {
            switch (this.settings.choice?.length ?? 1) {
                case 1:
                    return 'grid-cols-1';
                case 2:
                    return 'grid-cols-2';
                case 3:
                    return 'grid-cols-3';
                case 4:
                    return 'grid-cols-5';
                default:
                    return 'grid-cols-5';
    
            }
        },
    
        initialize() {
            // check for a editor parent alpine element that is already lodead and in scope via editor.settings and use those settings instead of the defaults
            if (typeof editor !== 'undefined' && editor && editor.settings) {
                this.settings = editor.settings;
    
                // watch for changes in editor.settings and update the local settings accordingly
                this.$watch('editor.settings', () => {
                    this.generateLoopValues();
                }, { deep: true });
            }
    
            @if($settings)
            this.settings = {{ json_encode($settings) }}
            @endif
    
    
            this.generateLoopValues();
        }
    
    }" x-init="initialize()">
    <div class="flex justify-between items-center ">
        <p>{{ $mp ? $mp->name : 'Sample Name' }}</p>
        <div class="flex items-center justify-center"
            x-show="settings.mode == 'default' && settings.min <= 0 && settings.max >= 0">
            <input type="radio" required class="w-0 h-0 peer" value="0" name="mp-{{ $containerId }}"
                x-model="value" id="mp-{{ $containerId }}-0" mp-zero-judge="mp-j-{{ $mp->getJudge->id }}">
            <label for="mp-{{ $containerId }}-0"
                class="  flex items-center justify-center px-4 py-0.5 font-semibold  rounded-xs bg-gray-200 text-xs peer-checked:bg-bulsca_red peer-checked:text-white ">
                ZERO
            </label>
        </div>
    </div>


    <div x-show="settings.mode == 'default'">
        <div class="grid grid-cols-5 gap-2 gap-y-4" x-show="!settings.use_toggle_for_half || !half_open">
            <template x-for="i in loops.main" :key="i">
                <div class="flex items-center justify-center">
                    <input type="radio" required class="w-0 h-0 peer" :value="i" x-model="value"
                        name="mp-{{ $containerId }}" :id="'mp-{{ $containerId }}-' + i">
                    <label :for="'mp-{{ $containerId }}-' + i"
                        class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white "
                        x-text="i">

                    </label>
                </div>
            </template>
        </div>

        <div class="grid grid-cols-5 gap-2 gap-y-4" x-show="!settings.use_toggle_for_half || half_open">
            <template x-for="i in loops.secondary" :key="i">
                <div class="flex items-center justify-center">
                    <input type="radio" required class="w-0 h-0 peer" :value="i" x-model="value"
                        name="mp-{{ $containerId }}" :id="'mp-{{ $containerId }}-' + i">
                    <label :for="'mp-{{ $containerId }}-' + i"
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


        @php
            $stats = $mp?->minMaxAvg();
        @endphp

        @if ($stats)
            <div class="text-gray-500 pt-2 flex justify-between">
                <small>Min: {{ $stats->min_result }}</small><small>Avg: {{ $stats->avg_result }}</small><small>Max:
                    {{ $stats->max_result }}</small>
            </div>
        @endif


    </div>

    <div x-show="settings.mode == 'choice'" class="grid  gap-2 gap-y-4" :class="getChoiceColsClass()">
        <template x-for="(choice, index) in settings.choice" :key="index">
            <div class="flex items-center justify-center">

                <div class="flex items-center justify-center">
                    <input type="radio" required class="w-0 h-0 peer" :value="choice.value" x-model="value"
                        name="mp-{{ $containerId }}" :id="'mp-{{ $containerId }}-choice-' + index">
                    <label :for="'mp-{{ $containerId }}-choice-' + index"
                        class=" h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white "
                        x-text="choice.label">

                    </label>
                </div>
            </div>
        </template>
    </div>
</div>
