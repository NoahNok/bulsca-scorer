<div class="se-table">
    <table>
        <thead>
            <tr>
                <th scope="col">
                    Team
                </th>
                <th scope="col">
                    DQ
                </th>
                <th scope="col">
                    Raw Mark
                </th>
                <th scope="col">
                    Points
                </th>
                <th scope="col">
                    Position
                </th>
                <th scope="col">
                    Results
                </th>

            </tr>
        </thead>
        <tbody>

            @forelse ($serc->getResults() as $result)
                <tr x-data="{ name: `{{ $result->team }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                    <th scope="row">
                        {{ $result->team }}
                    </th>
                    <td>
                        {{ $serc->getTeamDQ(\App\Models\CompetitionTeam::find($result->tid))?->code ?: '-' }}
                    </td>
                    <td>
                        {{ round($result->score, 1) }}
                    </td>
                    <td>
                        {{ round($result->points) }}
                    </td>
                    <td>
                        {{ $result->place }}
                    </td>
                    <td>
                        <a href="{{ route('comps.events.sercs.editResults', [$comp, $serc, $result->tid]) }}"
                            class="se-btn text-black">
                            Edit
                        </a>
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
