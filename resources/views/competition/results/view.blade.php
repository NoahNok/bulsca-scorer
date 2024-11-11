@extends('layout')

@section('title')
    {{ $schema->name }} | {{ $comp->name }}
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps') }}">Competitions</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.view.results', [$comp]) }}">Results</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('comps.results.view-schema', [$comp, $schema]) }}">{{ $schema->name }}</a>
    </div>
@endsection

@section('content')
    <div class="grid-2">
        <div class="flex flex-col space-y-4">

            <div class="flex justify-between">
                <h2 class="mb-0">{{ $schema->name }}</h2>

            </div>

            <h4>Results</h4>
            <div class="  relative w-full  ">
                @include(
                    'competition.results.table_templates.' .
                        $comp->scoring_type .
                        (array_key_exists('overalls', $results) ? '-overalls' : ''))
            </div>



        </div>
        <div class="flex flex-col space-y-4">
            <h2 class="mb-0">Options</h2>
            <div class="card space-y-6">
                <div class="flex justify-between items-center">
                    <strong>Delete Results Sheet</strong>
                    <form action="{{ route('comps.view.results.delete', [$comp, $schema->id]) }}"
                        onsubmit="return confirm('Are you sure you want to delete this Results Sheet!')" method="post">
                        <input type="hidden" name="sid" value="{{ $schema->id }}">
                        {{ method_field('DELETE') }}
                        @csrf
                        <button class="btn btn-danger">Delete Results Sheet</button>
                    </form>
                </div>
                <div class="flex justify-between ">
                    <div class="flex flex-col">
                        <strong>Print</strong>
                        <small><strong>Print Places</strong> will print table at the top left of this page showing just the
                            final point total and places. <br><strong>Print Detailed</strong> will print all the events,
                            showing weighted final scores and places as the original "Comp Results" sheet would
                            show!</small>
                    </div>
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('comps.results.view-schema-print-basic', $schema) }}" target="_blank"
                            class="btn">Print
                            Places</a>
                        <a href="{{ route('comps.results.view-schema-print', $schema) }}" target="_blank"
                            class="btn">Print Detailed</a>
                    </div>
                </div>
                @if ($schema->viewable)
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <strong>Hide Results</strong>
                            <small>This will make this result sheet hidden on the public results area!</small>
                        </div>

                        <div>
                            <a href="{{ route('comps.view.results.hide', [$comp, $schema->id]) }}"
                                class="btn btn-danger">Hide Results Sheet</a>
                        </div>

                    </div>
                @else
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <strong>Unhide Results</strong>
                            <small>This will make this result sheet visible on the public results area!</small>
                        </div>

                        <div>
                            <a href="{{ route('comps.view.results.hide', [$comp, $schema->id]) }}" class="btn ">Unhide
                                Results Sheet</a>

                        </div>
                    </div>
                @endif

            </div>
        </div>


    </div>

    <br>

    <div>
        <h3>Weightings</h3>
        <ul>
            @foreach ($schema->getEvents as $event)
                @php
                    if (!$event->getActualEvent) {
                        continue;
                    }
                @endphp
                <li><strong>{{ $event->getActualEvent->getName() }}</strong>: {{ $event->weight }}</li>
            @endforeach
        </ul>
        <br>
        <h3>League</h3>
        <p><strong>Target League</strong>:
            {{ is_numeric($schema->league) ? \App\Models\League::find($schema->league)->name : $schema->league }}</p>
        <small>Overall (O), A League (A), B League (B), Freshers League (F), Non-counting (NC), Non-student (NS)</small>
    </div>

    <br>
    @if ($comp->scoring_type == 'bulsca')
        <div class=" overflow-hidden " id="raw_data">
            <h2>Raw Data</h2>
            <div class=" relative overflow-x-auto max-w-[85vw]  ">
                <table class="w-full text-sm shadow-md  rounded-lg text-left text-gray-500 ">
                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                        <tr>


                            @if (count($results) != 0)
                                @foreach ($results[0] as $key => $value)
                                    <th scope="col" class="py-2 px-4 whitespace-nowrap">
                                        {{ str_replace('_', ' ', preg_replace('/_[0-9]/mi', '', $key)) }}
                                    </th>
                                @endforeach
                            @endif




                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($results as $result)
                            <tr class="bg-white border-b text-right ">
                                @foreach ($result as $key => $value)
                                    <td class="py-2 px-4 text-black text-xs whitespace-nowrap">
                                        {{ $value }}
                                    </td>
                                @endforeach



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
        </div>
    @elseif ($comp->scoring_type == 'rlss-nationals')
        <div class=" overflow-hidden " id="raw_data">
            <h2>Raw Data</h2>
            <div class=" relative overflow-x-auto max-w-[85vw]  ">
                <table class="w-full text-sm shadow-md  rounded-lg text-left text-gray-500 ">
                    <thead class="text-xs text-gray-700  uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="py-2 px-4 whitespace-nowrap">Name</th>

                            @foreach ($results['eventOrder'] as $event)
                                <th scope="col" class="py-2 px-4 whitespace-nowrap">
                                    {{ $event }}
                                </th>
                            @endforeach
                            <th scope="col" class="py-2 px-4 whitespace-nowrap">Total</th>
                            <th scope="col" class="py-2 px-4 whitespace-nowrap">Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results['results'] as $result)
                            <tr class="bg-white border-b text-left ">

                                <td class="py-2 px-4 text-black text-xs whitespace-nowrap">
                                    {{ $result->name }}
                                </td>


                                @foreach ($result->events as $event)
                                    <td class="py-2 px-4 text-black text-xs whitespace-nowrap">
                                        {{ $event?->place ?? 16 }}
                                    </td>
                                @endforeach

                                <td class="py-2 px-4 text-black text-xs whitespace-nowrap">
                                    {{ $result->score }}
                                </td>

                                <td class="py-2 px-4 text-black text-xs whitespace-nowrap">
                                    {{ $result->place }}
                                </td>





                            </tr>
                        @endforeach
                    </tbody>
                </table>

    @endif

@endsection
