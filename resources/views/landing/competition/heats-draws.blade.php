@extends('layouts.landing-comp')

@section('title', 'Heats & Draws')

@section('content')


    @php
        $events = $comp->getHeats();
        $sercs = $comp->getDraws();

        $first_item = 'heats';

        if ($comp->heats_per_event && count($events) > 0) {
            $event = $events[0]['event'];
            $first_item = "sp:{$event->id}";
        }

    @endphp

    <div class="flex flex-col space-y-4" x-data="{
        open: '{{ $first_item }}',
    
    }">





        <div class="tabbed-bar mb-4">
            @if ($comp->heats_per_event)
                @foreach ($events as $heatevent)
                    <div @click="open = 'sp:{{ $heatevent['event']->id }}'"
                        :class="open == 'sp:{{ $heatevent['event']->id }}' ? 'active' : ''">
                        {{ $heatevent['event']->getName() }}</div>
                @endforeach
            @else
                <div @click="open = 'heats'" :class="open == 'heats' ? 'active' : ''">Heats</div>
            @endif

            @foreach ($sercs as $heatevent)
                <div @click="open = 'se:{{ $heatevent['serc']->id }}'"
                    :class="open == 'se:{{ $heatevent['serc']->id }}' ? 'active' : ''">
                    {{ $heatevent['serc']->getName() }}</div>
            @endforeach

        </div>





        @forelse ($comp->getHeats() as $heatevent)

            @php

                $speed_key = $comp->heats_per_event ? "sp:{$heatevent['event']->id}" : 'heats';
            @endphp



            <div class="grid grid-flow-col auto-cols-max gap-4 flex-wrap overflow-x-auto snap-x snap-mandatory"
                x-show="open == '{{ $speed_key }}'">

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
                                    {{ $lane?->entity->getName($comp) ?? '-' }}
                                </div>
                            @endfor


                        </div>
                    </div>
                @endforeach


            </div>

        @empty
            <div class="alert-box alert-warning md:w-1/3">

                <p>Heats are not currently available, please check back later.</p>
            </div>
        @endforelse








        @php
            $use_tanks = $comp->getScoringSettings->use_tanks;
        @endphp

        @forelse ($comp->getDraws() as $heatevent)



            <div class="grid grid-flow-row grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4    gap-4 flex-wrap"
                x-show="open == 'se:{{ $heatevent['serc']->id }}'">



                @foreach ($heatevent['draws'] as $tank_no => $draw)
                    @if ($use_tanks)
                        <div>
                            <h2 class="-mb-1!">Tank {{ $tank_no + 1 }}</h2>

                            @php
                                $uniqueLeagues = $draw
                                    ->map(function ($drawEntry) {
                                        return $drawEntry->entity->getLeague->name ?? null;
                                    })
                                    ->filter()
                                    ->unique()
                                    ->values()
                                    ->implode(', ');

                            @endphp

                            <small class="text-gray-600 font-semibold">{{ $uniqueLeagues }}</small>



                            <div class="flex flex-col space-y-2 mt-2">
                                @foreach ($draw as $drawEntry)
                                    <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded ">
                                        {{ $drawEntry->draw }}.
                                        {{ $drawEntry->entity->getName($comp) ?? '-' }}</div>
                                @endforeach
                            </div>




                        </div>
                    @else
                        @foreach ($draw as $drawEntry)
                            <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded">
                                {{ $drawEntry->draw }}. {{ $drawEntry->entity->getName($comp) ?? '-' }}
                            </div>
                        @endforeach
                    @endif
                @endforeach


            </div>
        @empty
            <div class="alert-box alert-warning md:w-1/3">

                <p>Draws are not currently available, please check back later.</p>
            </div>
        @endforelse







    </div>

@endsection
