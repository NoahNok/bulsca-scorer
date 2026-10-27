@extends('layouts.competition')

@section('title')
    (Edit) {{ $event->getName() }}
@endsection

@section('content')

    <div class="grid-3">
        <div class="flex flex-col space-y-4 col-span-2">

            <div class="flex justify-between">
                <h2 class="mb-0">Edit - {{ $event->getName() }}</h2>

            </div>
            <p>
                Enter
                <strong>DNF/DNS</strong> as required!

                @if ($event->getName() == 'Rope Throw')
                    <br>
                    <strong>Rope Throw:</strong> Enter a time for all in (00:00.000), otherwise a number between 0-3 for how
                    many.
                @endif
                <br>
            </p>

            @php
                $allResults = $event->getRawResults(true);
                $hasHeats = $heatFilters->isNotEmpty();
                $hasPoolToggle = $poolFilters->count() > 1;
                $heatFor = fn($result) => $heatLookup->get($result->entity->getMorphClass() . ':' . $result->entity->id);

                if ($hasHeats) {
                    // Order by heat then lane, with entities that have no heat at the end
                    usort($allResults, function ($a, $b) use ($heatFor) {
                        $ha = $heatFor($a);
                        $hb = $heatFor($b);

                        return [$ha?->pool ?? 0, $ha?->heat ?? PHP_INT_MAX, $ha?->lane ?? PHP_INT_MAX] <=>
                            [$hb?->pool ?? 0, $hb?->heat ?? PHP_INT_MAX, $hb?->lane ?? PHP_INT_MAX];
                    });
                }
            @endphp

            <div class="  relative w-full  ">
                <div class="se-form-input imb-0 ">
                    <input type="text" table-search placeholder="Search teams">
                </div>

                <br>

                @if ($hasPoolToggle)
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="font-semibold font-archivo text-sm mr-1">Pool</span>
                        @foreach ($poolFilters as $pool)
                            <button type="button" table-pool-filter="scores" data-pool="{{ $pool->id }}"
                                class="se-btn {{ $loop->first ? 'se-btn-selected' : '' }}">{{ $pool->name }}</button>
                        @endforeach
                    </div>
                @endif

                @if ($hasHeats)
                    <div class="flex flex-wrap gap-2 mb-2">
                        <button type="button" table-heat-filter="scores" data-heat=""
                            class="se-btn se-btn-selected">All</button>
                        @foreach ($heatFilters as $heatKey => $heatFilter)
                            <button type="button" table-heat-filter="scores" data-heat="{{ $heatKey }}"
                                data-pool="{{ $heatFilter['pool'] }}" class="se-btn">{{ $heatFilter['label'] }}</button>
                        @endforeach
                    </div>
                @endif

                <p class="font-semibold font-archivo text-sm text-right w-full"><span
                        table-visible-count="scores">{{ count($allResults) }}</span> results</p>

                <div class="se-table ">
                    <table editable-table="scores" table-submit-csrf="{{ csrf_token() }}"
                        table-after-url="{{ route('comps.events.speeds.view', [$comp, $event]) }}"
                        table-submit-url="{{ route('comps.view.events.speeds.editResultPost', [$comp, $event]) }}"
                        class="  ">
                        <thead>
                            <tr>
                                <th scope="col">
                                    Team
                                </th>
                                @if ($hasHeats)
                                    <th scope="col" class="w-0 whitespace-nowrap">
                                        H/L
                                    </th>
                                @endif
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

                            @forelse ($allResults as $result)
                                @php
                                    $heat = $heatFor($result);
                                @endphp
                                <tr table-row table-row-owner="{{ $result->id }}"
                                    data-heat="{{ $heat ? \App\Models\Orders\Heat::keyFor($heat->pool, $heat->heat) : '' }}"
                                    data-pool="{{ $heat ? ($heat->pool ?? 0) : '' }}"
                                    data-lane="{{ $heat?->lane }}">
                                    <th scope="row">
                                        {{ $result->entity->getName($comp) }}
                                    </th>
                                    @if ($hasHeats)
                                        <td class="whitespace-nowrap">
                                            {{ $heat ? \App\Models\Pool::heatLaneLabel($heat->getPool, $heat->heat, $heat->lane) : '-' }}
                                        </td>
                                    @endif
                                    <td class="table-input">
                                        @php
                                            $initialDqStr = $result->getDisqualificationsString();
                                            $dqStr = $initialDqStr;
                                            $hasSpecialDq = false;

                                            if ($dqStr == 'OOT' || $dqStr == 'DNF' || $dqStr == 'DNS') {
                                                $dqStr = null;
                                                $hasSpecialDq = true;
                                            }

                                        @endphp
                                        @if ($hasSpecialDq)
                                            @php

                                                $code = $initialDqStr;

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
                                            value="{{ $dqStr }}">

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


    <link href="/vendor/tom-select@2.2.2/tom-select.css" rel="stylesheet">
    <script src="/vendor/tom-select@2.2.2/tom-select.complete.min.js"></script>
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

    @if ($hasHeats)
        <script>
            // Pool toggle and heat filter: hide rows via inline display so they stack with the table search
            // (which uses the hidden attribute). With pools, only one pool's rows and heat chips show at a time.
            // Rows with no heat show in every pool so they can't get lost.
            (() => {
                const table = document.querySelector("[editable-table='scores']");
                const rows = [...table.querySelectorAll("[table-row]")];
                const poolButtons = [...document.querySelectorAll("[table-pool-filter='scores']")];
                const heatButtons = [...document.querySelectorAll("[table-heat-filter='scores']")];
                const counter = document.querySelector("[table-visible-count='scores']");
                const search = document.querySelector("[table-search]");

                let pool = poolButtons[0]?.dataset.pool ?? "";
                let heat = "";

                const inPool = (el) => pool === "" || el.dataset.pool === "" || el.dataset.pool === pool;

                const updateCount = () => {
                    counter.textContent = rows.filter((row) => !row.hidden && row.style.display !== "none").length;
                };

                const apply = () => {
                    rows.forEach((row) => {
                        const shown = inPool(row) && (heat === "" || row.dataset.heat === heat);
                        row.style.display = shown ? "" : "none";
                    });
                    poolButtons.forEach((btn) => btn.classList.toggle("se-btn-selected", btn.dataset.pool === pool));
                    heatButtons.forEach((btn) => {
                        btn.style.display = btn.dataset.heat === "" || inPool(btn) ? "" : "none";
                        btn.classList.toggle("se-btn-selected", btn.dataset.heat === heat);
                    });
                    updateCount();
                };

                const setPool = (value) => {
                    pool = value;
                    heat = "";
                    apply();
                };

                const setHeat = (value) => {
                    heat = value;
                    apply();
                };

                poolButtons.forEach((btn) => btn.addEventListener("click", () => setPool(btn.dataset.pool)));
                heatButtons.forEach((btn) => btn.addEventListener("click", () => setHeat(btn.dataset.heat)));

                // Search toggles row.hidden in its own handler, so recount after it has run
                search?.addEventListener("input", () => setTimeout(updateCount));

                // If a save fails on a hidden row, switch to its pool and all heats so the error is visible
                new MutationObserver(() => {
                    const invalid = rows.find((row) => row.style.display === "none" && row.querySelector(".invalid"));
                    if (invalid) {
                        pool = poolButtons.length && invalid.dataset.pool !== "" ? invalid.dataset.pool : pool;
                        setHeat("");
                    }
                }).observe(table, { subtree: true, attributes: true, attributeFilter: ["class"] });

                apply();
            })();
        </script>
    @endif
@endsection
