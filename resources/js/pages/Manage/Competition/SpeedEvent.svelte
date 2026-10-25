<!--
    A speed event's page (manage.competitions.events.speeds.show), in CompetitionLayout. Its ranked results
    (by league, with any time and order of finish conflicts) fill the left; a column of actions on the right
    swaps them for the grid to enter results, its setup or its scoring (each saved on its own) and back, and holds
    showing it in results, printing and deleting it. The view follows the url's hash (#setup).
-->
<script lang="ts">
    import { destroy, saveScoring, saveSetup, visibility } from "@/actions/App/Http/Controllers/Manage/SpeedEventController";
    import { confirm } from "@/lib/confirm";
    import Alert from "@/manage/components/Alert.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import { leaveUnsaved } from "@/manage/unsaved";
    import ScoringSchemaEditor from "@/manage/events/ScoringSchemaEditor.svelte";
    import SpeedResultsGrid from "@/manage/events/SpeedResultsGrid.svelte";
    import type { CompetitionHeader, EventScoring, EventTarget, SpeedGridRow, SpeedResultRow } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";
    import { tick } from "svelte";

    let {
        competition,
        event,
        leagues,
        league,
        results,
        conflicts,
        grid,
        scoring,
        eventsUrl,
        printUrl,
    }: {
        competition: CompetitionHeader;
        event: {
            id: number;
            name: string;
            target: EventTarget;
            complete: boolean;
            viewable: boolean;
            hasPenalties: boolean;
            digitalJudge: boolean;
            ropeThrow: boolean;
            /** How many heats it has, which deleting it removes */
            heats: number;
        };
        leagues: { id: number; name: string }[];
        /** The league Results shows, null for all */
        league: number | null;
        /** Null until it has scoring */
        results: SpeedResultRow[] | null;
        /** Heats where the times and the order of finish disagree */
        conflicts: { heat: number; rows: { lane: number; entity_name: string; time: string; oof: number }[] }[];
        grid: SpeedGridRow[];
        scoring: EventScoring;
        eventsUrl: string;
        printUrl: string;
    } = $props();

    // What the left side shows instead of the results, by its hash
    const VIEWS = [
        { id: "enter-results", label: "Enter results" },
        { id: "setup", label: "Setup" },
        { id: "scoring", label: "Scoring" },
    ] as const;

    type View = "results" | (typeof VIEWS)[number]["id"];

    function fromHash(): View {
        const hash = typeof location === "undefined" ? "" : location.hash.slice(1);
        return VIEWS.find((view) => view.id === hash)?.id ?? "results";
    }

    let view = $state<View>(fromHash());

    async function open(click: MouseEvent, next: View) {
        click.preventDefault();
        if (next === view || !(await leaveUnsaved())) return;
        view = next;
        history.replaceState(history.state, "", next === "results" ? location.pathname + location.search : `#${next}`);
        if (next === "setup") resetSetup();
    }

    const TARGETS: Record<EventTarget, string> = { club: "Clubs", team: "Teams", competitor: "Competitors" };

    const comp = $derived(competition.id);

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

    // Setup: changing what it scores starts its results again, so ask first
    // svelte-ignore state_referenced_locally
    let target = $state(event.target);
    // svelte-ignore state_referenced_locally
    let hasPenalties = $state(event.hasPenalties);
    let setupErrors = $state<Record<string, string>>({});
    let setupSaving = $state(false);
    const setupDirty = $derived(target !== event.target || hasPenalties !== event.hasPenalties);

    function resetSetup() {
        target = event.target;
        hasPenalties = event.hasPenalties;
        setupErrors = {};
    }

    async function saveSetupPart(submit: SubmitEvent) {
        submit.preventDefault();
        if (target !== event.target) {
            const ok = await confirm({
                title: `Score ${TARGETS[target].toLowerCase()} instead?`,
                description: `This deletes all of ${event.name}'s results, and starts an empty one for each of the ${TARGETS[target].toLowerCase()}.`,
                confirmLabel: "Delete results and save",
                confirmVariant: "danger",
            });
            if (!ok) return;
        }
        router.post(saveSetup({ comp, event: event.id }).url, { target_entity: target, has_penalties: hasPenalties }, {
            preserveScroll: true,
            onStart: () => (setupSaving = true),
            onFinish: () => (setupSaving = false),
            onSuccess: () => (setupErrors = {}),
            onError: (errors) => (setupErrors = errors),
        });
    }

    async function remove() {
        const ok = await confirm({
            title: `Delete ${event.name}?`,
            description:
                (event.heats > 0 ? "This also removes its heats, " : "This removes ") + "its results and its scoring. It can't be undone.",
            confirmLabel: "Delete event",
            confirmVariant: "danger",
        });
        if (ok) router.delete(destroy({ comp, event: event.id }).url);
    }

    // Close a dialog on a click on its backdrop (the dialog itself, outside its content)
    function onBackdrop(click: MouseEvent) {
        if (click.target === click.currentTarget) (click.currentTarget as HTMLDialogElement).close();
    }

    const heading = "text-lg font-extrabold uppercase";
    // A row in the actions column: the whole row is the target, the current view on --v2-sunk
    const action =
        "flex cursor-pointer items-center gap-3 px-2 py-[0.8rem] text-left font-semibold transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover aria-[current=true]:bg-v2-sunk motion-reduce:transition-none";
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
    const chip =
        "inline-flex cursor-pointer items-center rounded-full border border-v2-border bg-v2-card px-[0.8rem] py-[0.3rem] text-sm font-semibold transition-colors hover:bg-v2-row-hover aria-pressed:border-v2-ink aria-pressed:bg-v2-ink aria-pressed:text-v2-bg motion-reduce:transition-none";
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

