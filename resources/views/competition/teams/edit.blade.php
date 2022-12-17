@extends('layout')

@section('title')
Edit Teams | {{ $comp->name }}
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
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.teams.edit', $comp) }}">Edit Teams</a>
</div>


@endsection

@section('content')
<div class="grid grid-cols-2">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">Edit Teams</h2>
            <button table-submit="teams" class="btn">Save</button>
        </div>
        <p>Editable cells are white!
        </p>



        <div class="  relative w-full  ">
            <table editable-table="teams" table-submit-csrf="{{ csrf_token() }}" table-after-url="{{ route('comps.view.teams', $comp) }}" table-submit-url="{{ route('comps.view.teams.editPost', $comp) }}" class=" editable-table text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
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
                            Swim Tow Time
                        </th>
                        <th scope="col" class="py-3 px-6 text-center">
                            Remove
                        </th>


                    </tr>
                </thead>
                <tbody>

                    @forelse ($comp->getCompetitionTeams as $team)
                    <tr table-row table-row-owner="{{ $team->id }}" class="bg-white border-b text-right ">
                        <th scope="row" class="text-left ">
                            <input class="table-input" style="text-align: left !important" table-cell table-cell-name="club" placeholder="Club" type="text" value="{{ $team->getClubName() }}">
                        </th>
                        <td class="">
                            <input class="table-input" table-cell table-cell-name="team" placeholder="A, B, C, etc..." type="text" value="{{ $team->team }}">


                        </td>
                        <td class="">
                            <select class="table-input" table-cell table-cell-name="league">
                                <option value="null">Please select</option>
                                @foreach (App\Models\League::all() as $league)
                                <option value="{{ $league->id }}" @if ($team->getLeague()->first()->id == $league->id) selected @endif>{{ $league->name }}</option>
                                @endforeach
                            </select>


                        </td>
                        <td class="">
                            <input class="table-input" table-cell table-cell-name="st_time" placeholder="" type="time" value="{{ $team->getSwimTowTimeForDefault() }}">


                        </td>
                        <td>
                            <form action="{{ route('comps.view.teams.delete', $comp) }}" class="w-full flex items-center justify-center" method="post" onsubmit="return confirm('Are you sure you want to delete this team?')">
                                <input type="hidden" name="ctid" class="hidden" value="{{ $team->id }}">
                                {{method_field('DELETE')}}
                                @csrf

                                <button type="submit" class=" hover:text-red-500 transition-colors ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>

                                </button>
                            </form>
                        </td>


                    </tr>
                    @empty
                    <tr class="bg-white border-b text-right ">
                        <th colspan="100" scope="row" class="py-4 px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                            None
                        </th>
                    </tr>
                    @endforelse



                </tbody>
            </table>


        </div>
        <button table-add-row="teams" class="btn">Add</button>
        <br>
        <br>
    </div>
</div>













@endsection