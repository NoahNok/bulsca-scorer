@extends('layouts.competition')

@section('title')
    Edit SERC Draw | Heats and Draws | {{ $comp->name }}
@endsection

@section('content')
    @php
        $assignedEntityIds = $serc->draw()->pluck('entity_id')->unique();
        $unassigned = $serc
            ->getScorableEntities()
            ->whereNotIn('id', $assignedEntityIds)
            ->map(
                fn($entity) => [
                    'id' => $entity->id,
                    'name' => $entity->getName($comp),
                ],
            )
            ->values();
    @endphp

    <div x-data="drawEditor(@js([
    'tanks' => $serc->getTankDraw(),
    'unassigned' => $unassigned,
    'useTanks' => $comp->getScoringSettings->use_tanks,
    'urls' => [
        'swap' => route('comps.heats_and_draws.draws.swap', [$comp, $serc]),
        'remove' => route('comps.heats_and_draws.draws.remove', [$comp, $serc]),
        'move' => route('comps.heats_and_draws.draws.move', [$comp, $serc]),
        'assign' => route('comps.heats_and_draws.draws.assign', [$comp, $serc]),
        'addTank' => route('comps.heats_and_draws.draws.addTank', [$comp, $serc]),
        'compactTanks' => route('comps.heats_and_draws.draws.compactTanks', [$comp, $serc]),
        'removeTank' => route('comps.heats_and_draws.draws.removeTank', [$comp, $serc]),
    ],
    'csrf' => csrf_token(),
]), message => askConfirm(message))" x-cloak>
        <div class="flex flex-col gap-6">
            <header class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="mb-1">SERC order</h2>
                    <p class="text-sm text-gray-600">Select two entries to swap. Select an entry and choose before or
                        after to reorder it, or select an unassigned entity to place it in the draw.</p>
                </div>
                <a href="{{ route('comps.heats_and_draws', $comp) }}" class="se-btn">Back to heats</a>
            </header>

            <div
                class="flex flex-col gap-3 rounded-md border border-gray-200 bg-gray-50 p-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-8 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white"
                        x-text="selection.type === 'draw' ? 'D' : selection.type === 'entity' ? 'E' : '-'">
                    </span>
                    <div>
                        <p class="font-semibold" x-text="selectionTitle"></p>
                        <p class="text-sm text-gray-600" x-text="selectionHint"></p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-if="selection.type">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="se-btn border-gray-300 bg-white text-gray-700 hover:border-se"
                                x-show="selection.type === 'entity'" @click="addSelectedToTank()"
                                x-text="useTanks ? 'Add to end of tank' : 'Add to end of draw'"></button>
                            <button type="button" class="se-btn" @click="clearSelection()">Clear selection</button>
                        </div>
                    </template>
                </div>
            </div>

            <section>
                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                    <div class="tabbed-bar mb-0" x-show="useTanks && tanks.length">
                        <template x-for="tank in tanks" :key="tank.number">
                            <div @click="activeTankNumber = tank.number"
                                :class="activeTankNumber === tank.number ? 'active' : ''">Tank <span
                                    x-text="tank.number"></span></div>
                        </template>
                    </div>
                    <button type="button" class="se-btn" x-show="useTanks" :disabled="busy" @click="addTank()">Add
                        tank</button>
                </div>

                <div class="mb-3 flex items-center justify-between gap-3" x-show="activeTank">
                    <div class="flex items-center gap-3">
                        <h3 class="mb-0" x-text="useTanks ? `Tank ${activeTankNumber}` : 'Draw'"></h3>
                        <button type="button" class="se-btn" x-show="hasTankGaps" :disabled="busy"
                            @click="compactTanks()">Compact draw order</button>
                    </div>
                    <button type="button" class="se-btn se-btn-danger" x-show="useTanks && activeTankNumber > 0"
                        :disabled="busy" @click="removeTank()">Remove tank</button>
                </div>

                <div class="grid grid-flow-row grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    x-show="activeTank">
                    <template x-for="draw in activeTank?.draws ?? []" :key="draw.id">
                        <div class="flex min-w-0 items-center gap-1" @mouseenter="hoveredDrawId = draw.id"
                            @mouseleave="if (hoveredDrawId === draw.id) hoveredDrawId = null">
                            <button type="button"
                                class="shrink-0 cursor-pointer rounded border border-gray-300 bg-white px-2 py-1 text-xs font-semibold text-gray-600 transition hover:border-se disabled:cursor-not-allowed disabled:opacity-40"
                                x-show="showMoveControls(draw)" x-transition.opacity :disabled="busy"
                                title="Move selected entry before this draw"
                                @click.stop="moveTo(draw.id, 'before')">Before</button>
                            <div class="se-card se-card-body se-card-hover flex min-w-0 flex-1 flex-row! items-center justify-between gap-2 text-sm transition-all duration-200"
                                @click="selectDraw(draw, $event)"
                                :class="{
                                    'se-card-active': selection.type === 'draw' && selection.value === draw.id,
                                    'border-se!': selection.type === 'draw' && selection.value !== draw.id &&
                                        hoveredDrawId === draw.id
                                }">
                                <button type="button" class="min-w-0 flex-1 cursor-pointer truncate text-left">
                                    <strong x-text="`${draw.draw}.`"></strong> <span
                                        x-text="draw.entity_name"></span></button>
                                <button type="button"
                                    class="shrink-0 cursor-pointer rounded p-1 text-gray-500 hover:text-red-600 tooltip-left"
                                    :aria-label="`Remove ${draw.entity_name} from draw ${draw.draw}`"
                                    :title="`Remove ${draw.entity_name} from draw ${draw.draw}`"
                                    @click.stop="removeDraw(draw)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" aria-hidden="true" focusable="false"
                                        class="size-5 shrink-0 transition-transform hover:rotate-90">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <button type="button"
                                class="shrink-0 cursor-pointer rounded border border-gray-300 bg-white px-2 py-1 text-xs font-semibold text-gray-600 transition hover:border-se disabled:cursor-not-allowed disabled:opacity-40"
                                x-show="showMoveControls(draw)" x-transition.opacity :disabled="busy"
                                title="Move selected entry after this draw"
                                @click.stop="moveTo(draw.id, 'after')">After</button>
                        </div>
                    </template>
                    <button type="button"
                        class="flex min-h-12 w-full cursor-pointer items-center justify-center rounded-md border-2 border-dashed border-gray-300 bg-gray-50 p-3 text-center text-xs font-semibold text-gray-600 transition hover:border-se disabled:cursor-not-allowed"
                        x-show="selection.type === 'draw' || selection.type === 'entity'" :disabled="busy"
                        @click="addSelectedToTank()" x-text="endOfTankLabel"></button>
                </div>
                <p class="text-sm text-gray-500" x-show="!activeTank && !useTanks">The draw is empty. Add an unassigned
                    entity below to start the order.</p>
            </section>

            <section class="se-card se-card-body" x-show="unassigned.length > 0">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="mb-1">Unassigned entities</h3>
                        <p class="text-sm text-gray-600">Select one, then move it before or after an assigned entry, or add
                            it to the end of the active tank.</p>
                    </div>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800"
                        x-text="unassigned.length"></span>
                </div>
                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                    <template x-for="entity in unassigned" :key="entity.id">
                        <button type="button"
                            class="cursor-pointer rounded-md border border-gray-200 bg-gray-50 p-3 text-left text-sm font-semibold hover:border-se hover:bg-white"
                            :class="selection.type === 'entity' && selection.value === entity.id ?
                                'border-se bg-white ring-2 ring-se/20' : ''"
                            @click="selectEntity(entity, $el)"><span x-text="entity.name"></span></button>
                    </template>
                </div>
            </section>

            @if (!$comp->getScoringSettings->use_tanks)
                <section class="se-card se-card-body">
                    <h3 class="mb-2">Regenerate Draw</h3>
                    <p class="mb-4 text-sm text-gray-600">Regenerating the draw will randomly assign teams. Manual changes
                        will be lost.</p>
                    <form action="{{ route('comps.heats_and_draws.draws.reset', [$comp, $serc]) }}"
                        @submit="doConfirm($event, 'Are you sure you want to reset the draw?')" method="post">
                        @csrf
                        <button class="se-btn se-btn-danger">Regenerate</button>
                    </form>
                </section>
            @else
                <section class="se-card se-card-body">
                    <h3 class="mb-2">Reset tank draw</h3>
                    <p class="mb-4 text-sm text-gray-600">Reopen tank setup to change tank assignments. Saving there will
                        regenerate the draw and replace manual ordering.</p>
                    <a href="{{ route('comps.heats_and_draws.draws.tank_setup', $comp) }}" class="se-btn">Open tank
                        setup</a>
                </section>
            @endif
        </div>

        <div class="fixed bottom-4 left-1/2 z-20 -translate-x-1/2 rounded-full bg-gray-900 px-5 py-3 text-sm font-semibold text-white shadow-lg"
            x-show="busy || status" x-transition>
            <span x-show="busy">Saving changes...</span><span x-show="!busy" x-text="status"></span>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        function drawEditor(config, confirmAction) {
            const normalizeTanks = tanks => {
                const parsed = Object.entries(tanks ?? {}).map(([number, draws]) => ({
                    number: Number(number),
                    draws: draws ?? []
                })).sort((first, second) => first.number - second.number);
                return parsed.length || config.useTanks ? parsed : [{
                    number: 0,
                    draws: []
                }];
            };
            const tanks = normalizeTanks(config.tanks);

            return {
                tanks,
                unassigned: config.unassigned,
                useTanks: config.useTanks,
                urls: config.urls,
                csrf: config.csrf,
                activeTankNumber: tanks[0]?.number ?? null,
                selection: {
                    type: null,
                    value: null
                },
                hoveredDrawId: null,
                busy: false,
                status: '',
                statusTimer: null,
                get activeTank() {
                    return this.tanks.find(tank => tank.number === this.activeTankNumber) ?? null;
                },
                get hasTankGaps() {
                    return this.tanks.some((tank, index) =>
                        (this.useTanks && tank.number !== index + 1) ||
                        tank.draws.some((draw, drawIndex) => Number(draw.draw) !== drawIndex + 1)
                    );
                },
                get selectionTitle() {
                    if (this.selection.type === 'draw') return 'Draw entry selected';
                    if (this.selection.type === 'entity') return 'Unassigned entity selected';
                    return 'Nothing selected';
                },
                get selectionHint() {
                    if (this.selection.type === 'draw')
                        return 'Select another entry to swap, or use its side controls to move this entry before or after it.';
                    if (this.selection.type === 'entity')
                        return 'Use a draw entry side control to place this entity before or after it, or add it to the end of this tank.';
                    return 'Select an entry to swap or move; hover a draw to reveal before/after controls.';
                },
                get endOfTankLabel() {
                    if (this.selection.type === 'entity') return 'Add selected entity to the end';
                    if (this.selection.type === 'draw') return 'Move selected draw to the end';
                    return `Select an entity or draw, then click to add it to the end of this ${this.useTanks ? 'tank' : 'draw'}.`;
                },
                showMoveControls(draw) {
                    return this.selection.type === 'entity' ||
                        (this.hoveredDrawId === draw.id && this.selection.type === 'draw' && this.selection.value !== draw
                            .id);
                },
                applyPayload(data) {
                    const previousTank = this.activeTankNumber;
                    this.tanks = normalizeTanks(data.tanks);
                    this.unassigned = data.unassigned;
                    this.activeTankNumber = this.tanks.some(tank => tank.number === previousTank) ?
                        previousTank :
                        this.tanks[0]?.number ?? null;
                    this.clearSelection();
                },
                selectDraw(draw, event) {
                    if (this.busy) return;
                    if (this.selection.type === 'entity') {
                        event?.target.closest?.('button')?.blur();
                        return;
                    }
                    if (!this.selection.type) {
                        this.selection = {
                            type: 'draw',
                            value: draw.id
                        };
                        return;
                    }
                    if (this.selection.type === 'draw' && this.selection.value === draw.id) {
                        this.clearSelection();
                        event?.target.closest?.('button')?.blur();
                        return;
                    }
                    if (this.selection.type === 'draw') {
                        this.post(this.urls.swap, {
                            swap_from: this.selection.value,
                            swap_to: draw.id
                        });
                    }
                },
                selectEntity(entity, button) {
                    if (this.busy) return;
                    if (this.selection.type === 'entity' && this.selection.value === entity.id) {
                        this.clearSelection();
                        button?.blur();
                        return;
                    }
                    this.selection = {
                        type: 'entity',
                        value: entity.id
                    };
                },
                moveTo(target, placement) {
                    this.post(this.urls.move, {
                        source_type: this.selection.type,
                        source: this.selection.value,
                        target,
                        placement,
                    });
                },
                addSelectedToTank() {
                    if (this.busy || this.activeTankNumber === null) return;
                    if (this.selection.type === 'entity') {
                        this.post(this.urls.assign, {
                            entity: this.selection.value,
                            tank: this.activeTankNumber
                        });
                        return;
                    }
                    if (this.selection.type === 'draw') {
                        this.post(this.urls.move, {
                            source_type: 'draw',
                            source: this.selection.value,
                            target_tank: this.activeTankNumber,
                            placement: 'end'
                        });
                        return;
                    }
                    this.setStatus('Select a draw or unassigned entity first.');
                },
                async removeDraw(draw) {
                    if (!await confirmAction(
                            `Remove ${draw.entity_name} from draw ${draw.draw}? Its position will remain open.`))
                        return;
                    await this.post(this.urls.remove, {
                        draw: draw.id
                    });
                },
                async addTank() {
                    await this.post(this.urls.addTank, {});
                    this.activeTankNumber = this.tanks[this.tanks.length - 1]?.number ?? null;
                },
                async compactTanks() {
                    if (!await confirmAction(
                            'Compact the draw order? Tank numbers and draw positions will be renumbered consecutively, preserving their current order.'
                        )) return;
                    await this.post(this.urls.compactTanks, {});
                },
                async removeTank() {
                    if (!await confirmAction(
                            `Remove Tank ${this.activeTankNumber}? Its entities will become unassigned.`)) return;
                    await this.post(this.urls.removeTank, {
                        tank: this.activeTankNumber
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
                    this.statusTimer = setTimeout(() => this.status = '', 3000);
                },
                async post(url, values) {
                    if (this.busy) return;
                    this.busy = true;
                    this.status = '';
                    const body = new FormData();
                    body.append('_token', this.csrf);
                    Object.entries(values).forEach(([key, value]) => body.append(key, value));
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json'
                            },
                            body,
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Unable to save the draw.');
                        this.applyPayload(data);
                        this.setStatus('Draw updated');
                        return data;
                    } catch (error) {
                        this.setStatus(error.message || 'Unable to save the draw.');
                    } finally {
                        this.busy = false;
                    }
                },
            };
        }
    </script>
@endsection
