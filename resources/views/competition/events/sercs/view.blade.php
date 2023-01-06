@extends('layout')

@section('title')
{{ $serc->name }} | {{ $comp->name }}
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
    <a href="{{ route('comps.view.events', $comp) }}">Events</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}">{{ $serc->name }}</a>
</div>

@endsection

@section('content')
<div class="grid-2">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">{{ $serc->name }}</h2>
            <a href="{{ route('comps.view.events.sercs.edit', [$comp, $serc]) }}" class="btn">Edit SERC Setup</a>
        </div>

        <h4>Marked Teams</h4>
        <div class="  relative w-full overflow-x-auto  ">
            <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="py-3 px-6 text-left">
                            Team
                        </th>
                        <th scope="col" class="py-3 px-6">
                            DQ
                        </th>

                        <th scope="col" class="py-3 px-6">
                            Points
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Position
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Results
                        </th>

                    </tr>
                </thead>
                <tbody>

                    @forelse ($serc->getResults() as $result)
                    <tr class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $result->team }}
                        </th>
                        <td class="py-4 px-6">
                            N/A
                        </td>

                        <td class="py-4 px-6">
                            {{ round($result->points) }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $result->place }}
                        </td>
                        <td class="py-4 px-6">
                            <a href="{{ route('comps.view.events.sercs.editResults', [$comp, $serc, $result->tid]) }}" class="btn btn-primary btn-thin">
                                Edit
                            </a>
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

        <h4>All teams</h4>
        <div class="  relative w-full  ">
            <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="py-3 px-6 text-left">
                            Team
                        </th>

                        <th scope="col" class="py-3 px-6">
                            Results
                        </th>

                    </tr>
                </thead>
                <tbody>

                    @forelse ($serc->getTeams() as $team)
                    <tr class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $team->getFullname() }}
                        </th>

                        <td class="py-4 px-6">
                            <a href="{{ route('comps.view.events.sercs.editResults', [$comp, $serc, $team]) }}" class="btn btn-primary btn-thin">
                                Edit
                            </a>
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

    <div class="flex flex-col space-y-4">
        <h2 class="mb-0">Options</h2>
        <div class="card">
            <div class="flex justify-between items-center">
                <strong>Delete SERC</strong>
                <form action="{{ route('comps.view.events.sercs.delete', [$comp, $serc]) }}" onsubmit="return confirm('Are you sure you want to delete this SERC!')" method="post">
                    <input type="hidden" name="sid" value="{{ $serc->id }}">
                    {{ method_field('DELETE') }}
                    @csrf
                    <button class="btn btn-danger">Delete SERC</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection