<table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
        <tr>
            <th scope="col" class="py-3 px-6 text-left">
                Competitor - Club (Region) - League
            </th>

            <th scope="col" class="py-3 px-6">
                Heat
            </th>

            <th scope="col" class="py-3 px-6">

                Time

            </th>

            <th scope="col" class="py-3 px-6">
                DQ
            </th>


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



            @php
                $pair = property_exists($result, 'pair');
            @endphp

            <tr class="bg-white border-b text-right " x-data="{ name: `{{ $result->team . ' - ' . $result->league . ' ' . (property_exists($result, 'pair') ? $result->pair->name : '') }}` }"
                x-show="name.toLowerCase().includes(search.toLowerCase())">
                <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $result->team }} - {{ $result->league }}

                    @if ($pair)
                        <br>
                        {{ $result->pair->name }} - {{ $result->league }}
                    @endif

                </th>
                @if ($result->heat)
                    <td scope="col" class="py-3 px-6">
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


                <td class="py-4 px-6">
                    {{ App\Models\SpeedResult::remapDq($result->disqualification) ?: '-' }}
                    @if ($pair)
                        <br>
                        {{ App\Models\SpeedResult::remapDq($result->disqualification) ?: '-' }}
                    @endif
                </td>


                <td class="py-4 px-6">
                    {{ $result->place }}
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

<form action="" x-data x-ref="bracket_form">
    <div class="form-input mt-2">
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
