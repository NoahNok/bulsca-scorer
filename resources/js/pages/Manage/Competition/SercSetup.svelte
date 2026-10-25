<!--
    A SERC's setup (manage.competitions.events.sercs.setup), and New SERC (….create) when there's no SERC
    yet, in CompetitionLayout: a link back, the heading, then SercSetupEditor across the whole width.
-->
<script lang="ts">
    import { saveSetup, show, store } from "@/actions/App/Http/Controllers/Manage/SercEventController";
    import { index as events } from "@/actions/App/Http/Controllers/Manage/CompetitionEventsController";
    import SercSetupEditor from "@/manage/events/SercSetupEditor.svelte";
    import type { CompetitionHeader, SercEventHeader, SercSetup } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    let {
        competition,
        event,
        setup,
        leagues,
        templates,
        defaultTemplate,
    }: {
        competition: CompetitionHeader;
        /** Null for a new SERC */
        event: SercEventHeader | null;
        setup: SercSetup;
        leagues: { id: number; name: string }[];
        templates: { id: string; name: string }[];
        defaultTemplate: string | null;
    } = $props();

    const comp = $derived(competition.id);
    const backUrl = $derived(event ? show({ comp, serc: event.id }).url : events(comp).url);
</script>

<svelte:head>
    <title>{event ? `Edit setup - ${event.name}` : "New SERC"} - {competition.name} - Scoring.Events</title>
</svelte:head>

<div class="mb-6">
    <Link href={backUrl} class="v2-link-muted text-sm wrap-anywhere">← {event ? event.name : "Events"}</Link>
    <h2 class="mt-1 text-2xl font-extrabold uppercase">{event ? "Edit setup" : "New SERC"}</h2>
</div>

<SercSetupEditor
    {setup}
    {leagues}
    {templates}
    {defaultTemplate}
    url={event ? saveSetup({ comp, serc: event.id }).url : store(comp).url}
    cancelUrl={backUrl}
    isNew={!event}
/>
