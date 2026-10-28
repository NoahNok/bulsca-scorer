<!--
    Heats & Draws (manage.competitions.heats_and_draws), in CompetitionLayout. The first column shows the
    Heats as a lane × Heat table, one event (with heats per event) and one Pool (with pools) at a time,
    picked by chips that are kept in the url, then the Draw tank by tank. The sticky actions column on the
    right generates and edits them, opens Tank setup, and holds the switches that show them publicly.
-->
<script lang="ts">
    import { edit as editDraw, generate as generateDraw, tankSetup } from "@/actions/App/Http/Controllers/Manage/DrawsController";
    import { visibility } from "@/actions/App/Http/Controllers/Manage/HeatsAndDrawsController";
    import { edit as editHeats, generate as generateHeats } from "@/actions/App/Http/Controllers/Manage/HeatsController";
    import ActionSwitch from "@/manage/components/ActionSwitch.svelte";
    import Alert from "@/manage/components/Alert.svelte";
    import ChipGroup from "@/manage/components/ChipGroup.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import SmoothHeight from "@/manage/components/SmoothHeight.svelte";
    import SidebarPage, { sidebarRow } from "@/manage/components/SidebarPage.svelte";
    import type { CompetitionHeader, DrawTank, HeatGrid, NamedOption } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        heats,
        draw,
        show,
        eventsUrl,
    }: {
        competition: CompetitionHeader;
        heats: {
            perEvent: boolean;
            usePools: boolean;
            /** The events that hold Heats: every one with heats per event, otherwise only the first */
            events: NamedOption[];
            event: number | null;
            pools: NamedOption[];
            pool: number | null;
            /** Null without speed events */
            grid: HeatGrid | null;
            /** Whether any event has Heats yet */
            generated: boolean;
            canGenerate: boolean;
        };
        draw: { hasSerc: boolean; useTanks: boolean; tanks: DrawTank[]; generated: boolean };
        show: { heats: boolean; draws: boolean };
        eventsUrl: string;
    } = $props();

    const comp = $derived(competition.id);
    // The editor opens on the Pool picked here
    const poolName = $derived(heats.pools.find((option) => option.id === heats.pool)?.name ?? null);
    const editHeatsUrl = $derived(heats.event ? editHeats({ comp, event: heats.event }, { query: heats.pool ? { pool: heats.pool } : {} }).url : null);

    let generating = $state(false);

    function pick(data: { event?: number | null; pool?: number | null }) {
        const picked = { event: heats.event, pool: heats.pool, ...data };
        router.reload({ data: { event: picked.event ?? undefined, pool: picked.pool ?? undefined }, only: ["heats"] });
    }

    function post(url: string) {
        router.post(url, {}, { preserveScroll: true, onStart: () => (generating = true), onFinish: () => (generating = false) });
    }

    function setShown(field: "show_heats" | "show_draws", value: boolean) {
        router.post(visibility(comp).url, { [field]: value }, { preserveScroll: true, preserveState: true });
    }

    const heading = "text-lg font-extrabold uppercase";
</script>

<svelte:head>
    <title>Heats & Draws - {competition.name} - Scoring.Events</title>
</svelte:head>

