<!--
    The heat editor (manage.competitions.heats_and_draws.heats.edit), in CompetitionLayout, full width:
    an event's Heats in one Pool as a lane × Heat table. Select an entry, then an open lane to move it
    or another entry to swap them; select two Heat headers to swap whole Heats. Each change shows at
    once, lit up, and saves on its own (ChangeSaver). Chips pick the event (with heats per event) and
    the Pool (with pools); entries not in any Heat are listed under the table to place.
-->
<script lang="ts">
    import { index as overview } from "@/actions/App/Http/Controllers/Manage/HeatsAndDrawsController";
    import { assign, deleteHeat, edit, insertHeat, reset, swap, swapHeats, unassign } from "@/actions/App/Http/Controllers/Manage/HeatsController";
    import { confirm } from "@/lib/confirm";
    import SmoothHeight from "@/manage/components/SmoothHeight.svelte";
    import { ChangeSaver } from "@/manage/orders.svelte";
    import type { CompetitionHeader, HeatCell, HeatGrid, NamedOption, UnplacedEntity } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        perEvent,
        events,
        event,
        pools,
        pool,
        grid,
        unassigned,
        useSeeds,
        canReset,
    }: {
        competition: CompetitionHeader;
        perEvent: boolean;
        events: NamedOption[];
        event: NamedOption;
        pools: NamedOption[];
        /** The Pool being edited, null without pools */
        pool: number | null;
        grid: HeatGrid;
        unassigned: UnplacedEntity[];
        useSeeds: boolean;
        /** Without pools: pooled heats have no generated layout to go back to */
        canReset: boolean;
    } = $props();

    type Heat = HeatGrid["heats"][number];
    type Selection = { kind: "entity"; entity: number } | { kind: "heat"; number: number } | null;

    const comp = $derived(competition.id);
    const args = $derived({ comp, event: event.id });
    // Every edit names its Pool, as heat numbers restart in each
    const inPool = $derived(pool ? { pool } : {});

    // The page's own copy, changed as soon as you make a change, and replaced by the props each save brings back
    let heats = $derived(grid.heats.map((heat) => ({ number: heat.number, lanes: [...heat.lanes] })));
    let waiting = $derived([...unassigned]);
    let selection = $state<Selection>(null);

    const saver = new ChangeSaver();

    const cellKey = (heat: number, lane: number) => `${heat}:${lane}`;
    const heatKey = (heat: number) => `heat:${heat}`;

    function locate(entity: number) {
        for (const heat of heats) {
            const index = heat.lanes.findIndex((cell) => cell?.entity === entity);
            if (index !== -1) return { heat: heat.number, lane: index + 1, cell: heat.lanes[index]! };
        }
        return null;
    }

    /** The Heats with one lane changed */
    function withLane(list: Heat[], heat: number, lane: number, cell: HeatCell | null): Heat[] {
        return list.map((each) => (each.number === heat ? { ...each, lanes: each.lanes.map((old, index) => (index === lane - 1 ? cell : old)) } : each));
    }

    const renumber = (list: Heat[]) => list.map((heat, index) => ({ ...heat, number: index + 1 }));

    const selectedName = $derived.by(() => {
        if (selection?.kind !== "entity") return null;
        const entity = selection.entity;
        return locate(entity)?.cell.name ?? waiting.find((each) => each.id === entity)?.name ?? null;
    });
    const selectedIsPlaced = $derived(selection?.kind === "entity" && locate(selection.entity) !== null);

    function selectCell(heat: number, lane: number) {
        if (saver.busy) return;
        const target = heats.find((each) => each.number === heat)?.lanes[lane - 1] ?? null;

        if (selection?.kind !== "entity") {
            selection = target ? { kind: "entity", entity: target.entity } : null;
            return;
        }
        if (target?.entity === selection.entity) {
            selection = null;
            return;
        }

        const entity = selection.entity;
        const source = locate(entity);
        selection = null;

        if (target) {
            // An entry that isn't in a Heat can only go in an open lane, so this picks the other one instead
            if (!source) {
                selection = { kind: "entity", entity: target.entity };
                return;
            }
            heats = withLane(withLane(heats, source.heat, source.lane, target), heat, lane, source.cell);
            saver.save(swap(args).url, { from: source.cell.id, to: target.id, ...inPool }, [cellKey(source.heat, source.lane), cellKey(heat, lane)]);
            return;
        }

        const waitingEntity = waiting.find((each) => each.id === entity);
        const moving = source?.cell ?? { id: 0, entity, name: waitingEntity?.name ?? "", seed: waitingEntity?.seed ?? null };
        heats = withLane(source ? withLane(heats, source.heat, source.lane, null) : heats, heat, lane, moving);
        waiting = waiting.filter((each) => each.id !== entity);
        saver.save(assign(args).url, { entity, heat, lane, ...inPool }, [cellKey(heat, lane)]);
    }

    function selectHeat(number: number) {
        if (saver.busy) return;
        if (selection?.kind !== "heat") {
            selection = { kind: "heat", number };
            return;
        }
        const first = selection.number;
        selection = null;
        if (first === number) return;

        const lanesOf = (n: number) => heats.find((heat) => heat.number === n)!.lanes;
        const [firstLanes, secondLanes] = [lanesOf(first), lanesOf(number)];
        heats = heats.map((heat) => (heat.number === first ? { ...heat, lanes: secondLanes } : heat.number === number ? { ...heat, lanes: firstLanes } : heat));
        saver.save(swapHeats(args).url, { first, second: number, ...inPool }, [heatKey(first), heatKey(number)]);
    }

    function selectEntity(entity: number) {
        if (saver.busy) return;
        selection = selection?.kind === "entity" && selection.entity === entity ? null : { kind: "entity", entity };
    }

    function addHeat(position: number) {
        if (saver.busy) return;
        const empty = { number: position, lanes: Array<HeatCell | null>(grid.lanes).fill(null) };
        heats = renumber([...heats.slice(0, position - 1), empty, ...heats.slice(position - 1)]);
        selection = null;
        saver.save(insertHeat(args).url, { position, ...inPool }, [heatKey(position)]);
    }

    async function removeHeat(number: number) {
        if (saver.busy) return;
        const removed = heats.find((heat) => heat.number === number)!;
        const entries = removed.lanes.filter((cell) => cell !== null);
        const ok = await confirm({
            title: `Remove Heat ${number}?`,
            description: entries.length > 0 ? `Its ${entries.length === 1 ? "entry goes" : `${entries.length} entries go`} back to Unassigned, and later heats move up.` : "Later heats move up.",
            confirmLabel: "Remove heat",
            confirmVariant: "danger",
        });
        if (!ok) return;

        heats = renumber(heats.filter((heat) => heat.number !== number));
        waiting = [...waiting, ...entries.map((cell) => ({ id: cell.entity, name: cell.name, seed: cell.seed }))];
        selection = null;
        saver.save(deleteHeat(args).url, { heat: number, ...inPool });
    }

    function unassignSelected() {
        if (saver.busy || selection?.kind !== "entity") return;
        const source = locate(selection.entity);
        if (!source) return;

        heats = withLane(heats, source.heat, source.lane, null);
        waiting = [...waiting, { id: source.cell.entity, name: source.cell.name, seed: source.cell.seed }];
        selection = null;
        saver.save(unassign(args).url, { entity: source.cell.entity, ...inPool }, [`entity:${source.cell.entity}`]);
    }

    /** Swaps in another event's or Pool's Heats, keeping the rest of the page */
    function show(eventId: number, poolId: number | null) {
        if (saver.busy) return;
        selection = null;
        router.get(edit({ comp, event: eventId }, { query: poolId ? { pool: poolId } : {} }).url, {}, {
            only: ["event", "pool", "grid", "unassigned"],
            preserveState: true,
            preserveScroll: true,
        });
    }

    async function resetHeats() {
        const ok = await confirm({
            title: "Reset the heats?",
            description: "They go back to the generated layout, and every change made here is lost.",
            confirmLabel: "Reset heats",
            confirmVariant: "danger",
        });
        if (ok) router.post(reset(args).url, {}, { preserveScroll: true });
    }

    const hint = $derived(
        selection?.kind === "heat"
            ? `Heat ${selection.number} selected. Select another heat to swap them.`
            : selection?.kind === "entity"
              ? `${selectedName} selected. Select an open lane to move it there${selectedIsPlaced ? ", or another entry to swap them" : ""}.`
              : "Select an entry, then an open lane to move it or another entry to swap them. Select two heat headers to swap whole heats.",
    );

    function cellClass(heat: number, lane: number, cell: HeatCell | null) {
        const selected = selection?.kind === "entity" && cell?.entity === selection.entity;
        const lit = saver.changed.has(cellKey(heat, lane)) || saver.changed.has(heatKey(heat));
        return [
            "flex min-h-14 w-full cursor-pointer flex-col justify-center rounded-md border px-3 py-2 text-left text-sm transition-colors motion-reduce:transition-none",
            cell ? "border-v2-border bg-v2-card hover:border-v2-teal" : "border-dashed border-v2-border text-v2-muted hover:border-v2-teal hover:text-v2-ink",
            selected && "border-v2-teal! bg-v2-teal-soft! ring-2 ring-v2-teal/30",
            lit && "animate-changed motion-reduce:animate-none",
        ];
    }
