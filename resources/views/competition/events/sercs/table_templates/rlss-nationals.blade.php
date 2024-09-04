<table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
        <tr>
            <th scope="col" class="py-3 px-6 text-left">
                Competitor(s) - CLUB (REGION) - League
            </th>
            <th scope="col" class="py-3 px-6">
                DQ
            </th>
            <th scope="col" class="py-3 px-6">
                Raw Mark
            </th>
            <th scope="col" class="py-3 px-6">
                Points
            </th>
            <th scope="col" class="py-3 px-6">
                Position
            </th>
            <th scope="col" class="py-3 px-6">
                Results
            </th>

        </tr>
    </thead>
    <tbody>

        @forelse ($serc->getResults() as $result)
            <tr class="bg-white border-b text-right " x-data="{ name: `{{ $result->team . (property_exists($result, 'pair') ? ' & ' . $result->pair : '') . ' - ' . $result->club_name . '(' . $result->club_region . ') - ' . $result->league }}` }"
                x-show="name.toLowerCase().includes(search.toLowerCase())">
                <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $result->team . (property_exists($result, 'pair') ? ' & ' . $result->pair : '') }} -
                    {{ $result->club_name }} ({{ $result->club_region }}) - {{ $result->league }}
                </th>
                <td class="py-4 px-6">
                    {{ $serc->getTeamDQ(\App\Models\CompetitionTeam::find($result->tid))?->code ?: '-' }}
                </td>
                <td class="py-4 px-6">
                    {{ round($result->score, 1) }}
                </td>
                <td class="py-4 px-6">
                    {{ $result->place }}
                </td>
                <td class="py-4 px-6">
                    {{ $result->place }}
                </td>
                <td class="py-4 px-6">
                    <a href="{{ route('comps.view.events.sercs.editResults', [$comp, $serc, $result->tid]) }}"
                        class="btn btn-primary btn-thin">
                        Edit
                    </a>
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
