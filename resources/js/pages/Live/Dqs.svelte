<!--
    DQs & Penalties: the competition's events, grouped into SERCs and speeds, each a link to its DQs.
    Inside LiveLayout.
-->
<script lang="ts">
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import type { LiveHeader } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    type Event = { key: string; name: string; url: string };

    let { competition, groups }: { competition: LiveHeader; groups: { title: string; events: Event[] }[] } = $props();
</script>

<svelte:head><title>DQs & Penalties - {competition.name} - Scoring.Events</title></svelte:head>

<h2 class="text-[1.75rem] font-extrabold uppercase">DQs & Penalties</h2>
<p class="mt-1 mb-6 text-[0.9375rem]/[1.6] text-v2-muted">Pick an event to see its DQs and penalties.</p>

{#if groups.length == 0}
    <EmptyState>Events appear here once the organiser adds them.</EmptyState>
{:else}
    <div class="flex flex-col gap-8">
        {#each groups as group (group.title)}
            <section>
                <h3 class="mb-2 text-lg font-extrabold uppercase">{group.title}</h3>
                <RowTable rows={group.events.map((event) => ({ ...event, id: event.key }))} columns={["Event"]}>
                    {#snippet row(event)}
                        <th scope="row" class="text-left font-normal">
                            <Link href={event.url} class={rowLink}>{event.name}</Link>
                        </th>
                    {/snippet}
                </RowTable>
            </section>
        {/each}
    </div>
{/if}
