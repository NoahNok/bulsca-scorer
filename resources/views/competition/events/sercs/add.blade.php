@extends('layout')

@section('title')
Add SERC | {{ $comp->name }}
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events.sercs.add', $comp) }}">Add SERC</a>
</div>

@endsection

@section('content')
<div class="flex flex-row justify-between items-center mb-1">
    <h2 class="mb-0">Add SERC</h2>
    <button class="btn w-36" serc-builder-save>Save</button>
</div>

<p>Judges without names will be automatically named as "Judge #".
    <br>
    Marking points <strong>missing</strong> either a description or weight will be ignored when saved!
</p>
<br>

<div class="form-input">
    <label for="">Name</label>
    <input type="text" placeholder="Name" serc-builder-name>
</div>

<div class="grid-3" serc-builder="builder" serc-builder-id="null" serc-builder-csrf="{{ csrf_token() }}" serc-builder-url="{{ route('comps.view.events.sercs.addPost', $comp) }}" serc-builder-after-url="{{ route('comps.view.events.sercs.view', [$comp, ':rep:']) }}">
    <div class="card" serc-builder-judge sbj-nodelete serc-builder-judge-id="null">
        <div class="flex justify-between items-center">
            <h4>New Judge</h4>
            <div title="Delete Judge" class="flex items-center justify-center  h-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 cross" serc-builder-judge-delete>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </div>
        <div class="form-input">
            <label for="">Name</label>
            <input type="text" placeholder="Judge 1" serc-builder-judge-name>
        </div>
        <h5>Marking Points</h5>
        <div serc-builder-marking-points class="mp-list">
            <div class="flex flex-row space-x-2" serc-builder-marking-point="null">
                <div class="form-input w-[75%]">
                    <label for="">Description</label>
                    <input type="text" style="margin-bottom: 0 !important;" placeholder="Marking Point 1" serc-builder-marking-point-desc>
                </div>
                <div class="form-input w-[20%]">
                    <label for="">Weight</label>
                    <input type="number" step="0.1" style="margin-bottom: 0 !important;" placeholder="1.0" serc-builder-marking-point-weight>
                </div>
                <div class="w-[5%] flex items-center justify-center" title="Delete Marking Point">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 cross" serc-builder-marking-point-delete>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>
        <button class="btn" serc-builder-marking-point-add>Add Marking Point</button>
    </div>

    <div class="card items-center justify-center shadow-xl hover:bg-gray-300 cursor-pointer hover:text-white group transition-colors ease-in-out" serc-builder-judge-add>

        <p class="text-2xl font-semibold">Add Judge</p>


    </div>

</div>

<br>
<br>






@endsection