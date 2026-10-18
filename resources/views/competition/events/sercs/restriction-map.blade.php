@extends('layouts.competition')

@section('title')
    Restriction Map | {{ $serc->name }}
@endsection

@section('content')
    <div class="grid-3">
        <div class="flex flex-col space-y-4 col-span-3">
            <div>
                <h2 class="mb-0">{{ $serc->name }} - Restriction Map</h2>
                <small>Shows which judge the entity will be marked against.
                    Mapped/Total Entities ({{ count($restrictionMap) }}/{{ $totalEntities }})</small>
            </div>

            @if (!$serc->use_restricted_judges)
                <div class="alert-box alert-info">
                    <p>Restricted judges are not enabled for this SERC. All judges will see all entities.</p>
                </div>
            @endif

            <div class="se-table">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Entity</th>
                            <th scope="col">League</th>
                            <th scope="col">Visible to Judges</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($restrictionMap as $item)
                            <tr>
                                <th>
                                    {{ $item['entity_name'] }}
                                </th>
                                <td>
                                    {{ $item['league_name'] ?? 'No League' }}
                                </td>
                                <td>
                                    @if (empty($item['judges']))
                                        <span class="text-yellow-600">No judges assigned</span>
                                    @else
                                        <div class=" gap-1 ">
                                            @foreach ($item['judges'] as $judge)
                                                <span class="badge badge-info  ">{{ $judge }}</span>
                                            @endforeach
                                        </div>
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
                    <h3 class="mb-0">Judges <-> League</h3>
                    <small>Shows which league(s) each judge can be connected to.</small>
                </div>
                <div class="se-table">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Judge</th>
                                <th scope="col">Restricted to Leagues</th>
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
                                            <span class="text-gray-500">No restrictions (sees all entities)</span>
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
                                    <td colspan="2" class="text-center">No judges found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
