@extends('layouts.competition')

@section('title')
    {{ $team->getName($comp) }} | {{ $serc->name }}
@endsection



@section('content')
    <div class="grid-3">
        <div class="flex flex-col space-y-4 col-span-2">



            <div class="flex justify-between items-start relative">
                <div>
                    <h2 class="mb-0">{{ $team->getName($comp) }}</h2>
                    <small>SERC: {{ $serc->name }}</small>
                    @php
                        $draw = $serc->getDraw()->whereMorphedTo('entity', $team)->first();
                        $use_tanks = $serc->getCompetition->getScoringSettings->use_tanks;
                    @endphp
                    <small>, {{ ($use_tanks ? "Tank $draw->tank-" : '') . ($draw?->draw ?? 'No Draw') }}</small>
                </div>


            </div>


            <div>
                <div class="  se-table  ">
                    <table editable-table="scores" table-submit-csrf="{{ csrf_token() }}"
                        table-after-url="{{ route('comps.events.sercs.view', [$comp, $serc]) }}"
                        table-submit-url="{{ route('comps.view.events.sercs.editResultsPost', [$comp, $serc, $team]) }}">
                        <thead>
                            <tr>
                                <th scope="col">
                                    Marking Point
                                </th>

                                <th scope="col">
                                    Value
                                </th>




                            </tr>
                        </thead>
                        <tbody>
                            <tr class="">
                                <td colspan="100"
                                    class="bg-black/60 py-4 text-left text-lg px-6  font-medium text-white whitespace-nowrap ">
                                    Disqualification & Penalties</td>
                            </tr>
                            <tr table-row table-row-owner="disqualification" class="bg-white border-b text-right ">
                                <th scope="row">
                                    <span class="pl-4">Disqualification</span>
                                </th>
                                <td class="table-input">
                                    <input class="table-input" table-cell table-cell-name="disqualification"
                                        placeholder="DQ###" x-data x-mask="DQ999" type="text"
                                        value="{{ $serc->getEntityDisqualifications($team)->first()?->code }}">
                                </td>
                            </tr>
                            <tr table-row table-row-owner="penalties" class="bg-white border-b text-right ">
                                <th scope="row">
                                    <span class="pl-4">Penalties</span>
                                </th>
                                <td class="table-input">
                                    <input class="table-input" table-cell table-cell-name="penalties" placeholder="P###"
                                        type="text"
                                        value="{{ $serc->getEntityPenalties($team)->get()->map(fn($pen) => "$pen")->implode(', ') }}">
                                </td>
                            </tr>

                            @forelse ($serc->getJudges as $judge)
                                <tr class="">
                                    <td colspan="100"
                                        class="bg-black/60 py-4 text-left text-lg px-6  font-medium text-white whitespace-nowrap ">
                                        {{ $judge->name }}</td>
                                </tr>

                                @foreach ($judge->getMarkingPoints as $mp)
                                    <tr table-row table-row-owner="{{ $mp->id }}"
                                        class="bg-white border-b text-right ">
                                        <th scope="row">
                                            <span class="pl-4">{{ $mp->name }}</span>
                                        </th>

                                        <td class="table-input">

                                            <input class="table-input" table-cell table-cell-name="score" min="0"
                                                max="10" step=1 placeholder="0-10" type="number"
                                                value="{{ $mp->getScoreForTeam($team) ?: '' }}">

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
            </div>





        </div>

        <div>

            <div class="flex justify-end items-center space-x-3 sticky top-4">
                <a href="{{ route('comps.events.sercs.view', [$comp, $serc]) }}" class="se-btn">Back</a>
                <a href="{{ route('comps.view.events.sercs.next', [$comp, $serc, $team]) }}" class="se-btn">Next Team</a>
                <button class="se-btn se-btn-success " table-submit="scores">Save & Next</button>
            </div>

        </div>


    </div>

@endsection
