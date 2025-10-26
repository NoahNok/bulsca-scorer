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
        search: '',
    
        matchesSearch(term) {
            return term.trim().startsWith(this.search.trim())
        },
    
        displayMode: $persist('grid')
    
    }">

        <div class="flex md:space-x-3 w-full items-center md:flex-row flex-col">
            <div class="se-form-input w-full imb-0! mb-0!">
                <input type="text" x-model="search" placeholder="Search...">
            </div>

            <div class="flex space-x-2 items-center  ">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    class="size-8 cursor-pointer transition-colors ease-in-out hover:text-se hover:bg-gray-100 rounded-md p-1"
                    :class="displayMode == 'grid' ? 'se-bg-se/10 text-se bg-gray-100' : 'text-gray-600'"
                    @click="displayMode = 'grid'">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    class="size-8 cursor-pointer transition-colors ease-in-out hover:text-se hover:bg-gray-100 rounded-md p-1"
                    :class="displayMode == 'list' ? 'se-bg-se/10 text-se bg-gray-100' : 'text-gray-600'"
                    @click="displayMode = 'list'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>

            </div>
        </div>



        <div class="tabbed-bar mb-4 ">
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



            <div class=" hidden" :class="displayMode == 'grid' ? 'grid-4' : 'flex! flex-col space-y-'"
                x-data="{
                
                    children: [],
                
                    shouldShow() {
                        return open == '{{ $speed_key }}' || (search.trim() != '' && this.children.some(Boolean))
                
                
                    }
                }" x-show.important="shouldShow" x-transition>






                @foreach ($heatevent['heats'] as $heat_no => $lanes)
                    @php
                        $names = $lanes->map(fn($lane) => $lane->entity?->getName($comp))->filter()->implode(' ');
                    @endphp

                    <div class="flex flex-col space-x-2 " x-data="{
                        shouldShow() {
                            let show = search.trim() === '' || `{{ strtolower($names) }}`.includes(search.trim().toLowerCase())
                            children['{{ $heat_no }}'] = show
                            return show;
                        }
                    }" x-show="shouldShow">

                        <div class="flex space-x-3">
                            <h3 class="font-bold mb-2">L</h3>
                            <h3 class="font-bold  mb-2">Heat {{ $heat_no }}</h3>
                        </div>

                        <div class="space-y-2">

                            @for ($i = 0; $i < $comp->max_lanes; $i++)
                                @php
                                    $lane = $lanes->where('lane', $i + 1)->first();
                                @endphp
                                <div class="flex items-center w-full space-x-3">
                                    <h4 class="font-bold ">{{ $i + 1 }}</h4>
                                    <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded w-full"
                                        :class="(search.trim() !== '' &&
                                            `{{ strtolower($lane?->entity?->getName($comp) ?? '-') }}`
                                            .includes(search.trim().toLowerCase())) ? 'se-card-success' : ''">
                                        {{ $lane?->entity?->getName($comp) ?? '-' }}
                                    </div>
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



            <div class="hidden" :class="displayMode == 'grid' ? 'grid-4' : 'flex! flex-col space-y-1'">



                @foreach ($heatevent['draws'] as $tank_no => $draw)
                    @php
                        $names = $draw->map(fn($lane) => $lane->entity?->getName($comp))->filter()->implode(' ');
                    @endphp

                    <div x-data="{
                    
                        children: [],
                    
                        shouldShow() {
                    
                            if (search.trim() != '') {
                                return this.children.some(Boolean)
                            }
                    
                            return open == 'se:{{ $heatevent['serc']->id }}'
                    
                    
                        }
                    }" x-show="shouldShow" x-transition>
                        @if ($use_tanks)
                            <h2 class="-mb-1!">Tank {{ $tank_no + 1 }}</h2>

                            @php
                                $uniqueLeagues = $draw
                                    ->map(function ($drawEntry) {
                                        return $drawEntry->entity?->getLeague->name ?? null;
                                    })
                                    ->filter()
                                    ->unique()
                                    ->values()
                                    ->implode(', ');

                            @endphp

                            <small class="text-gray-600 font-semibold">{{ $uniqueLeagues }}</small>
                        @else
                            <h2 class="-mb-1!">
                                Draw
                            </h2>
                        @endif



                        <div class="flex flex-col space-y-2 mt-2">
                            @foreach ($draw as $drawEntry)
                                <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded " x-data="{
                                    shouldShow() {
                                        let show = search.trim() === '' || `{{ strtolower($drawEntry->entity?->getName($comp) ?? '-') }}`.includes(search.trim().toLowerCase())
                                        children['{{ $drawEntry->draw }}'] = show
                                        return show;
                                    }
                                }"
                                    x-show="shouldShow"
                                    :class="(shouldShow && search.trim()) != '' ? 'se-card-success' : ''">
                                    {{ $drawEntry->draw }}.
                                    {{ $drawEntry->entity?->getName($comp) ?? '-' }}</div>
                            @endforeach
                        </div>




                    </div>
                @endforeach


            </div>
        @empty
            <div class="alert-box alert-warning md:w-1/3">

                <p>Draws are not currently available, please check back later.</p>
            </div>
        @endforelse







    </div>

@endsection
