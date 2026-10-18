@extends('layouts.competition')

@section('title')
    Edit Heats | Heats and Draws
@endsection

@section('content')
    @php
        $heatdata = $event->heats->sortBy(['heat', 'lane'])->groupBy('heat');
        $highestHeat = max((int) ($heatdata->keys()->max() ?? 0), 1);
        $initialHeats = [];

        for ($heatNumber = 1; $heatNumber <= $highestHeat; $heatNumber++) {
            $lanes = $heatdata->get($heatNumber, collect())->keyBy('lane');
            $initialHeats[] = [
                'number' => $heatNumber,
                'lanes' => collect(range(1, $comp->max_lanes))
                    ->map(function ($lane) use ($lanes, $comp, $event) {
                        $allocation = $lanes->get($lane);

                        return $allocation
                            ? [
                                'id' => $allocation->id,
                                'entity' => $allocation->entity_id,
                                'name' => $allocation->entity?->getName($comp),
                                'seed' => $comp->use_seeds
                                    ? $allocation->entity
                                        ?->getSeeds()
                                        ->where('speed_event', $event->id)
                                        ->first()
                                        ?->prettySeed()
                                    : null,
                            ]
                            : null;
                    })
                    ->values()
                    ->all(),
            ];
        }

        $assignedIds = $event->heats->pluck('entity_id')->unique();
        $unassigned = $event
            ->getScorableEntities()
            ->whereNotIn('id', $assignedIds)
            ->map(
                fn($entity) => [
                    'id' => $entity->id,
                    'name' => $entity->getName($comp),
                    'seed' => $comp->use_seeds
                        ? $entity->getSeeds()->where('speed_event', $event->id)->first()?->prettySeed()
                        : null,
                ],
            )
            ->values();
    @endphp

    <div x-data="heatEditor(@js([
    'heats' => $initialHeats,
    'unassigned' => $unassigned,
    'maxLanes' => $comp->max_lanes,
    'urls' => [
        'swap' => route('comps.heats_and_draws.heats.swap', [$comp, $event]),
        'swapHeats' => route('comps.heats_and_draws.heats.swapHeats', [$comp, $event]),
        'assign' => route('comps.heats_and_draws.heats.assign', [$comp, $event]),
        'unassign' => route('comps.heats_and_draws.heats.unassign', [$comp, $event]),
        'insert' => route('comps.heats_and_draws.heats.insertHeat', [$comp, $event]),
        'delete' => route('comps.heats_and_draws.heats.deleteHeats', [$comp, $event]),
    ],
    'csrf' => csrf_token(),
    'scrollKey' => 'heat-editor-scroll-{{ $event->id }}',
]))" x-init="restoreScroll()" x-cloak>
        <div class="flex flex-col gap-6">
            <header class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>

                    <h2 class="mb-1">{{ $comp->heats_per_event ? $event->getName() : '' }} heats</h2>
                    <p class=" text-sm text-gray-600">Arrange entities by selecting a competitor and then an open
                        lane or another competitor. Select two heat headers to swap complete heats.</p>
                </div>
                <a href="{{ route('comps.heats_and_draws', $comp) }}" class="se-btn">Back to heats</a>
            </header>



            <div
                class="flex flex-col gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-8 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white"
                        x-text="selectionLabel"></span>
                    <div>
                        <p class="font-semibold" x-text="selectionTitle"></p>
                        <p class="text-sm text-gray-600" x-text="selectionHint"></p>
                    </div>
                </div>
                <button type="button" class="se-btn" x-show="selection.type" @click="clearSelection()">Clear
                    selection</button>
            </div>

            <section class="se-card overflow-hidden">

                <div class="overflow-x-auto w-full" x-ref="matrixScroller">

                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-900 text-left text-xs uppercase tracking-wider text-white">
                                <th class="sticky left-0 z-10 w-16 min-w-16 max-w-16 bg-gray-900 px-4 py-3">Lane</th>
                                <template x-for="heat in heats" :key="heat.number">
                                    <th class="max-w-100 min-w-[20rem]! border-l border-gray-700 px-4 py-3"
                                        style="width: 18rem; min-width: 18rem;" :data-heat-number="heat.number">
                                        <div class="flex items-center justify-between gap-3">
                                            <button type="button" class="font-semibold hover:text-se-accent"
                                                :class="{
                                                    'text-se-accent': selection.type === 'heat' && selection
                                                        .value === heat.number
                                                }"
                                                @click="selectHeat(heat.number)">Heat <span
                                                    x-text="heat.number"></span></button>
                                            <div class="flex items-center gap-2 normal-case tracking-normal">
                                                <button type="button"
                                                    class="text-xs text-gray-300 hover:text-white tooltip-left"
                                                    title="Insert heat after this heat" @click="addHeat(heat.number + 1)">+
                                                    after</button>
                                                <button type="button"
                                                    class="text-lg leading-none text-gray-300 hover:text-red-300 tooltip-left"
                                                    title="Remove this heat"
                                                    @click="removeHeat(heat.number)">&times;</button>
                                            </div>
                                        </div>
                                    </th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="lane in maxLanes" :key="lane">
                                <tr class="border-b border-gray-200 last:border-0">
                                    <th class="sticky left-0 z-10 w-16 min-w-16 max-w-16 bg-gray-50 px-4 py-4 text-left text-sm font-bold text-gray-500"
                                        x-text="lane"></th>
                                    <template x-for="heat in heats" :key="heat.number">
                                        <td class="h-px border-l border-gray-200 p-2">
                                            <button type="button"
                                                class="flex h-full min-h-16 w-full flex-col justify-center rounded-md border-2 p-3 text-left transition"
                                                :class="cellClass(heat, lane)" @click="selectCell(heat, lane)">
                                                <template x-if="cell(heat, lane)"><span><span class="block font-semibold"
                                                            x-text="cell(heat, lane).name"></span><span
                                                            class="text-xs text-gray-500" x-show="cell(heat, lane).seed"
                                                            x-text="cell(heat, lane).seed"></span></span></template>
                                                <template x-if="!cell(heat, lane)"><span
                                                        class="text-center text-xs font-semibold uppercase tracking-wider text-gray-400">Open
                                                        lane</span></template>
                                            </button>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
                <div class="se-card se-card-body">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="mb-1">Unassigned entities</h3>
                            <p class="text-sm text-gray-600">Select one, then select an open lane.</p>
                        </div><span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800"
                            x-text="unassigned.length"></span>
                    </div>
                    <button type="button"
                        class="mb-4 flex w-full items-center justify-center rounded-md border-2 border-dashed border-amber-300 bg-amber-50 p-4 text-sm font-semibold text-amber-900 hover:border-amber-500"
                        x-show="selection.type === 'entity'" @click="unassignSelected()">Click here to unassign selected
                        entity</button>
                    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                        <template x-for="entity in unassigned" :key="entity.id"><button type="button"
                                class="rounded-md border border-gray-200 bg-gray-50 p-3 text-left text-sm font-semibold hover:border-se hover:bg-white"
                                :class="{
                                    'border-se bg-white ring-2 ring-se/20': selection.type === 'entity' && selection
                                        .value === entity.id
                                }"
                                @click="selectEntity(entity)"><span x-text="entity.name"></span></button></template>
                        <p class="text-sm text-gray-500" x-show="unassigned.length === 0">Every entity is assigned to a
                            lane.</p>
                    </div>
                </div>
                <div class="se-card se-card-body">
                    <h3 class="mb-2">Reset layout</h3>
                    <p class="mb-4 text-sm text-gray-600">Restore the generated heat layout. Manual changes will be lost.
                    </p>
                    <form action="{{ route('comps.heats_and_draws.heats.reset', [$comp, $event]) }}" method="get"
                        @submit="doConfirm($event, 'Are you sure you want to reset the heats?')"><button
                            class="se-btn se-btn-danger w-full">Reset heats</button></form>
                </div>
            </section>
        </div>

        <div class="fixed bottom-4 left-1/2 z-20 -translate-x-1/2 rounded-full bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-lg"
            x-show="busy || status" x-transition><span x-show="busy">Saving changes...</span><span x-show="!busy"
                x-text="status"></span></div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        function heatEditor(config) {
            return {
                heats: config.heats,
                unassigned: config.unassigned,
                maxLanes: config.maxLanes,
                urls: config.urls,
                csrf: config.csrf,
                busy: false,
                status: '',
                statusTimer: null,
                selection: {
                    type: null,
                    value: null
                },
                get assignedCount() {
                    return this.heats.reduce((total, heat) => total + heat.lanes.filter(Boolean).length, 0);
                },
                get selectionLabel() {
                    return this.selection.type === 'heat' ? 'H' : this.selection.type === 'entity' ? 'E' : '-';
                },
                get selectionTitle() {
                    if (this.selection.type === 'heat') return `Heat ${this.selection.value} selected`;
                    if (this.selection.type === 'entity') return 'Entity selected';
                    return 'Nothing selected';
                },
                get selectionHint() {
                    if (this.selection.type === 'heat') return 'Select another heat header to swap their contents.';
                    if (this.selection.type === 'entity')
                        return 'Select an open lane to place it, or an occupied lane to swap.';
                    return 'Choose an entity or heat header to begin editing.';
                },
                cell(heat, lane) {
                    return heat.lanes[lane - 1];
                },
                cellClass(heat, lane) {
                    const selected = this.selection.type === 'entity' && this.cell(heat, lane)?.entity === this.selection
                        .value;
                    return {
                        'border-gray-200 bg-white': !this.cell(heat, lane) && !selected,
                        'hover:border-se': !this.cell(heat, lane) && !selected && this.selection.type === 'entity',
                        'border-gray-300 bg-gray-50 hover:border-se': this.cell(heat, lane) && !selected,
                        'border-se bg-se/10 ring-2 ring-se/20': selected
                    };
                },
                selectEntity(entity) {
                    if (this.busy) return;
                    this.selection = {
                        type: 'entity',
                        value: entity.id
                    };
                    this.status = '';
                },
                selectHeat(number) {
                    if (this.busy) return;
                    if (this.selection.type !== 'heat') {
                        this.selection = {
                            type: 'heat',
                            value: number
                        };
                        return;
                    }
                    if (this.selection.value === number) {
                        this.clearSelection();
                        return;
                    }
                    const first = this.heats.find(heat => heat.number === this.selection.value);
                    const second = this.heats.find(heat => heat.number === number);
                    this.post(this.urls.swapHeats, {
                        first: this.selection.value,
                        second: number
                    }).then(() => {
                        [first.lanes, second.lanes] = [second.lanes, first.lanes];
                        this.clearSelection();
                        this.setStatus('Heats swapped');
                    });
                },
                selectCell(heat, lane) {
                    if (this.busy) return;
                    if (this.selection.type !== 'entity') {
                        if (this.cell(heat, lane)) this.selectEntity({
                            id: this.cell(heat, lane).entity
                        });
                        return;
                    }
                    const target = this.cell(heat, lane);
                    if (target?.entity === this.selection.value) {
                        this.clearSelection();
                        return;
                    }
                    const sourceLocation = this.findAllocationLocation(this.selection.value);
                    if (target) {
                        this.post(this.urls.swap, {
                            team: sourceLocation.allocation.id,
                            'target-heat': target.id,
                            'target-heatlane': `${heat.number}:${lane}`
                        }).then(() => {
                            const sourceLane = sourceLocation.heat.lanes[sourceLocation.lane - 1];
                            sourceLocation.heat.lanes[sourceLocation.lane - 1] = target;
                            heat.lanes[lane - 1] = sourceLane;
                            this.clearSelection();
                            this.setStatus('Entities swapped');
                        });
                        return;
                    }
                    const movingEntity = sourceLocation?.allocation ?? this.unassigned.find(entity => entity.id === this
                        .selection.value);
                    this.post(this.urls.assign, {
                        entity: this.selection.value,
                        heat: heat.number,
                        lane
                    }).then(response => response.json()).then(data => {
                        const movingEntityId = sourceLocation ? movingEntity.entity : movingEntity.id;
                        if (sourceLocation) sourceLocation.heat.lanes[sourceLocation.lane - 1] = null;
                        heat.lanes[lane - 1] = {
                            id: data.allocation_id,
                            entity: movingEntityId,
                            name: movingEntity.name,
                            seed: movingEntity.seed ?? null
                        };
                        this.unassigned = this.unassigned.filter(entity => entity.id !== movingEntityId);
                        this.clearSelection();
                        this.setStatus('Entity assigned');
                    });
                },
                findAllocation(entityId) {
                    for (const heat of this.heats) {
                        const lane = heat.lanes.find(item => item?.entity === entityId);
                        if (lane) return lane;
                    }
                    return null;
                },
                findAllocationLocation(entityId) {
                    for (const heat of this.heats) {
                        const lane = heat.lanes.findIndex(item => item?.entity === entityId);
                        if (lane !== -1) return {
                            heat,
                            lane: lane + 1,
                            allocation: heat.lanes[lane]
                        };
                    }
                    return null;
                },
                addHeat(position) {
                    if (this.busy) return;
                    this.post(this.urls.insert, {
                        position
                    }).then(() => {
                        this.heats.splice(Math.min(position - 1, this.heats.length), 0, {
                            number: position,
                            lanes: Array(this.maxLanes).fill(null)
                        });
                        this.heats = this.heats.map((heat, index) => ({
                            ...heat,
                            number: index + 1
                        }));
                        this.setStatus('Heat added');
                        this.$nextTick(() => this.scrollToHeat(position));
                    });
                },
                removeHeat(number) {
                    if (this.busy) return;
                    if (!confirm(`Remove heat ${number}? Its entities will become unassigned.`)) return;
                    const removed = this.heats.find(heat => heat.number === number);
                    this.post(this.urls.delete, {
                        heat: number
                    }).then(() => {
                        removed.lanes.filter(Boolean).forEach(allocation => this.unassigned.push({
                            id: allocation.entity,
                            name: allocation.name,
                            seed: allocation.seed ?? null
                        }));
                        this.heats = this.heats.filter(heat => heat.number !== number).map((heat, index) => ({
                            ...heat,
                            number: index + 1
                        }));
                        this.setStatus('Heat removed');
                    });
                },
                unassignSelected() {
                    if (this.busy) return;
                    if (this.selection.type !== 'entity') return;
                    const location = this.findAllocationLocation(this.selection.value);
                    this.post(this.urls.unassign, {
                        entity: this.selection.value
                    }).then(() => {
                        this.unassigned.push({
                            id: location.allocation.entity,
                            name: location.allocation.name,
                            seed: location.allocation.seed ?? null
                        });
                        location.heat.lanes[location.lane - 1] = null;
                        this.clearSelection();
                        this.setStatus('Entity unassigned');
                    });
                },
                clearSelection() {
                    this.selection = {
                        type: null,
                        value: null
                    };
                },
                setStatus(message) {
                    clearTimeout(this.statusTimer);
                    this.status = message;
                    this.statusTimer = setTimeout(() => {
                        this.status = '';
                    }, 3000);
                },
                rememberScroll(heatNumber) {
                    sessionStorage.setItem(config.scrollKey, heatNumber);
                },
                scrollToHeat(heatNumber) {
                    const header = this.$refs.matrixScroller.querySelector(`[data-heat-number="${heatNumber}"]`);
                    if (!header) return;
                    this.$refs.matrixScroller.scrollLeft = header.offsetLeft - (this.$refs.matrixScroller.clientWidth -
                        header.offsetWidth) / 2;
                },
                restoreScroll() {
                    this.$nextTick(() => {
                        const heatNumber = sessionStorage.getItem(config.scrollKey);
                        if (!heatNumber) return;
                        sessionStorage.removeItem(config.scrollKey);
                        this.scrollToHeat(heatNumber);
                    });
                },
                post(url, values) {
                    this.busy = true;
                    const body = new FormData();
                    body.append('_token', this.csrf);
                    Object.entries(values).forEach(([key, value]) => body.append(key, value));
                    return fetch(url, {
                        method: 'POST',
                        body
                    }).then(response => response.ok ? response : response.json().then(error => Promise.reject(error
                        .message || 'Unable to save changes.'))).catch(error => {
                        this.setStatus(error);
                        throw error;
                    }).finally(() => {
                        this.busy = false;
                    });
                },
                reload() {
                    window.location.reload();
                },
            };
        }
    </script>
@endsection
