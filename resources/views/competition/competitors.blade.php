@extends('layouts.competition')

@section('title')
    Competitors
@endsection

@section('content')
    <div class=" row-start-1 md:row-start-auto">
        <div class="alert-box alert-warning">
            <h1>Heat & SERC Order</h1>
            <p>You will need to <strong>regenerate</strong> the Heat and SERC Order after adding any
                <strong>new</strong> teams.
                <br>
                <strong>Tip:</strong> Only generate the heats and SERC Order after adding all your teams!
            </p>
        </div>
    </div>

    <br>
    <div class="flex justify-between mb-2">
        <h2 class="mb-0">Competitors ({{ $comp->getCompetitionTeams->count() }})</h2>
        <a href="{{ route('comps.competitors.edit', $comp) }}" table-submit="teams" class="se-btn">Edit</a>
    </div>
    <div class="grid-3">
        <div class="col-span-2">


            <div class="  se-table  ">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">
                                Competitor
                            </th>
                            <th scope="col">
                                Bracket
                            </th>
                            <th scope="col">
                                Club
                            </th>
                            <th scope="col">
                                Region
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($comp->getCompetitionTeams as $team)
                            <tr>
                                <th scope="row">
                                    {{ $team->team }}
                                </th>
                                <td>
                                    {{ $team->getLeague->name }}

                                </td>
                                <td>
                                    {{ $team->getClubName() }}

                                </td>
                                <td>
                                    {{ $team->getClub->region }}

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

        </div>


    </div>
@endsection
