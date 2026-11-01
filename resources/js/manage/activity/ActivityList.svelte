<!--
    Activity entries newest first under day headings ("Today", "Yesterday", "Sun 1 Nov 2026"), each with
    its time, title, description, who did it, related items and a "N changes" table. A related item with a
    `filter` is a button calling `onrelated` with its `related` value; one with a `link` gets an open
    button. `older` adds a "Load older" button under the list. Times are in the browser's time zone.
-->
<script lang="ts">
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import type { ActivityEntry, ActivityKind, ActivityRelated } from "@/manage/types";
    import { ArrowUpRight, Ban, ClipboardList, LifeBuoy, Timer, TriangleAlert, Trophy, User, Users } from "@lucide/svelte";
    import type { Component } from "svelte";
    import { Link } from "@inertiajs/svelte";

    let {
        entries,
        onrelated,
        older,
    }: {
        entries: ActivityEntry[];
        /** Toggles a `related` filter */
        onrelated: (value: string) => void;
        /** Null on the last page */
        older: { loading: boolean; load: () => void } | null;
    } = $props();

    const KINDS: Record<ActivityKind, { label: string; icon: Component }> = {
        comp: { label: "Competition", icon: Trophy },
        serc: { label: "SERC", icon: LifeBuoy },
        speed: { label: "Speed event", icon: Timer },
        team: { label: "Team", icon: Users },
        competitor: { label: "Competitor", icon: User },
        submission: { label: "Submission", icon: ClipboardList },
        dq: { label: "DQ", icon: Ban },
        penalty: { label: "Penalty", icon: TriangleAlert },
    };

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
        const groups: { key: string; label: string; entries: (ActivityEntry & { date: Date })[] }[] = [];
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
</script>

{#snippet relatedItem(item: ActivityRelated)}
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
                onclick={() => onrelated(filter.value)}
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

    {#if older}
        <div class="mt-6 flex justify-center">
            <button type="button" class="v2-btn" disabled={older.loading} onclick={older.load}>
                {older.loading ? "Loading…" : "Load older"}
            </button>
        </div>
    {/if}
{/if}
