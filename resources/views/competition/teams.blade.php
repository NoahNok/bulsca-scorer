@extends('layouts.competition')

@section('title')
    Teams | {{ $comp->name }}
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
        <h2 class="mb-0">Teams ({{ $comp->getCompetitionTeams->count() }})</h2>
        <a href="{{ route('comps.teams.edit', $comp) }}" table-submit="teams" class="se-btn  flex items-center ">Edit</a>
    </div>



    <div class=" se-table  md:w-2/3! ">
        <table>
            <thead>
                <tr>
                    <th scope="col">
                        Club
                    </th>
                    <th scope="col">
                        Team
                    </th>
                    <th scope="col">
                        League
                    </th>
                    <th scope="col">
                        Swim and Tow
                    </th>

                </tr>
            </thead>
            <tbody>

                @forelse ($comp->getCompetitionTeams as $team)
                    <tr>
                        <th scope="row">
                            {{ $team->getClubName() }}
                        </th>
                        <td>
                            {{ $team->team }}
                        </td>
                        <td>
                            {{ $team->getLeague->name }}
                        </td>
                        <td>
                            {{ $team->getSwimTowTime() }}
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
@endsection
