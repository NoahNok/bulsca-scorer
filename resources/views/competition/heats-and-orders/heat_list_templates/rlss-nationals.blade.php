<div x-data="{
    selectedHeat: {{ $comp->getSpeedEvents->first()->id }}
}" x-init="document.getElementById('edit-heats-kill').outerHTML = ''">



    <div class ="p-2 flex space-x-3">
        @foreach ($comp->getSpeedEvents as $event)
            <a class="btn" x-bind:class="selectedHeat == {{ $event->id }} ? 'btn-primary' : 'btn-white'"
                @click="selectedHeat = {{ $event->id }}">
                {{ $event->getName() }}
            </a>
        @endforeach

        <a x-bind:href="'{{ route('comps.view.heats.edit', $comp) }}?event=' + selectedHeat" class="btn !ml-auto">Edit
            Event Heats</a>
    </div>


    <div>
        @foreach ($heatEntries->groupBy('event') as $eventId => $data)
            <div class="flex
        space-x-2 " x-show="selectedHeat == {{ $eventId }}">
                <div class=" hidden md:block  ">

                    <h5>Lane</h5>


                </div>
                <div class=" w-full grid grid-cols-1 md:grid-cols-2 gap-3 ">


                    @forelse ($data->sortBy(['heat','lane'])->groupBy('heat') as $key=>
        $heat)
                        <div>
                            <h5>Heat {{ $key }}</h5>
                            <ol class=" list-item space-y-2">
                                @for ($l = 1; $l <= $comp->max_lanes; $l++)
                                    @php
                                        $lane = $heat->where('lane', $l)->first();
                                    @endphp

                                    <li class="card relative ">
                                        @if ($lane)
                                            <span
                                                class="whitespace-nowrap overflow-ellipsis overflow-hidden">{{ $lane->team }}
                                                -
                                                {{ $lane->club }} ({{ $lane->region }}) - {{ $lane->league }}</span>
                                        @else
                                            &nbsp;
                                        @endif

                                        @if ($key % 2 == 1)
                                            <div class="absolute h-full top-0 -left-14 ">
                                                <p class="px-5 py-3 border border-transparent">{{ $l }}</p>
                                            </div>
                                        @endif
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
