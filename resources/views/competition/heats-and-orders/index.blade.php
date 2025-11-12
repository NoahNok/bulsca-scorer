@extends('layouts/competition')

@section('title')
    Heats and Draws
@endsection


@section('content')
    <div class="">
        <x-se-floating-sidebar>
            <div class="flex justify-between">
                <h2 class="mb-0">Heats</h2>

            </div>


            @forelse ($comp->getHeats() as $heatevent)
                @if ($comp->heats_per_event)
                    <div class="flex items-center justify-between">
                        <h3>{{ $heatevent['event']->getName() }} Heats</h3>
                        <a href="{{ route('comps.heats_and_draws.heats.edit', [$comp, $heatevent['event']]) }}"
                            id="edit-heats-kill" class="se-btn">Edit
                            Heats</a>
                    </div>
                @else
                    <a href="{{ route('comps.heats_and_draws.heats.edit', [$comp, $heatevent['event']]) }}"
                        id="edit-heats-kill" class="se-btn">Edit
                        Heats</a>
                @endif


                <div class="grid grid-flow-col auto-cols-max gap-4 flex-wrap overflow-x-auto snap-x snap-mandatory">

                    <div class="flex flex-col  py-2 sticky top-0 left-0 bg-white pr-2 ">
                        <h3 class="font-bold  mb-2">L</h3>
                        <div class="space-y-2">

                            @for ($i = 0; $i < $comp->max_lanes; $i++)
                                <div class="border border-transparent font-semibold py-2  rounded">
                                    {{ $i + 1 }}
                                </div>
                            @endfor


                        </div>
                    </div>

                    @foreach ($heatevent['heats'] as $heat_no => $lanes)
                        <div class="flex flex-col  py-2 snap-start pl-5">
                            <h3 class="font-bold  mb-2">Heat {{ $heat_no }}</h3>
                            <div class="space-y-2">

                                @for ($i = 0; $i < $comp->max_lanes; $i++)
                                    @php
                                        $lane = $lanes->where('lane', $i + 1)->first();
                                    @endphp
                                    <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded">
                                        {{ $lane?->entity?->getName($comp) ?? '-' }}
                                    </div>
                                @endfor


                            </div>
                        </div>
                    @endforeach


                </div>

            @empty
                @if ($comp->getSpeedEvents->count() == 0)
                    <div class="alert-box">
                        <h1>Heats</h1>
                        <p>You must add a Speed Event before you can generate heats.</p>
                    </div>
                @else
                    <a href="{{ route('comps.heats_and_draws.heats.generate', $comp) }}" class="se-btn">Generate Heats</a>
                @endif
            @endforelse


            <br>



            @php
                $use_tanks = $comp->getScoringSettings->use_tanks;
            @endphp

            @forelse ($comp->getDraws() as $heatevent)
                <div class="flex items-center justify-between">

                    <h2 class="mb-0">Draw</h2>

                    <div>
                        @if ($use_tanks)
                            <a href="{{ route('comps.heats_and_draws.draws.tank_setup', $comp) }}" id="edit-heats-kill"
                                class="se-btn">Edit Tank Setup</a>
                        @endif
                        <a href="{{ route('comps.heats_and_draws.draws.edit', [$comp, $heatevent['serc']]) }}"
                            id="edit-heats-kill" class="se-btn">Edit
                            Draw</a>
                    </div>


                </div>



                <div class="grid grid-flow-row grid-cols-6 gap-4 flex-wrap">



                    @foreach ($heatevent['draws'] as $tank_no => $draw)
                        @if ($use_tanks)
                            <div>
                                <h4>Tank {{ $tank_no + 1 }}</h4>
                                <ol>
                                    @foreach ($draw as $drawEntry)
                                        <li class="overflow-hidden line-clamp-1 hover:line-clamp-none ">
                                            {{ $drawEntry->draw }}.
                                            {{ $drawEntry->entity?->getName($comp) ?? '-' }}</li>
                                    @endforeach
                                </ol>

                            </div>
                        @else
                            @foreach ($draw as $drawEntry)
                                <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded">
                                    {{ $drawEntry->draw }}. {{ $drawEntry->entity?->getName($comp) ?? '-' }}
                                </div>
                            @endforeach
                        @endif
                    @endforeach


                </div>
            @empty

                @if ($comp->getSERCs->count() == 0)
                    <div class="alert-box">
                        <h1>Draw</h1>
                        <p>You must add a SERC before you can generate a draw. (The SERC can be empty)</p>
                    </div>
                @else
                    <a href="{{ route('comps.heats_and_draws.draws.generate', $comp) }}" class="se-btn">Generate Draw</a>
                @endif
            @endforelse


            @slot('sidebar')
                @if ($comp->show_heats)
                    <a href="{{ route('comps.heats_and_draws.heats.hide', [$comp]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Hide Heats</p>
                            <small class="block mt-1 ml-5 text-gray-500">Prevents the public from viewing heats</small>
                        </div>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>

                    </a>
                @else
                    <a href="{{ route('comps.heats_and_draws.heats.hide', [$comp]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Show Heats</p>
                            <small class="block mt-1 ml-5 text-gray-500">Allows the public to view heats</small>
                        </div>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                    </a>
                @endif

                @if ($comp->show_draws)
                    <a href="{{ route('comps.heats_and_draws.draws.hide', [$comp]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Hide Draws</p>
                            <small class="block mt-1 ml-5 text-gray-500">Prevents the public from viewing draws</small>
                        </div>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>

                    </a>
                @else
                    <a href="{{ route('comps.heats_and_draws.draws.hide', [$comp]) }}"
                        class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                        <div class="font-archivo">
                            <p class="-mb-1">Show Draws</p>
                            <small class="block mt-1 ml-5 text-gray-500">Allows the public to view draws</small>
                        </div>


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                    </a>
                @endif
            @endslot


        </x-se-floating-sidebar>
    @endsection
