@extends('layouts.competition')

@section('title')
    (Edit) {{ $serc->name }}
@endsection



@section('content')
    <div x-data="{
        has_changes: false,
    
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
            <h2 class="mb-0">Edit - {{ $serc->name }}</h2>

            <div class="sticky top-4 flex space-x-2 ">
                <a href="{{ route('comps.events.sercs.view', [$comp, $serc]) }}" class="se-btn">Back</a>
                <button class="se-btn se-btn-success" serc-builder-save @click="has_changes=false">Save</button>
            </div>
        </div>

        <p>Welcome to the SERC/Initiative Editor.
            <br>
            <br>
            Please enter all casulaties/objectives indiviudally, along with their marking points and weights.
            <br>
            Marking points <strong>missing</strong> either a description or weight will be ignored when saved!
        </p>
        <br>

        <div>

            <div class="grid-4">
                <div class="se-form-input col-span-1">
                    <label for="">Name</label>
                    <input type="text" class="input" placeholder="Name" serc-builder-name value="{{ $serc->name }}">
                </div>

                <div class="se-form-input">
                    <label for="">Restricted Judge</label>
                    <select serc-builder-restricted-judge>
                        <option value="1" @if ($serc->use_restricted_judges) selected @endif>Yes</option>
                        <option value="0" @if (!$serc->use_restricted_judges) selected @endif>No</option>
                    </select>
                </div>

                <div class="se-form-input">
                    <label for="">Type</label>
                    <select serc-builder-type>
                        <option value="DRY" @if ($serc->type == 'DRY') selected @endif>Dry</option>
                        <option value="WET" @if ($serc->type == 'WET') selected @endif>Wet</option>
                    </select>
                </div>
                <div class="se-form-input" style="margin-bottom: 0 !important">
                    <label for="">Target</label>

                    <select style="margin-bottom: 0 !important" name="target_entity" serc-builder-target>
                        <option value="club" @if ($serc->scorable_entity == 'club') selected @endif>Clubs</option>
                        <option value="team" @if ($serc->scorable_entity == 'team') selected @endif>Teams</option>
                        <option value="competitor" @if ($serc->scorable_entity == 'competitor') selected @endif>Competitiors</option>
                    </select>
                </div>
            </div>

            <div class="grid-3 gap-8! " serc-builder="builder" serc-builder-id="{{ $serc->id }}"
                serc-builder-csrf="{{ csrf_token() }}"
                serc-builder-url="{{ route('comps.view.events.sercs.editPost', [$comp, $serc]) }}"
                serc-builder-after-url="{{ route('comps.events.sercs.view', [$comp, $serc]) }}">

                @foreach ($serc->getJudges as $judge)
                    <div serc-builder-judge serc-builder-judge-id="{{ $judge->id }}"
                        class="border border-black/25 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <h4>Casualty/Objective</h4>
                            <div title="Delete Judge" class="flex items-center justify-center  h-full">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cross"
                                    serc-builder-judge-delete>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                        <div class="se-form-input ">
                            <label for="">Name</label>
                            <input type="text" placeholder="Casualty/Objective X" value="{{ $judge->name }}"
                                serc-builder-judge-name>
                        </div>


                        <div class="seform-input -mt-3">
                            <label for="">Marking Hints</label>
                            <div id="editor" serc-builder-judge-description>
                                {!! $judge->description !!}

                            </div>
                        </div>

                        <br>
                        <h4>Marking Points</h4>
                        <div serc-builder-marking-points class="mp-list">
                            @forelse ($judge->getMarkingPoints as $mp)
                                <div class="flex flex-row space-x-2" serc-builder-marking-point="{{ $mp->id }}">
                                    <div class="se-form-input w-[75%]">
                                        @if ($loop->index == 0)
                                            <label for="">Description</label>
                                        @endif
                                        <input type="text" style="margin-bottom: 0 !important;"
                                            placeholder="Marking Point 1" value="{{ $mp->name }}"
                                            serc-builder-marking-point-desc>
                                    </div>
                                    <div class="se-form-input w-[20%]">
                                        @if ($loop->index == 0)
                                            <label for="">Weight</label>
                                        @endif
                                        <input type="number" step="0.1" style="margin-bottom: 0 !important;"
                                            placeholder="1.0" value="{{ $mp->weight }}"
                                            serc-builder-marking-point-weight>
                                    </div>
                                    <div class="w-[5%] flex items-center justify-center" title="Delete Marking Point">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cross"
                                            serc-builder-marking-point-delete>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-row space-x-2" serc-builder-marking-point="null">
                                    <div class="se-form-input w-[75%]">
                                        <label for="">Description</label>

                                        <input type="text" style="margin-bottom: 0 !important;"
                                            placeholder="Marking Point 1" serc-builder-marking-point-desc>
                                    </div>
                                    <div class="se-form-input w-[20%]">
                                        <label for="">Weight</label>
                                        <input type="number" step="0.1" style="margin-bottom: 0 !important;"
                                            placeholder="1.0" serc-builder-marking-point-weight>
                                    </div>
                                    <div class="w-[5%] flex items-center justify-center" title="Delete Marking Point">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cross"
                                            serc-builder-marking-point-delete>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <button class="se-btn se-btn-outline-success w-full" serc-builder-marking-point-add>Add Marking
                            Point</button>

                        <div serc-builder-judge-restricted-show>
                            <br>
                            <br>
                            @php
                                $selected = $judge->restrictedLeagues;
                            @endphp
                            <div class="se-form-input">
                                <label for="">League Restriction</label>
                                <select name="" id="" serc-builder-judge-restricted multiple>
                                    @foreach ($comp->getLeagues as $league)
                                        <option value="{{ $league->id }}"
                                            @if ($selected->contains($league)) selected @endif>{{ $league->name }}</option>
                                    @endforeach

                                </select>
                                <small>Hold <kbd>CTRL</kbd> or <kbd>⌘</kbd> whilst selecting/deseecting options.
                                    <strong>Select
                                        nothing for
                                        no restriction</strong></small>
                            </div>
                        </div>



                    </div>
                @endforeach

                <div class="se-btn border-green-500! text-green-500 hover:bg-green-500 flex  items-center justify-center"
                    serc-builder-judge-add>

                    <p class="text-2xl font-semibold">Add Casualty/Objective</p>


                </div>

            </div>
        </div>

        <br>
        <br>
    </div>
@endsection
