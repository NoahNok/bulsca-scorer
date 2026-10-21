<!--
    Management home, at / on app.: the competitions you opened recently, each of your organisations with
    its competitions, then competitions you can manage outside them (personal ones, and ones you were
    invited to). Officiating isn't listed here; that's the Digital Judge.
-->
<script lang="ts">
    import { shortDate } from "@/lib/dates";
    import CompetitionTable from "@/manage/components/CompetitionTable.svelte";
    import type { ManagedCompetition, ManagedOrganisation } from "@/manage/types";
    import comps from "@/routes/comps";
    import orgs from "@/routes/orgs";

    let {
        recent,
        organisations,
        competitions,
        competitionsShown,
    }: {
        recent: ManagedCompetition[];
        organisations: ManagedOrganisation[];
        competitions: ManagedCompetition[];
        /** As many as an organisation shows, until "Show all" */
        competitionsShown: number;
    } = $props();

    let showAll = $state(false);
    let shown = $derived(showAll ? competitions : competitions.slice(0, competitionsShown));

    let empty = $derived(recent.length === 0 && organisations.length === 0 && competitions.length === 0);

    const newCompetition = comps.create({ query: { type: "acc" } }).url;
    const newOrganisation = orgs.create().url;
</script>

<svelte:head>
    <title>Management - Scoring.Events</title>
</svelte:head>

<div class="h-head">
    <h1 class="h-title">Your competitions</h1>
    {#if !empty}
        <div class="h-actions">
            <!-- Plain links: both forms are still Blade -->
            <a href={newOrganisation} class="v2-btn">New organisation</a>
            <a href={newCompetition} class="v2-btn v2-btn-accent">New competition</a>
        </div>
    {/if}
</div>

{#if recent.length > 0}
    <section class="h-section" aria-labelledby="h-recent">
        <h2 id="h-recent" class="h-heading">Recent</h2>
        <ul class="h-recent">
            {#each recent as comp (comp.id)}
                <li>
                    <a href={comp.url} class="h-card">
                        <span class="h-card-name">{comp.name}</span>
                        <span class="h-card-meta">
                            {#if comp.live}
                                <span class="h-card-live"><span class="v2-dot is-live" aria-hidden="true"></span> Live now</span>
                            {:else if comp.today}
                                Today
                            {:else}
                                {shortDate(comp.when)}
                            {/if}
                            · {comp.where}
                        </span>
                    </a>
                </li>
            {/each}
        </ul>
    </section>
{/if}

{#each organisations as org (org.url)}
    <section class="h-section" aria-label={org.name}>
        <div class="h-section-head">
            <h2 class="h-heading h-org">
                {#if org.logo}<img src={org.logo} alt="" />{/if}
                {org.name}
            </h2>
            <a href={org.url} class="v2-link">{org.more ? "View all" : "Organisation"}<span class="sr-only"> {org.name}</span></a>
        </div>
        {#if org.competitions.length > 0}
            <CompetitionTable competitions={org.competitions} />
        {:else}
            <p class="h-none">No competitions yet. They appear here once one is added to {org.name}.</p>
        {/if}
    </section>
{/each}

{#if competitions.length > 0}
    <section class="h-section" aria-labelledby="h-own">
        <h2 id="h-own" class="h-heading">{organisations.length > 0 ? "Other competitions" : "Competitions"}</h2>
        <CompetitionTable competitions={shown} />
        {#if shown.length < competitions.length}
            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm h-more" onclick={() => (showAll = true)}>
                Show all {competitions.length}
            </button>
        {/if}
    </section>
{/if}

{#if empty}
    <div class="h-empty">
        <p>Competitions you organise appear here. Start one of your own, or set up an organisation to run several under one name.</p>
        <div class="h-actions">
            <a href={newCompetition} class="v2-btn v2-btn-accent">New competition</a>
            <a href={newOrganisation} class="v2-btn">New organisation</a>
        </div>
        <ul class="h-pitch">
            <li>Branded results, live and information pages at your own address</li>
            <li>Organisers across all your competitions, each with just the access they need</li>
            <li>Custom events and leagues, and more scoring options</li>
        </ul>
    </div>
{/if}

<style>
    .h-head {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .h-title {
        font-weight: 800;
        font-size: 1.75rem;
        text-transform: uppercase;
    }

    .h-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .h-section {
        margin-top: 2.5rem;
    }

    .h-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .h-heading {
        font-weight: 800;
        font-size: 1.125rem;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
    }

    .h-org {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .h-org img {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 9999px;
    }

    .h-recent {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(15rem, 1fr));
        gap: 0.75rem;
    }

    .h-card {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        height: 100%;
        padding: 0.875rem 1rem;
        border: 1px solid var(--v2-border);
        border-radius: 0.75rem;
        background: var(--v2-card);
        transition: background-color 150ms;
    }

    .h-card:hover,
    .h-card:focus-visible {
        background: var(--v2-row-hover);
    }

    .h-card-name {
        font-weight: 700;
    }

    .h-card:hover .h-card-name {
        color: var(--v2-teal-text);
    }

    .h-card-meta {
        font-size: 0.875rem;
        color: var(--v2-muted);
    }

    .h-card-live {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        color: var(--v2-teal-text);
    }

    .h-none {
        color: var(--v2-muted);
    }

    .h-more {
        margin-top: 0.75rem;
    }

    .h-empty {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
        margin-top: 1.5rem;
        padding: 1.5rem;
        border: 1px dashed var(--v2-border);
        border-radius: 1rem;
        color: var(--v2-muted);
    }

    .h-pitch {
        list-style: disc;
        padding-left: 1.25rem;
        font-size: 0.9375rem;
    }

    @media (prefers-reduced-motion: reduce) {
        .h-card {
            transition: none;
        }
    }
</style>
