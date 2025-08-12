@extends('layouts.competition')

@section('title')
    (Edit) {{ $event->getName() }}
@endsection

@section('breadcrumbs')

@section('content')

    <div class="grid-3">
        <div class="flex flex-col space-y-4 col-span-2">

            <div class="flex justify-between">
                <h2 class="mb-0">Edit - {{ $event->getName() }}</h2>

            </div>
            <p>Be aware of milliseconds! If your stopwatch only displays a two digit milliseconds then make sure to multiply
                the value by 10 before entering!
                <br>
                <br>

                @if ($event->getName() == 'Rope Throw')
                    <strong>Rope Throw:</strong> Enter a time for all in (00:00.000), otherwise a number between 0-3 for how
                    many.
                @endif
                <br>
            </p>



            <div class="  relative w-full  ">
                <div class="se-form-input imb-0 ">
                    <input type="text" table-search placeholder="Search teams">
                </div>

                <br>

                <div class="se-table ">
                    <table editable-table="scores" table-submit-csrf="{{ csrf_token() }}"
                        table-after-url="{{ route('comps.events.speeds.view', [$comp, $event]) }}"
                        table-submit-url="{{ route('comps.view.events.speeds.editPost', [$comp, $event]) }}" class="  ">
                        <thead>
                            <tr>
                                <th scope="col">
                                    Team
                                </th>
                                <th scope="col">
                                    @if ($event->getName() == 'Rope Throw')
                                        Ropes/Time
                                    @else
                                        Time
                                    @endif
                                </th>
                                <th scope="col">
                                    DQ
                                </th>

                                @if ($event->hasPenalties())
                                    <th scope="col">
                                        Penalties
                                    </th>
                                @endif


                            </tr>
                        </thead>
                        <tbody>

                            @forelse ($event->getRawResults(true) as $result)
                                <tr table-row table-row-owner="{{ $result->id }}">
                                    <th scope="row">
                                        {{ $result->entity->getName() }}
                                    </th>
                                    <td class="table-input">

                                        @if (in_array($result->getDisqualificationsString(), ['DQ015', 'DQ004', 'DQ1001']))
                                            @php

                                                $code = match ($result->disqualification) {
                                                    'DQ015' => 'DNF',
                                                    'DQ004' => 'DNS',
                                                    'DQ1001' => 'OOT',
                                                };

                                            @endphp

                                            <input class="table-input" table-cell table-cell-name="result"
                                                placeholder="00:00.00" type="text" x-data
                                                x-mask:dynamic="$input.toUpperCase().startsWith('D') ? 'DNa' : ($input.startsWith('O') ? 'OOT' : '99:99.99')"
                                                value="{{ $code }}">
                                        @else
                                            @if ($event->getName() == 'Rope Throw')
                                                @if ($result->result < 4)
                                                    <input class="table-input" table-cell table-cell-name="result"
                                                        placeholder="Ropes In OR 00:00.00" type="text" x-data
                                                        x-mask:dynamic="$input.startsWith('D') ? 'DNa' : ($input.startsWith('O') ? 'OOT' : '99:99.99')"
                                                        value="{{ $result->result }}">
                                                @else
                                                    @php
                                                        $mins = floor($result->result / 60000);
                                                        $secs = ($result->result - $mins * 60000) / 1000;
                                                    @endphp

                                                    <input class="table-input" table-cell table-cell-name="result"
                                                        placeholder="00:00.00" type="text" x-data
                                                        x-mask:dynamic="$input.startsWith('D') ? 'DNa' : ($input.startsWith('O') ? 'OOT' : '99:99.99')"
                                                        value="{{ $result->result != null ? sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : '' }}">
                                                @endif
                                            @else
                                                @php
                                                    $mins = floor($result->result / 60000);
                                                    $secs = ($result->result - $mins * 60000) / 1000;
                                                @endphp

                                                <input class="table-input" table-cell table-cell-name="result"
                                                    placeholder="00:00.00" type="text" x-data
                                                    x-mask:dynamic="$input.startsWith('D') ? 'DNa' : ($input.startsWith('O') ? 'OOT' : '99:99.99')"
                                                    value="{{ $result->result != null ? sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : '' }}">
                                            @endif
                                        @endif




                                    </td>
                                    <td class="table-input">

                                        <input class="table-input" ts table-cell table-cell-name="disqualification"
                                            table-cell-optional placeholder="DQ###" type="text" x-data
                                            x-mask:dynamic="$input.startsWith('DQ100') ? 'DQ9999' : 'DQ999'"
                                            value="{{ $result->getDisqualificationsString() }}">

                                    </td>

                                    @if ($event->hasPenalties())
                                        <td class="table-input">
                                            <input class="table-input" ts-p table-cell table-cell-name="penalties"
                                                table-cell-optional placeholder="P###, P###, etc..." type="text"
                                                value="{{ $result->getPenaltiesString() }}">
                                        </td>
                                    @endif


                                </tr>
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

        <div class="relative">
            <div class="sticky top-4 flex space-x-2 ">
                <a href="{{ route('comps.events.speeds.view', [$comp, $event]) }}" class="se-btn ml-auto">Back</a>
                <button table-submit="scores" class="se-btn se-btn-success">Save</button>
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
