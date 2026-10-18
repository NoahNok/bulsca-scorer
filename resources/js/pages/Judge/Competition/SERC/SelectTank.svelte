<script module lang="ts">
    export const layout = {
        title: "Select Tank",
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { setTank } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import NumberedList from "@/components/NumberedList/NumberedList.svelte";
    import NumberedListItem from "@/components/NumberedList/NumberedListItem.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import type { Competition, SERC } from "@/types/base";
    import { Waves } from "@lucide/svelte";

    let {
        competition,
        serc,
        tanks,
    }: {
        competition: Competition;
        serc: SERC;
        tanks: number[];
    } = $props();
</script>

<AppHead title="Select Tank - {serc.name} - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{serc.name}</h2>

    <BackLink href={home(competition)} label="All events" class="mt-2 mb-4" />

    <SectionLabel class="mb-1">Select a tank</SectionLabel>
    <p class="mb-3 text-sm text-gray-500">Which tank are you marking?</p>

    {#if tanks.length === 0}
        <EmptyState
            icon={Waves}
            title="No tanks found"
            description="The draw hasn't been set up yet."
        />
    {:else}
        <NumberedList>
            {#each tanks as tank (tank)}
                <NumberedListItem
                    number={tank}
                    href={setTank({ competition, serc, tank })}
                >
                    <span class="font-medium text-gray-900">Tank {tank}</span>
                </NumberedListItem>
            {/each}
        </NumberedList>
    {/if}
</section>