</script>

<svelte:head>
    <title>Edit heats - {competition.name} - Scoring.Events</title>
</svelte:head>

<div class="mb-6">
    <Link href={overview(comp).url} class="v2-link-muted text-sm">← Heats & Draws</Link>
    <h2 class="mt-1 text-2xl font-extrabold uppercase wrap-anywhere">Edit heats{#if perEvent}: {event.name}{/if}</h2>
    <p class="mt-[0.35rem] text-[0.9375rem]/[1.6] text-v2-muted">Every change saves as you make it.</p>
</div>

{#if (perEvent && events.length > 1) || pools.length > 1}
    <div class="mb-4 flex flex-col gap-2">
        {#if perEvent && events.length > 1}
            <div class="flex flex-wrap gap-2" role="group" aria-label="Event">
                {#each events as option (option.id)}
                    <button type="button" class="v2-chip" aria-pressed={option.id === event.id} onclick={() => show(option.id, pool)}>{option.name}</button>
                {/each}
            </div>
        {/if}
        {#if pools.length > 1}
            <div class="flex flex-wrap gap-2" role="group" aria-label="Pool">
                {#each pools as option (option.id)}
                    <button type="button" class="v2-chip" aria-pressed={option.id === pool} onclick={() => show(event.id, option.id)}>{option.name}</button>
                {/each}
            </div>
        {/if}
    </div>
{/if}

<!-- What's selected and what to do next, kept in view while the table scrolls -->
<div class="sticky top-0 z-20 -mx-1 mb-3 flex flex-wrap items-center gap-3 bg-v2-bg px-1 py-2" aria-live="polite">
    <p class="min-w-0 flex-1 text-[0.9375rem] text-v2-muted">{hint}</p>
    {#if selectedIsPlaced}
        <button type="button" class="v2-btn v2-btn-sm" disabled={saver.busy} onclick={unassignSelected}>Unassign</button>
    {/if}
    {#if selection}
        <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm" onclick={() => (selection = null)}>Clear selection</button>
    {/if}
</div>

<!-- Fades in as another event or Pool is picked, the height easing to fit -->
<SmoothHeight>
    {#key `${event.id}:${pool}`}
        <div class="animate-fade-in motion-reduce:animate-none">
            {#if heats.length === 0}
                <div class="rounded-md border border-dashed border-v2-border px-4 py-8 text-center">
                    <p class="text-[0.9375rem] text-v2-muted">No heats yet.</p>
                    <button type="button" class="v2-btn mt-3" disabled={saver.busy} onclick={() => addHeat(1)}>Add a heat</button>
                </div>
            {:else}
                <div class="overflow-x-auto pb-2" aria-busy={saver.busy}>
                    <table class="border-separate border-spacing-x-2 border-spacing-y-1.5 text-left">
                        <thead>
                            <tr>
                                <th scope="col" class="sticky left-0 z-10 w-px bg-v2-bg pr-2 text-sm font-semibold text-v2-muted">Lane</th>
                                {#each heats as heat (heat.number)}
                                    <th scope="col" class="min-w-52 pb-1">
                                        <div class="flex items-center gap-1">
                                            <button
                                                type="button"
                                                class={[
                                                    "v2-chip",
                                                    saver.changed.has(heatKey(heat.number)) && "animate-changed motion-reduce:animate-none",
                                                ]}
                                                aria-pressed={selection?.kind === "heat" && selection.number === heat.number}
                                                onclick={() => selectHeat(heat.number)}>Heat {heat.number}</button
                                            >
                                            <button
                                                type="button"
                                                class="ml-auto cursor-pointer rounded px-1.5 py-1 text-sm font-semibold text-v2-muted hover:bg-v2-row-hover hover:text-v2-ink"
                                                title="Add a heat after Heat {heat.number}"
                                                aria-label="Add a heat after Heat {heat.number}"
                                                onclick={() => addHeat(heat.number + 1)}>+</button
                                            >
                                            <button
                                                type="button"
                                                class="cursor-pointer rounded px-1.5 py-1 text-sm font-semibold text-v2-muted hover:bg-v2-danger-soft hover:text-v2-danger-text"
                                                title="Remove Heat {heat.number}"
                                                aria-label="Remove Heat {heat.number}"
                                                onclick={() => removeHeat(heat.number)}>×</button
                                            >
                                        </div>
                                    </th>
                                {/each}
                            </tr>
                        </thead>
                        <tbody>
                            {#each { length: grid.lanes } as _, index (index)}
                                {@const lane = index + 1}
                                <tr>
                                    <th scope="row" class="sticky left-0 z-10 bg-v2-bg pr-2 text-sm font-semibold text-v2-muted tabular-nums">{lane}</th>
                                    {#each heats as heat (heat.number)}
                                        {@const cell = heat.lanes[index]}
                                        <td>
                                            <button type="button" class={cellClass(heat.number, lane, cell)} onclick={() => selectCell(heat.number, lane)}>
                                                {#if cell}
                                                    <span class="font-semibold wrap-anywhere">{cell.name}</span>
                                                    {#if useSeeds && cell.seed}<span class="text-xs text-v2-muted tabular-nums">{cell.seed}</span>{/if}
                                                {:else}
                                                    <span class="text-xs font-semibold tracking-wide uppercase">Open</span>
                                                {/if}
                                            </button>
                                        </td>
                                    {/each}
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            {/if}
        </div>
    {/key}
</SmoothHeight>

<section aria-labelledby="he-unassigned" class="mt-8">
    <div class="flex items-center gap-3">
        <h3 id="he-unassigned" class="text-lg font-extrabold uppercase">Unassigned</h3>
        <span class="v2-badge">{waiting.length}</span>
    </div>
    {#if waiting.length === 0}
        <p class="mt-2 text-[0.9375rem] text-v2-muted">Every entry is in a lane.</p>
    {:else}
        <p class="mt-1 text-[0.9375rem] text-v2-muted">Select one, then an open lane to put it in.</p>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            {#each waiting as entity (entity.id)}
                <button
                    type="button"
                    class={[
                        "flex cursor-pointer flex-col rounded-md border border-v2-border bg-v2-card px-3 py-2 text-left text-sm hover:border-v2-teal",
                        selection?.kind === "entity" && selection.entity === entity.id && "border-v2-teal bg-v2-teal-soft ring-2 ring-v2-teal/30",
                        saver.changed.has(`entity:${entity.id}`) && "animate-changed motion-reduce:animate-none",
                    ]}
                    aria-pressed={selection?.kind === "entity" && selection.entity === entity.id}
                    onclick={() => selectEntity(entity.id)}
                >
                    <span class="font-semibold wrap-anywhere">{entity.name}</span>
                    {#if useSeeds && entity.seed}<span class="text-xs text-v2-muted tabular-nums">{entity.seed}</span>{/if}
                </button>
            {/each}
        </div>
    {/if}
</section>

{#if canReset}
    <section aria-labelledby="he-reset" class="mt-10 max-w-xl border-t border-v2-border pt-6">
        <h3 id="he-reset" class="text-lg font-extrabold uppercase">Reset heats</h3>
        <p class="mt-1 text-[0.9375rem] text-v2-muted">Go back to the generated layout. Every change made here is lost.</p>
        <button type="button" class="v2-btn v2-btn-danger mt-3" onclick={resetHeats}>Reset heats</button>
    </section>
{/if}
