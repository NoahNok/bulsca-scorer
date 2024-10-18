@extends('layout')

@section('title')
    {{ $serc->name }} | {{ $comp->name }}
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
        <a
            href="{{ route('comps.view.events.sercs.editResults', [$comp, $serc, $team->id]) }}">{{ $team->getFullname() }}</a>
    </div>
@endsection

@section('content')
    <div class="grid-2">
        <div class="flex flex-col space-y-4">



            <div class="flex justify-between items-start">
                <div>
                    <h2 class="mb-0">{{ $team->getFullname() }}</h2>
                    <small>{{ $serc->name }}</small>
                </div>


            </div>
            <div class="flex justify-end items-center space-x-3">
                <a href="{{ route('comps.view.events.sercs.next', [$comp, $serc, $team]) }}"
                    class="btn btn-white btn-thin">Next</a>
                <button class="btn whitespace-nowrap btn-thin " table-submit="scores">Save & Next</button>
            </div>

            <div class="  relative w-full  ">
                <table editable-table="scores" table-submit-csrf="{{ csrf_token() }}"
                    table-after-url="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}"
                    table-submit-url="{{ route('comps.view.events.sercs.editResultsPost', [$comp, $serc, $team]) }}"
                    class=" editable-table text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
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
                        <tr class="">
                            <td colspan="100" style="background: rgb(156, 163, 175);"
                                class="py-4 text-left text-lg px-6  font-medium text-white whitespace-nowrap ">
                                Disqualification & Penalties</td>
                        </tr>
                        <tr table-row table-row-owner="disqualification" class="bg-white border-b text-right ">
                            <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                                <span class="pl-4">Disqualification</span>
                            </th>
                            <td class="">
                                <input class="table-input" table-cell table-cell-name="disqualification" placeholder="DQ###"
                                    x-data x-mask="DQ999" type="text" value="{{ $serc->getTeamDQ($team)?->code }}">
                            </td>
                        </tr>
                        <tr table-row table-row-owner="penalties" class="bg-white border-b text-right ">
                            <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                                <span class="pl-4">Penalties</span>
                            </th>
                            <td class="">
                                <input class="table-input" table-cell table-cell-name="penalties" placeholder="P###"
                                    type="text" value="{{ $serc->getTeamPenalties($team)?->codes }}">
                            </td>
                        </tr>

                        @forelse ($serc->getJudges as $judge)
                            <tr class="">
                                <td colspan="100" style="background: rgb(156, 163, 175);"
                                    class="py-4 text-left text-lg px-6  font-medium text-white whitespace-nowrap ">
                                    {{ $judge->name }}</td>
                            </tr>

                            @foreach ($judge->getMarkingPoints as $mp)
                                <tr table-row table-row-owner="{{ $mp->id }}" class="bg-white border-b text-right ">
                                    <th scope="row"
                                        class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                                        <span class="pl-4">{{ $mp->name }}</span>
                                    </th>

                                    <td class="">

                                        <input class="table-input" table-cell table-cell-name="score" min="0"
                                            max="10" step=1 placeholder="0-10" type="number"
                                            value="{{ $mp->getScoreForTeam($team->id) ?: '' }}">

                                    </td>




                                </tr>
                            @endforeach


                        @empty
                            <tr class="bg-white border-b text-right ">
                                <th colspan="100" scope="row"
                                    class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                                    None
                                </th>
                            </tr>
                        @endforelse



                    </tbody>
                </table>
            </div>


            <div class="flex justify-end items-center space-x-3">
                <a href="{{ route('comps.view.events.sercs.next', [$comp, $serc, $team]) }}"
                    class="btn btn-white btn-thin">Next</a>
                <button class="btn whitespace-nowrap btn-thin " table-submit="scores">Save & Next</button>
            </div>
        </div>


    </div>

@endsection
