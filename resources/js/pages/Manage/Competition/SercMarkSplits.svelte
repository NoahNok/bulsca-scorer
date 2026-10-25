<!--
    A SERC's mark splits (manage.competitions.events.sercs.mark-splits), in CompetitionLayout: a pill per
    Objective, and for the one picked (?objective=, reloading only the splits) every Entity in draw order
    with its mark for each of that Objective's marking points.
-->
<script lang="ts">
    import { show } from "@/actions/App/Http/Controllers/Manage/SercEventController";
    import type { CompetitionHeader, SercEventHeader } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        event,
        objectives,
        objective,
        splits,
    }: {
        competition: CompetitionHeader;
        event: SercEventHeader;
        objectives: { id: number; name: string }[];
        /** The Objective shown, null before one is picked */
        objective: number | null;
        splits: {
            markingPoints: { id: number; name: string }[];
            /** In draw order; marks by marking point id */
            rows: { key: string; name: string; draw: string | null; marks: Record<string, number> }[];
        } | null;
    } = $props();

    function showObjective(id: number) {
        router.reload({ data: { objective: id }, only: ["splits", "objective"] });
    }

    const chip =
        "inline-flex cursor-pointer items-center rounded-full border border-v2-border bg-v2-card px-[0.8rem] py-[0.3rem] text-sm font-semibold transition-colors hover:bg-v2-row-hover aria-pressed:border-v2-ink aria-pressed:bg-v2-ink aria-pressed:text-v2-bg motion-reduce:transition-none";
    const tableHead = "text-sm text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold";
</script>

<svelte:head>
    <title>Mark splits - {event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<div class="mb-6">
    <Link href={show({ comp: competition.id, serc: event.id }).url} class="v2-link-muted text-sm wrap-anywhere">← {event.name}</Link>
    <h2 class="mt-1 text-2xl font-extrabold uppercase">Mark splits</h2>
    <p class="mt-[0.35rem] text-[0.9375rem]/[1.6] text-v2-muted">Each entry's mark on every marking point of one Objective.</p>
</div>

{#if objectives.length === 0}
    <p class="text-[0.9375rem]/[1.6] text-v2-muted">It has no Objectives yet. Add them in its setup, and their marks show here.</p>
{:else}
    <div class="flex flex-wrap gap-2" role="group" aria-label="Objective">
        {#each objectives as option (option.id)}
            <button type="button" class={chip} aria-pressed={objective === option.id} onclick={() => showObjective(option.id)}>{option.name}</button>
        {/each}
    </div>

    {#if splits}
        <div class="mt-5 overflow-x-auto">
            <table class="w-full border-t border-v2-border text-left">
                <thead>
                    <tr class={tableHead}>
                        <th scope="col" class="w-px whitespace-nowrap">Draw</th>
                        <th scope="col">Entry</th>
                        {#each splits.markingPoints as point (point.id)}
                            <th scope="col" class="min-w-24 text-right">{point.name}</th>
                        {/each}
                    </tr>
                </thead>
                <tbody>
                    {#each splits.rows as row (row.key)}
                        <tr class="*:border-b *:border-v2-border *:px-2 *:py-2.5">
                            <td class="whitespace-nowrap text-v2-muted tabular-nums">{row.draw ?? "–"}</td>
                            <th scope="row" class="font-bold wrap-anywhere">{row.name}</th>
                            {#each splits.markingPoints as point (point.id)}
                                <td class="text-right tabular-nums">{row.marks[point.id] ?? "–"}</td>
                            {/each}
                        </tr>
                    {/each}
                </tbody>
            </table>
            {#if splits.rows.length === 0}
                <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries yet.</p>
            {/if}
        </div>
    {:else}
        <p class="mt-5 text-[0.9375rem]/[1.6] text-v2-muted">Pick an Objective to see its marks for every entry.</p>
    {/if}
{/if}
