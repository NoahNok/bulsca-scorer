<!--
    What an organisation's page shows someone without the access it needs (App\Exceptions\Handler, a 403),
    in OrganisationLayout, so the tabs they can open are right above it.
-->
<script lang="ts">
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import type { OrganisationHeader } from "@/manage/types";

    let { organisation }: { organisation: OrganisationHeader } = $props();
</script>

<svelte:head>
    <title>Not allowed - {organisation.name} - Scoring.Events</title>
</svelte:head>

<EmptyState>
    <p>You don't have access to this page of {organisation.name}. {organisation.tabs.length > 0 ? "Pick one of the tabs above." : ""}</p>
</EmptyState>
