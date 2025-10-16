@extends('layouts.landing-comp')

@section('title', 'Heats & Draws')

@section('content')


    <div class="flex flex-col space-y-4">



        <div class="flex justify-between">
            <h2 class="mb-0">Heats</h2>

        </div>

        @forelse ($comp->getHeats() as $heatevent)
            @if ($comp->heats_per_event)
                <div class="flex items-center justify-between">
                    <h3>{{ $heatevent['event']->getName() }} Heats</h3>

                </div>
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
                                    {{ $lane?->entity->getName() ?? '-' }}
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




        <br>

        <div class="flex justify-between">
            <h2 class="mb-0">Draw</h2>

        </div>

        @php
            $use_tanks = $comp->getScoringSettings->use_tanks;
        @endphp

        @forelse ($comp->getDraws() as $heatevent)



            <div class="grid grid-flow-row grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4    gap-4 flex-wrap">



                @foreach ($heatevent['draws'] as $tank_no => $draw)
                    @if ($use_tanks)
                        <div>
                            <h4>Tank {{ $tank_no + 1 }}</h4>
                            <ol>
                                @foreach ($draw as $drawEntry)
                                    <li
                                        class="overflow-hidden line-clamp-1 hover:line-clamp-none focus:line-clamp-none active:line-clamp-none focus-within:line-clamp-none ">
                                        {{ $drawEntry->draw }}.
                                        {{ $drawEntry->entity->getName() ?? '-' }}</li>
                                @endforeach
                            </ol>

                        </div>
                    @else
                        @foreach ($draw as $drawEntry)
                            <div class="se-card  se-card-body min-w-56 p-2! px-4! rounded">
                                {{ $drawEntry->draw }}. {{ $drawEntry->entity->getName() ?? '-' }}
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
