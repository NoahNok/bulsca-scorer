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
                        @if ($h)
                            H{{ $h->heat }}L{{ $h->lane }}:
                            {{ $h->getOOF($event->id)?->oof ?: '-' }}
                        @else
                            -
                        @endif
                    </td>
                @endif
                <td class="py-4 px-6">

                    @php
                        $actualResult = $event->getName() == 'Rope Throw' ? $result->result_penalties : $result->result;
                    @endphp

                    {{ App\Models\SpeedResult::prettyTime($actualResult) }}

                    @if ($actualResult != $result->base_result)
                        <br>
                        <small>
                            Was {{ App\Models\SpeedResult::prettyTime((int) $result->base_result) }}
                        </small>
                    @endif


                </td>


                <td class="py-4 px-6">
                    {{ $result->disqualification ?: '-' }}
                </td>

                @if ($event->hasPenalties())
                    <td class="py-3 px-6">
                        @php
                            $blank = true;
                        @endphp
                        @if ($result->penalties != 0)
                            {{ App\Models\Penalty::where('speed_result', $result->id)->get('code')->implode('code', ', ') }}
                            @php
                                $blank = false;
                            @endphp
                        @endif
                        @if ($event->getName() == 'Swim & Tow' && $result->{'900_penalties'} != 0)
                            (P900
                            x{{ $result->{'900_penalties'} }})
                            @php
                                $blank = false;
                            @endphp
                        @endif
                        @if ($blank)
                            -
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
