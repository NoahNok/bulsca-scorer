<!--
    A speed event's page (manage.competitions.events.speeds.show), in CompetitionLayout. Its ranked results
    (by league, with any time and order of finish conflicts) fill the left; a column of actions on the right
    links to the page to edit its results and its settings, and holds showing it in results and printing it.
-->
<script lang="ts">
    import { editResults, settings, visibility } from "@/actions/App/Http/Controllers/Manage/SpeedEventController";
    import Alert from "@/manage/components/Alert.svelte";
    import SidebarPage, { sidebarRow } from "@/manage/components/SidebarPage.svelte";
    import type { CompetitionHeader, SpeedEventHeader, SpeedResultRow } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";
    import { tick } from "svelte";
    import { closeOnBackdrop } from "@/manage/ui";

    let {
        competition,
        event,
        leagues,
        league,
        results,
        conflicts,
        eventsUrl,
        printUrl,
    }: {
        competition: CompetitionHeader;
        event: SpeedEventHeader;
        leagues: { id: number; name: string }[];
        /** The league Results shows, null for all */
        league: number | null;
        /** Null until it has scoring */
        results: SpeedResultRow[] | null;
        /** Heats where the times and the order of finish disagree */
        conflicts: { heat: number; rows: { lane: number; entity_name: string; time: string; oof: number }[] }[];
        eventsUrl: string;
        printUrl: string;
    } = $props();

    const comp = $derived(competition.id);
    const resultsUrl = $derived(editResults({ comp, event: event.id }).url);
    const settingsUrl = $derived(settings({ comp, event: event.id }).url);

    // Results

    let query = $state("");
    const shownResults = $derived(
        (results ?? []).filter((row) => row.names.some((name) => name.toLowerCase().includes(query.trim().toLowerCase()))),
    );

    function showLeague(id: number | null) {
        router.reload({ data: { league: id ?? undefined }, only: ["results", "league"] });
    }

    function setViewable(viewable: boolean) {
        router.post(visibility({ comp, event: event.id }).url, { viewable }, { preserveScroll: true });
    }

    let conflictDialog: HTMLDialogElement;
    let conflict = $state<(typeof conflicts)[number] | null>(null);

    async function openConflict(which: (typeof conflicts)[number]) {
        conflict = which;
        await tick();
        conflictDialog.showModal();
    }

    const tableHead = "text-sm text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold";
</script>

<svelte:head>
    <title>{event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet chevron()}
    <svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="m9 6 6 6-6 6" />
    </svg>
{/snippet}

