@extends('layout')

@section('title')
    {{ $event->getName() }} | {{ $comp->name }}
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
        <a href="{{ route('comps.view.events.speeds.view', [$comp, $event]) }}">{{ $event->getName() }}</a>
    </div>
@endsection

@section('content')
    <div class="grid-2">
        <div class="flex flex-col space-y-4">
            <div class="flex justify-between">
                <h2 class="mb-0">{{ $event->getName() }}</h2>
                <a href="{{ route('comps.view.events.speeds.edit', [$comp, $event]) }}" class="btn">Edit</a>
            </div>


            <div class="  relative w-full overflow-x-auto  ">
                <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="py-3 px-6 text-left">
                                Team
                            </th>
                            @if ($event->digitalJudgeEnabled)
                                <th scope="col" class="py-3 px-6">
                                    OOF
                                </th>
                            @endif
                            <th scope="col" class="py-3 px-6">
                                @if ($event->getName() == 'Rope Throw')
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
                            <th scope="col" class="py-3 px-6">
                                Points
                            </th>
                            <th scope="col" class="py-3 px-6">
                                Position
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($event->getResults() as $result)
                            <tr class="bg-white border-b text-right ">
                                <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ $result->team }}
                                </th>
                                @if ($event->digitalJudgeEnabled)
                                    <td scope="col" class="py-3 px-6">
                                        @php
                                            $h = App\Models\Heat::where('competition', $comp->id)
                                                ->where('team', $result->tid)
                                                ->first();
                                        @endphp
                                        H{{ $h->heat }}L{{ $h->lane }}:
                                        {{ $h->getOOF($event->id)?->oof ?: '-' }}
                                    </td>
                                @endif
                                <td class="py-4 px-6">

                                    @if ($result->result < 4)
                                        {{ $result->result }}
                                    @else
                                        @php
                                            $mins = floor($result->result / 60000);
                                            $secs = ($result->result - $mins * 60000) / 1000;
                                        @endphp
                                        {{ sprintf('%02d', $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) }}
                                    @endif


                                </td>


                                <td class="py-4 px-6">
                                    {{ $result->disqualification ?: '-' }}
                                </td>

                                @if ($event->hasPenalties())
                                    <td class="py-3 px-6">


                                        {{ App\Models\Penalty::where('speed_result', $result->id)->get('code')->implode('code', ', ') ?:($result->penalties == 0? '-': '') }}
                                        @if ($event->getName() == 'Swim & Tow' && $result->penalties != 0)
                                            (P900
                                            x{{ $result->penalties - App\Models\Penalty::where('speed_result', $result->id)->count() }})
                                        @endif
                                    </td>
                                @endif
                                <td class="py-4 px-6">
                                    {{ round($result->points) }}
                                </td>
                                <td class="py-4 px-6">
                                    {{ $result->place }}
                                </td>

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

        <div class="flex flex-col space-y-4">
            <h2 class="mb-0">Options</h2>
            <div class="card space-y-4">
                <div class="flex justify-between items-center">
                    <strong>Delete event</strong>
                    <form action="{{ route('comps.view.events.speeds.delete', [$comp, $event]) }}"
                        onsubmit="return confirm('Are you sure you want to delete this event!')" method="post">
                        <input type="hidden" name="eid" value="{{ $event->id }}">
                        {{ method_field('DELETE') }}
                        @csrf
                        <button class="btn btn-danger">Delete Event</button>
                    </form>
                </div>


                <div class="flex justify-between items-center">
                    <strong>DigitalJudge @if ($event->digitalJudgeEnabled)
                            (Enabled)
                        @endif
                    </strong>
                    @if ($event->digitalJudgeEnabled)
                        <a href="{{ route('dj.speedToggle', [$comp, $event]) }}" class="btn btn-danger">Disable</a>
                    @else
                        <a href="{{ route('dj.speedToggle', [$comp, $event]) }}" class="btn">Enable</a>
                    @endif
                </div>

            </div>
        </div>
    </div>

@endsection
