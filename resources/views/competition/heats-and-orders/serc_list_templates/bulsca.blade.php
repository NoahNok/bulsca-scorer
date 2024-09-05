@foreach ($comp->getCompetitionTeams as $team)
    <div class="card">
        {{ $loop->index + 1 }}. {{ $team->getFullname() }}
    </div>
@endforeach
