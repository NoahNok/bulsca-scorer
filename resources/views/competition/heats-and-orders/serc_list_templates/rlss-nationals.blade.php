<div class="grid-4">
    @foreach ($comp->getCompetitionTeams->groupBy('serc_tank') as $ind => $tank)
        <div class="card">
            <h4>Tank {{ $ind }}</h4>
            <ul>
                @foreach ($tank as $competitor)
                    <li>{{ $competitor->formatName() }}</li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
