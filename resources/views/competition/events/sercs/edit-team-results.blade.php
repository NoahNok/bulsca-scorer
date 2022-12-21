@extends('layout')

@section('title')
{{ $serc->name }} | {{ $comp->name }}
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
    <a href="{{ route('comps.view.events', $comp) }}">Events</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}">{{ $serc->name }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events.sercs.editResults', [$comp, $serc, $team->id]) }}">{{ $team->getFullname() }}</a>
</div>

@endsection

@section('content')
<div class="grid grid-cols-2 gap-6">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between items-start">
            <div>
                <h2 class="mb-0">{{ $team->getFullname() }}</h2>
                <small>{{ $serc->name }}</small>
            </div>

            <button class="btn" table-submit="scores">Save & Next</button>
        </div>

        <div class="  relative w-full  ">
            <table editable-table="scores" table-submit-csrf="{{ csrf_token() }}" table-after-url="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}" table-submit-url="{{ route('comps.view.events.sercs.editResultsPost', [$comp, $serc, $team]) }}" class=" editable-table text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="py-3 px-6 text-left">
                            Marking Point
                        </th>

                        <th scope="col" class="py-3 px-6">
                            Value
                        </th>




                    </tr>
                </thead>
                <tbody>

                    @forelse ($serc->getJudges as $judge)

                    <tr class="">
                        <td colspan="100" style="background: rgb(156, 163, 175);" class="py-4 text-left text-lg px-6  font-medium text-white whitespace-nowrap ">{{ $judge->name }}</td>
                    </tr>

                    @foreach ($judge->getMarkingPoints as $mp)

                    <tr table-row table-row-owner="{{ $mp->id }}" class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            <span class="pl-4">{{ $mp->name }}</span>
                        </th>

                        <td class="">

                            <input class="table-input" table-cell table-cell-name="score" min="0" max="10" step=1 placeholder="0-10" type="number" value="{{ $mp->getScoreForTeam($team->id) ?: '' }}">

                        </td>




                    </tr>
                    @endforeach


                    @empty
                    <tr class="bg-white border-b text-right ">
                        <th colspan="100" scope="row" class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                            None
                        </th>
                    </tr>
                    @endforelse



                </tbody>
            </table>
        </div>



    </div>


</div>

@endsection