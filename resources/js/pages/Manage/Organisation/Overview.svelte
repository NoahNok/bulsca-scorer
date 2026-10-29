<!--
    An organisation's Overview (manage.organisations.show), in OrganisationLayout: its competitions,
    upcoming soonest first then past, and for its Organisers a button to start a new one in it.
-->
<script lang="ts">
    import CompetitionTable from "@/manage/components/CompetitionTable.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import type { ManagedCompetition, OrganisationHeader } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    let {
        organisation,
        competitions,
        newCompetitionUrl,
    }: {
        organisation: OrganisationHeader;
        competitions: ManagedCompetition[];
        /** Organisers only */
        newCompetitionUrl: string | null;
    } = $props();
</script>

<svelte:head>
    <title>{organisation.name} - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="o-competitions">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-4">
        <h2 id="o-competitions" class="text-lg font-extrabold uppercase">Competitions</h2>
        {#if newCompetitionUrl && competitions.length > 0}
            <Link href={newCompetitionUrl} class="v2-btn v2-btn-accent v2-btn-sm">New competition</Link>
        {/if}
    </div>

    {#if competitions.length > 0}
        <CompetitionTable {competitions} />
    {:else}
        <EmptyState>
            <p>{organisation.name}'s competitions appear here.</p>
            {#if newCompetitionUrl}
                <Link href={newCompetitionUrl} class="v2-btn v2-btn-accent mt-4">New competition</Link>
            {/if}
        </EmptyState>
    {/if}
</section>
