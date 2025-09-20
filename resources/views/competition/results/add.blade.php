@extends('layouts.competition')

@section('title')
    Add Results Sheet
@endsection


@section('content')
    <div class="grid-2">
        <div class="flex flex-col space-y-4">

            <div class="flex justify-between">
                <h2 class="mb-0">Add Results Sheet</h2>

            </div>
            <p>
                Please specify the weighting for each event
            </p>



            <div class="  se-table  ">
                <table editable-table="teams" table-submit-csrf="{{ csrf_token() }}"
                    table-after-url="{{ route('comps.results.view-schema', [$comp, ':rep:']) }}"
                    table-submit-url="{{ route('comps.results.addPost', $comp) }}" class=" ">
                    <thead>
                        <tr>
                            <th scope="col">
                                Event
                            </th>
                            <th scope="col">
                                Weight
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($comp->getSpeedEvents as $event)
                            <tr table-row table-row-owner="{{ $event->id }}">
                                <th scope="row" class=>
                                    {{ $event->getName() }}
                                </th>
                                <td class="table-input">
                                    <input table-cell table-cell-required table-cell-name="weight" placeholder="1"
                                        type="number">


                                </td>
                                <td class="hidden">
                                    <input class="hidden" table-cell table-cell-required table-cell-name="type"
                                        placeholder="1" type="text" value="{{ $event->getType() }}">
                                </td>




                            </tr>
                        @empty
                        @endforelse
                        @forelse ($comp->getSERCs as $event)
                            <tr table-row table-row-owner="{{ $event->id }}" class="bg-white border-b text-right ">
                                <th scope="row">
                                    {{ $event->getName() }}
                                </th>
                                <td class="table-input">
                                    <input table-cell table-cell-required table-cell-name="weight" placeholder="2"
                                        type="number">


                                </td>
                                <td class="hidden">
                                    <input class="hidden" table-cell table-cell-required table-cell-name="type"
                                        placeholder="1" type="text" value="{{ $event->getType() }}">
                                </td>




                            </tr>
                        @empty
                        @endforelse
                        <tr class="">
                            <td colspan="100"
                                class="bg-black/60 py-4 text-left text-lg px-6  font-medium text-white whitespace-nowrap ">
                                Sheet Name &
                                League</td>
                        </tr>
                        <tr table-row table-row-owner="name">
                            <th scope="row">
                                Name
                            </th>
                            <td class="table-input">
                                <input class="table-input" table-cell table-cell-required table-cell-name="name"
                                    placeholder="Result Sheet Name" type="text" value="">


                            </td>




                        </tr>
                        <tr table-row table-row-owner="league">
                            <th scope="row" class="whitespace-nowrap flex flex-col  ">
                                League

                            </th>
                            <td class="table-input">


                                <select table-cell table-cell-required table-cell-name="league">
                                    <option value="all">All</option>

                                    @foreach ($comp->getLeagues as $league)
                                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                                    @endforeach

                                </select>

                                <!-- <input class="table-input" table-cell table-cell-required table-cell-name="league" placeholder="O, A, B, F, NC, NS" type="text" value=""> -->


                            </td>




                        </tr>


                    </tbody>
                </table>


            </div>


            <button table-submit="teams" class="se-btn se-btn-success mr-auto mt-2">Add</button>


        </div>
    </div>
@endsection
