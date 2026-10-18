<script module lang="ts">
    export const layout = {
        title: "Select Heat",
    };
</script>

<script lang="ts">
    import { markOOF } from "@/actions/App/Http/Controllers/DigitalJudge/Event/EventJudgeController";
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import HeatSelector from "@/components/Judging/Speed/HeatSelector.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import type { Competition, Event, Heat } from "@/types/base";
    import { ListOrdered } from "@lucide/svelte";

    let {
        competition,
        event,
        heats,
    }: {
        competition: Competition;
        event: Event;
        heats: Heat[];
    } = $props();
</script>

<AppHead title="Select Heat - Order of finish - {event.name} - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{event.name}</h2>

    <BackLink href={home(competition)} label="All events" class="mt-2 mb-4" />

    <SectionLabel class="mb-1">Order of finish · Select a heat</SectionLabel>
    <p class="mb-3 text-sm text-gray-500">
        Pick a heat to record the order of finish. Heats are marked complete once places are saved (unless no-one finished).
    </p>

    {#if heats.length === 0}
        <EmptyState
            icon={ListOrdered}
            title="No heats yet"
            description="Heats will show up here once the draw is set up."
        />
    {:else}
        <HeatSelector {heats} href={markOOF} params={{ competition, event }} />
    {/if}
</section>
