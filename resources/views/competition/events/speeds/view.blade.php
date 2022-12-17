@extends('layout')

@section('title')
{{ $event->getName() }} | {{ $comp->name }}
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
    <a href="{{ route('comps.view.events.speeds.view', [$comp, $event]) }}">{{ $event->getName() }}</a>
</div>

@endsection

@section('content')
<div class="grid grid-cols-2">
    <div class="flex flex-col space-y-4">
        <div class="flex justify-between">
            <h2 class="mb-0">{{ $event->getName() }}</h2>
            <a href="{{ route('comps.view.events.speeds.edit', [$comp, $event]) }}" class="btn">Edit</a>
        </div>


        <div class="  relative w-full  ">
            <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="py-3 px-6 text-left">
                            Team
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Time
                        </th>
                        <th scope="col" class="py-3 px-6">
                            DQ/Penalty
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Points
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Position
                        </th>

                    </tr>
                </thead>
                <tbody>

                    @forelse ($event->getResults() as $result)
                    <tr class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $result->team }}
                        </th>
                        <td class="py-4 px-6">
                            @php
                            $mins = floor($result->result / 60000);
                            $secs = (($result->result)-($mins*60000))/1000;
                            @endphp
                            {{ sprintf("%02d", $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT)}}
                        </td>
                        <td class="py-4 px-6">
                            {{ $result->disqualification ?: 'N/A' }}
                        </td>
                        <td class="py-4 px-6">
                            {{ round($result->points) }}
                        </td>
                        <td class="py-4 px-6">
                            {{ $result->place }}
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