@extends('layouts.competition')

@section('title')
    Printables
@endsection



@section('content')
    <div class="">
        <div class="flex flex-col space-y-4">



            <div class="flex justify-between">
                <h2 class="mb-0">Printables</h2>
            </div>

            <div class="grid-3">
                <div>
                    <h3>Speeds</h3>
                    <a href="{{ route('comps.printables.chief-timekeeper-pack', $comp) }}" target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Chief Timekeeper Pack</p>
                            <small class=" ml-5 text-gray-500">CContains heat sheets for all speed events. Pre-filled with
                                data.</small>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <a href="{{ route('comps.printables.marshalling', $comp) }}" target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Marshalling Pack</p>
                            <small class=" ml-5 text-gray-500">Contains a list of competitior names grouped and ordered by
                                heat.</small>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
                <div>
                    <h3>SERCs</h3>
                    <a href="{{ route('comps.printables.serc-marking-pack', $comp) }}" target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Marking Pack</p>
                            <small class=" ml-5 text-gray-500">Contains marking sheets for all teams/competitiors in every
                                SERC/initiative.</small>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <a href="{{ route('comps.printables.marshalling', $comp) }}?type=serc" target="_blank"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Marshalling Pack</p>
                            <small class=" ml-5 text-gray-500">Contains a list of competitior names grouped and ordered by
                                heat.</small>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>

                    <hr class="spacer">

                    @foreach ($comp->getSERCs as $serc)
                        <a href="{{ route('comps.printables.serc-sheets', [$comp, $serc]) }}" target="_blank"
                            class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                            <div class="font-archivo">
                                <p class="-mb-1">{{ $serc->getName() }} Raw Marking Sheets</p>
                                <small class=" ml-5 text-gray-500">Table based marking sheets for rough judge marks</small>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
