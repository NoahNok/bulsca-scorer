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

            @forelse ($event->getResults() as $result)
                <tr x-data="{ name: `{{ $result->team }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                    <th scope="row">
                        {{ $result->team }}
                    </th>
                    @if ($event->digitalJudgeEnabled)
                        <td scope="col">
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
                    <td>

                        @php
                            $actualResult =
                                $event->getName() == 'Rope Throw' ? $result->result_penalties : $result->result;
                        @endphp

                        {{ App\Models\SpeedResult::prettyTime($actualResult) }}

                        @if ($actualResult != $result->base_result)
                            <br>
                            <small>
                                Was {{ App\Models\SpeedResult::prettyTime((int) $result->base_result) }}
                            </small>
                        @endif


                    </td>


                    <td>
                        {{ App\Models\SpeedResult::remapDq($result->disqualification) ?: '-' }}
                    </td>

                    @if ($event->hasPenalties())
                        <td>
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
                    <td>
                        {{ round($result->points) }}
                    </td>
                    <td>
                        {{ $result->place }}
                    </td>

                </tr>
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
