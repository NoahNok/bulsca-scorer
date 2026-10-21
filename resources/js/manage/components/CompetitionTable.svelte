<!--
    Competitions an Organiser can manage, one whole-row link each, in the order given (upcoming soonest
    first, then past). Past rows sit back like results on the fixtures sheet; on phones the venue moves
    under the name and the status column hides.
-->
<script lang="ts">
    import { shortDate } from "@/lib/dates";
    import type { ManagedCompetition } from "@/manage/types";

    let { competitions }: { competitions: ManagedCompetition[] } = $props();
</script>

<table class="c-table">
    <thead class="sr-only">
        <tr>
            <th>Date</th>
            <th>Competition</th>
            <th>Venue</th>
            <th>Status</th>
            <th>Link</th>
        </tr>
    </thead>
    <tbody>
        {#each competitions as comp (comp.id)}
            {@const [date, year] = shortDate(comp.when).split(/ (?=\d{4}$)/)}
            <tr class="v2-row" class:is-past={comp.past}>
                <td class="c-date">
                    {date}
                    {#if year}<span class="c-year">{year}</span>{/if}
                </td>
                <td>
                    <span class="c-name">{comp.name}</span>
                    <span class="c-where-sm">{comp.where}</span>
                </td>
                <td class="c-where c-hide-sm">{comp.where}</td>
                <td class="c-hide-sm">
                    <span class="c-status">
                        {#if comp.live}
                            <span class="v2-badge is-live"><span class="v2-dot is-live" aria-hidden="true"></span> Live now</span>
                        {:else if comp.today}
                            <span class="v2-badge is-live">Today</span>
                        {:else if !comp.past}
                            <span class="c-when">{comp.days === 1 ? "Tomorrow" : `In ${comp.days} days`}</span>
                        {/if}
                        {#if comp.private}<span class="v2-badge">Private</span>{/if}
                        {#if comp.limited}<span class="v2-badge">Limited access</span>{/if}
                    </span>
                </td>
                <!-- A plain link: competition pages are still Blade -->
                <td><a href={comp.url} class="v2-link v2-row-link">Manage<span class="sr-only"> {comp.name}</span></a></td>
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
        text-align: right;
    }

    .c-date {
        width: 5.5rem;
        font-family: "Anton", "Archivo", sans-serif;
        font-size: 1.25rem;
        line-height: 1;
        letter-spacing: 0.01em;
        white-space: nowrap;
    }

    .c-year {
        display: block;
        margin-top: 0.2rem;
        font-family: "Archivo", sans-serif;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--v2-muted);
    }

    .c-name {
        font-weight: 700;
    }

    .c-where,
    .c-where-sm,
    .c-when {
        color: var(--v2-muted);
    }

    .c-where-sm {
        display: none;
        font-size: 0.875rem;
    }

    .c-status {
        display: inline-flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.4rem;
    }

    .c-when {
        font-size: 0.8125rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .is-past .c-date,
    .is-past .c-name {
        color: var(--v2-muted);
    }

    .v2-row:not(.is-past):hover .c-name,
    .v2-row:not(.is-past):focus-within .c-name {
        color: var(--v2-teal-text);
    }

    @media (max-width: 640px) {
        .c-hide-sm {
            display: none;
        }

        .c-where-sm {
            display: block;
        }

        .c-date {
            width: 4.5rem;
            font-size: 1.125rem;
        }
    }
</style>
