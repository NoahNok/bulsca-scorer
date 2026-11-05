<!--
    The SERC order (see GLOSSARY.md): each Tank's Teams in Draw order, column first, with how many SERCs
    each has finished and its Estimated start. "Up next" is the first Team in each Tank still to start.
    Tiles whose name has `search` in it are outlined. With `display` it fills the screen for a TV across
    the pool: solid fills, one line a tile, never scrolling.
-->
<script lang="ts">
    import { follow } from "@/manage/live/liveData.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import type { SercOrder } from "@/manage/types";

    let {
        order,
        url,
        search = "",
        display = false,
    }: { order: SercOrder; /** LiveHeader.dataUrl */ url: string; search?: string; display?: boolean } = $props();

    const live = $derived(follow(url));
    const term = $derived(search.trim().toLowerCase());

    // On the display: fill top to bottom, then across, at most 12 rows a column
    function rows(count: number): number {
        return Math.ceil(count / Math.max(Math.ceil(count / 12), 1));
    }

    function tileClass(id: number) {
        const state = live.state(id, order.sercCount);

        return [
            "rounded-xl border whitespace-nowrap transition-colors motion-reduce:transition-none",
            display ? "flex items-center gap-4 px-5" : "flex flex-col gap-1 p-3",
            state == "waiting" && "border-v2-border bg-v2-card",
            state == "part" && "border-transparent bg-v2-warning-soft text-v2-warning-text",
            state == "done" && "border-transparent bg-v2-success-soft text-v2-success-text",
            live.teams[id]?.next && "ring-2 ring-v2-teal ring-inset",
        ];
    }
</script>

{#if order.tanks.length == 0}
    <EmptyState>The SERC order appears here once the organiser makes the Draw. Check back closer to the start.</EmptyState>
{:else}
    <div class={["mb-3 flex flex-wrap items-center gap-2 font-semibold", display ? "shrink-0 text-lg" : "text-sm"]}>
        {#if order.sercCount > 1}
            <span>Finished:</span>
            <span class="rounded-md bg-v2-warning-soft px-3 py-0.5 text-v2-warning-text">Dry</span>
            <span class="rounded-md bg-v2-success-soft px-3 py-0.5 text-v2-success-text">Both</span>
        {:else}
            <span class="rounded-md bg-v2-success-soft px-3 py-0.5 text-v2-success-text">Finished</span>
        {/if}
        <span class="ml-2 font-normal text-v2-muted">Estimates only: teams may start earlier or later.</span>
    </div>

    <div class={display ? "flex min-h-0 flex-1 gap-6" : "flex flex-col gap-8"}>
        {#each order.tanks as tank, i (i)}
            <section class={[display && "flex min-h-0 flex-1 flex-col"]} aria-label={tank.label ?? "SERC order"}>
                {#if tank.label}
                    <h3 class={["font-extrabold uppercase", display ? "mb-2 text-2xl" : "mb-3 text-lg"]}>{tank.label}</h3>
                {/if}

                <ol
                    class={display
                        ? "grid min-h-0 flex-1 grid-flow-col gap-3"
                        : "grid gap-3 sm:grid-cols-2 md:grid-flow-col md:grid-cols-none md:grid-rows-8 2xl:grid-rows-6"}
                    style:grid-template-rows={display ? `repeat(${rows(tank.teams.length)}, minmax(0, 1fr))` : undefined}
                    style:font-size={display ? "min(2.8vh, 1.75rem)" : undefined}
                >
                    {#each tank.teams as team (team.id)}
                        {@const match = term !== "" && team.name.toLowerCase().includes(term)}
                        <li class={[tileClass(team.id), match && "outline-2 outline-offset-2 outline-v2-ink"]}>
                            <div class={["flex min-w-0 items-center justify-between gap-2", display && "flex-1"]}>
                                <span class="truncate font-bold">{team.draw}. {team.name}</span>
                                {#if live.teams[team.id]?.next}
                                    <span class={["v2-badge is-live shrink-0", display && "text-[0.6em]!"]}>Up next</span>
                                {/if}
                            </div>
                            <span class={["font-semibold tabular-nums", display ? "text-[0.85em]" : "text-xs"]}>{live.label(team.id, order.sercCount)}</span>
                        </li>
                    {/each}
                </ol>
            </section>
        {/each}
    </div>
{/if}
