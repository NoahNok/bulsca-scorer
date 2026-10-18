@extends('layouts.competition')

@section('title')
    League Objectives | {{ $serc->name }}
@endsection

@section('content')
    @php
        $problemCount = collect($restrictionMap)->whereNotNull('problem')->count();
    @endphp
    <div class="grid-3">
        <div class="flex flex-col space-y-4 col-span-3">
            <div>
                <h2 class="mb-0">{{ $serc->name }} - League Objectives</h2>
                <small>Shows which Objective each entity will be marked against.
                    Mapped/Total Entities ({{ $totalEntities - $problemCount }}/{{ $totalEntities }})</small>
            </div>

            @if (!$serc->use_restricted_judges)
                <div class="se-alert-box alert-info">
                    <p>League Objectives aren't turned on for this SERC. Judges pick the Objective they mark against.</p>
                </div>
            @elseif ($problemCount > 0)
                <div class="se-alert-box">
                    <p>{{ $problemCount }} {{ $problemCount == 1 ? 'entity has' : 'entities have' }} no single Objective
                        to be marked against. Judges will be asked to get you to fix this before they can mark
                        {{ $problemCount == 1 ? 'it' : 'them' }}.</p>
                </div>
            @endif

            <div class="se-table">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Entity</th>
                            <th scope="col">League</th>
                            <th scope="col">Objective</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($restrictionMap as $item)
                            <tr>
                                <th>
                                    {{ $item['entity_name'] }}
                                </th>
                                <td>
                                    {{ empty($item['league_names']) ? 'No League' : implode(', ', $item['league_names']) }}
                                </td>
                                <td>
                                    @if ($item['problem'] == 'no_objective')
                                        <span class="badge badge-danger">No Objective for this League</span>
                                    @elseif ($item['problem'] == 'several_objectives')
                                        <span class="badge badge-danger">Leagues on more than one Objective</span>
                                    @else
                                        <span class="badge badge-info">{{ $item['objective'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No entities found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <hr>

            <div>
                <div>
                    <h3 class="mb-0">Objectives and Leagues</h3>
                    <small>Shows which League(s) each Objective is marked against.</small>
                </div>
                <div class="se-table">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Objective</th>
                                <th scope="col">Leagues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serc->getJudges as $judge)
                                <tr>
                                    <th>
                                        {{ $judge->name }}
                                    </th>
                                    <td>
                                        @if ($judge->restrictedLeagues->isEmpty())
                                            <span class="badge badge-gray">No Leagues (not used)</span>
                                        @else
                                            <div class="gap-1">
                                                @foreach ($judge->restrictedLeagues as $league)
                                                    <span class="badge badge-info">{{ $league->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No Objectives found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
