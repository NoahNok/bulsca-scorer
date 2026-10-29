<!--
    New Results sheet (manage.competitions.results.create) and Edit sheet (….edit), one full-width page in
    CompetitionLayout: its name and League (and, when new, Repeat for every League), the events it ranks
    with their weights, its Formula (a built-in, one of the Organisation's copied in, or custom), how it
    ranks, and the events that break ties, in order. Editing a published sheet says the change shows
    publicly as soon as it's saved.
-->
<script lang="ts">
    import { index, show, store, update } from "@/actions/App/Http/Controllers/Manage/ResultSheetsController";
    import Alert from "@/manage/components/Alert.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import UnconfirmedAlert from "@/manage/results/UnconfirmedAlert.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { BuiltInFormula, CompetitionHeader, FormulaVariable, ResultSheetSettings, SheetEventRef, UnconfirmedEvent } from "@/manage/types";
    import { Link, useForm } from "@inertiajs/svelte";

    type SheetEvent = SheetEventRef & { name: string; weight: number };

    let {
        competition,
        sheet,
        events,
        leagues,
        builtIns,
        organisationFormulas,
        organisation,
        published,
        canCreate,
        unconfirmed,
    }: {
        competition: CompetitionHeader;
        /** Null for a new sheet */
        sheet: ResultSheetSettings | null;
        /** Every event in the competition, with its weight on this sheet (or the default) */
        events: SheetEvent[];
        leagues: { id: number; name: string }[];
        builtIns: BuiltInFormula[];
        organisationFormulas: { id: number; name: string; equation: string; global_variables: FormulaVariable[] }[];
        organisation: string | null;
        published: boolean;
        canCreate: boolean;
        unconfirmed: UnconfirmedEvent[];
    } = $props();

    const comp = $derived(competition.id);
    const backUrl = $derived(sheet ? show({ comp, sheet: sheet.id }).url : index(comp).url);
    const keyOf = (event: SheetEventRef) => `${event.type}:${event.id}`;

    // svelte-ignore state_referenced_locally
    const form = useForm({
        name: sheet?.name ?? "",
        league: sheet?.league ?? null,
        repeat_for_all_leagues: false,
        // Every event's weight, and which are on the sheet
        weights: Object.fromEntries(events.map((event) => [keyOf(event), event.weight])) as Record<string, number>,
        selected: (sheet?.events ?? []).map(keyOf),
        equation: sheet?.equation ?? "",
        global_variables: structuredClone($state.snapshot(sheet?.global_variables ?? [])) as FormulaVariable[],
        group_on: sheet?.group_on ?? "team",
        rank_higher: sheet?.rank_higher ?? true,
        ignore_dq: sheet?.ignore_dq ?? false,
        break_ties: (sheet?.break_ties ?? []).map(keyOf),
    });

    // The Formula picker: a built-in sets the equation and direction; an Organisation's is copied in to edit
    // svelte-ignore state_referenced_locally
    let formula = $state(sheet ? (sheet.builtIn ? `builtin:${sheet.builtIn}` : "custom") : "");

    function pickFormula(value: string) {
        formula = value;
        const [kind, key] = value.split(":");
        if (kind === "builtin") {
            const builtIn = builtIns.find((option) => option.key === key)!;
            form.equation = builtIn.equation;
            form.rank_higher = builtIn.rankHigher;
            form.global_variables = [];
        } else if (kind === "org") {
            const copied = organisationFormulas.find((option) => String(option.id) === key)!;
            form.equation = copied.equation;
            form.global_variables = structuredClone($state.snapshot(copied.global_variables));
            formula = "custom";
        }
    }

    function toggleEvent(key: string) {
        if (form.selected.includes(key)) {
            form.selected = form.selected.filter((selected) => selected !== key);
            form.break_ties = form.break_ties.filter((tieBreak) => tieBreak !== key);
        } else {
            form.selected = [...form.selected, key];
        }
    }

    function toggleTieBreak(key: string) {
        form.break_ties = form.break_ties.includes(key) ? form.break_ties.filter((tieBreak) => tieBreak !== key) : [...form.break_ties, key];
    }

    const onSheet = $derived(events.filter((event) => form.selected.includes(keyOf(event))));
    const filled = (value: string | null | undefined) => (value ?? "").trim() !== "";
    const refOf = (key: string): SheetEventRef => {
        const [type, id] = key.split(":");
        return { type: type as SheetEventRef["type"], id: Number(id) };
    };

    function save(submit: SubmitEvent) {
        submit.preventDefault();
        form.transform((data) => ({
            name: data.name,
            league: data.league,
            repeat_for_all_leagues: data.repeat_for_all_leagues,
            // In the order the competition lists them
            events: events.filter((event) => data.selected.includes(keyOf(event))).map((event) => ({ ...refOf(keyOf(event)), weight: data.weights[keyOf(event)] })),
            equation: data.equation,
            global_variables: data.global_variables.filter((v) => filled(v.name) && filled(v.expression)),
            group_on: data.group_on,
            rank_higher: data.rank_higher,
            ignore_dq: data.ignore_dq,
            break_ties: data.break_ties.map(refOf),
        }));
        if (sheet) form.post(update({ comp, sheet: sheet.id }).url, { onSuccess: () => form.defaults() });
        else form.post(store(comp).url);
    }

    // Errors on keys the form doesn't hold: the events list (or one event) and the whole sheet
    const errors = $derived(form.errors as Record<string, string>);
    const eventsError = $derived(Object.entries(errors).find(([key]) => key === "events" || key.startsWith("events."))?.[1]);
    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-4 text-[0.9375rem]/[1.6] text-v2-muted";
    const section = "border-t border-v2-border pt-6";
    const code = "v2-input min-w-0 px-2 py-1.5 font-mono text-sm";
    const pickCard =
        "flex w-full cursor-pointer items-center gap-3 rounded-xl border border-v2-border bg-v2-card px-4 py-3 text-left transition-colors hover:bg-v2-row-hover aria-pressed:border-v2-teal aria-pressed:bg-v2-teal-soft motion-reduce:transition-none";
</script>

<svelte:head>
    <title>{sheet ? `Edit ${sheet.name}` : "New Results sheet"} - {competition.name} - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: backUrl, label: sheet ? sheet.name : "Results" }} title={sheet ? "Edit sheet" : "New Results sheet"} />

