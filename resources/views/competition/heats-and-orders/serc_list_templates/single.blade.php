@foreach ($comp->getCompetitionTeams as $team)
    <div class="se-btn se-btn-no-hover" style="text-align: left !important;">
        {{ $loop->index + 1 }}. {{ $team->getFullname() }}
    </div>
@endforeach
