<!--
    Admin's Competitions (manage.admin.competitions.index, at /admin), in AdminLayout: every competition on
    the site, newest first, 25 to a page, each with its organisation and linking to its Admin page. Search
    matches the name on the server, so it covers every page. New competition is Management's.
-->
<script lang="ts">
    import CompetitionTable from "@/manage/components/CompetitionTable.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import Pagination from "@/manage/components/Pagination.svelte";
    import type { ManagedCompetition, Pagination as Page } from "@/manage/types";
    import { index } from "@/routes/manage/admin/competitions";
    import { Link, router } from "@inertiajs/svelte";

    let {
        q,
        competitions,
        pagination,
        newUrl,
    }: {
        q: string;
        competitions: (ManagedCompetition & { organisationName: string | null })[];
        pagination: Page;
        newUrl: string;
    } = $props();

    // Seeded from the url's search, then the box's own
    // svelte-ignore state_referenced_locally
    let query = $state(q);

    function search(event: SubmitEvent) {
        event.preventDefault();
        router.get(index().url, query.trim() ? { q: query.trim() } : {}, { preserveState: true });
    }
</script>

<svelte:head>
    <title>Competitions - Admin - Scoring.Events</title>
</svelte:head>

<section aria-labelledby="ac-heading">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 id="ac-heading" class="text-lg font-extrabold uppercase">
            Competitions <span class="text-v2-muted tabular-nums">{pagination.total}</span>
        </h2>
        <Link href={newUrl} class="v2-btn v2-btn-accent v2-btn-sm">New competition</Link>
    </div>
    <p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">Every competition on the site, newest first.</p>

    <form role="search" class="mb-4 flex gap-2" onsubmit={search}>
        <input type="search" class="v2-input w-full" placeholder="Search by name" aria-label="Search competitions" bind:value={query} />
        <button type="submit" class="v2-btn">Search</button>
    </form>

    {#if competitions.length > 0}
        <CompetitionTable {competitions} />
        <Pagination {pagination} label="Competitions pages" />
    {:else if q}
        <EmptyState>No competitions match “{q}”.</EmptyState>
    {:else}
        <EmptyState>No competitions yet.</EmptyState>
    {/if}
</section>
