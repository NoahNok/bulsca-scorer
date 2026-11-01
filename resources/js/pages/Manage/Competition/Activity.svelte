<!--
    A competition's Activity (manage.competitions.activity), in CompetitionLayout, Organisers only: "Add …"
    pickers, the History shortcut and the active filters as removable chips grouped by kind, then the
    Activity newest first under day headings, with "Load older" appending the next page. Filters live in
    the url (`related`, `by`, `activity`, each |-separated) as on the Admin Activity Monitor, and changing
    one visits the page again, keeping the scroll. Times are in the browser's time zone.
-->
<script lang="ts">
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import type { CompetitionHeader } from "@/manage/types";
    import { ArrowUpRight, Ban, ClipboardList, History, LifeBuoy, Timer, TriangleAlert, Trophy, User, Users, X } from "@lucide/svelte";
    import type { Component } from "svelte";
    import { Link, router } from "@inertiajs/svelte";

    type Param = "related" | "by" | "activity";
    type Filters = Record<Param, string[]>;
    type Kind = "comp" | "serc" | "speed" | "team" | "competitor" | "submission" | "dq" | "penalty";

    /** Something an entry is related to (App\Services\ActivityFeed::relatedItem) */
    type Related = {
        kind: Kind;
        name: string;
        detail: string | null;
        state: "present" | "deleted" | "appealed";
        /** Its own page; null when deleted or what the page is forced to */
        link: string | null;
        /** The `related` value it toggles; null when deleted or what the page is forced to */
        filter: { value: string; active: boolean } | null;
    };

    type Entry = {
        id: string;
        /** ISO */
        at: string;
        type: string;
        title: string;
        description: string | null;
        /** Null for the System */
        by: string | null;
        related: Related[];
        changes: {
            columns: { label: string; old: string; new: string };
            rows: { label: string; old: string | null; new: string | null }[];
        } | null;
    };

    type Picker = {
        label: string;
        param: Param;
        placeholder: string;
        groups: { label: string | null; options: { value: string; label: string }[] }[];
    };

    let {
        competition,
        overviewUrl,
        url,
        entries,
        olderPage,
        filters,
        chipGroups,
        pickers,
        history,
    }: {
        competition: CompetitionHeader;
        overviewUrl: string;
        url: string;
        entries: Entry[];
        /** The next page to load, null on the last */
        olderPage: number | null;
        filters: Filters;
        chipGroups: { label: string; chips: { label: string; param: Param; value: string }[] }[];
        pickers: Picker[];
        /** The History shortcut: the Mark, Time and Place types */
        history: { value: string[]; active: boolean };
    } = $props();

    const KINDS: Record<Kind, { label: string; icon: Component }> = {
        comp: { label: "Competition", icon: Trophy },
        serc: { label: "SERC", icon: LifeBuoy },
        speed: { label: "Speed event", icon: Timer },
        team: { label: "Team", icon: Users },
        competitor: { label: "Competitor", icon: User },
        submission: { label: "Submission", icon: ClipboardList },
        dq: { label: "DQ", icon: Ban },
        penalty: { label: "Penalty", icon: TriangleAlert },
    };

    let loadingOlder = $state(false);

    function query(next: Filters) {
        return Object.fromEntries(
            Object.entries(next)
                .filter(([, values]) => values.length > 0)
                .map(([param, values]) => [param, values.join("|")]),
        );
    }

    /** Shows the Activity for these filters, from the newest */
    function visit(next: Filters) {
        router.get(url, query(next), { preserveState: true, preserveScroll: true });
    }

    function add(param: Param, value: string) {
        if (value && !filters[param].includes(value)) visit({ ...filters, [param]: [...filters[param], value] });
    }

    function remove(param: Param, value: string) {
        visit({ ...filters, [param]: filters[param].filter((v) => v !== value) });
    }

    function toggle(param: Param, value: string) {
        if (filters[param].includes(value)) remove(param, value);
        else add(param, value);
    }

    function toggleHistory() {
        visit({ ...filters, activity: history.active ? [] : history.value });
    }

    function loadOlder() {
        if (olderPage === null) return;
        router.get(url, { ...query(filters), page: olderPage }, {
            only: ["entries", "olderPage"],
            preserveState: true,
            preserveScroll: true,
            onStart: () => (loadingOlder = true),
            onFinish: () => (loadingOlder = false),
        });
    }

    const dayKey = (date: Date) => `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;

    function dayLabel(date: Date) {
        const today = new Date();
        const yesterday = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1);
        if (dayKey(date) === dayKey(today)) return "Today";
        if (dayKey(date) === dayKey(yesterday)) return "Yesterday";
        // "Sun 1 Nov 2026": en-GB puts a comma after the weekday
        const part = (options: Intl.DateTimeFormatOptions) => date.toLocaleDateString("en-GB", options);
        return `${part({ weekday: "short" })} ${part({ day: "numeric" })} ${part({ month: "short" })} ${part({ year: "numeric" })}`;
    }

    const time = (date: Date) => date.toLocaleTimeString("en-GB", { hour: "2-digit", minute: "2-digit", second: "2-digit", hourCycle: "h23" });

    /** The entries under a heading per day, newest first as they come */
    const days = $derived.by(() => {
        const groups: { key: string; label: string; entries: (Entry & { date: Date })[] }[] = [];
        for (const entry of entries) {
            const date = new Date(entry.at);
            const key = dayKey(date);
            let group = groups.at(-1);
            if (group?.key !== key) {
                group = { key, label: dayLabel(date), entries: [] };
                groups.push(group);
            }
            group.entries.push({ ...entry, date });
        }
        return groups;
    });

    const filtered = $derived(chipGroups.length > 0);
    const muted = "text-xs font-semibold text-v2-muted";
</script>

<svelte:head>
    <title>Activity - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet relatedItem(item: Related)}
    {@const kind = KINDS[item.kind]}
    {@const title = item.detail ? `${kind.label}: ${item.detail}` : kind.label}
    <li
        class={[
            "inline-flex items-stretch rounded-md border text-[0.8125rem]",
            item.filter?.active ? "border-v2-teal bg-v2-teal-soft" : "border-v2-border",
        ]}
    >
        {#snippet label()}
            <kind.icon size={14} class="shrink-0 text-v2-muted" aria-hidden="true" />
            <span class="sr-only">{kind.label}:</span>
            <span class={["wrap-anywhere", item.state === "appealed" && "line-through"]}>{item.name}</span>
            {#if item.state === "appealed"}<span class="font-semibold text-v2-muted">Appealed</span>{/if}
        {/snippet}
        {#if item.filter}
            {@const filter = item.filter}
            <button
                type="button"
                class="inline-flex cursor-pointer items-center gap-1.5 rounded-md px-2 py-0.5 font-semibold hover:bg-v2-row-hover"
                aria-pressed={filter.active}
                title="{title}. {filter.active ? 'Stop filtering by this' : 'Filter by this'}"
                onclick={() => toggle("related", filter.value)}
            >
                {@render label()}
            </button>
        {:else}
            <span
                class={["inline-flex items-center gap-1.5 px-2 py-0.5", item.state === "deleted" ? "text-v2-muted italic" : "font-semibold"]}
                {title}
            >
                {@render label()}
            </span>
        {/if}
        {#if item.link}
            <Link
                href={item.link}
                class="grid place-items-center rounded-r-md border-l border-v2-border px-1.5 text-v2-muted hover:bg-v2-row-hover hover:text-v2-ink"
                aria-label="Open {item.name}"
                title="Open page"
            >
                <ArrowUpRight size={14} aria-hidden="true" />
            </Link>
        {/if}
    </li>
{/snippet}

<PageHeading back={{ href: overviewUrl, label: "Overview" }} title="Activity" lead="Everything done on this competition, newest first." />

<section aria-label="Filters" class="mb-8 flex flex-col gap-3">
    <div class="grid gap-2 sm:grid-cols-[repeat(auto-fit,minmax(12rem,1fr))]">
        {#each pickers as picker (picker.label)}
            <select
                class="v2-input"
                aria-label="Filter by {picker.label.toLowerCase()}"
                onchange={(event) => {
                    add(picker.param, event.currentTarget.value);
                    event.currentTarget.value = "";
                }}
            >
                <option value="" selected>{picker.placeholder}</option>
                {#each picker.groups as group, i (i)}
                    {#if group.label === null}
                        {#each group.options as option (option.value)}
                            <option value={option.value}>{option.label}</option>
                        {/each}
                    {:else if group.options.length > 0}
                        <optgroup label={group.label}>
                            {#each group.options as option (option.value)}
                                <option value={option.value}>{option.label}</option>
                            {/each}
                        </optgroup>
                    {/if}
                {/each}
            </select>
        {/each}
    </div>

    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
        <button
            type="button"
            class="v2-chip inline-flex items-center gap-1.5"
            aria-pressed={history.active}
            title="Marks, Times and Places"
            onclick={toggleHistory}
        >
            <History size={14} aria-hidden="true" />History
        </button>
        {#each chipGroups as group, g (group.label)}
            {#if g > 0}<span class={muted}>and</span>{/if}
            <span class="flex flex-wrap items-center gap-1.5" role="group" aria-label={group.label}>
                <span class={muted}>{group.label}</span>
                {#each group.chips as chip, c (chip.value)}
                    {#if c > 0}<span class={muted}>or</span>{/if}
                    <span class="inline-flex items-center gap-1 rounded-md bg-v2-sunk py-0.5 pr-1 pl-2 text-sm font-semibold">
                        {chip.label}
                        <button
                            type="button"
                            class="grid size-5 cursor-pointer place-items-center rounded text-v2-muted hover:text-v2-ink"
                            aria-label="Remove filter {group.label}: {chip.label}"
                            onclick={() => remove(chip.param, chip.value)}
                        >
                            <X size={14} aria-hidden="true" />
                        </button>
                    </span>
                {/each}
            </span>
        {/each}
        {#if filtered}
            <button type="button" class="v2-link-muted cursor-pointer text-sm" onclick={() => visit({ related: [], by: [], activity: [] })}>
                Clear
            </button>
        {/if}
    </div>
</section>

{#if entries.length === 0}
    <EmptyState>No activity yet</EmptyState>
{:else}
    <div class="flex flex-col gap-8">
        {#each days as day (day.key)}
            <section aria-labelledby="day-{day.key}">
                <h2 id="day-{day.key}" class="border-b border-v2-border pb-2 text-base font-extrabold uppercase">{day.label}</h2>
                <ol class="*:border-b *:border-v2-border">
                    {#each day.entries as entry (entry.id)}
                        <li class="grid gap-x-5 gap-y-1 py-3.5 sm:grid-cols-[4.5rem_minmax(0,1fr)_auto]">
                            <time datetime={entry.at} class="pt-px text-sm font-semibold text-v2-muted tabular-nums">{time(entry.date)}</time>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold">{entry.title}</h3>
                                {#if entry.description}
                                    <p class="text-[0.9375rem]/[1.5] wrap-anywhere">{entry.description}</p>
                                {/if}
                                {#if entry.related.length > 0}
                                    <ul class="mt-2 flex flex-wrap gap-1.5" aria-label="Related to">
                                        {#each entry.related as item, i (i)}
                                            {@render relatedItem(item)}
                                        {/each}
                                    </ul>
                                {/if}
                                {#if entry.changes}
                                    {@const changes = entry.changes}
                                    <details class="mt-2">
                                        <summary class="v2-link-muted w-fit cursor-pointer text-sm">
                                            {changes.rows.length === 1 ? "1 change" : `${changes.rows.length} changes`}
                                        </summary>
                                        <table class="mt-2 w-full max-w-2xl text-sm">
                                            <thead>
                                                <tr class="border-b border-v2-border text-left text-xs text-v2-muted uppercase *:py-1.5 *:pr-4 *:font-semibold">
                                                    <th scope="col">{changes.columns.label}</th>
                                                    <th scope="col">{changes.columns.old}</th>
                                                    <th scope="col">{changes.columns.new}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {#each changes.rows as row, i (i)}
                                                    <tr class="border-b border-v2-border *:py-1.5 *:pr-4 *:text-left">
                                                        <th scope="row" class="font-semibold">{row.label}</th>
                                                        <td class="text-v2-muted tabular-nums">{row.old ?? "–"}</td>
                                                        <td class="tabular-nums">{row.new ?? "–"}</td>
                                                    </tr>
                                                {/each}
                                            </tbody>
                                        </table>
                                    </details>
                                {/if}
                            </div>
                            <p class="text-sm text-v2-muted sm:text-right">
                                <span class="sr-only">Actioned by</span>{entry.by ?? "System"}
                            </p>
                        </li>
                    {/each}
                </ol>
            </section>
        {/each}
    </div>

    {#if olderPage !== null}
        <div class="mt-6 flex justify-center">
            <button type="button" class="v2-btn" disabled={loadingOlder} onclick={loadOlder}>
                {loadingOlder ? "Loading…" : "Load older"}
            </button>
        </div>
    {/if}
{/if}
