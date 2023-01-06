@extends('layout')

@section('title')
Add Results Sheet | {{ $comp->name }}
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
    <a href="{{ route('comps.view.results', $comp) }}">Results</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.results.add', $comp) }}">Add Results Sheet</a>
</div>


@endsection

@section('content')
<div class="grid-2">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">Add Results Sheet</h2>
            <button table-submit="teams" class="btn">Save</button>
        </div>
        <p>
            Please specify the weighting for each event
        </p>



        <div class="  relative w-full  ">
            <table editable-table="teams" table-submit-csrf="{{ csrf_token() }}" table-after-url="{{ route('comps.results.view-schema', [$comp, ':rep:']) }}" table-submit-url="{{ route('comps.view.results.addPost', $comp) }}" class=" editable-table text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="py-3 px-6 text-left">
                            Event
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Weight
                        </th>
                    </tr>
                </thead>
                <tbody>

                    @forelse ($comp->getSpeedEvents as $event)
                    <tr table-row table-row-owner="{{ $event->id }}" class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap  ">
                            {{ $event->getName() }}
                        </th>
                        <td class="">
                            <input class="table-input" table-cell table-cell-required table-cell-name="weight" placeholder="1" type="number" value="">


                        </td>
                        <td class="hidden">
                            <input class="hidden" table-cell table-cell-required table-cell-name="type" placeholder="1" type="text" value="{{ $event->getType() }}">
                        </td>




                    </tr>
                    @empty

                    @endforelse
                    @forelse ($comp->getSERCs as $event)
                    <tr table-row table-row-owner="{{ $event->id }}" class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap  ">
                            {{ $event->getName() }}
                        </th>
                        <td class="">
                            <input class="table-input" table-cell table-cell-required table-cell-name="weight" placeholder="1" type="number" value="">


                        </td>
                        <td class="hidden">
                            <input class="hidden" table-cell table-cell-required table-cell-name="type" placeholder="1" type="text" value="{{ $event->getType() }}">
                        </td>




                    </tr>
                    @empty

                    @endforelse
                    <tr class="">
                        <td colspan="100" style="background: rgb(156, 163, 175);" class="py-4 text-left text-lg px-6  font-medium text-white whitespace-nowrap ">Sheet Name & League</td>
                    </tr>
                    <tr table-row table-row-owner="name" class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap  ">
                            Name
                        </th>
                        <td class="">
                            <input class="table-input" table-cell table-cell-required table-cell-name="name" placeholder="Result Sheet Name" type="text" value="">


                        </td>




                    </tr>
                    <tr table-row table-row-owner="league" class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap flex flex-col  ">
                            League
                            <small>Overall (O), A League (A), B League (B), Freshers League (F), Non-counting (NC), Non-student (NS)</small>
                        </th>
                        <td class="">
                            <input class="table-input" table-cell table-cell-required table-cell-name="league" placeholder="O, A, B, F, NC, NS" type="text" value="">


                        </td>




                    </tr>


                </tbody>
            </table>


        </div>

        <br>
        <br>
    </div>
</div>













@endsection