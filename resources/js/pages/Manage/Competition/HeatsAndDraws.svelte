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
    import Alert from "@/manage/components/Alert.svelte";
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

    function pick(data: { event?: number; pool?: number }) {
        router.reload({ data: { event: heats.event ?? undefined, pool: heats.pool ?? undefined, ...data }, only: ["heats"] });
    }

    function post(url: string) {
        router.post(url, {}, { preserveScroll: true, onStart: () => (generating = true), onFinish: () => (generating = false) });
    }

    function setShown(field: "show_heats" | "show_draws", value: boolean) {
        router.post(visibility(comp).url, { [field]: value }, { preserveScroll: true, preserveState: true });
    }

    const heading = "text-lg font-extrabold uppercase";
    const empty = "rounded-md border border-dashed border-v2-border px-4 py-6 text-center text-[0.9375rem] text-v2-muted";
</script>

<svelte:head>
    <title>Heats & Draws - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet chevron()}
    <svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="m9 6 6 6-6 6" />
    </svg>
{/snippet}

{#snippet visibilitySwitch(id: string, label: string, checked: boolean, field: "show_heats" | "show_draws")}
    <label class="v2-check mt-4 first:mt-0">
        <button type="button" class="v2-switch" role="switch" aria-checked={checked} aria-labelledby={id} onclick={() => setShown(field, !checked)}></button>
        <span id={id} class="font-semibold">{label}</span>
    </label>
{/snippet}

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
                        <div class="flex flex-wrap gap-2" role="group" aria-label="Event">
                            {#each heats.events as option (option.id)}
                                <button type="button" class="v2-chip" aria-pressed={heats.event === option.id} onclick={() => pick({ event: option.id })}>{option.name}</button>
                            {/each}
                        </div>
                    {/if}
                    {#if heats.pools.length > 1}
                        <div class="flex flex-wrap gap-2" role="group" aria-label="Pool">
                            {#each heats.pools as option (option.id)}
                                <button type="button" class="v2-chip" aria-pressed={heats.pool === option.id} onclick={() => pick({ pool: option.id })}>{option.name}</button>
                            {/each}
                        </div>
                    {/if}
                </div>
            {/if}

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
                <p class={["mt-4", empty]}>No heats yet. They appear here once they're generated.</p>
            {/if}
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
            <p class={["mt-4", empty]}>No draw yet. The running order appears here once it's generated.</p>
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
                            {@render chevron()}
                        </button>
                    </li>
                {:else if editHeatsUrl}
                    <li>
                        <Link href={editHeatsUrl} class={sidebarRow}>
                            Edit {poolName ? `${poolName} heats` : "heats"}
                            {@render chevron()}
                        </Link>
                    </li>
                {/if}
                {#if draw.hasSerc && !draw.generated}
                    <li>
                        <button type="button" class={sidebarRow} disabled={generating} onclick={() => post(generateDraw(comp).url)}>
                            Generate draw
                            {@render chevron()}
                        </button>
                    </li>
                {:else if draw.generated}
                    <li>
                        <Link href={editDraw(comp).url} class={sidebarRow}>
                            Edit draw
                            {@render chevron()}
                        </Link>
                    </li>
                {/if}
                {#if draw.hasSerc && draw.useTanks}
                    <li>
                        <Link href={tankSetup(comp).url} class={sidebarRow}>
                            Tank setup
                            {@render chevron()}
                        </Link>
                    </li>
                {/if}
            </ul>

            <div class="mt-5">
                {@render visibilitySwitch("hd-show-heats", "Show heats publicly", show.heats, "show_heats")}
                {@render visibilitySwitch("hd-show-draws", "Show draw publicly", show.draws, "show_draws")}
            </div>
        </nav>
    {/snippet}
</SidebarPage>