<svelte:window onhashchange={() => (view = fromHash())} />

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
    <p class="mt-1 text-sm text-v2-muted">Scores {TARGETS[event.target].toLowerCase()}</p>
</div>

<div class="grid gap-10 min-[56rem]:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] min-[56rem]:items-start">
    <div class="min-w-0 max-[56rem]:order-2">
        {#if view === "results"}
            <section aria-labelledby="sp-results">
                <h3 id="sp-results" class={heading}>Results</h3>

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
                            <p class="mt-1">Results get points and positions once you <a href="#scoring" class="v2-link-inline" onclick={(click) => open(click, "scoring")}>set up scoring</a>.</p>
                        </Alert>
                    </div>
                {:else}
                    <div class="mt-5 flex flex-col gap-3">
                        {#if leagues.length > 0}
                            <div class="flex flex-wrap gap-2" role="group" aria-label="League">
                                <button type="button" class={chip} aria-pressed={league === null} onclick={() => showLeague(null)}>Overall</button>
                                {#each leagues as option (option.id)}
                                    <button type="button" class={chip} aria-pressed={league === option.id} onclick={() => showLeague(option.id)}>{option.name}</button>
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
                                No results yet. They appear here, ranked, as you <a href="#enter-results" class="v2-link-inline" onclick={(click) => open(click, "enter-results")}>enter them</a>.
                            </p>
                        {:else if shownResults.length === 0}
                            <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries match.</p>
                        {/if}
                    </div>
                {/if}
            </section>
        {:else if view === "enter-results"}
            <section aria-labelledby="sp-enter">
                <h3 id="sp-enter" class={heading}>Enter results</h3>
                <p class={lead}>Change as many rows as you like, then save them together.</p>
                <SpeedResultsGrid comp={competition.id} event={event.id} rows={grid} penalties={event.hasPenalties} ropeThrow={event.ropeThrow} />
            </section>
        {:else if view === "setup"}
            <section aria-labelledby="sp-setup">
                <h3 id="sp-setup" class={heading}>Setup</h3>
                <p class={lead}>What {event.name} scores, and whether it takes penalties.</p>
                <form onsubmit={saveSetupPart}>
                    <div class="v2-field mt-0 max-w-sm">
                        <label for="sp-target">Scores</label>
                        <select id="sp-target" class="v2-input w-full" bind:value={target}>
                            {#each Object.entries(TARGETS) as [value, label] (value)}
                                <option {value}>{label}</option>
                            {/each}
                        </select>
                        {#if target !== event.target}
                            <span class="v2-hint text-v2-danger-text">Saving this deletes all its results.</span>
                        {/if}
                        {#if setupErrors.target_entity}<span class="v2-error">{setupErrors.target_entity}</span>{/if}
                    </div>
                    <div class="mt-5">
                        <Switch name="has_penalties" label="Has penalties" bind:checked={hasPenalties} hint="Penalties can be entered, and show in its results." />
                    </div>

                    <UnsavedChanges dirty={setupDirty} />
                    <div class="mt-6 flex items-center justify-end gap-4">
                        {#if setupDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
                        <button type="submit" class="v2-btn v2-btn-primary" disabled={setupSaving}>Save setup</button>
                    </div>
                </form>
            </section>
        {:else}
            <section aria-labelledby="sp-scoring">
                <h3 id="sp-scoring" class={heading}>Scoring</h3>
                <p class={lead}>How results become points and positions.</p>
                <ScoringSchemaEditor {scoring} url={saveScoring({ comp, event: event.id }).url} />
            </section>
        {/if}
    </div>

    <nav class="min-w-0 max-[56rem]:order-1 min-[56rem]:sticky min-[56rem]:top-6 min-[56rem]:self-start" aria-labelledby="sp-actions">
        <!-- A heading level with the left side's, as Entries & Leagues has, so both columns start together -->
        <h3 id="sp-actions" class="mb-3 {heading}">Event</h3>
        <ul class="border-t border-v2-border *:border-b *:border-v2-border">
            <li>
                <a
                    href={location.pathname + location.search}
                    class={action}
                    aria-current={view === "results" ? "true" : undefined}
                    onclick={(click) => open(click, "results")}
                >
                    Results
                    {@render chevron()}
                </a>
            </li>
            {#each VIEWS as item (item.id)}
                <li>
                    <a href="#{item.id}" class={action} aria-current={view === item.id ? "true" : undefined} onclick={(click) => open(click, item.id)}>
                        {item.label}
                        {@render chevron()}
                    </a>
                </li>
            {/each}
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
                <a href={printUrl} target="_blank" rel="noopener" class={action}>
                    Print results
                    <svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" />
                    </svg>
                </a>
            </li>
            <li>
                <button type="button" class="{action} w-full text-v2-danger-text" onclick={remove}>Delete event</button>
            </li>
        </ul>
    </nav>
</div>

<dialog bind:this={conflictDialog} class="v2-modal" aria-labelledby="sp-conflict-title" onclick={onBackdrop}>
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
