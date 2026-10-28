<!--
    A competition's Entries & Leagues (manage.competitions.entries), in CompetitionLayout: its clubs, teams
    and competitors as one tree, with their leagues and seed times, and beside it its leagues. A row opens
    a dialog that saves or removes that one entry straight away (CompetitionEntriesController), and the
    same for a league (LeagueController).
-->
<script lang="ts">
    import {
        destroyClub,
        destroyCompetitor,
        destroyTeam,
        storeClub,
        storeCompetitor,
        storeTeam,
        updateClub,
        updateCompetitor,
        updateTeam,
    } from "@/actions/App/Http/Controllers/Manage/CompetitionEntriesController";
    import { deleteMethod as deleteLeague, store as storeLeague, update as updateLeague } from "@/actions/App/Http/Controllers/LeagueController";
    import { confirm } from "@/lib/confirm";
    import Alert from "@/manage/components/Alert.svelte";
    import Field from "@/manage/components/Field.svelte";
    import MultiSelect from "@/manage/components/MultiSelect.svelte";
    import SidebarPage from "@/manage/components/SidebarPage.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import type { CompetitionHeader, CompetitionLeague, Entry, EntryClub, EntryTeam } from "@/manage/types";
    import { Form, router } from "@inertiajs/svelte";
    import { tick } from "svelte";
    import { closeOnBackdrop } from "@/manage/ui";

    let {
        competition,
        leagues,
        clubs,
        teams,
        competitors,
        heatsUrl,
        seeds,
    }: {
        competition: CompetitionHeader;
        leagues: CompetitionLeague[];
        clubs: EntryClub[];
        /** Teams without a club */
        teams: EntryTeam[];
        /** Competitors without a team */
        competitors: Entry[];
        /** Null without access to Heats & Draws */
        heatsUrl: string | null;
        /** Null when the competition doesn't use seed times. With them on but not per event, one event */
        seeds: { perEvent: boolean; events: { id: number; name: string }[] } | null;
    } = $props();

    type Kind = "club" | "team" | "competitor";

    /** One row of the tree: a club, a team (under its club) or a competitor (under its team) */
    type Row = {
        kind: Kind;
        depth: number;
        entry: Entry | EntryClub;
        /** The club a team is in, or the team a competitor is in */
        parent: number | null;
        /** A club's teams or a team's competitors, for what removing it takes with it */
        children: number;
    };

    const KIND_LABEL: Record<Kind, string> = { club: "Club", team: "Team", competitor: "Competitor" };

    function teamRows(team: EntryTeam, club: number | null, depth: number): Row[] {
        return [
            { kind: "team", depth, entry: team, parent: club, children: team.competitors.length },
            ...team.competitors.map((c) => ({ kind: "competitor" as const, depth: depth + 1, entry: c, parent: team.id, children: 0 })),
        ];
    }

    const rows = $derived<Row[]>([
        ...clubs.flatMap((club) => [
            { kind: "club" as const, depth: 0, entry: club, parent: null, children: club.teams.length },
            ...club.teams.flatMap((team) => teamRows(team, club.id, 1)),
        ]),
        ...teams.flatMap((team) => teamRows(team, null, 0)),
        ...competitors.map((c) => ({ kind: "competitor" as const, depth: 0, entry: c, parent: null, children: 0 })),
    ]);

    // Search by name: a match shows with the club and team it's in, and everything under it
    let query = $state("");

    const shownRows = $derived.by(() => {
        const term = query.trim().toLowerCase();
        if (!term) return rows;
        const shown = new Set<number>();
        // The rows above this one in its tree, by depth
        const path: { index: number; matched: boolean }[] = [];
        rows.forEach((row, index) => {
            path.length = row.depth;
            const matched = row.entry.name.toLowerCase().includes(term);
            if (matched || path.some((above) => above.matched)) {
                shown.add(index);
                path.forEach((above) => shown.add(above.index));
            }
            path.push({ index, matched });
        });
        return rows.filter((_, index) => shown.has(index));
    });

    const counts = $derived({
        club: clubs.length,
        team: rows.filter((row) => row.kind === "team").length,
        competitor: rows.filter((row) => row.kind === "competitor").length,
    });

    const leagueNames = $derived(new Map(leagues.map((league) => [league.id, league.name])));
    const leagueOptions = $derived(leagues.map((league) => ({ value: String(league.id), label: league.name })));
    // Seed columns only once there's an event to seed
    const seedEvents = $derived(seeds?.events ?? []);

    function leaguesOf(row: Row) {
        return "leagues" in row.entry ? row.entry.leagues.map((id) => leagueNames.get(id)).filter(Boolean).join(", ") : "";
    }

    // The heading and Add buttons stick to the top of the screen over a long table, with a rule under
    // them only once they're stuck: a marker just above them has scrolled off the top
    let stuck = $state(false);

    function watchStuck(marker: HTMLElement) {
        const observer = new IntersectionObserver(([entry]) => (stuck = !entry.isIntersecting && entry.boundingClientRect.top < 0));
        observer.observe(marker);
        return () => observer.disconnect();
    }

    function plural(count: number, word: string) {
        return `${count} ${word}${count === 1 ? "" : "s"}`;
    }

    // Entries: one dialog, adding (row null) or changing one club, team or competitor

    let entryDialog: HTMLDialogElement;
    /** `parent` is the club or team the entry is in, or for a new one, the row whose Add button opened it */
    let editing = $state<{ kind: Kind; row: Row | null; parent: number | null } | null>(null);
    // Remounts the form each time the dialog opens, so it starts from the entry's saved values
    let opened = $state(0);

    // The dialog's leagues, picked in a MultiSelect
    let entryLeagues = $state<string[]>([]);

    async function openEntry(kind: Kind, row: Row | null = null, parent: number | null = null) {
        editing = { kind, row, parent: row ? row.parent : parent };
        entryLeagues = row && "leagues" in row.entry ? row.entry.leagues.map(String) : [];
        opened++;
        await tick();
        entryDialog.showModal();
    }

    function entryAction(kind: Kind, id: number | undefined) {
        const comp = competition.id;
        if (kind === "club") return id ? updateClub({ comp, club: id }) : storeClub(comp);
        if (kind === "team") return id ? updateTeam({ comp, team: id }) : storeTeam(comp);
        return id ? updateCompetitor({ comp, competitor: id }) : storeCompetitor(comp);
    }

    async function removeEntry(row: Row) {
        const comp = competition.id;
        const id = row.entry.id;
        const also =
            row.kind === "club" && row.children > 0
                ? `This also removes its ${plural(row.children, "team")} and their competitors, with all their results.`
                : row.kind === "team" && row.children > 0
                  ? `This also removes its ${plural(row.children, "competitor")}, with all their results.`
                  : "This removes their results too.";

        const ok = await confirm({
            title: `Remove ${row.entry.name}?`,
            description: `${also} It can't be undone.`,
            confirmLabel: "Remove",
            confirmVariant: "danger",
        });
        if (!ok) return;

        const url =
            row.kind === "club"
                ? destroyClub({ comp, club: id }).url
                : row.kind === "team"
                  ? destroyTeam({ comp, team: id }).url
                  : destroyCompetitor({ comp, competitor: id }).url;

        router.delete(url, { preserveScroll: true, onSuccess: () => entryDialog.close() });
    }

    // Leagues: one dialog, adding (null) or renaming one

    let leagueDialog: HTMLDialogElement;
    let league = $state<CompetitionLeague | null>(null);

    async function openLeague(which: CompetitionLeague | null = null) {
        league = which;
        opened++;
        await tick();
        leagueDialog.showModal();
    }

    async function removeLeague(which: CompetitionLeague) {
        const ok = await confirm({
            title: `Delete ${which.name}?`,
            description: "Its teams and competitors stay in the competition, just without this league.",
            confirmLabel: "Delete league",
            confirmVariant: "danger",
        });
        if (!ok) return;

        router.delete(deleteLeague({ comp: competition.id, league: which.id }).url, {
            preserveScroll: true,
            onSuccess: () => leagueDialog.close(),
        });
    }

    const heading = "text-lg font-extrabold uppercase";
    // A list row that opens something: the whole row is the button
    const listRow =
        "flex w-full cursor-pointer items-center gap-3 px-2 py-[0.8rem] text-left transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover focus-visible:outline-none motion-reduce:transition-none";
    const fieldset = "mt-5 flex flex-col gap-[0.6rem] *:mt-0";
    const select = "v2-input w-full";
