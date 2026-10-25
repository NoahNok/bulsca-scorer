<!--
    The page to edit a speed event's results (manage.competitions.events.speeds.results.edit), in
    CompetitionLayout: a link back to the event, then SpeedResultsGrid across the whole width.
-->
<script lang="ts">
    import { show } from "@/actions/App/Http/Controllers/Manage/SpeedEventController";
    import SpeedResultsGrid from "@/manage/events/SpeedResultsGrid.svelte";
    import type { CompetitionHeader, SpeedEventHeader, SpeedGridRow } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

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

<div class="mb-6">
    <Link href={show({ comp: competition.id, event: event.id }).url} class="v2-link-muted text-sm wrap-anywhere">← {event.name}</Link>
    <h2 class="mt-1 text-2xl font-extrabold uppercase">Edit results</h2>
    <p class="mt-[0.35rem] text-[0.9375rem]/[1.6] text-v2-muted">Change as many rows as you like, then save them together.</p>
</div>

<SpeedResultsGrid comp={competition.id} event={event.id} rows={grid} penalties={event.hasPenalties} ropeThrow={event.ropeThrow} />
