@extends('layouts.competition')

@section('title')
    Log
@endsection



@section('content')
    <h2 class="mb-0">Log</h2>
    <br>


    <form action="" method="get">

        <div class="flex justify-between items-center">
            <h5>Filters</h5>
            <button class="se-btn se-btn-success">Apply Filters</button>
        </div>

        <div class="flex flex-col md:flex-row md:space-x-6">
            <div class="se-form-input w-max max-w-full">
                <label for="event-filter">Type</label>
                <select name="filterType" id="event-filter">
                    <option value="">All</option>
                    @foreach ($comp->getSERCs->where('digitalJudgeEnabled', 1) as $serc)
                        <optgroup label="SERC: {{ $serc->getName() }}">
                            @foreach ($serc->getJudges as $judge)
                                <option value="se{{ $judge->id }}" @if (Request::input('filterType') == 'se' . $judge->id) selected @endif>
                                    {{ $judge->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach

                    <optgroup label="Speeds">
                        @foreach ($comp->getSpeedEvents as $speed)
                            <option value="sp{{ $speed->id }}" @if (Request::input('filterType') == 'sp' . $speed->id) selected @endif>
                                {{ $speed->getName() }}</option>
                        @endforeach
                    </optgroup>

                    <optgroup label="DQ/Penalties">
                        <option value="dppending" @if (Request::input('filterType') == 'dppending') selected @endif>
                            Pending</option>
                        <option value="dpaccepted" @if (Request::input('filterType') == 'dpaccepted') selected @endif>
                            Accepted</option>
                        <option value="dprejected" @if (Request::input('filterType') == 'dprejected') selected @endif>
                            Rejected</option>

                    </optgroup>

                </select>
            </div>

            <div class="se-form-input w-max">
                <label for="judge-filter">Judge Name</label>
                <select name="filterJudge" id="judge-filter">
                    <option value="">All</option>
                    @foreach (\App\Models\DigitalJudge\BetterJudgeLog::where('competition', $comp->id)->select('judge_name')->distinct()->get() as $name)
                        <option value="{{ $name->judge_name }}" @if (Request::input('filterJudge') == $name->judge_name) selected @endif>
                            {{ $name->judge_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="se-form-input w-max">
                <label for="team-filter">Team</label>
                <select name="filterTeam" id="team-filter">
                    <option value="">All</option>
                    @foreach ($comp->getCompetitionTeams()->get()->sortBy('team')->sortBy('club') as $team)
                        <option value="{{ $team->id }}" @if (Request::input('filterTeam') == $team->id) selected @endif>
                            {{ $team->getFullname() }}</option>
                    @endforeach
                </select>
            </div>
        </div>


    </form>

    <div class="w-full flex justify-between items-center mt-4">
        {{ $log->links() }}
    </div>
    <div class="flex flex-col space-y-2 mt-2">
        @forelse ($log as $l)
            <x-loggable-item :loggable="$l"></x-loggable-item>

        @empty
            <p class="text-gray-700 indent-4">No judging log's found, if you have set any filters try clearing them!</p>
        @endforelse

        {{ $log->links() }}
    </div>
@endsection