</script>

<svelte:head>
    <title>Entries & Leagues - {competition.name} - Scoring.Events</title>
</svelte:head>

<SidebarPage>
    <section aria-labelledby="e-entries">
        <div {@attach watchStuck}></div>
        <div
            class={[
                "sticky top-0 z-10 -mx-2 flex flex-wrap items-center justify-between gap-x-4 gap-y-3 border-b bg-v2-bg px-2 py-2",
                stuck ? "border-v2-border" : "border-transparent",
            ]}
        >
            <div>
                <h2 id="e-entries" class={heading}>Entries</h2>
                <p class="mt-0.5 text-sm text-v2-muted">
                    {plural(counts.club, "club")} · {plural(counts.team, "team")} · {plural(counts.competitor, "competitor")}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="v2-btn v2-btn-sm" onclick={() => openEntry("club")}>Add club</button>
                <button type="button" class="v2-btn v2-btn-sm" onclick={() => openEntry("team")}>Add team</button>
                <button type="button" class="v2-btn v2-btn-sm" onclick={() => openEntry("competitor")}>Add competitor</button>
            </div>
            {#if rows.length > 0}
                <input
                    type="search"
                    class="v2-input w-full basis-full"
                    placeholder="Search clubs, teams and competitors"
                    aria-label="Search entries"
                    bind:value={query}
                />
            {/if}
        </div>

        <div class="mt-5 flex flex-col gap-3">
            <Alert tone="info" title="Heats and SERC order">
                Added entries after making them? Generate them again{#if heatsUrl}
                    on <a href={heatsUrl} class="v2-link-inline">Heats & Draws</a>{/if}, so the new entries are in them.
            </Alert>
            {#if seeds && seeds.events.length === 0}
                <Alert tone="info" title="Seed times">Add a speed event on Events before entering seed times.</Alert>
            {/if}
        </div>

        {#if rows.length > 0}
            <div class="mt-5 overflow-x-auto">
                <table class="w-full border-t border-v2-border text-left">
                    <thead>
                        <tr class="text-[0.8125rem] font-semibold text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold">
                            <th scope="col">Entry</th>
                            <th scope="col" class="max-sm:hidden">Leagues</th>
                            {#each seedEvents as event (event.id)}
                                <th scope="col" class="whitespace-nowrap max-sm:hidden">{seeds?.perEvent ? event.name : "Seed"}</th>
                            {/each}
                            <th scope="col"><span class="sr-only">Edit</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each shownRows as row (`${row.kind}:${row.entry.id}`)}
                            {@const leagueList = leaguesOf(row)}
                            <tr class="v2-row group *:border-b *:border-v2-border *:px-2 *:py-3 *:align-middle">
                                <td class="min-w-0" style:padding-left="{0.5 + row.depth * 1.5}rem">
                                    <button
                                        type="button"
                                        class="v2-row-link flex cursor-pointer flex-col items-start text-left focus-visible:outline-none"
                                        onclick={() => openEntry(row.kind, row)}
                                    >
                                        <span class="flex items-baseline gap-2">
                                            <span class={["wrap-anywhere", row.kind === "competitor" ? "font-semibold" : "font-bold"]}>{row.entry.name}</span>
                                            <span class="text-[0.8125rem] text-v2-muted">{KIND_LABEL[row.kind]}</span>
                                        </span>
                                        <span class="sr-only">Edit</span>
                                        {#if leagueList}<span class="text-sm text-v2-muted sm:hidden">{leagueList}</span>{/if}
                                    </button>
                                </td>
                                <td class="text-sm text-v2-muted max-sm:hidden">{leagueList}</td>
                                {#each seedEvents as event (event.id)}
                                    <td class="text-sm whitespace-nowrap tabular-nums max-sm:hidden">
                                        {#if "seeds" in row.entry}{row.entry.seeds[event.id] ?? "–"}{/if}
                                    </td>
                                {/each}
                                <td class="w-px whitespace-nowrap">
                                    <span class="flex items-center justify-end gap-2">
                                        <!-- Add a team to a club or a competitor to a team: shown on hover or keyboard focus, not on touch screens -->
                                        {#if row.kind !== "competitor"}
                                            {@const child = row.kind === "club" ? "team" : "competitor"}
                                            <button
                                                type="button"
                                                class="v2-btn v2-btn-sm v2-btn-ghost v2-over-row opacity-0 transition-opacity group-focus-within:opacity-100 group-hover:opacity-100 focus-visible:opacity-100 motion-reduce:transition-none [@media(hover:none)]:hidden"
                                                onclick={() => openEntry(child, null, row.entry.id)}
                                            >
                                                <span aria-hidden="true">+</span>
                                                {KIND_LABEL[child]}<span class="sr-only"> in {row.entry.name}</span>
                                            </button>
                                        {/if}
                                        <RowIcon />
                                    </span>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
                {#if shownRows.length === 0}
                    <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries match “{query.trim()}”.</p>
                {/if}
            </div>
        {:else}
            <EmptyState class="mt-5">No entries yet. Add the clubs, teams or competitors taking part.</EmptyState>
        {/if}
    </section>

    {#snippet sidebar()}
        <section aria-labelledby="e-leagues">
            <div class="mb-2 flex items-baseline justify-between gap-4">
                <h2 id="e-leagues" class={heading}>Leagues</h2>
                <button type="button" class="v2-link cursor-pointer" onclick={() => openLeague()}>Add league</button>
            </div>
            {#if leagues.length > 0}
                <ul class="border-t border-v2-border *:border-b *:border-v2-border">
                    {#each leagues as item (item.id)}
                        <li>
                            <button type="button" class={listRow} onclick={() => openLeague(item)}>
                                <span class="min-w-0">
                                    <span class="block font-bold wrap-anywhere">{item.name}</span>
                                    <span class="block text-sm text-v2-muted">
                                        {plural(item.teams, "team")} · {plural(item.competitors, "competitor")}
                                    </span>
                                </span>
                                <RowIcon />
                            </button>
                        </li>
                    {/each}
                </ul>
            {:else}
                <p class="pt-1 text-[0.9375rem]/[1.6] text-v2-muted">
                    No leagues. Add one to group entries, like Open and Novice.
                </p>
            {/if}
        </section>
    {/snippet}
</SidebarPage>

<dialog bind:this={entryDialog} class="v2-modal" aria-labelledby="e-entry-title" onclick={closeOnBackdrop}>
    {#if editing}
        {@const { kind, row, parent } = editing}
        {@const entry = row?.entry}
        <h2 id="e-entry-title">{entry ? entry.name : `Add ${KIND_LABEL[kind].toLowerCase()}`}</h2>
        {#if entry}<p>{KIND_LABEL[kind]}</p>{/if}

        {#key opened}
            <Form action={entryAction(kind, entry?.id)} options={{ preserveScroll: true }} onSuccess={() => entryDialog.close()}>
                {#snippet children({ errors, processing })}
                    <div class="mt-5">
                        <Field id="e-name" name="name" label="Name" value={entry?.name ?? ""} required autocomplete="off" error={errors.name} />
                    </div>

                    {#if kind === "team"}
                        <div class="v2-field">
                            <label for="e-club">Club</label>
                            <select id="e-club" name="club" class={select} value={String(parent ?? "")}>
                                <option value="">No club</option>
                                {#each clubs as club (club.id)}
                                    <option value={String(club.id)}>{club.name}</option>
                                {/each}
                            </select>
                            {#if errors.club}<span class="v2-error">{errors.club}</span>{/if}
                        </div>
                    {:else if kind === "competitor"}
                        <div class="v2-field">
                            <label for="e-team">Team</label>
                            <select id="e-team" name="team" class={select} value={String(parent ?? "")}>
                                <option value="">No team</option>
                                {#each clubs.filter((club) => club.teams.length > 0) as club (club.id)}
                                    <optgroup label={club.name}>
                                        {#each club.teams as team (team.id)}
                                            <option value={String(team.id)}>{team.name}</option>
                                        {/each}
                                    </optgroup>
                                {/each}
                                {#if teams.length > 0}
                                    <optgroup label={clubs.length > 0 ? "No club" : "Teams"}>
                                        {#each teams as team (team.id)}
                                            <option value={String(team.id)}>{team.name}</option>
                                        {/each}
                                    </optgroup>
                                {/if}
                            </select>
                            {#if errors.team}<span class="v2-error">{errors.team}</span>{/if}
                        </div>
                    {/if}

                    {#if kind !== "club"}
                        <div class="mt-4">
                            {#if leagues.length > 0}
                                <MultiSelect
                                    id="e-leagues"
                                    name="leagues[]"
                                    label="Leagues"
                                    options={leagueOptions}
                                    bind:value={entryLeagues}
                                    placeholder="No league"
                                    emptyText="No league by that name"
                                    error={errors.leagues ?? Object.entries(errors).find(([key]) => key.startsWith("leagues."))?.[1]}
                                />
                            {:else}
                                <div class="v2-field">
                                    <span class="text-sm font-semibold">Leagues</span>
                                    <span class="v2-hint">None yet. Add leagues beside the entries.</span>
                                </div>
                            {/if}
                        </div>

                        {#if seedEvents.length > 0}
                            {@const saved = entry && "seeds" in entry ? entry.seeds : {}}
                            <fieldset class={fieldset}>
                                <legend class="mb-[0.6rem] text-sm font-semibold">{seeds?.perEvent ? "Seed times" : "Seed time"}</legend>
                                <div class="grid gap-4 *:mt-0 sm:grid-cols-2">
                                    {#each seedEvents as event (event.id)}
                                        <Field
                                            id="e-seed-{event.id}"
                                            name="seeds[{event.id}]"
                                            label={seeds?.perEvent ? event.name : "Seed"}
                                            value={saved[event.id] ?? ""}
                                            placeholder="01:23.456"
                                            autocomplete="off"
                                            error={errors[`seeds.${event.id}`]}
                                        />
                                    {/each}
                                </div>
                                <span class="v2-hint">Minutes, seconds and thousandths. Leave empty for none.</span>
                            </fieldset>
                        {/if}
                    {/if}

                    <div class="v2-modal-actions mt-6">
                        {#if row}
                            <button type="button" class="v2-btn v2-btn-ghost mr-auto text-v2-danger-text" onclick={() => removeEntry(row)}>Remove</button>
                        {/if}
                        <button type="button" class="v2-btn" onclick={() => entryDialog.close()}>Cancel</button>
                        <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>
                            {row ? "Save" : `Add ${KIND_LABEL[kind].toLowerCase()}`}
                        </button>
                    </div>
                {/snippet}
            </Form>
        {/key}
    {/if}
</dialog>

<dialog bind:this={leagueDialog} class="v2-modal" aria-labelledby="e-league-title" onclick={closeOnBackdrop}>
    <h2 id="e-league-title">{league ? league.name : "Add league"}</h2>

    {#key opened}
        <Form
            action={league ? updateLeague({ comp: competition.id, league: league.id }) : storeLeague(competition.id)}
            options={{ preserveScroll: true }}
            onSuccess={() => leagueDialog.close()}
        >
            {#snippet children({ errors, processing })}
                <div class="mt-5">
                    <Field id="e-league-name" name="name" label="Name" value={league?.name ?? ""} required autocomplete="off" error={errors.name} />
                </div>
                <div class="v2-modal-actions mt-6">
                    {#if league}
                        {@const current = league}
                        <button type="button" class="v2-btn v2-btn-ghost mr-auto text-v2-danger-text" onclick={() => removeLeague(current)}>Delete</button>
                    {/if}
                    <button type="button" class="v2-btn" onclick={() => leagueDialog.close()}>Cancel</button>
                    <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>{league ? "Save" : "Add league"}</button>
                </div>
            {/snippet}
        </Form>
    {/key}
</dialog>
