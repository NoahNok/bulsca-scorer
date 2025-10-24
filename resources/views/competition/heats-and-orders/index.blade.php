@extends('layouts/competition')

@section('title')
    Heats and Draws
@endsection


@section('content')
    <div class="">
        <div class="flex flex-col space-y-4">



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
                <a href="{{ route('comps.heats_and_draws.heats.generate', $comp) }}" class="se-btn">Generate Heats</a>
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





        </div>
    </div>
@endsection
