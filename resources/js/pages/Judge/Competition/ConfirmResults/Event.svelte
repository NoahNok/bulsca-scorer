<script module lang="ts">
    export const layout = {
        title: "Confirm Results",
    };
</script>

<script lang="ts">
    import { storeEvent } from "@/actions/App/Http/Controllers/DigitalJudge/ConfirmResultsController";
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import ConfirmResultsPanel from "@/components/Judging/ConfirmResults/ConfirmResultsPanel.svelte";
    import ConfirmResultsSummary from "@/components/Judging/ConfirmResults/ConfirmResultsSummary.svelte";
    import HeatResultsCard from "@/components/Judging/ConfirmResults/HeatResultsCard.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import Spinner from "@/components/Spinner.svelte";

    import { submissions } from "@/routes/judge/competition/violation";
    import type { Competition, SpeedEvent } from "@/types/base";
    import type { ConfirmHeat, ConfirmSummary, ScrollData } from "@/types/results";
    import { InfiniteScroll } from "@inertiajs/svelte";
    import { Info, ListX } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    let {
        competition,
        event,
        isRopeThrow,
        summary,
        heats,
    }: {
        competition: Competition;
        event: SpeedEvent;
        isRopeThrow: boolean;
        summary: ConfirmSummary;
        heats: ScrollData<ConfirmHeat>;
    } = $props();

    let hasWarnings = $derived(
        summary.missing > 0 || summary.pending > 0 || (summary.missingOof ?? 0) > 0,
    );
</script>

<AppHead title="Confirm Results - {event.name} - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{event.name}</h2>

    <BackLink href={home({ competition: competition.id })} label="Back to competition" class="mt-2 mb-4" />

    <SectionLabel>Confirm Results</SectionLabel>

    <ConfirmResultsSummary
        {summary}
        entityLabel="Lanes"
        missingLabel={isRopeThrow ? "No result" : "No time"}
        submissionsHref={submissions(competition, { query: { event: `sp-${event.id}` } })}
        class="mt-2"
    />

    <div class="mt-3 flex items-start gap-3 rounded-xl bg-gray-50 p-3 text-sm text-gray-700">
        <Info size={16} class="mt-0.5 shrink-0 text-gray-400" />
        <p>
            Each lane shows its order of finish, {isRopeThrow ? "result" : "time"}
            and any DQs or penalties. Scroll through every heat, then confirm at the
            bottom.
        </p>
    </div>

    {#if heats.data.length === 0}
        <EmptyState
            icon={ListX}
            title="No heats"
            description="This event has no heats set up yet."
            class="mt-4"
        />
    {:else}
        <InfiniteScroll data="heats" preserveUrl buffer={400} class="mt-4 flex flex-col gap-5">
            {#each heats.data as heat (heat.heat)}
                <HeatResultsCard {heat} {isRopeThrow} />
            {/each}

            {#snippet next({ hasMore })}
                {#if hasMore}
                    <div class="flex flex-col items-center gap-2 py-6 text-sm text-gray-500">
                        <Spinner />
                        <p>Loading more heats…</p>
                    </div>
                {:else}
                    <div transition:slide>
                        <ConfirmResultsPanel
                            {competition}
                            url={storeEvent({ competition: competition.id, event: event.id }).url}
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
