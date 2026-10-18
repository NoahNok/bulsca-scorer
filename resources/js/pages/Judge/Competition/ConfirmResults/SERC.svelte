<script module lang="ts">
    export const layout = {
        title: "Confirm Results",
    };
</script>

<script lang="ts">
    import { storeSerc } from "@/actions/App/Http/Controllers/DigitalJudge/ConfirmResultsController";
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import ConfirmResultsPanel from "@/components/Judging/ConfirmResults/ConfirmResultsPanel.svelte";
    import ConfirmResultsSummary from "@/components/Judging/ConfirmResults/ConfirmResultsSummary.svelte";
    import SERCEntityResultsCard from "@/components/Judging/ConfirmResults/SERCEntityResultsCard.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import Spinner from "@/components/Spinner.svelte";

    import { submissions } from "@/routes/judge/competition/violation";
    import type { Competition, Event } from "@/types/base";
    import type {
        ConfirmJudge,
        ConfirmSercEntity,
        ConfirmSummary,
        ScrollData,
    } from "@/types/results";
    import { InfiniteScroll } from "@inertiajs/svelte";
    import { Info, ListX } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    let {
        competition,
        event,
        useTanks,
        judges,
        summary,
        entities,
    }: {
        competition: Competition;
        event: Event;
        useTanks: boolean;
        judges: ConfirmJudge[];
        summary: ConfirmSummary;
        entities: ScrollData<ConfirmSercEntity>;
    } = $props();

    let hasWarnings = $derived(summary.missing > 0 || summary.pending > 0);
</script>

<AppHead title="Confirm Results - {event.name} - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{event.name}</h2>

    <BackLink href={home({ competition: competition.id })} label="Back to competition" class="mt-2 mb-4" />

    <SectionLabel>Confirm Results</SectionLabel>

    <ConfirmResultsSummary
        {summary}
        entityLabel="In draw"
        missingLabel="Missing marks"
        submissionsHref={submissions(competition, { query: { event: `se-${event.id}` } })}
        class="mt-2"
    />

    <div class="mt-3 flex items-start gap-3 rounded-xl bg-gray-50 p-3 text-sm text-gray-700">
        <Info size={16} class="mt-0.5 shrink-0 text-gray-400" />
        <p>
            Results are in draw order. Each shows every judge's marks (weighted
            totals on the right), any DQs or penalties, and judges' notes. Scroll
            through them all, then confirm at the bottom.
        </p>
    </div>

    {#if entities.data.length === 0}
        <EmptyState
            icon={ListX}
            title="No draw"
            description="This SERC has no draw set up yet."
            class="mt-4"
        />
    {:else}
        <InfiniteScroll data="entities" preserveUrl buffer={400} class="mt-4 flex flex-col gap-3">
            {#each entities.data as row (`${row.tank}-${row.draw}`)}
                <SERCEntityResultsCard {row} {judges} {useTanks} />
            {/each}

            {#snippet next({ hasMore })}
                {#if hasMore}
                    <div class="flex flex-col items-center gap-2 py-6 text-sm text-gray-500">
                        <Spinner />
                        <p>Loading more results…</p>
                    </div>
                {:else}
                    <div transition:slide>
                        <ConfirmResultsPanel
                            {competition}
                            url={storeSerc({ competition: competition.id, serc: event.id }).url}
                            eventName={event.name}
                            {hasWarnings}
                            class="mt-6"
                        />
                    </div>
                {/if}
            {/snippet}
        </InfiniteScroll>
    {/if}
</section>
