<!--
    Competitions an Organiser can manage, one whole-row link each, in the order given (upcoming soonest
    first, then past): the date, the name (with its organisation, muted, when given), and any status
    beside Manage. Past rows sit back like results on the fixtures sheet. `actions` adds to the end of a
    row, e.g. a Championship's Remove; give anything clickable in it .v2-over-row.
-->
<script lang="ts" generics="T extends ManagedCompetition & { organisationName?: string | null }">
    import { shortDate } from "@/lib/dates";
    import type { ManagedCompetition } from "@/manage/types";
    import type { Snippet } from "svelte";

    let { competitions, actions }: { competitions: T[]; actions?: Snippet<[T]> } = $props();
</script>

<table class="w-full border-collapse">
    <thead class="sr-only">
        <tr>
            <th>Date</th>
            <th>Competition</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        {#each competitions as comp (comp.id)}
            <!-- Cells inset at the ends, so a hovered row's shading doesn't sit flush with its text -->
            <tr
                class="v2-row group *:border-t *:border-v2-border *:py-3.5 *:pr-4 *:align-middle *:first:pl-3 *:last:pr-3"
            >
                <td class="w-28 text-sm font-semibold whitespace-nowrap text-v2-muted max-sm:w-22">{shortDate(comp.when)}</td>
                <td>
                    <span
                        class={[
                            "font-bold",
                            comp.past ? "text-v2-muted" : "group-hover:text-v2-teal-text group-focus-within:text-v2-teal-text",
                        ]}>{comp.name}</span
                    >
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
                        <a href={comp.url} class="v2-link v2-row-link">Manage<span class="sr-only"> {comp.name}</span></a>
                        {@render actions?.(comp)}
                    </span>
                </td>
            </tr>
        {/each}
    </tbody>
</table>
