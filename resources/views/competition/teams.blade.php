@extends('layout')

@section('title')
Teams | {{ $comp->name }}
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.teams', $comp) }}">Teams</a>
</div>


@endsection

@section('content')


<div class="grid grid-cols-4 gap-6">
    <div class="flex flex-col space-y-4">
        <div class="flex justify-between">
            <h2 class="mb-0">Teams ({{ $comp->getCompetitionTeams->count() }})</h2>
            <a href="{{ route('comps.view.teams.edit', $comp) }}" table-submit="teams" class="btn">Edit</a>
        </div>

        <div class="  relative w-full  ">
            <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
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
                    <tr class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $team->getClubName() }}
                        </th>
                        <td class="py-4 px-6">
                            {{ $team->team }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $team->getLeague->name }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $team->getSwimTowTime() }}
                        </td>

                    </tr>
                    @empty
                    <tr class="bg-white border-b text-right ">
                        <th colspan="100" scope="row" class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
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