<!--
    Competitions an Organiser can manage, in a RowTable in the order given (upcoming soonest first, then
    past): the date, the name as the row's link (with its organisation, muted, when given), and any status.
    Past rows sit back like results on the fixtures sheet. `actions` adds to the end of a row, before the
    chevron, e.g. a Championship's Remove; give anything clickable in it .v2-over-row.
-->
<script lang="ts" generics="T extends ManagedCompetition & { organisationName?: string | null }">
    import { shortDate } from "@/lib/dates";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import type { ManagedCompetition } from "@/manage/types";
    import type { Snippet } from "svelte";

    let { competitions, actions }: { competitions: T[]; actions?: Snippet<[T]> } = $props();
</script>

<RowTable rows={competitions} columns={["Date", "Competition", "Status"]}>
    {#snippet row(comp)}
        <td class="w-28 text-sm font-semibold whitespace-nowrap text-v2-muted max-sm:w-22">{shortDate(comp.when)}</td>
        <td>
            <a href={comp.url} class={comp.past ? "v2-row-link font-bold wrap-anywhere text-v2-muted" : rowLink}>{comp.name}</a>
            {#if comp.organisationName}<span class="ml-2 text-sm text-v2-muted max-sm:hidden">{comp.organisationName}</span>{/if}
        </td>
        <td>
            <span class="flex flex-wrap items-center justify-end gap-x-3 gap-y-[0.4rem]">
                {#if comp.live}
                    <span class="v2-badge is-live"><span class="v2-dot is-live" aria-hidden="true"></span> Live now</span>
                {:else if comp.today}
                    <span class="v2-badge is-live">Today</span>
                {:else if !comp.past}
                    <span class="text-[0.8125rem] font-semibold whitespace-nowrap text-v2-muted max-sm:hidden"
                        >{comp.days === 1 ? "Tomorrow" : `In ${comp.days} days`}</span
                    >
                {/if}
                {#if comp.private}<span class="v2-badge">Private</span>{/if}
                {#if comp.limited}<span class="v2-badge">Limited access</span>{/if}
                {@render actions?.(comp)}
            </span>
        </td>
    {/snippet}
</RowTable>
