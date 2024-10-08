@extends('layout')

@section('title')
    (Edit) {{ $serc->name }} | {{ $comp->name }}
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps') }}">Competitions</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.events', $comp) }}">Events</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}">{{ $serc->name }}</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.events.sercs.edit', [$comp, $serc]) }}">Edit</a>
    </div>
@endsection

@section('content')
    <div class="flex flex-row justify-between items-center mb-1">
        <h2 class="mb-0">Edit - {{ $serc->name }}</h2>
        <button class="btn w-36" serc-builder-save>Save</button>
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
        <div class="form-input col-span-3">
            <label for="">Name</label>
            <input type="text" class="input" placeholder="Name" serc-builder-name value="{{ $serc->name }}">
        </div>

        <div class="form-input">
            <label for="">Type</label>
            <select class="input " style="padding-top: 0.65rem; padding-bottom: 0.65rem" serc-builder-type>
                <option value="DRY" @if ($serc->type == 'DRY') selected @endif>Dry</option>
                <option value="WET" @if ($serc->type == 'WET') selected @endif>Wet</option>
            </select>
        </div>
    </div>

    <div class="grid-3" serc-builder="builder" serc-builder-id="{{ $serc->id }}"
        serc-builder-csrf="{{ csrf_token() }}"
        serc-builder-url="{{ route('comps.view.events.sercs.editPost', [$comp, $serc]) }}"
        serc-builder-after-url="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}">

        @foreach ($serc->getJudges as $judge)
            <div class="card" serc-builder-judge serc-builder-judge-id="{{ $judge->id }}">
                <div class="flex justify-between items-center">
                    <h4>Casualty/Objective</h4>
                    <div title="Delete Judge" class="flex items-center justify-center  h-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-8 h-8 cross" serc-builder-judge-delete>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <div class="form-input imb-0">
                    <label for="">Name</label>
                    <input type="text" placeholder="Judge 1" value="{{ $judge->name }}" serc-builder-judge-name>
                </div>

                <div class="mt-2"></div>
                <div class="form-input">
                    <label for="">Marking Hints</label>
                    <div id="editor" serc-builder-judge-description>
                        {!! $judge->description !!}

                    </div>
                </div>


                <h5>Marking Points</h5>
                <div serc-builder-marking-points class="mp-list">
                    @forelse ($judge->getMarkingPoints as $mp)
                        <div class="flex flex-row space-x-2" serc-builder-marking-point="{{ $mp->id }}">
                            <div class="form-input w-[75%]">
                                @if ($loop->index == 0)
                                    <label for="">Description</label>
                                @endif
                                <input type="text" style="margin-bottom: 0 !important;" placeholder="Marking Point 1"
                                    value="{{ $mp->name }}" serc-builder-marking-point-desc>
                            </div>
                            <div class="form-input w-[20%]">
                                @if ($loop->index == 0)
                                    <label for="">Weight</label>
                                @endif
                                <input type="number" step="0.1" style="margin-bottom: 0 !important;"
                                    placeholder="1.0" value="{{ $mp->weight }}" serc-builder-marking-point-weight>
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
                            <div class="form-input w-[75%]">
                                <label for="">Description</label>

                                <input type="text" style="margin-bottom: 0 !important;" placeholder="Marking Point 1"
                                    serc-builder-marking-point-desc>
                            </div>
                            <div class="form-input w-[20%]">
                                <label for="">Weight</label>
                                <input type="number" step="0.1" style="margin-bottom: 0 !important;"
                                    placeholder="1.0" serc-builder-marking-point-weight>
                            </div>
                            <div class="w-[5%] flex items-center justify-center" title="Delete Marking Point">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cross"
                                    serc-builder-marking-point-delete>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                    @endforelse
                </div>
                <button class="btn" serc-builder-marking-point-add>Add Marking Point</button>
            </div>
        @endforeach

        <div class="card items-center justify-center shadow-xl hover:bg-gray-300 cursor-pointer hover:text-white group transition-colors ease-in-out"
            serc-builder-judge-add>

            <p class="text-2xl font-semibold">Add Casualty/Objective</p>


        </div>

    </div>

    <br>
    <br>
@endsection
