<div x-data="{
    selectedHeat: {{ $comp->getSpeedEvents->first()->id }}
}" x-init="document.getElementById('edit-heats-kill').outerHTML = ''">



    <div class =" flex space-x-3">
        @foreach ($comp->getSpeedEvents as $event)
            <a class="se-btn" x-bind:class="selectedHeat == {{ $event->id }} ? 'se-btn-primary' : ''"
                @click="selectedHeat = {{ $event->id }}">
                {{ $event->getName() }}
            </a>
        @endforeach

        <a x-bind:href="'{{ route('comps.heats.edit', $comp) }}?event=' + selectedHeat" class="se-btn ml-auto!">Edit
            Event Heats</a>
    </div>
    <br>


    <div>
        @foreach ($heatEntries->groupBy('event') as $eventId => $data)
            <div class="flex
        space-x-2 " x-show="selectedHeat == {{ $eventId }}">
                <div class=" hidden md:block pt-2 ">

                    <h4>Lane</h4>


                </div>
                <div class=" w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8!  ">


                    @forelse ($data->sortBy(['heat','lane'])->groupBy('heat') as $key=>
        $heat)
                        <div
                            class="md:border-r mr-8 py-2 md:nth-[2n]:border-r-0 lg:nth-[2n]:border-r lg:nth-[3n]:border-r-0">
                            <h4>Heat {{ $key }}</h4>
                            <ol class=" list-item space-y-2">
                                @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                    @php
                                        $lane = $heat->where('lane', $l)->first();
                                    @endphp

                                    <li class="relative ">
                                        @if ($lane)
                                            <span
                                                class="whitespace-nowrap text-ellipsis overflow-hidden z-10">{{ $lane->team }}
                                                -
                                                {{ $lane->club }} ({{ $lane->region }}) - {{ $lane->league }}</span>
                                        @else
                                            -
                                        @endif


                                        <div class="absolute h-full top-0 -left-14 ">
                                            <p class="px-5 z-30 border border-transparent">{{ $l }}</p>
                                        </div>

                                    </li>
                                @endfor
                            </ol>
                        </div>
                    @empty

                        <a href="{{ route('comps.view.heats.gen', $comp) }}"
                            class="btn flex items-center justify-center">Generate
                            Heats</a>
                    @endforelse

                </div>
            </div>
        @endforeach
    </div>




</div>
