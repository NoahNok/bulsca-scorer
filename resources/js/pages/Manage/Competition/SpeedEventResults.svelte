<!--
    The page to edit a speed event's results (manage.competitions.events.speeds.results.edit), in
    CompetitionLayout: a link back to the event, then SpeedResultsGrid across the whole width.
-->
<script lang="ts">
    import { show } from "@/actions/App/Http/Controllers/Manage/SpeedEventController";
    import SpeedResultsGrid from "@/manage/events/SpeedResultsGrid.svelte";
    import type { CompetitionHeader, SpeedEventHeader, SpeedGridRow } from "@/manage/types";
    import PageHeading from "@/manage/components/PageHeading.svelte";

    let {
        competition,
        event,
        grid,
    }: {
        competition: CompetitionHeader;
        event: SpeedEventHeader;
        grid: SpeedGridRow[];
    } = $props();
</script>

<svelte:head>
    <title>Edit results - {event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<PageHeading
    back={{ href: show({ comp: competition.id, event: event.id }).url, label: event.name }}
    title="Edit results"
    lead="Change as many rows as you like, then save them together."
/>

<SpeedResultsGrid comp={competition.id} event={event.id} rows={grid} penalties={event.hasPenalties} ropeThrow={event.ropeThrow} />
