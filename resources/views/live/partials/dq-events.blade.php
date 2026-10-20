{{-- Links to each event's DQs & penalties, grouped into SERCs and speeds --}}
@php
    $groups = [
        'SERCs' => $comp->getSERCs->map(fn($serc) => ['key' => 'se:' . $serc->id, 'name' => $serc->getName()]),
        'Speeds' => $comp->getSpeedEvents->map(fn($speed) => ['key' => 'sp:' . $speed->id, 'name' => $speed->getName()]),
    ];
@endphp

@if (collect($groups)->every->isEmpty())
    <p class="text-gray-500">No events yet.</p>
@else
    <div class="flex flex-col gap-8">
        @foreach ($groups as $title => $events)
            @if ($events->isNotEmpty())
                <div>
                    <h3>{{ $title }}</h3>
                    <div class="grid-4">
                        @foreach ($events as $event)
                            <a href="{{ route('live.dqs.event', array_merge([$event['key']], request()->all())) }}"
                                class="se-card se-card-hover">
                                <div class="se-card-body">
                                    <h4 class="mb-0!">{{ $event['name'] }}</h4>
                                    <p>View DQs & penalties</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endif
