<!--
    A SERC's page (manage.competitions.events.sercs.show), in CompetitionLayout. Its ranked results (by
    league) and then every Entity in draw order, each opening its marks, fill the left; a column of actions
    on the right links to its setup, mark splits, League Objectives and settings, and holds showing it in
    results and printing it. A SERC Writer sees the results and only the link to the setup.
-->
<script lang="ts">
    import { leagueObjectives, markSplits, marks, settings, setup, visibility } from "@/actions/App/Http/Controllers/Manage/SercEventController";
    import Alert from "@/manage/components/Alert.svelte";
    import SidebarPage, { sidebarRow } from "@/manage/components/SidebarPage.svelte";
    import type { CompetitionHeader, SercEntity, SercEventHeader, SercResultRow } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";

    let {
        competition,
        event,
        leagues,
        league,
        marking,
        results,
        entities,
        drawn,
        eventsUrl,
        printUrl,
        sheetsUrl,
    }: {
        competition: CompetitionHeader;
        event: SercEventHeader;
        leagues: { id: number; name: string }[];
        /** The league the results show, null for all */
        league: number | null;
        /** Marks in, of every mark the Entities take */
        marking: { marked: number; possible: number };
        /** Null until it has scoring */
        results: SercResultRow[] | null;
        /** In draw order */
        entities: SercEntity[];
        /** Whether there's a SERC draw; without one the Entities aren't in any order */
        drawn: boolean;
        eventsUrl: string;
        printUrl: string;
        /** Null without access to printables */
        sheetsUrl: string | null;
    } = $props();

    const comp = $derived(competition.id);
    const settingsUrl = $derived(settings({ comp, serc: event.id }).url);

    const byKey = $derived(new Map(entities.map((entity) => [entity.key, entity])));

    let query = $state("");
    const matches = (name: string) => name.toLowerCase().includes(query.trim().toLowerCase());
    const shownResults = $derived((results ?? []).filter((row) => row.names.some(matches)));
    const shownEntities = $derived(entities.filter((entity) => matches(entity.name)));

    function showLeague(id: number | null) {
        router.reload({ data: { league: id ?? undefined }, only: ["results", "league"] });
    }

    function setViewable(viewable: boolean) {
        router.post(visibility({ comp, serc: event.id }).url, { viewable }, { preserveScroll: true });
    }

    const marksUrl = (entity: SercEntity) => marks({ comp, serc: event.id, entity: entity.id }).url;

    const chip =
        "inline-flex cursor-pointer items-center rounded-full border border-v2-border bg-v2-card px-[0.8rem] py-[0.3rem] text-sm font-semibold transition-colors hover:bg-v2-row-hover aria-pressed:border-v2-ink aria-pressed:bg-v2-ink aria-pressed:text-v2-bg motion-reduce:transition-none";
    const tableHead = "text-sm text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold";
    const heading = "text-lg font-extrabold uppercase";
</script>

<svelte:head>
    <title>{event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet chevron()}
    <svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="m9 6 6 6-6 6" />
    </svg>
{/snippet}

