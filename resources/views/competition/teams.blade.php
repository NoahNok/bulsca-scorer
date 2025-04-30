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



    <div class="  relative w-1/2  ">
        <table class=" text-sm w-full shadow-md text-left text-gray-500 ">
            <thead class="text-xs bg-black text-white text-right uppercase  font-archivo ">
                <tr>
                    <th scope="col" class="py-3 px-6 text-left">
                        Club
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Team
                    </th>
                    <th scope="col" class="py-3 px-6">
                        League
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Swim and Tow
                    </th>

                </tr>
            </thead>
            <tbody>

                @forelse ($comp->getCompetitionTeams as $team)
                    <tr class="bg-white border-b text-right hover:bg-gray-200 ">
                        <th scope="row"
                            class="py-3 text-left px-6 font-medium text-gray-900 whitespace-nowrap font-archivo ">
                            {{ $team->getClubName() }}
                        </th>
                        <td class="py-3 px-6">
                            {{ $team->team }}
                        </td>
                        <td class="py-3 px-6">
                            {{ $team->getLeague->name }}
                        </td>
                        <td class="py-3 px-6">
                            {{ $team->getSwimTowTime() }}
                        </td>

                    </tr>
                @empty
                    <tr class="bg-white border-b text-right ">
                        <th colspan="100" scope="row"
                            class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                            None
                        </th>
                    </tr>
                @endforelse



            </tbody>
        </table>
    </div>
@endsection