{#if !canCreate}
    <div class="mt-5">
        <UnconfirmedAlert {unconfirmed} />
    </div>
{/if}

{#if sheet && published}
    <div class="mt-5">
        <Alert tone="info" title="Results are published">
            <p class="mt-1">Your changes show in the public results as soon as you save.</p>
        </Alert>
    </div>
{/if}

<form onsubmit={save} class="mt-6 flex max-w-4xl flex-col gap-8">
    {#if sheet}<UnsavedChanges dirty={form.isDirty} />{/if}

    <section aria-labelledby="sf-details" class="flex flex-col gap-4">
        <h2 id="sf-details" class={heading}>Details</h2>
        <div class="grid gap-4 *:mt-0 sm:grid-cols-2">
            <div class={["v2-field", form.errors.name && "is-invalid"]}>
                <label for="sf-name">Name</label>
                <input
                    id="sf-name"
                    class="v2-input w-full"
                    bind:value={form.name}
                    placeholder="The League's name"
                    autocomplete="off"
                    aria-invalid={form.errors.name ? "true" : undefined}
                    aria-describedby={form.errors.name ? "sf-name-hint sf-name-error" : "sf-name-hint"}
                />
                <span id="sf-name-hint" class="v2-hint">Leave it blank to use the League's name.</span>
                {#if form.errors.name}<span id="sf-name-error" class="v2-error">{form.errors.name}</span>{/if}
            </div>
            <div class={["v2-field", form.errors.league && "is-invalid"]}>
                <label for="sf-league">Ranks</label>
                <select
                    id="sf-league"
                    class="v2-input w-full"
                    bind:value={form.league}
                    disabled={form.repeat_for_all_leagues}
                    aria-invalid={form.errors.league ? "true" : undefined}
                    aria-describedby={form.errors.league ? "sf-league-error" : undefined}
                >
                    <option value={null}>The whole competition</option>
                    {#each leagues as league (league.id)}
                        <option value={league.id}>{league.name}</option>
                    {/each}
                </select>
                {#if form.errors.league}<span id="sf-league-error" class="v2-error">{form.errors.league}</span>{/if}
            </div>
        </div>
        {#if !sheet && leagues.length > 1}
            <Switch name="repeat_for_all_leagues" label="Make one for every League" bind:checked={form.repeat_for_all_leagues} hint="Makes a sheet like this for each League, named after it unless you give a name." />
        {/if}
    </section>

    <section aria-labelledby="sf-events" class={section}>
        <h2 id="sf-events" class={heading}>Events</h2>
        <p class={lead}>Pick the events it ranks, and how much each one counts.</p>
        {#if events.length === 0}
            <p class="text-[0.9375rem] text-v2-muted">This competition has no events yet.</p>
        {/if}
        <ul class="grid gap-2 sm:grid-cols-2">
            {#each events as event (keyOf(event))}
                {@const key = keyOf(event)}
                {@const picked = form.selected.includes(key)}
                <li class="flex items-stretch gap-2">
                    <button type="button" class={pickCard} aria-pressed={picked} onclick={() => toggleEvent(key)}>
                        <span class="min-w-0 flex-1 font-semibold wrap-anywhere">{event.name}</span>
                        {#if event.type === "serc"}<span class="v2-badge">SERC</span>{/if}
                    </button>
                    {#if picked}
                        <label class="flex shrink-0 flex-col justify-center text-xs font-semibold text-v2-muted">
                            Weight
                            <input type="number" min="0" step="any" class="{code} w-20" bind:value={form.weights[key]} aria-label="{event.name} weight" />
                        </label>
                    {/if}
                </li>
            {/each}
        </ul>
        {#if eventsError}<span class="v2-error mt-2 block">{eventsError}</span>{/if}
    </section>

    <section aria-labelledby="sf-formula" class={section}>
        <h2 id="sf-formula" class={heading}>Formula</h2>
        <p class={lead}>How each event's results become points on this sheet.</p>
        <div class="v2-field mt-0 max-w-md">
            <label for="sf-formula-pick">Formula</label>
            <select id="sf-formula-pick" class="v2-input w-full" value={formula} onchange={(change) => pickFormula(change.currentTarget.value)}>
                <option value="" disabled>Pick a Formula</option>
                <optgroup label="Built-in">
                    {#each builtIns as builtIn (builtIn.key)}
                        <option value="builtin:{builtIn.key}">{builtIn.label}</option>
                    {/each}
                </optgroup>
                {#if organisationFormulas.length > 0}
                    <optgroup label="{organisation ?? 'Organisation'} (copied in to edit)">
                        {#each organisationFormulas as copied (copied.id)}
                            <option value="org:{copied.id}">{copied.name}</option>
                        {/each}
                    </optgroup>
                {/if}
                <option value="custom">Custom</option>
            </select>
            {#if form.errors.equation && formula !== "custom"}<span class="v2-error">Pick a Formula.</span>{/if}
        </div>

        {#if formula === "custom"}
            <div class="mt-5 flex flex-col gap-5 rounded-xl border border-v2-border bg-v2-card px-4 py-4">
                <div>
                    <h3 class="text-sm font-extrabold uppercase">Global variables</h3>
                    <p class="mt-1 mb-3 text-sm/[1.6] text-v2-muted">Worked out once per event, for the result equation.</p>
                    <ul class="flex flex-col gap-2">
                        {#each form.global_variables as variable, i (i)}
                            <li class="flex items-center gap-2">
                                <input class="{code} w-32" placeholder="name" aria-label="Global variable {i + 1} name" bind:value={variable.name} />
                                <span class="text-sm text-v2-muted">=</span>
                                <input class="{code} flex-1" placeholder="MINIMUM(results, 'points')" aria-label="Global variable {i + 1} expression" bind:value={variable.expression} />
                                <button type="button" class="v2-btn v2-btn-sm v2-btn-ghost" aria-label="Remove global variable {i + 1}" onclick={() => form.global_variables.splice(i, 1)}>Remove</button>
                            </li>
                        {/each}
                    </ul>
                    <button
                        type="button"
                        class="v2-btn v2-btn-sm mt-3"
                        onclick={() => form.global_variables.push({ name: "", expression: "", order: Math.max(0, ...form.global_variables.map((v) => v.order ?? 0)) + 1 })}
                    >
                        Add global variable
                    </button>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold uppercase">Result equation</h3>
                    <p class="mt-1 mb-3 text-sm/[1.6] text-v2-muted">Gives each event result its points on this sheet. It can use the variables above.</p>
                    <input
                        class={[code, "w-full", form.errors.equation && "is-invalid"]}
                        placeholder="item.points"
                        aria-label="Result equation"
                        aria-invalid={form.errors.equation ? "true" : undefined}
                        aria-describedby={form.errors.equation ? "sf-equation-error" : undefined}
                        bind:value={form.equation}
                    />
                    {#if form.errors.equation}<span id="sf-equation-error" class="v2-error">{form.errors.equation}</span>{/if}
                </div>
                <label class="v2-check mt-0"><input type="checkbox" bind:checked={form.rank_higher} /> Highest points total wins</label>
            </div>
        {/if}
    </section>

    <section aria-labelledby="sf-ranking" class={section}>
        <h2 id="sf-ranking" class={heading}>Ranking</h2>
        <p class={lead}>What it ranks, and which events break ties, in order. With none, tied entries share a place.</p>
        <div class="grid gap-4 *:mt-0 sm:grid-cols-2">
            <div class="v2-field">
                <label for="sf-group">Ranks</label>
                <select id="sf-group" class="v2-input w-full" bind:value={form.group_on}>
                    <option value="team">Teams</option>
                    <option value="club">Clubs</option>
                    <option value="competitor">Competitors</option>
                </select>
            </div>
            <div class="flex items-end pb-2">
                <label class="v2-check mt-0"><input type="checkbox" bind:checked={form.ignore_dq} /> Keep disqualified results' points</label>
            </div>
        </div>

        <h3 class="mt-6 mb-2 text-sm font-extrabold uppercase">Tie-breaks</h3>
        {#if onSheet.length === 0}
            <p class="text-[0.9375rem] text-v2-muted">Pick events above to break ties with them.</p>
        {:else}
            <ul class="flex flex-wrap gap-2" role="group" aria-label="Tie-breaks">
                {#each onSheet as event (keyOf(event))}
                    {@const order = form.break_ties.indexOf(keyOf(event))}
                    <li>
                        <button type="button" class="v2-chip" aria-pressed={order >= 0} onclick={() => toggleTieBreak(keyOf(event))}>
                            {#if order >= 0}<span class="tabular-nums">{order + 1}.</span>{/if}
                            {event.name}
                        </button>
                    </li>
                {/each}
            </ul>
        {/if}
        {#if form.errors.break_ties}<span class="v2-error mt-2 block">{form.errors.break_ties}</span>{/if}
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-4 border-t border-v2-border bg-v2-bg py-4">
        {#if errors.sheet}
            <span class="mr-auto text-sm font-semibold text-v2-danger-text">{errors.sheet}</span>
        {:else if form.hasErrors}
            <span class="mr-auto text-sm font-semibold text-v2-danger-text">Check the fields above.</span>
        {/if}
        {#if sheet && form.isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
        <Link href={backUrl} class="v2-link-muted text-sm">Cancel</Link>
        <button type="submit" class="v2-btn v2-btn-primary" disabled={form.processing || !canCreate}>{sheet ? "Save sheet" : "Make sheet"}</button>
    </div>
</form>