{#snippet external()}
    <svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" />
    </svg>
{/snippet}

<!-- How far an Entity's marking has got: all in, some in, none yet, or no single Objective to mark it against -->
{#snippet status(entity: SercEntity | undefined)}
    {#if !entity}
        <!-- A combined row has no one Entity's marking -->
    {:else if entity.possible === null}
        <span class="v2-badge is-danger" title="Its Leagues give it no Objective, or more than one">No Objective</span>
    {:else if entity.possible > 0 && entity.marked >= entity.possible}
        <span class="v2-badge is-success">Marked</span>
    {:else if entity.marked > 0}
        <span class="v2-badge is-provisional">{entity.marked}/{entity.possible} marked</span>
    {:else}
        <span class="v2-badge">Waiting</span>
    {/if}
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
                {#if marking.possible > 0}
                    <span class="v2-badge is-provisional" title="Marks entered, of every mark it takes">{marking.marked}/{marking.possible} marks</span>
                {/if}
            {/if}
            <span class="v2-badge">{event.type === "WET" ? "Wet" : "Dry"}</span>
            {#if !event.viewable}<span class="v2-badge" title="Left out of the results">Hidden</span>{/if}
        </div>
    </div>

    <!-- Under the SERC's name, so no heading of its own; the first block's top margin goes too -->
    <section aria-label="Results" class="*:first:mt-0">
        {#if results === null}
            <div class="mt-5">
                <Alert tone="info" title="No scoring yet">
                    <p class="mt-1">
                        Results get points and positions once you
                        {#if event.canManage}<Link href="{settingsUrl}#scoring" class="v2-link-inline">set up scoring</Link>{:else}set up scoring{/if}.
                    </p>
                </Alert>
            </div>
        {/if}

        <div class="mt-5 flex flex-col gap-3">
            {#if results !== null && leagues.length > 0}
                <div class="flex flex-wrap gap-2" role="group" aria-label="League">
                    <button type="button" class={chip} aria-pressed={league === null} onclick={() => showLeague(null)}>Overall</button>
                    {#each leagues as option (option.id)}
                        <button type="button" class={chip} aria-pressed={league === option.id} onclick={() => showLeague(option.id)}>{option.name}</button>
                    {/each}
                </div>
            {/if}
            {#if entities.length > 0}
                <input type="search" class="v2-input w-full" placeholder="Search entries" aria-label="Search entries" bind:value={query} />
            {/if}
        </div>

        {#if results !== null}
            <div class="mt-4 overflow-x-auto">
                <table class="w-full border-t border-v2-border text-left">
                    <thead>
                        <tr class={tableHead}>
                            <th scope="col" class="w-px">#</th>
                            <th scope="col">Entry</th>
                            <th scope="col">DQ</th>
                            <th scope="col" class="text-right">Mark</th>
                            <th scope="col" class="text-right">Points</th>
                            <th scope="col"><span class="sr-only">Marking</span></th>
                            {#if event.canManage}<th scope="col" class="w-px"><span class="sr-only">Enter marks</span></th>{/if}
                        </tr>
                    </thead>
                    <tbody>
                        {#each shownResults as row (row.key)}
                            <!-- A combined row stands for several Entities, so it has no one page of marks -->
                            {@const entity = event.canManage ? byKey.get(row.key) : undefined}
                            <tr class={[entity && "v2-row", "*:border-b *:border-v2-border *:px-2 *:py-3 *:align-top"]}>
                                <td class="font-semibold tabular-nums">{row.position ?? "–"}</td>
                                <th scope="row" class="font-bold wrap-anywhere">
                                    {#if entity}
                                        <Link href={marksUrl(entity)} class="v2-row-link focus-visible:outline-none">
                                            {entity.name}<span class="sr-only">: enter marks</span>
                                        </Link>
                                    {:else}
                                        {#each row.names as name, index (index)}<span class="block">{name}</span>{/each}
                                    {/if}
                                </th>
                                <td class={["whitespace-nowrap", row.dq && "font-semibold text-v2-danger-text"]}>{row.dq ?? "–"}</td>
                                <td class="text-right tabular-nums">{row.mark}</td>
                                <td class="text-right font-semibold whitespace-nowrap tabular-nums">
                                    {#if row.disqualified}<s class="font-normal text-v2-muted">{row.points}</s> DQ{:else}{row.points}{/if}
                                </td>
                                <td class="text-right">{@render status(byKey.get(row.key))}</td>
                                {#if event.canManage}<td class="align-middle!">{#if entity}{@render chevron()}{/if}</td>{/if}
                            </tr>
                        {/each}
                    </tbody>
                </table>
                {#if results.length === 0}
                    <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">
                        No marks yet. Entries appear here, ranked, once they're marked.
                    </p>
                {:else if shownResults.length === 0}
                    <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries match.</p>
                {/if}
            </div>
        {/if}
    </section>

    <section aria-labelledby="se-draw" class="mt-10">
        <h3 id="se-draw" class={heading}>Draw order</h3>
        {#if !drawn}
            <div class="mt-3">
                <Alert tone="info" title="No SERC draw">
                    <p class="mt-1">There's no draw yet, so these aren't in the order they compete.</p>
                </Alert>
            </div>
        {/if}

        <div class="mt-4 overflow-x-auto">
            <table class="w-full border-t border-v2-border text-left">
                <thead>
                    <tr class={tableHead}>
                        <th scope="col" class="w-px whitespace-nowrap">Draw</th>
                        <th scope="col">Entry</th>
                        <th scope="col"><span class="sr-only">Marking</span></th>
                        {#if event.canManage}<th scope="col" class="w-px"><span class="sr-only">Enter marks</span></th>{/if}
                    </tr>
                </thead>
                <tbody>
                    {#each shownEntities as entity (entity.key)}
                        <tr class={[event.canManage && "v2-row", "*:border-b *:border-v2-border *:px-2 *:py-3 *:align-middle"]}>
                            <td class="whitespace-nowrap text-v2-muted tabular-nums">{entity.draw ?? "–"}</td>
                            <th scope="row" class="font-bold wrap-anywhere">
                                {#if event.canManage}
                                    <Link href={marksUrl(entity)} class="v2-row-link focus-visible:outline-none">
                                        {entity.name}<span class="sr-only">: enter marks</span>
                                    </Link>
                                {:else}
                                    {entity.name}
                                {/if}
                            </th>
                            <td class="text-right">{@render status(entity)}</td>
                            {#if event.canManage}<td>{@render chevron()}</td>{/if}
                        </tr>
                    {/each}
                </tbody>
            </table>
            {#if entities.length === 0}
                <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">
                    No {event.target === "club" ? "clubs" : event.target === "competitor" ? "competitors" : "teams"} yet. Add them on Entries & Leagues, and they appear here to mark.
                </p>
            {:else if shownEntities.length === 0}
                <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries match.</p>
            {/if}
        </div>
    </section>

    {#snippet sidebar()}
        <nav aria-label={event.name}>
            <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                <li>
                    <Link href={setup({ comp, serc: event.id }).url} class={sidebarRow}>
                        Edit setup
                        {@render chevron()}
                    </Link>
                </li>
                {#if event.canManage}
                    <li>
                        <Link href={markSplits({ comp, serc: event.id }).url} class={sidebarRow}>
                            Mark splits
                            {@render chevron()}
                        </Link>
                    </li>
                    {#if event.leagueObjectives}
                        <li>
                            <Link href={leagueObjectives({ comp, serc: event.id }).url} class={sidebarRow}>
                                League Objectives
                                {@render chevron()}
                            </Link>
                        </li>
                    {/if}
                    <li>
                        <Link href={settingsUrl} class={sidebarRow}>
                            Settings
                            {@render chevron()}
                        </Link>
                    </li>
                {/if}
            </ul>

            {#if event.canManage}
                <label class="v2-check mt-5">
                    <button
                        type="button"
                        class="v2-switch"
                        role="switch"
                        aria-checked={event.viewable}
                        aria-labelledby="se-viewable"
                        onclick={() => setViewable(!event.viewable)}
                    ></button>
                    <span id="se-viewable" class="font-semibold">Shown in results</span>
                </label>
            {/if}

            <ul class="mt-5 border-t border-v2-border *:border-b *:border-v2-border">
                {#if sheetsUrl}
                    <li>
                        <a href={sheetsUrl} target="_blank" rel="noopener" class={sidebarRow}>
                            Print rough mark sheets
                            {@render external()}
                        </a>
                    </li>
                {/if}
                <li>
                    <a href={printUrl} target="_blank" rel="noopener" class={sidebarRow}>
                        Print results
                        {@render external()}
                    </a>
                </li>
            </ul>
        </nav>
    {/snippet}
</SidebarPage>
