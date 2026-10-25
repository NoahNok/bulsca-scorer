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

    // Filters: a pill per organisation, ink when chosen like the primary button

    function choose(next: typeof filter) {
        filter = next;
        showAll = false;
    }
</script>

<svelte:head>
    <title>Management - Scoring.Events</title>
</svelte:head>

<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="text-[1.75rem] font-extrabold uppercase">Your competitions</h1>
    {#if !empty}
        <div class="flex flex-wrap gap-2">
            <Link href={manage.organisations.create()} class="v2-btn">New organisation</Link>
            <Link href={manage.competitions.create()} class="v2-btn v2-btn-accent">New competition</Link>
        </div>
    {/if}
</div>

{#if recent.length > 0}
    <section class="mt-10" aria-labelledby="h-recent">
        <h2 id="h-recent" class="mb-3 text-lg font-extrabold uppercase">Recent</h2>
        <ul class="grid grid-cols-[repeat(auto-fill,minmax(15rem,1fr))] gap-3">
            {#each recent as comp (comp.id)}
                <li>
                    <a
                        href={comp.url}
                        class="group flex h-full flex-col gap-1 rounded-xl border border-v2-border bg-v2-card px-4 py-3.5 transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover motion-reduce:transition-none"
                    >
                        <span class="font-bold group-hover:text-v2-teal-text">{comp.name}</span>
                        <span class="text-sm text-v2-muted">
                            {#if comp.live}
                                <span class="inline-flex items-center gap-[0.4rem] font-semibold text-v2-teal-text"
                                    ><span class="v2-dot is-live" aria-hidden="true"></span> Live now</span
                                >
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
    <section class="mt-10" aria-labelledby="h-all">
        <h2 id="h-all" class="mb-3 text-lg font-extrabold uppercase">Competitions</h2>

        {#if organisations.length > 0}
            <div class="mb-4 flex flex-wrap gap-2" role="group" aria-label="Filter by organisation">
                <button type="button" class="v2-chip" aria-pressed={filter === "all"} onclick={() => choose("all")}>All</button>
                {#each organisations as org (org.id)}
                    <button type="button" class="v2-chip" aria-pressed={filter === org.id} onclick={() => choose(org.id)}>
                        {#if org.logo}<img src={org.logo} alt="" class="size-5 rounded-full" />{/if}
                        {org.name}
                    </button>
                {/each}
                {#if hasPersonal}
                    <button type="button" class="v2-chip" aria-pressed={filter === "personal"} onclick={() => choose("personal")}>Personal</button>
                {/if}
            </div>
        {/if}

        {#if rows.length > 0}
            <CompetitionTable competitions={rows} />
        {:else}
            <p class="text-v2-muted">No competitions yet. They appear here once one is added to {selected?.name ?? "your organisations"}.</p>
        {/if}

        {#if shown.length < filtered.length || selected?.url}
            <div class="mt-3 flex items-center justify-between gap-4">
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
    <div class="mt-6 flex flex-col items-start gap-4 rounded-2xl border border-dashed border-v2-border p-6 text-v2-muted">
        <p>Competitions you organise appear here. Start one of your own, or set up an organisation to run several under one name.</p>
        <div class="flex flex-wrap gap-2">
            <Link href={manage.competitions.create()} class="v2-btn v2-btn-accent">New competition</Link>
            <Link href={manage.organisations.create()} class="v2-btn">New organisation</Link>
        </div>
        <ul class="list-disc pl-5 text-[0.9375rem]">
            <li>Branded results, live and information pages at your own address</li>
            <li>Organisers across all your competitions, each with just the access they need</li>
            <li>Custom events and leagues, and more scoring options</li>
        </ul>
    </div>
{/if}
