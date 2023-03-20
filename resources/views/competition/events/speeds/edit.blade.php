@extends('layout')

@section('title')
(Edit) {{ $event->getName() }} | {{ $comp->name }}
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
    <a href="{{ route('comps.view.events.speeds.view', [$comp, $event]) }}">{{ $event->getName() }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events.speeds.edit', [$comp, $event]) }}">Edit</a>
</div>

@endsection

@section('content')

<div class="grid-2">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">Edit - {{ $event->getName() }}</h2>
            <button table-submit="scores" class="btn">Save</button>
        </div>
        <p>Be aware of milliseconds! If your stopwatch only displays a two digit milliseconds then make sure to multiply the value by 10 before entering!
            <br>
            <br>

            @if ($event->getName() == "Rope Throw")

            <strong>Rope Throw:</strong> Enter a time for all in (00:00.000), otherwise a number between 0-3 for how many.
            @endif
            <br>
        </p>



        <div class="  relative w-full  ">
            <table editable-table="scores" table-submit-csrf="{{ csrf_token() }}" table-after-url="{{ route('comps.view.events.speeds.view', [$comp, $event]) }}" table-submit-url="{{ route('comps.view.events.speeds.editPost', [$comp, $event]) }}" class=" editable-table text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="py-3 px-6 text-left">
                            Team
                        </th>
                        <th scope="col" class="py-3 px-6">
                            @if ($event->getName() == "Rope Throw")
                            Ropes/Time
                            @else
                            Time
                            @endif
                        </th>
                        <th scope="col" class="py-3 px-6">
                            DQ
                        </th>

                        @if ($event->hasPenalties())
                        <th scope="col" class="py-3 px-6">
                            Penalties
                        </th>
                        @endif


                    </tr>
                </thead>
                <tbody>

                    @forelse ($event->getSimpleResults as $result)
                    <tr table-row table-row-owner="{{ $result->id }}" class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $result->getTeam->getFullname() }}
                        </th>
                        <td class="">

                            @if ($event->getName() == "Rope Throw")


                            @if ($result->result < 4) <input class="table-input" table-cell table-cell-name="result" placeholder="Ropes In OR 00:00.000" type="text" value="{{ $result->result }}"> @else @php $mins=floor($result->result / 60000);
                                $secs = (($result->result)-($mins*60000))/1000;
                                @endphp
                                <input class="table-input" table-cell table-cell-name="result" placeholder="00:00.000" type="text" value="{{ $result->result != null ? sprintf("%02d", $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : '' }}">
                                @endif



                                @else
                                @php
                                $mins = floor($result->result / 60000);
                                $secs = (($result->result)-($mins*60000))/1000;
                                @endphp

                                <input class="table-input" table-cell table-cell-name="result" placeholder="00:00.000" type="text" value="{{ $result->result != null ? sprintf("%02d", $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : '' }}">

                                @endif



                        </td>
                        <td class="">

                            <input class="table-input" ts table-cell table-cell-name="disqualification" table-cell-optional placeholder="DQ###" type="text" value="{{ $result->disqualification }}">

                        </td>

                        @if ($event->hasPenalties())
                        <td>
                            <input class="table-input" ts-p table-cell table-cell-name="penalties" table-cell-optional placeholder="P###, P###, etc..." type="text" value="{{ $result->getPenaltiesAsString() }}">
                        </td>
                        @endif


                    </tr>
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


<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    async function run() {
        let opts = await fetch("/dq").then(d => d.json());
        document.querySelectorAll('[ts]').forEach((el) => {
            let settings = {
                maxItems: 1,
                options: opts,
            };
            new TomSelect(el, settings);
        });
        document.querySelectorAll('[ts-p]').forEach((el) => {
            let settings = {

                create: true
            };
            new TomSelect(el, settings);
        });
    }
    //run();
</script>
@endsection