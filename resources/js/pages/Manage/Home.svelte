<!--
    Management home, at / on app.: the competitions you opened recently, then one list of every
    competition you can manage, filtered by organisation (yours, then ones you were invited into) or
    Personal. Officiating isn't listed here; that's the Digital Judge.
-->
<script lang="ts">
    import { shortDate } from "@/lib/dates";
    import CompetitionTable from "@/manage/components/CompetitionTable.svelte";
    import type { ListedCompetition, ManagedCompetition, ManagedOrganisation } from "@/manage/types";
    import manage from "@/routes/manage";
    import { Link } from "@inertiajs/svelte";

    let {
        recent,
        organisations,
        competitions,
    }: {
        recent: ManagedCompetition[];
        organisations: ManagedOrganisation[];
        competitions: ListedCompetition[];
    } = $props();

    // Past competitions shown under the upcoming ones until "Show all"
    const PAST_SHOWN = 3;

    let filter = $state<"all" | "personal" | number>("all");
    let showAll = $state(false);

    let names = $derived(new Map(organisations.map((org) => [org.id, org.name])));
    let hasPersonal = $derived(competitions.some((comp) => comp.organisation === null));
    let selected = $derived(typeof filter === "number" ? organisations.find((org) => org.id === filter) : undefined);

    let filtered = $derived(
        competitions.filter((comp) =>
            filter === "all" ? true : filter === "personal" ? comp.organisation === null : comp.organisation === filter,
        ),
    );
    let shown = $derived(
        showAll ? filtered : filtered.slice(0, filtered.filter((comp) => !comp.past).length + PAST_SHOWN),
    );
    // In All, say which organisation each one is under, once there's more than one place it could be
    let rows = $derived(
        shown.map((comp) => ({
            ...comp,
            organisationName:
                filter === "all" && organisations.length + (hasPersonal ? 1 : 0) > 1 && comp.organisation !== null
                    ? names.get(comp.organisation)
                    : null,
        })),
    );

    let empty = $derived(recent.length === 0 && organisations.length === 0 && competitions.length === 0);

    function choose(next: typeof filter) {
        filter = next;
        showAll = false;
    }
</script>

<svelte:head>
    <title>Management - Scoring.Events</title>
</svelte:head>

<div class="h-head">
    <h1 class="h-title">Your competitions</h1>
    {#if !empty}
        <div class="h-actions">
            <Link href={manage.organisations.create()} class="v2-btn">New organisation</Link>
            <Link href={manage.competitions.create()} class="v2-btn v2-btn-accent">New competition</Link>
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

{#if organisations.length > 0 || competitions.length > 0}
    <section class="h-section" aria-labelledby="h-all">
        <h2 id="h-all" class="h-heading">Competitions</h2>

        {#if organisations.length > 0}
            <div class="h-filters" role="group" aria-label="Filter by organisation">
                <button type="button" class="h-chip" aria-pressed={filter === "all"} onclick={() => choose("all")}>All</button>
                {#each organisations as org (org.id)}
                    <button type="button" class="h-chip" aria-pressed={filter === org.id} onclick={() => choose(org.id)}>
                        {#if org.logo}<img src={org.logo} alt="" />{/if}
                        {org.name}
                    </button>
                {/each}
                {#if hasPersonal}
                    <button type="button" class="h-chip" aria-pressed={filter === "personal"} onclick={() => choose("personal")}>Personal</button>
                {/if}
            </div>
        {/if}

        {#if rows.length > 0}
            <CompetitionTable competitions={rows} />
        {:else}
            <p class="h-none">No competitions yet. They appear here once one is added to {selected?.name ?? "your organisations"}.</p>
        {/if}

        {#if shown.length < filtered.length || selected?.url}
            <div class="h-foot">
                {#if shown.length < filtered.length}
                    <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm" onclick={() => (showAll = true)}>
                        Show all {filtered.length}
                    </button>
                {/if}
                {#if selected?.url}
                    <!-- A plain link: organisation pages are still Blade -->
                    <a href={selected.url} class="v2-link">{selected.name}'s page</a>
                {/if}
            </div>
        {/if}
    </section>
{/if}

{#if empty}
    <div class="h-empty">
        <p>Competitions you organise appear here. Start one of your own, or set up an organisation to run several under one name.</p>
        <div class="h-actions">
            <Link href={manage.competitions.create()} class="v2-btn v2-btn-accent">New competition</Link>
            <Link href={manage.organisations.create()} class="v2-btn">New organisation</Link>
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


    .h-heading {
        font-weight: 800;
        font-size: 1.125rem;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
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

    .h-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Filters: a pill per organisation, ink when chosen like the primary button */
    .h-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.8rem;
        border: 1px solid var(--v2-border);
        border-radius: 9999px;
        background: var(--v2-card);
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 150ms;
    }

    .h-chip:hover {
        background: var(--v2-row-hover);
    }

    .h-chip[aria-pressed="true"] {
        border-color: var(--v2-ink);
        background: var(--v2-ink);
        color: var(--v2-bg);
    }

    .h-chip img {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 9999px;
    }

    .h-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
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
        .h-card,
        .h-chip {
            transition: none;
        }
    }
</style>
