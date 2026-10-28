<!--
    The draw editor (manage.competitions.heats_and_draws.draws.edit), in CompetitionLayout, full width: the
    competition's one Draw, a Tank at a time in Tabs when it uses tanks. Select an entry, then another to
    swap them, or Before/After beside one (or the end of the Tank) to move it there; entries not in the
    Draw are listed under it to add the same way. Each change shows at once, lit up, and saves on its own
    (ChangeSaver). Tank changes and compacting confirm first, then wait for the saved Draw.
-->
<script lang="ts">
    import { addTank, assign, compactTanks, move, regenerate, remove, removeTank, swap, tankSetup } from "@/actions/App/Http/Controllers/Manage/DrawsController";
    import { index as overview } from "@/actions/App/Http/Controllers/Manage/HeatsAndDrawsController";
    import { confirm } from "@/lib/confirm";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import Tabs from "@/manage/components/Tabs.svelte";
    import { ChangeSaver, changedClass, selectedClass } from "@/manage/orders.svelte";
    import RemoveButton from "@/manage/orders/RemoveButton.svelte";
    import SelectionBar from "@/manage/orders/SelectionBar.svelte";
    import UnassignedList from "@/manage/orders/UnassignedList.svelte";
    import type { CompetitionHeader, DrawTank, UnplacedEntity } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        useTanks,
        tanks,
        unassigned,
    }: {
        competition: CompetitionHeader;
        useTanks: boolean;
        /** Without tanks, one Tank numbered 0 */
        tanks: DrawTank[];
        unassigned: UnplacedEntity[];
    } = $props();

    type Entry = DrawTank["draws"][number];
    type Selection = { kind: "draw"; id: number } | { kind: "entity"; id: number } | null;

    const comp = $derived(competition.id);

    // The page's own copy, changed as soon as you make a change, and replaced by the props each save brings back
    let list = $derived(tanks.map((tank) => ({ number: tank.number, draws: [...tank.draws] })));
    let waiting = $derived([...unassigned]);
    let selection = $state<Selection>(null);
    let chosenTank = $state<string>("");

    const saver = new ChangeSaver();

    const activeTank = $derived(list.find((tank) => String(tank.number) === chosenTank) ?? list[0] ?? null);
    const tabs = $derived(list.map((tank) => ({ id: String(tank.number), label: `Tank ${tank.number}` })));
    // A Draw renumbered or a Tank left out of order, as removing leaves them
    const hasGaps = $derived(list.some((tank, index) => (useTanks && tank.number !== index + 1) || tank.draws.some((entry, at) => entry.draw !== at + 1)));

    const drawKey = (id: number) => `draw:${id}`;
    const renumber = (draws: Entry[]) => draws.map((entry, index) => ({ ...entry, draw: index + 1 }));

    function locate(id: number) {
        for (const tank of list) {
            const entry = tank.draws.find((each) => each.id === id);
            if (entry) return { tank: tank.number, entry };
        }
        return null;
    }

    const selectedName = $derived.by(() => {
        if (!selection) return null;
        const id = selection.id;
        return selection.kind === "draw" ? locate(id)?.entry.name : waiting.find((each) => each.id === id)?.name;
    });

    function selectDraw(entry: Entry) {
        if (saver.busy) return;
        if (!selection) {
            selection = { kind: "draw", id: entry.id };
            return;
        }
        if (selection.kind !== "draw") return;
        if (selection.id === entry.id) {
            selection = null;
            return;
        }

        const from = locate(selection.id)!;
        selection = null;
        // Each takes the other's place and number
        const swapped = (each: Entry) =>
            each.id === from.entry.id ? { ...entry, draw: from.entry.draw } : each.id === entry.id ? { ...from.entry, draw: entry.draw } : each;
        list = list.map((tank) => ({ ...tank, draws: tank.draws.map(swapped) }));
        saver.save(swap(comp).url, { from: from.entry.id, to: entry.id }, [drawKey(from.entry.id), drawKey(entry.id)]);
    }

    function selectEntity(id: number) {
        if (saver.busy) return;
        selection = selection?.kind === "entity" && selection.id === id ? null : { kind: "entity", id };
    }

    /** The entry being moved, taken out of its Tank (renumbering what's left when it's leaving for another) */
    function takeOut(targetTank: number): { entry: Entry; rest: typeof list } | null {
        if (selection?.kind === "draw") {
            const from = locate(selection.id)!;
            const rest = list.map((tank) => {
                if (tank.number !== from.tank) return tank;
                const draws = tank.draws.filter((each) => each.id !== from.entry.id);
                return { ...tank, draws: tank.number === targetTank ? draws : renumber(draws) };
            });
            return { entry: from.entry, rest };
        }
        if (selection?.kind === "entity") {
            const id = selection.id;
            const entity = waiting.find((each) => each.id === id)!;
            return { entry: { id: -entity.id, entity: entity.id, name: entity.name, draw: 0 }, rest: list };
        }
        return null;
    }

    function moveTo(target: Entry, placement: "before" | "after") {
        const tank = locate(target.id)!.tank;
        const source = selection;
        const taken = takeOut(tank);
        if (saver.busy || !source || !taken) return;

        list = taken.rest.map((each) => {
            if (each.number !== tank) return each;
            const at = each.draws.findIndex((entry) => entry.id === target.id) + (placement === "after" ? 1 : 0);
            return { ...each, draws: renumber([...each.draws.slice(0, at), taken.entry, ...each.draws.slice(at)]) };
        });
        if (source.kind === "entity") waiting = waiting.filter((each) => each.id !== source.id);
        selection = null;
        saver.save(move(comp).url, { source_type: source.kind, source: source.id, target: target.id, placement }, [drawKey(taken.entry.id)]);
    }

    function moveToEnd() {
        if (!activeTank) return;
        const tank = activeTank.number;
        const source = selection;
        const taken = takeOut(tank);
        if (saver.busy || !source || !taken) return;

        list = taken.rest.map((each) => {
            if (each.number !== tank) return each;
            // A moved Draw renumbers its new Tank; an added entry just goes after the highest number
            return source.kind === "draw"
                ? { ...each, draws: renumber([...each.draws, taken.entry]) }
                : { ...each, draws: [...each.draws, { ...taken.entry, draw: Math.max(0, ...each.draws.map((entry) => entry.draw)) + 1 }] };
        });
        selection = null;

        if (source.kind === "entity") {
            waiting = waiting.filter((each) => each.id !== source.id);
            saver.save(assign(comp).url, { entity: source.id, tank }, [drawKey(taken.entry.id)]);
        } else {
            saver.save(move(comp).url, { source_type: "draw", source: source.id, target_tank: tank, placement: "end" }, [drawKey(taken.entry.id)]);
        }
    }

    async function removeEntry(entry: Entry) {
        if (saver.busy) return;
        const ok = await confirm({
            title: `Take ${entry.name} out of the draw?`,
            description: `Draw ${entry.draw} stays open until you compact the draw.`,
            confirmLabel: "Take out",
            confirmVariant: "danger",
        });
        if (!ok) return;

        list = list.map((tank) => ({ ...tank, draws: tank.draws.filter((each) => each.id !== entry.id) }));
        waiting = [...waiting, { id: entry.entity, name: entry.name }];
        selection = null;
        saver.save(remove(comp).url, { draw: entry.id });
    }

    function newTank() {
        if (saver.busy) return;
        const number = Math.max(0, ...list.map((tank) => tank.number)) + 1;
        list = [...list, { number, draws: [] }];
        chosenTank = String(number);
        saver.save(addTank(comp).url, {});
    }

    async function dropTank() {
        if (saver.busy || !activeTank) return;
        const tank = activeTank;
        const ok = await confirm({
            title: `Remove Tank ${tank.number}?`,
            description:
                tank.draws.length > 0
                    ? `Its ${tank.draws.length === 1 ? "entry goes" : `${tank.draws.length} entries go`} back to Unassigned, and later tanks move down a number.`
                    : "Later tanks move down a number.",
            confirmLabel: "Remove tank",
            confirmVariant: "danger",
        });
        if (!ok) return;

        list = list.filter((each) => each !== tank).map((each) => (each.number > tank.number ? { ...each, number: each.number - 1 } : each));
        waiting = [...waiting, ...tank.draws.map((entry) => ({ id: entry.entity, name: entry.name }))];
        chosenTank = String(Math.max(1, tank.number - 1));
        selection = null;
        saver.save(removeTank(comp).url, { tank: tank.number });
    }

    async function compact() {
        if (saver.busy) return;
        const ok = await confirm({
            title: "Compact the draw?",
            description: `${useTanks ? "Tanks and draws are" : "Draws are"} numbered from 1 again with no gaps, keeping their order.`,
            confirmLabel: "Compact",
        });
        if (!ok) return;

        list = list.map((tank, index) => ({ number: useTanks ? index + 1 : tank.number, draws: renumber(tank.draws) }));
        saver.save(compactTanks(comp).url, {});
    }

    async function redraw() {
        const ok = await confirm({
            title: "Regenerate the draw?",
            description: "Every entry is drawn again at random, and every change made here is lost.",
            confirmLabel: "Regenerate",
            confirmVariant: "danger",
        });
        if (ok) router.post(regenerate(comp).url, {}, { preserveScroll: true });
    }

    const hint = $derived(
        selection?.kind === "draw"
            ? `${selectedName} selected. Select another entry to swap them, or Before or After to move it.`
            : selection?.kind === "entity"
              ? `${selectedName} selected. Select Before or After an entry, or the end of the ${useTanks ? "tank" : "draw"}, to add it there.`
              : "Select an entry to swap or move it.",
    );

    const sideButton =
        "shrink-0 cursor-pointer rounded border border-v2-border bg-v2-card px-2 py-1 text-xs font-semibold text-v2-muted hover:border-v2-teal hover:text-v2-ink disabled:cursor-not-allowed disabled:opacity-40";
