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
            <tr class="bg-white border-b text-right ">
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
