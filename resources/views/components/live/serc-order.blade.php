{{-- The live SERC order: each team's draw, whether it's finished the dry SERC or both, and its estimated start.
     State comes from $store.live (live.partials.store). With `searchable`, tiles matching the parent's matches(name) are highlighted.
     size="display" fills the screen, for the full-screen page. --}}
@props(['comp', 'size' => 'default', 'searchable' => false])

@php
    $sercDraw = $comp->getDraws()[0] ?? null;
    $useTanks = $comp->getScoringSettings?->use_tanks;

    $groups = collect($sercDraw['draws'] ?? [])
        ->map(fn($draws) => $draws->filter(fn($draw) => $draw->entity)->values())
        ->filter(fn($draws) => $draws->isNotEmpty())
        ->values();

    $sercCount = $comp->getSERCs->count();

    $display = $size == 'display';
@endphp

@if ($groups->isEmpty())
    <div class="se-alert-box alert-info">
        <p>No SERC order available yet. Check back closer to the start.</p>
    </div>
@else
    <div class="flex flex-wrap items-center gap-2 mb-3 {{ $display ? 'shrink-0 text-lg' : 'text-sm' }}">
        @if ($sercCount > 1)
            <span class="font-semibold">Finished:</span>
            <span class="px-4 py-0.5 rounded-md bg-amber-500 text-white font-semibold">Dry</span>
            <span class="px-4 py-0.5 rounded-md bg-green-500 text-white font-semibold">Both</span>
        @else
            <span class="px-4 py-0.5 rounded-md bg-green-500 text-white font-semibold">Finished</span>
        @endif
        <span class="text-gray-500 ml-2">Start times are an estimate only.</span>
    </div>

    <div x-init="$store.live.sercCount = {{ $sercCount }}; $store.live.groups = @js($groups->map(fn($draws) => $draws->map(fn($draw) => $draw->entity->id)))"
        {{ $attributes->merge(['class' => $display ? 'flex gap-6 h-full min-h-0' : 'flex flex-col gap-6']) }}>

        @foreach ($groups as $tank_no => $draws)
            @php
                // Fill top to bottom, then across, at most 12 rows a column
                $columns = (int) ceil($draws->count() / 12);
                $rows = (int) ceil($draws->count() / max($columns, 1));
            @endphp

            <div class="{{ $display ? 'flex-1 flex flex-col min-h-0' : '' }}">
                @if ($useTanks)
                    <h3 class="{{ $display ? 'text-2xl! mb-2!' : '' }}">Tank {{ $tank_no + 1 }}</h3>
                @endif

                {{-- Column first, like the old live page --}}
                <div class="{{ $display ? 'grid grid-flow-col gap-3 flex-1 min-h-0' : 'grid grid-rows-12 md:grid-rows-8 2xl:grid-rows-6 gap-3 md:grid-flow-col' }}"
                    @if ($display) style="grid-template-rows: repeat({{ $rows }}, minmax(0, 1fr)); font-size: min(2.8vh, 1.75rem)" @endif>

                    @foreach ($draws as $draw)
                        @php
                            $id = $draw->entity->id;
                            $name = $draw->entity->getName($comp);
                        @endphp

                        {{-- On the display each tile is one line, so it fits however short the rows get --}}
                        <div class="se-card se-card-body whitespace-nowrap transition-colors {{ $display ? 'flex-row! items-center gap-4 py-0! px-5!' : 'p-3!' }}"
                            :class="{
                                'bg-amber-500! border-amber-500! text-white!': $store.live.state({{ $id }}) == 'part',
                                'bg-green-500! border-green-500! text-white!': $store.live.state({{ $id }}) == 'done',
                                'ring-2 ring-inset ring-se': $store.live.upNext.includes({{ $id }}),
                                @if ($searchable) 'ring-2 ring-se ring-offset-2': matches(@js($name)), @endif
                            }">
                            <div class="flex items-center justify-between gap-2 min-w-0 {{ $display ? 'flex-1' : '' }}">
                                <span class="font-semibold truncate">{{ $draw->draw }}. {{ $name }}</span>
                                <span x-show="$store.live.upNext.includes({{ $id }})" x-cloak
                                    class="badge shrink-0 {{ $display ? 'text-[0.6em]!' : 'badge-sm' }}">Up next</span>
                            </div>
                            <small class="font-semibold tabular-nums {{ $display ? 'text-[0.85em]' : 'text-xs' }}"
                                x-text="$store.live.label({{ $id }})">Est: –</small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif
