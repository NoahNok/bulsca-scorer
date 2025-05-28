<div class="se-table">
    <table>
        <thead>
            <tr>
                <th scope="col">
                    Competitor - Club (Region) - League
                </th>

                <th scope="col">
                    Heat
                </th>

                <th scope="col">
                    Time
                </th>

                <th scope="col">
                    DQ
                </th>

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



                @php
                    $pair = property_exists($result, 'pair');
                @endphp

                <tr x-data="{ name: `{{ $result->team . ' - ' . $result->league . ' ' . (property_exists($result, 'pair') ? $result->pair->name : '') }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                    <th scope="row">
                        {{ $result->team }} - {{ $result->league }}

                        @if ($pair)
                            <br>
                            {{ $result->pair->name }} - {{ $result->league }}
                        @endif

                    </th>
                    @if ($result->heat)
                        <td scope="col">
                            H{{ sprintf('%02d', $result->heat) }} L{{ $result->lane }}
                        </td>
                    @endif
                    <td class="py-4 px-6 ">

                        @if ($pair)
                            <div class="flex items-center justify-end">
                                <div class="border-r pr-2">
                                    {{ App\Models\SpeedResult::prettyTime($result->base_result) }}
                                    <br>
                                    {{ App\Models\SpeedResult::prettyTime($result->pair->base_result) }}

                                </div>
                                <div class="ml-2">
                                    {{ App\Models\SpeedResult::prettyTime($result->result) }}
                                </div>

                            </div>
                        @else
                            {{ App\Models\SpeedResult::prettyTime($result->result) }}

                            @if ($result->result != $result->base_result)
                                <br>
                                <small>
                                    Was {{ App\Models\SpeedResult::prettyTime((int) $result->base_result) }}
                                </small>
                            @endif
                        @endif



                    </td>


                    <td>
                        {{ App\Models\SpeedResult::remapDq($result->disqualification) ?: '-' }}
                        @if ($pair)
                            <br>
                            {{ App\Models\SpeedResult::remapDq($result->pair->disqualification) ?: '-' }}
                        @endif
                    </td>


                    <td>
                        {{ $result->place }}
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

<form action="" x-data x-ref="bracket_form">
    <div class="se-form-input mt-2">
        <label for="event-bracket">Bracket</label>
        <select name="bracket" id="event-bracket" class="input" @change="$refs.bracket_form.submit()">
            <option value="">All</option>
            @foreach (\App\Models\League::where('scoring_type', 'rlss-nationals')->get() as $bracket)
                <option value="{{ $bracket->id }}" @if (request()->get('bracket') == $bracket->id) selected @endif>
                    {{ $bracket->name }}</option>
            @endforeach
        </select>
    </div>
</form>
