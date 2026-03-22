@extends('layouts.competition')

@section('title')
    {{ $event->getName() }}
@endsection

@section('breadcrumbs')


@section('content')
    <div class="grid-3 ">
        <div class="flex flex-col space-y-4 col-span-2" x-data="{
            search: '',
        }">
            <div class="flex justify-between">
                <h2 class="mb-0 mt-0">{{ $event->getName() }}</h2>
                <a href="{{ route('comps.events.speeds.editResult', [$comp, $event]) }}" class="se-btn">Edit Results</a>
            </div>

            <div class=" -mt-3 font-archivo  ">
                @if ($event->isComplete())
                    <span class="badge badge-success">Event Complete</span>
                @else
                    <span class="badge badge-danger">Event Incomplete</span>
                @endif

            </div>




            @php
                $conflicts = $heatService->anyTimeAndOofConflicts($event);
            @endphp

            @if (count($conflicts) > 0)
                <div x-data="{
                    conflicts: {{ json_encode($conflicts) }},
                    open_conflict: null,
                
                    openHeatConflictModal(heat_no) {
                        this.open_conflict = heat_no;
                        this.modals.conflictDetailsModal = true;
                
                
                    }
                }">
                    <h4>Time & Order of Finish Conflict(s)</h4>
                    <div class="grid-3">
                        @foreach ($conflicts as $heat_no => $conflict)
                            <div @click="openHeatConflictModal({{ $heat_no }})"
                                class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                                <p class="font-archivo">Heat {{ $heat_no }}</p>

                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="ml-auto size-5 text-red-500 transition-all group-hover:stroke-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>


                            </div>
                        @endforeach
                    </div>

                    <x-s-e-modal id="conflictDetailsModal" title="Time & Order of Finish Conflicts">
                        <div class="se-table" x-data x-init="() => {
                            $watch('open_conflict', (value) => {
                                title = `Heat ${value} Conflicts`;
                            });
                        }">
                            <table>
                                <thead>
                                    <tr>
                                        <th scope="col">Entry</th>
                                        <th scope="col">Lane</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Order of Finish</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="conflict in conflicts[open_conflict]" :key="conflict.lane">
                                        <tr>
                                            <th x-text="conflict.entity_name"></th>
                                            <td x-text="conflict.lane"></td>
                                            <td x-text="conflict.time"></td>
                                            <td x-text="conflict.oof"></td>
                                        </tr>
                                    </template>


                                </tbody>
                            </table>
                        </div>
                        <br>
                        <small>Lanes are in order of finish time</small>

                    </x-s-e-modal>
                </div>
            @endif








            <div class="  relative  overflow-x-hidden max-w-full  ">
                @if ($event->scoringSchema)
                    <div class="se-form-input imb-0 ">
                        <input type="text" table-search placeholder="Search teams" x-model="search">
                    </div>

                    <br>

                    <div class="  tabbed-bar mt-2  ">

                        <a href="{{ url()->current() }}" class="@if (!$activeLeague) active @endif">All</a>
                        @foreach ($comp->getLeagues as $league)
                            <a href="?league={{ $league->id }}"
                                class="@if ($activeLeague?->id == $league->id) active @endif">{{ $league->name }}</a>
                        @endforeach





                    </div>

                    <div class="se-table">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">
                                        Team
                                    </th>
                                    @if ($event->digitalJudgeEnabled)
                                        <th scope="col">
                                            OOF
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
                                    <th scope="col">
                                        Points
                                    </th>
                                    <th scope="col">
                                        Position
                                    </th>

                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $eventHeats = $event->getHeats()->with('oofs')->get();
                                @endphp

                                @forelse ($eventResults as $result)
                                    @if ($result->isCombined())
                                        <tr x-data="{ name: `{{ $result->entity->getName($comp) }}` }"
                                            x-show="name.toLowerCase().includes(search.toLowerCase())">
                                            <th scope="row">
                                                {!! $result->combined->map(fn($item) => $item->entity->getName($comp))->implode('<br>') !!}


                                            </th>
                                            @if ($event->digitalJudgeEnabled)
                                                <td scope="col">



                                                    N/A
                                                </td>
                                            @endif
                                            <td>
                                                <div class="flex justify-end items-center ">
                                                    <div class="border-r-2 pr-2">
                                                        {!! $result->combined->map(fn($item) => App\Models\SpeedResult::prettyTime($item->result))->implode('<br>') !!}
                                                    </div>
                                                    <div class="pl-2">
                                                        {{ App\Models\SpeedResult::prettyTime($result->resolvedResult) }}
                                                    </div>
                                                </div>


                                            </td>


                                            <td>
                                                {!! $result->combined->map(fn($item) => $item->getDisqualificationsString() ?: '-')->implode('<br>') !!}

                                            </td>

                                            @if ($event->hasPenalties())
                                                <td>

                                                    {{ $result->getPenaltiesString() ?: '-' }}

                                                </td>
                                            @endif
                                            <td>
                                                {{ $result->isDisqualified() && !$show_dq_points ? 'DQ' : (round($result->points, 1) ?: '-') }}
                                            </td>
                                            <td>
                                                {{ $result->position }}
                                            </td>

                                        </tr>
                                    @else
                                        <tr x-data="{ name: `{{ $result->entity->getName($comp) }}` }"
                                            x-show="name.toLowerCase().includes(search.toLowerCase())">
                                            <th scope="row">
                                                {{ $result->entity->getName($comp) }}
                                            </th>
                                            @if ($event->digitalJudgeEnabled)
                                                <td scope="col">
                                                    @php
                                                        $h = $eventHeats
                                                            ->where('entity_id', $result->entity->id)
                                                            ->where('entity_type', $result->entity->getMorphClass())
                                                            ->first();
                                                    @endphp
                                                    @if ($h)
                                                        H{{ $h->heat }}L{{ $h->lane }}:
                                                        {{ $h->getOOF($event->id)?->oof ?: '-' }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                            <td>



                                                {{ App\Models\SpeedResult::prettyTime($result->resolvedResult) }}

                                                @if ($result->resolvedResult != $result->result)
                                                    <br>
                                                    <small>
                                                        Was {{ App\Models\SpeedResult::prettyTime($result->result) }}
                                                    </small>
                                                @endif


                                            </td>


                                            <td>
                                                {{ $result->getDisqualificationsString() ?: '-' }}
                                            </td>

                                            @if ($event->hasPenalties())
                                                <td>

                                                    {{ $result->getPenaltiesString() ?: '-' }}

                                                </td>
                                            @endif
                                            <td>
                                                {{ $result->isDisqualified() && !$show_dq_points ? 'DQ' : (round($result->points, 1) ?: '-') }}
                                            </td>
                                            <td>
                                                {{ $result->position }}
                                            </td>

                                        </tr>
                                    @endif


                                @empty
                                    <tr class="empty ">
                                        <th colspan="100" scope="row">
                                            None
                                        </th>
                                    </tr>
                                @endforelse



                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert-box alert-warning ">
                        <h1>No Scoring Setup</h1>
                        <p>You have not setup any scoring rules for this SERC, thus no results could be generated.
                            <br>
                            Please go to the <a href="{{ route('comps.events.speeds.scoring-settings', [$comp, $event]) }}"
                                class="link">scoring settings</a> page to set up scoring for this SERC.
                        </p>
                    </div>
                @endif

            </div>

        </div>

        <div class="flex flex-col space-y-4">

            <div class="sticky top-4">


                <a href="{{ route('comps.events.speeds.edit', [$comp, $event]) }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Edit Event</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </a>


                <form action="{{ route('comps.view.events.speeds.delete', [$comp, $event]) }}"
                    @submit="doConfirm($event, '{{ $event->heats->count() > 0 ? 'Deleting this event will also remove its attached heats!' : 'Are you sure you want to delete this event!' }}')"
                    method="post">
                    <input type="hidden" name="eid" value="{{ $event->id }}">
                    {{ method_field('DELETE') }}
                    @csrf
                    <button target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1 w-full">
                        <p class="font-archivo">Delete Event</p>



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>





                    </button>
                </form>

                <a href="{{ route('comps.events.speeds.scoring-settings', [$comp, $event]) }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Scoring Settings</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>


                </a>



                @if ($event->viewable)
                    <a href="{{ route('comps.view.speeds.hide', [$comp, $event]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Hide event from results</p>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>

                    </a>
                @else
                    <a href="{{ route('comps.view.speeds.hide', [$comp, $event]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <p class="font-archivo">Show event in results</p>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                    </a>
                @endif

                <hr class="spacer">

                <a href="{{ route('comps.view.events.speeds.printResults', [$comp, $event]) }}" target="_blank"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Print results</p>


                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>



                </a>

            </div>


        </div>
    </div>
@endsection