<SidebarPage sticky>
    <div class="mb-6">
        <Link href={eventsUrl} class="v2-link-muted text-sm">← Events</Link>
        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-2">
            <h2 class="text-2xl font-extrabold uppercase wrap-anywhere">{event.name}</h2>
            {#if event.complete}
                <span class="v2-badge is-success">Complete</span>
            {:else}
                <span class="v2-badge is-danger">Incomplete</span>
            {/if}
            {#if !event.viewable}<span class="v2-badge" title="Left out of the results">Hidden</span>{/if}
        </div>
    </div>

    <!-- Under the event's name, so no heading of its own; the first block's top margin goes too -->
    <section aria-label="Results" class="*:first:mt-0">

        {#if conflicts.length > 0}
            <div class="mt-5">
                <Alert tone="danger" title="Times and order of finish disagree">
                    <p class="mt-1">The times don't put these heats in the order the Digital Judge recorded:</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        {#each conflicts as which (which.heat)}
                            <button type="button" class="v2-btn v2-btn-sm" onclick={() => openConflict(which)}>Heat {which.heat}</button>
                        {/each}
                    </div>
                </Alert>
            </div>
        {/if}

        {#if results === null}
            <div class="mt-5">
                <Alert tone="info" title="No scoring yet">
                    <p class="mt-1">Results get points and positions once you <Link href="{settingsUrl}#scoring" class="v2-link-inline">set up scoring</Link>.</p>
                </Alert>
            </div>
        {:else}
            <div class="mt-5 flex flex-col gap-3">
                {#if leagues.length > 0}
                    <div class="flex flex-wrap gap-2" role="group" aria-label="League">
                        <button type="button" class="v2-chip" aria-pressed={league === null} onclick={() => showLeague(null)}>Overall</button>
                        {#each leagues as option (option.id)}
                            <button type="button" class="v2-chip" aria-pressed={league === option.id} onclick={() => showLeague(option.id)}>{option.name}</button>
                        {/each}
                    </div>
                {/if}
                {#if results.length > 0}
                    <input type="search" class="v2-input w-full" placeholder="Search entries" aria-label="Search results" bind:value={query} />
                {/if}
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full border-t border-v2-border text-left">
                    <thead>
                        <tr class={tableHead}>
                            <th scope="col" class="w-px">#</th>
                            <th scope="col">Entry</th>
                            {#if event.digitalJudge}<th scope="col">Order of finish</th>{/if}
                            <th scope="col">{event.ropeThrow ? "Ropes or time" : "Time"}</th>
                            <th scope="col">DQ</th>
                            {#if event.hasPenalties}<th scope="col">Penalties</th>{/if}
                            <th scope="col" class="text-right">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each shownResults as row (row.key)}
                            <tr class="*:border-b *:border-v2-border *:px-2 *:py-3 *:align-top">
                                <td class="font-semibold tabular-nums">{row.position ?? "–"}</td>
                                <th scope="row" class="font-bold wrap-anywhere">
                                    {#each row.names as name, index (index)}<span class="block">{name}</span>{/each}
                                </th>
                                {#if event.digitalJudge}<td class="text-sm whitespace-nowrap">{row.oof ?? "–"}</td>{/if}
                                <td class="whitespace-nowrap tabular-nums">
                                    {#if row.times.length > 0}
                                        <span class="flex items-start gap-3">
                                            <span class="text-sm text-v2-muted">{#each row.times as time, index (index)}<span class="block">{time ?? "–"}</span>{/each}</span>
                                            <span class="font-semibold">{row.time ?? "–"}</span>
                                        </span>
                                    {:else}
                                        {row.time ?? "–"}
                                        {#if row.was}<span class="block text-sm text-v2-muted">Was {row.was}</span>{/if}
                                    {/if}
                                </td>
                                <td class="whitespace-nowrap">
                                    {#each row.dqs as dq, index (index)}
                                        <span class={["block", dq && "font-semibold text-v2-danger-text"]}>{dq ?? "–"}</span>
                                    {/each}
                                </td>
                                {#if event.hasPenalties}<td class="text-sm">{row.penalties ?? "–"}</td>{/if}
                                <td class="text-right font-semibold tabular-nums">{row.points ?? "–"}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
                {#if results.length === 0}
                    <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">
                        No results yet. They appear here, ranked, as you <Link href={resultsUrl} class="v2-link-inline">enter them</Link>.
                    </p>
                {:else if shownResults.length === 0}
                    <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries match.</p>
                {/if}
            </div>
        {/if}
    </section>

    {#snippet sidebar()}
        <nav aria-label="{event.name}">
            <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                <li>
                    <Link href={resultsUrl} class={sidebarRow}>
                        Edit results
                        {@render chevron()}
                    </Link>
                </li>
                <li>
                    <Link href={settingsUrl} class={sidebarRow}>
                        Settings
                        {@render chevron()}
                    </Link>
                </li>
            </ul>

            <label class="v2-check mt-5">
                <button
                    type="button"
                    class="v2-switch"
                    role="switch"
                    aria-checked={event.viewable}
                    aria-labelledby="sp-viewable"
                    onclick={() => setViewable(!event.viewable)}
                ></button>
                <span id="sp-viewable" class="font-semibold">Shown in results</span>
            </label>

            <ul class="mt-5 border-t border-v2-border *:border-b *:border-v2-border">
                <li>
                    <a href={printUrl} target="_blank" rel="noopener" class={sidebarRow}>
                        Print results
                        <svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" />
                        </svg>
                    </a>
                </li>
            </ul>
        </nav>
    {/snippet}
</SidebarPage>

<dialog bind:this={conflictDialog} class="v2-modal" aria-labelledby="sp-conflict-title" onclick={closeOnBackdrop}>
    {#if conflict}
        <h2 id="sp-conflict-title">Heat {conflict.heat}</h2>
        <p>Lanes in order of their times, beside the order they finished.</p>
        <table class="mt-4 w-full text-left">
            <thead>
                <tr class={tableHead}>
                    <th scope="col">Entry</th>
                    <th scope="col">Lane</th>
                    <th scope="col">Time</th>
                    <th scope="col">Finished</th>
                </tr>
            </thead>
            <tbody>
                {#each conflict.rows as row (row.lane)}
                    <tr class="*:border-b *:border-v2-border *:px-2 *:py-2">
                        <th scope="row" class="font-semibold">{row.entity_name}</th>
                        <td>{row.lane}</td>
                        <td class="tabular-nums">{row.time}</td>
                        <td>{row.oof}</td>
                    </tr>
                {/each}
            </tbody>
        </table>
        <div class="v2-modal-actions mt-6">
            <button type="button" class="v2-btn" onclick={() => conflictDialog.close()}>Close</button>
        </div>
    {/if}
</dialog>
