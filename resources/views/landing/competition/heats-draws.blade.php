{{--
    A competition's Heats & Draws tab: the speed events' Heats (one event at a time when heats are per event) and
    the SERCs' Draw, each only once the Organisers release it. Searching finds a Team in every event's Heats and
    in the Draw: heats and Tanks without it hide, and within the rest only its lanes and places stay.
--}}
@extends('landing.competition.layout')

@section('title', 'Heats & Draws | ' . $comp->name)

@php
    $events = $comp->getHeats();
    $sercs = $comp->getDraws();
    $use_tanks = $comp->usesTanks();

    // Without heats per event, every speed event swims the first one's Heats
    $heatEvents = $comp->heats_per_event ? $events : array_slice($events, 0, 1);
    $hasHeats = $comp->getSpeedEvents()->exists();
    $hasDraws = $comp->getSERCs()->exists();
    $first = $comp->show_heats && count($heatEvents) > 0 ? 'heats' : ($hasDraws ? 'draws' : 'heats');
    // Searching only helps once there's something released to search
    $searchable = ($comp->show_heats && count($heatEvents) > 0) || ($comp->show_draws && count($sercs) > 0);
    $name = fn($entity) => $entity?->getName($comp) ?? '';
@endphp

@section('comp-content')
    <div x-data="{
        view: '{{ $first }}',
        event: {{ count($heatEvents) > 0 ? $heatEvents[0]['event']->id : 'null' }},
        search: '',
        get term() { return this.search.trim().toLowerCase() },
        matches(name) { return this.term !== '' && name.includes(this.term) },
        // A lane or place shows while nothing's searched, or when it's a match
        shows(name) { return this.term === '' || name.includes(this.term) },
        // A heat or Tank shows while nothing's searched, or when it holds a match
        holds(names) { return this.term === '' || names.includes(this.term) },
    }">
        <div class="v2-hd-bar">
            @if ($hasHeats && $hasDraws)
                <div class="v2-tabs" role="tablist" aria-label="Heats or Draws" style="--v2-tabs: 2">
                    <span class="v2-tabs-pill" aria-hidden="true"
                        :style="`transform: translateX(${view === 'draws' ? 100 : 0}%)`"></span>
                    <button type="button" role="tab" :aria-selected="(view === 'heats').toString()"
                        @click="view = 'heats'">Heats</button>
                    <button type="button" role="tab" :aria-selected="(view === 'draws').toString()"
                        @click="view = 'draws'">Draws</button>
                </div>
            @endif

            @if ($searchable)
                <div class="v2-field v2-hd-search">
                    <label for="hd-search" class="sr-only">Find a Team or competitor</label>
                    <input id="hd-search" type="search" class="v2-input" x-model="search"
                        placeholder="Find a Team or competitor">
                </div>
            @endif
        </div>

        {{-- Eases the height as Heats and Draws, events and search results swap, instead of the page jumping --}}
        <div x-smooth-height>
            <div>
                @if ($hasHeats)
                    <section x-show="view === 'heats'" x-transition:enter="v2-fade-in" x-transition:enter-start="v2-fade-from" @if ($first !== 'heats') x-cloak @endif aria-label="Heats" class="mt-6">
                        @if (!$comp->show_heats)
                            <p class="v2-empty">Heats appear here once the Organisers release them.</p>
                        @elseif (count($heatEvents) === 0)
                            <p class="v2-empty">There are no heats yet. They appear here once the Organisers set them.</p>
                        @else
                            @if (count($heatEvents) > 1)
                                <div class="mb-6 flex flex-wrap gap-2" role="group" aria-label="Event" x-show="term === ''">
                                    @foreach ($heatEvents as $heatEvent)
                                        <button type="button" class="v2-chip" @click="event = {{ $heatEvent['event']->id }}"
                                            :aria-pressed="(event === {{ $heatEvent['event']->id }}).toString()">{{ $heatEvent['event']->getName() }}</button>
                                    @endforeach
                                </div>
                            @endif

                            @foreach ($heatEvents as $heatEvent)
                                @php
                                    $eventNames = collect($heatEvent['pools'])
                                        ->flatMap(fn($p) => collect($p['heats'])->flatten())
                                        ->map(fn($lane) => strtolower($name($lane->entity)))
                                        ->implode('|');
                                @endphp
                                {{-- While searching, every event's heats show, so a Team is found in all of them --}}
                                <div x-show="term === '' ? event === {{ $heatEvent['event']->id }} : holds(@js($eventNames))"
                                    x-transition:enter="v2-fade-in" x-transition:enter-start="v2-fade-from"
                                    @if (!$loop->first) x-cloak @endif>
                                    @if (count($heatEvents) > 1)
                                        <h3 class="v2-hd-event" x-show="term !== ''" x-cloak>{{ $heatEvent['event']->getName() }}</h3>
                                    @endif
                                    <div class="v2-hd-grid">
                                        @foreach ($heatEvent['pools'] as $poolHeats)
                                            @foreach ($poolHeats['heats'] as $heat_no => $lanes)
                                                @php
                                                    $names = collect($lanes)->map(fn($lane) => strtolower($name($lane->entity)))->implode('|');
                                                    $laneCount = $poolHeats['pool']?->lanes ?? $comp->max_lanes;
                                                @endphp
                                                <div class="v2-hd-card" x-show="holds(@js($names))">
                                                    <h4>{{ \App\Models\Pool::heatLabel($poolHeats['pool'], $heat_no) }}</h4>
                                                    <ol>
                                                        @for ($i = 1; $i <= $laneCount; $i++)
                                                            @php
                                                                $laneName = $name(collect($lanes)->firstWhere('lane', $i)?->entity);
                                                            @endphp
                                                            <li x-show="shows(@js(strtolower($laneName)))" :class="matches(@js(strtolower($laneName))) && 'is-match'">
                                                                <span class="v2-hd-num">{{ $i }}</span>
                                                                <span @class(['v2-hd-empty' => $laneName === ''])>{{ $laneName ?: 'Empty' }}</span>
                                                            </li>
                                                        @endfor
                                                    </ol>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </section>
                @endif

                @if ($hasDraws)
                    <section x-show="view === 'draws'" x-transition:enter="v2-fade-in" x-transition:enter-start="v2-fade-from" @if ($first !== 'draws') x-cloak @endif aria-label="Draws" class="mt-6">
                        @if (!$comp->show_draws)
                            <p class="v2-empty">The Draw appears here once the Organisers release it.</p>
                        @elseif (count($sercs) === 0)
                            <p class="v2-empty">There's no Draw yet. It appears here once the Organisers make it.</p>
                        @else
                            <div class="v2-hd-grid">
                                @foreach ($sercs as $drawSet)
                                    @foreach ($drawSet['draws'] as $tank_no => $draw)
                                        @php
                                            $names = collect($draw)->map(fn($entry) => strtolower($name($entry->entity)))->implode('|');
                                        @endphp
                                        <div class="v2-hd-card" x-show="holds(@js($names))">
                                            @if ($use_tanks)
                                                <h4>Tank {{ $tank_no + 1 }}</h4>
                                                <p class="v2-hd-leagues">
                                                    {{ collect($draw)->map(fn($entry) => $entry->entity?->getLeague()->name ?? null)->filter()->unique()->implode(', ') }}
                                                </p>
                                            @else
                                                <h4>Draw</h4>
                                            @endif
                                            <ol>
                                                @foreach ($draw as $entry)
                                                    <li x-show="shows(@js(strtolower($name($entry->entity))))" :class="matches(@js(strtolower($name($entry->entity)))) && 'is-match'">
                                                        <span class="v2-hd-num">{{ $entry->draw }}</span>
                                                        <span>{{ $name($entry->entity) ?: '-' }}</span>
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

                @if (!$hasHeats && !$hasDraws)
                    <p class="v2-empty">Heats and the Draw appear here once the Organisers add the Events.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