<SidebarPage sticky>
    <h2 class="mb-6 text-2xl font-extrabold uppercase">Heats & Draws</h2>

    <section aria-labelledby="hd-heats">
        <h3 id="hd-heats" class={heading}>Heats</h3>

        {#if heats.events.length === 0}
            <div class="mt-3">
                <Alert tone="info" title="No speed events">
                    <p class="mt-1">Add a speed event on <Link href={eventsUrl} class="v2-link-inline">Events</Link> to generate heats for it.</p>
                </Alert>
            </div>
        {:else}
            {#if heats.usePools}
                <div class="mt-3">
                    <Alert tone="info" title="Heats in pools">
                        <p class="mt-1">This competition uses pools. Pooled heats are generated, and generated again, with the <code>heats-and-draws:pool-heats</code> command. Edit heats changes them a pool at a time.</p>
                    </Alert>
                </div>
            {/if}

            {#if (heats.perEvent && heats.events.length > 1) || heats.pools.length > 1}
                <div class="mt-3 flex flex-col gap-2">
                    {#if heats.perEvent && heats.events.length > 1}
                        <ChipGroup label="Event" options={heats.events} value={heats.event} onpick={(id) => pick({ event: id })} />
                    {/if}
                    {#if heats.pools.length > 1}
                        <ChipGroup label="Pool" options={heats.pools} value={heats.pool} onpick={(id) => pick({ pool: id })} />
                    {/if}
                </div>
            {/if}

            <!-- Fades in as another event or Pool is picked, the height easing to fit -->
            <SmoothHeight>
                {#key `${heats.event}:${heats.pool}`}
                    <div class="animate-fade-in motion-reduce:animate-none">
                        {#if heats.grid && heats.grid.heats.length > 0}
                            <div class="mt-4 overflow-x-auto">
                                <table class="border-separate border-spacing-0 text-left text-sm">
                                    <thead>
                                        <tr class="text-v2-muted *:border-b *:border-v2-border *:px-3 *:py-2 *:font-semibold">
                                            <th scope="col" class="sticky left-0 z-10 w-px bg-v2-bg">Lane</th>
                                            {#each heats.grid.heats as heat (heat.number)}
                                                <th scope="col" class="min-w-44 whitespace-nowrap">Heat {heat.number}</th>
                                            {/each}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {#each { length: heats.grid.lanes } as _, index (index)}
                                            <tr class="*:border-b *:border-v2-border *:px-3 *:py-2">
                                                <th scope="row" class="sticky left-0 z-10 bg-v2-bg font-semibold text-v2-muted tabular-nums">{index + 1}</th>
                                                {#each heats.grid.heats as heat (heat.number)}
                                                    {@const cell = heat.lanes[index]}
                                                    <td class={["wrap-anywhere", cell ? "font-semibold" : "text-v2-muted"]}>{cell?.name ?? "–"}</td>
                                                {/each}
                                            </tr>
                                        {/each}
                                    </tbody>
                                </table>
                            </div>
                        {:else if !heats.usePools || heats.generated}
                            <EmptyState class="mt-4">No heats yet. They appear here once they're generated.</EmptyState>
                        {/if}
                    </div>
                {/key}
            </SmoothHeight>
        {/if}
    </section>

    <section aria-labelledby="hd-draw" class="mt-10">
        <h3 id="hd-draw" class={heading}>Draw</h3>

        {#if !draw.hasSerc}
            <div class="mt-3">
                <Alert tone="info" title="No SERCs">
                    <p class="mt-1">Add a SERC on <Link href={eventsUrl} class="v2-link-inline">Events</Link> to make a draw. It can be empty: every SERC shares the one draw.</p>
                </Alert>
            </div>
        {:else if !draw.generated}
            <EmptyState class="mt-4">No draw yet. The running order appears here once it's generated.</EmptyState>
        {:else}
            <div class={["mt-4 grid gap-6", draw.useTanks && "sm:grid-cols-2 xl:grid-cols-3"]}>
                {#each draw.tanks as tank (tank.number)}
                    <div>
                        {#if draw.useTanks}<h4 class="mb-2 font-bold">Tank {tank.number}</h4>{/if}
                        <ol class={["border-t border-v2-border", !draw.useTanks && "sm:columns-2 xl:columns-3 sm:gap-6"]}>
                            {#each tank.draws as entry (entry.id)}
                                <li class="flex break-inside-avoid gap-3 border-b border-v2-border px-2 py-2">
                                    <span class="w-6 shrink-0 text-right text-v2-muted tabular-nums">{entry.draw}</span>
                                    <span class="font-semibold wrap-anywhere">{entry.name}</span>
                                </li>
                            {/each}
                        </ol>
                        {#if tank.draws.length === 0}<p class="py-3 text-sm text-v2-muted">Nobody in this tank yet.</p>{/if}
                    </div>
                {/each}
            </div>
        {/if}
    </section>

    {#snippet sidebar()}
        <nav aria-label="Heats & Draws">
            <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                {#if heats.canGenerate}
                    <li>
                        <button type="button" class={sidebarRow} disabled={generating} onclick={() => post(generateHeats(comp).url)}>
                            Generate heats
                            <RowIcon />
                        </button>
                    </li>
                {:else if editHeatsUrl}
                    <li>
                        <!-- Link keeps the url it was made with, so a new one for each event and Pool picked -->
                        {#key editHeatsUrl}
                            <Link href={editHeatsUrl} class={sidebarRow}>
                                Edit {poolName ? `${poolName} heats` : "heats"}
                                <RowIcon />
                            </Link>
                        {/key}
                    </li>
                {/if}
                {#if draw.hasSerc && !draw.generated}
                    <li>
                        <button type="button" class={sidebarRow} disabled={generating} onclick={() => post(generateDraw(comp).url)}>
                            Generate draw
                            <RowIcon />
                        </button>
                    </li>
                {:else if draw.generated}
                    <li>
                        <Link href={editDraw(comp).url} class={sidebarRow}>
                            Edit draw
                            <RowIcon />
                        </Link>
                    </li>
                {/if}
                {#if draw.hasSerc && draw.useTanks}
                    <li>
                        <Link href={tankSetup(comp).url} class={sidebarRow}>
                            Tank setup
                            <RowIcon />
                        </Link>
                    </li>
                {/if}
            </ul>

            <div class="mt-5">
                <ActionSwitch label="Show heats publicly" checked={show.heats} onchange={(checked) => setShown("show_heats", checked)} />
                <ActionSwitch label="Show draw publicly" checked={show.draws} onchange={(checked) => setShown("show_draws", checked)} />
            </div>
        </nav>
    {/snippet}
</SidebarPage>
