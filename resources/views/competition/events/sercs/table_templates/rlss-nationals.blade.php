<div class="se-table">
    <table>
        <thead>
            <tr>
                <th scope="col">
                    Competitor(s) - CLUB (REGION) - League
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
                <tr x-data="{ name: `{{ $result->team . (property_exists($result, 'pair') ? ' & ' . $result->pair : '') . ' - ' . $result->club_name . '(' . $result->club_region . ') - ' . $result->league }}` }" x-show="name.toLowerCase().includes(search.toLowerCase())">
                    <th scope="row">
                        {{ $result->team . (property_exists($result, 'pair') ? ' & ' . $result->pair : '') }} -
                        {{ $result->club_name }} ({{ $result->club_region }}) - {{ $result->league }}
                    </th>
                    <td>
                        {{ $serc->getTeamDQ(\App\Models\CompetitionTeam::find($result->tid))?->code ?: '-' }}
                    </td>
                    <td>
                        {{ round($result->score, 1) }}
                    </td>
                    <td>
                        {{ $result->place }}
                    </td>
                    <td>
                        {{ $result->place }}
                    </td>
                    <td>
                        <a href="{{ route('comps.events.sercs.editResults', [$comp, $serc, $result->tid]) }}"
                            class="se-btn">
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
