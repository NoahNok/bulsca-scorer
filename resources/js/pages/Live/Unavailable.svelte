<!--
    When no competition is live (or `?comp=` names none): why, and past competitions to open, a page at a
    time. Inside LiveLayout, with no competition header.
-->
<script lang="ts">
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import Pagination from "@/manage/components/Pagination.svelte";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import { shortDate } from "@/lib/dates";
    import type { Pagination as Page } from "@/manage/types";

    type Past = { id: number; name: string; when: string; where: string; url: string };

    let { message, competitions, pagination }: { message: string; competitions: Past[]; pagination: Page } = $props();
</script>

<svelte:head><title>Live - Scoring.Events</title></svelte:head>

<h1 class="text-[clamp(2rem,4.5vw,3.25rem)] leading-[1.05] font-extrabold uppercase">Live</h1>

<EmptyState class="mt-6">{message}</EmptyState>

{#if competitions.length}
    <section class="mt-10">
        <h2 class="mb-2 text-[1.75rem] font-extrabold uppercase">Previously</h2>
        <RowTable rows={competitions} columns={["Competition", "Date", "Venue"]}>
            {#snippet row(comp)}
                <th scope="row" class="text-left font-normal">
                    <a href={comp.url} class={rowLink}>{comp.name}</a>
                </th>
                <td class="font-anton whitespace-nowrap text-v2-muted tabular-nums">{shortDate(comp.when)}</td>
                <td class="text-v2-muted max-sm:hidden">{comp.where}</td>
            {/snippet}
        </RowTable>
        <Pagination {pagination} label="Previous competitions pages" />
    </section>
{/if}
