@extends('layouts.competition')

@section('title')
    Create SERC
@endsection



@section('content')
    <div class="flex flex-row justify-between items-center mb-1">
        <h2 class="mb-0">Create SERC</h2>
        <button class="se-btn se-btn-success" serc-builder-save>Create</button>
    </div>

    <p>Welcome to the SERC Editor.
        <br>
        Please enter each casualty individually, if any marking points are a combination of multiple casualties, add the
        marking point to both, with the weight divided by the amount sharing the point.
        <br>(e.g: If 2 casualties share an aftercare marking point with weight 2, create an aftercare marking point for
        both, with a weight of 1 on each!)
        <br><br>
        Casualties/Objectives without names will be automatically named as "Objective #".
        <br>
        Marking points <strong>missing</strong> either a description or weight will be ignored when saved!
    </p>
    <br>

    <div class="grid-4">
        <div class="se-form-input col-span-2">
            <label for="">Name</label>
            <input type="text" placeholder="Name" serc-builder-name>
        </div>
        <div class="se-form-input">
            <label for="">Type</label>
            <select serc-builder-type>
                <option value="DRY">Dry</option>
                <option value="WET">Wet</option>
            </select>
        </div>
        <div class="se-form-input" style="margin-bottom: 0 !important">
            <label for="">Target</label>

            <select style="margin-bottom: 0 !important" name="target_entity" serc-builder-target>
                <option value="club">Clubs</option>
                <option value="team" selected>Teams</option>
                <option value="competitor">Competitiors</option>
            </select>
        </div>
    </div>

    <div class="grid-3 gap-8!" serc-builder="builder" serc-builder-id="null" serc-builder-csrf="{{ csrf_token() }}"
        serc-builder-url="{{ route('comps.events.sercs.addPost', $comp) }}"
        serc-builder-after-url="{{ route('comps.events.sercs.view', [$comp, ':rep:']) }}">
        <div serc-builder-judge sbj-nodelete serc-builder-judge-id="null" class="border border-black/25 p-4 rounded-md">
            <div class="flex justify-between items-center">
                <h4>New Judge</h4>
                <div title="Delete Judge" class="flex items-center justify-center  h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 cross" serc-builder-judge-delete>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <div class="se-form-input">
                <label for="">Name</label>
                <input type="text" placeholder="Judge 1" serc-builder-judge-name>
            </div>


            <div class="se-form-input -mt-3 ">
                <label for="">Marking Hints</label>
                <div id="editor" serc-builder-judge-description>


                </div>
            </div>

            <h5>Marking Points</h5>
            <div serc-builder-marking-points class="mp-list">
                <div class="flex flex-row space-x-2" serc-builder-marking-point="null">
                    <div class="se-form-input w-[75%]">
                        <label for="">Description</label>
                        <input type="text" style="margin-bottom: 0 !important;" placeholder="Marking Point 1"
                            serc-builder-marking-point-desc>
                    </div>
                    <div class="se-form-input w-[20%]">
                        <label for="">Weight</label>
                        <input type="number" step="0.1" style="margin-bottom: 0 !important;" placeholder="1.0"
                            serc-builder-marking-point-weight>
                    </div>
                    <div class="w-[5%] flex items-center justify-center" title="Delete Marking Point">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 cross" serc-builder-marking-point-delete>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>
            <button class="se-btn se-btn-outline-success w-full" serc-builder-marking-point-add>Add Marking Point</button>
        </div>

        <div class="se-btn border-green-500! text-green-500 hover:bg-green-500 flex  items-center justify-center"
            serc-builder-judge-add>

            <p class="text-2xl font-semibold">Add Casualty/Objective</p>


        </div>

    </div>

    <br>
    <br>
@endsection
