<!--
    Competitions an Organiser can manage, one whole-row link each, in the order given (upcoming soonest
    first, then past): the date, the name (with its organisation, muted, when given), and any status
    beside Manage. Past rows sit back like results on the fixtures sheet.
-->
<script lang="ts">
    import { shortDate } from "@/lib/dates";
    import type { ManagedCompetition } from "@/manage/types";

    let { competitions }: { competitions: (ManagedCompetition & { organisationName?: string | null })[] } = $props();
</script>

<table class="c-table">
    <thead class="sr-only">
        <tr>
            <th>Date</th>
            <th>Competition</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        {#each competitions as comp (comp.id)}
            <tr class="v2-row" class:is-past={comp.past}>
                <td class="c-date">{shortDate(comp.when)}</td>
                <td>
                    <span class="c-name">{comp.name}</span>
                    {#if comp.organisationName}<span class="c-org">{comp.organisationName}</span>{/if}
                </td>
                <td>
                    <span class="c-end">
                        {#if comp.live}
                            <span class="v2-badge is-live"><span class="v2-dot is-live" aria-hidden="true"></span> Live now</span>
                        {:else if comp.today}
                            <span class="v2-badge is-live">Today</span>
                        {:else if !comp.past}
                            <span class="c-when">{comp.days === 1 ? "Tomorrow" : `In ${comp.days} days`}</span>
                        {/if}
                        {#if comp.private}<span class="v2-badge">Private</span>{/if}
                        {#if comp.limited}<span class="v2-badge">Limited access</span>{/if}
                        <!-- A plain link: competition pages are still Blade -->
                        <a href={comp.url} class="v2-link v2-row-link">Manage<span class="sr-only"> {comp.name}</span></a>
                    </span>
                </td>
            </tr>
        {/each}
    </tbody>
</table>

<style>
    .c-table {
        width: 100%;
        border-collapse: collapse;
    }

    .c-table td {
        padding: 0.875rem 1rem 0.875rem 0;
        border-top: 1px solid var(--v2-border);
        vertical-align: middle;
    }

    /* Inset, so a hovered row's shading doesn't sit flush with its text */
    .c-table td:first-child {
        padding-left: 0.75rem;
    }

    .c-table td:last-child {
        padding-right: 0.75rem;
    }

    .c-date {
        width: 7rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--v2-muted);
        white-space: nowrap;
    }

    .c-name {
        font-weight: 700;
    }

    .c-org {
        margin-left: 0.5rem;
        font-size: 0.875rem;
        color: var(--v2-muted);
    }

    .c-end {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 0.4rem 0.75rem;
    }

    .c-when {
        font-size: 0.8125rem;
        font-weight: 600;
        white-space: nowrap;
        color: var(--v2-muted);
    }

    .is-past .c-name {
        color: var(--v2-muted);
    }

    .v2-row:not(.is-past):hover .c-name,
    .v2-row:not(.is-past):focus-within .c-name {
        color: var(--v2-teal-text);
    }

    @media (max-width: 640px) {
        .c-date {
            width: 5.5rem;
        }

        .c-when,
        .c-org {
            display: none;
        }
    }
</style>