</script>

<svelte:head>
    <title>Edit draw - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet tankPanel(tank: (typeof list)[number])}
    <div class="flex flex-wrap items-center gap-2 py-3">
        {#if hasGaps}
            <button type="button" class="v2-btn v2-btn-sm" disabled={saver.busy} onclick={compact}>Compact draw</button>
        {/if}
        {#if useTanks}
            <button type="button" class="v2-btn v2-btn-sm v2-btn-danger ml-auto" disabled={saver.busy} onclick={dropTank}>Remove Tank {tank.number}</button>
        {/if}
    </div>

    <ol class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3" aria-busy={saver.busy}>
        {#each tank.draws as entry (entry.id)}
            {@const selected = selection?.kind === "draw" && selection.id === entry.id}
            {@const sides = selection !== null && !selected}
            <li class="flex min-w-0 items-center gap-1">
                {#if sides}<button type="button" class={sideButton} disabled={saver.busy} onclick={() => moveTo(entry, "before")}>Before</button>{/if}
                <div
                    class={[
                        "flex min-w-0 flex-1 items-center gap-2 rounded-md border border-v2-border bg-v2-card text-sm transition-colors hover:border-v2-teal motion-reduce:transition-none",
                        selected && selectedClass,
                        saver.changed.has(drawKey(entry.id)) && changedClass,
                    ]}
                >
                    <button
                        type="button"
                        class="flex min-w-0 flex-1 cursor-pointer items-baseline gap-2 py-2 pl-3 text-left"
                        aria-pressed={selected}
                        onclick={() => selectDraw(entry)}
                    >
                        <span class="w-6 shrink-0 text-right text-v2-muted tabular-nums">{entry.draw}</span>
                        <span class="truncate font-semibold">{entry.name}</span>
                    </button>
                    <RemoveButton class="mr-1" label="Take {entry.name} out of the draw" title="Take out of the draw" onclick={() => removeEntry(entry)} />
                </div>
                {#if sides}<button type="button" class={sideButton} disabled={saver.busy} onclick={() => moveTo(entry, "after")}>After</button>{/if}
            </li>
        {/each}
        {#if selection}
            <li>
                <button
                    type="button"
                    class="flex min-h-10 w-full cursor-pointer items-center justify-center rounded-md border border-dashed border-v2-border px-3 py-2 text-sm font-semibold text-v2-muted hover:border-v2-teal hover:text-v2-ink"
                    disabled={saver.busy}
                    onclick={moveToEnd}>{selection.kind === "entity" ? "Add" : "Move"} to the end{useTanks ? ` of Tank ${tank.number}` : ""}</button
                >
            </li>
        {/if}
    </ol>
    {#if tank.draws.length === 0 && !selection}
        <EmptyState>Nobody in {useTanks ? "this tank" : "the draw"} yet. Select an entry from Unassigned to add it.</EmptyState>
    {/if}
{/snippet}

<PageHeading
    back={{ href: overview(comp).url, label: "Heats & Draws" }}
    title="Edit draw"
    lead="The running order every SERC shares. Every change saves as you make it."
/>

<SelectionBar {hint} selected={selection !== null} onclear={() => (selection = null)} />

{#if useTanks}
    <div class="flex flex-wrap items-start gap-3">
        {#if list.length > 0}
            <div class="min-w-0 flex-1">
                <Tabs
                    {tabs}
                    bind:value={() => (activeTank ? String(activeTank.number) : ""), (id) => (chosenTank = id)}
                    id="draw-tanks"
                    label="Tanks"
                >
                    {#snippet panel(id)}
                        {@const tank = list.find((each) => String(each.number) === id)}
                        {#if tank}{@render tankPanel(tank)}{/if}
                    {/snippet}
                </Tabs>
            </div>
        {:else}
            <p class="flex-1 text-[0.9375rem] text-v2-muted">No tanks yet. Add one, or use <Link href={tankSetup(comp).url} class="v2-link-inline">Tank setup</Link> to draw each League into its own.</p>
        {/if}
        <button type="button" class="v2-btn v2-btn-sm" disabled={saver.busy} onclick={newTank}>Add tank</button>
    </div>
{:else if activeTank}
    {@render tankPanel(activeTank)}
{/if}

<UnassignedList
    id="de-unassigned"
    entries={waiting}
    selected={selection?.kind === "entity" ? selection.id : null}
    onselect={selectEntity}
    lead="Select one, then where to put it."
    done="Every entry is in the draw."
/>

<section aria-labelledby="de-redraw" class="mt-10 max-w-xl border-t border-v2-border pt-6">
    {#if useTanks}
        <h3 id="de-redraw" class="text-lg font-extrabold uppercase">Redraw by League</h3>
        <p class="mt-1 text-[0.9375rem] text-v2-muted">Tank setup puts each League in a tank. Saving it draws everyone again and replaces this order.</p>
        <Link href={tankSetup(comp).url} class="v2-btn mt-3">Open Tank setup</Link>
    {:else}
        <h3 id="de-redraw" class="text-lg font-extrabold uppercase">Regenerate draw</h3>
        <p class="mt-1 text-[0.9375rem] text-v2-muted">Draw every entry again at random. Every change made here is lost.</p>
        <button type="button" class="v2-btn v2-btn-danger mt-3" onclick={redraw}>Regenerate draw</button>
    {/if}
</section>
