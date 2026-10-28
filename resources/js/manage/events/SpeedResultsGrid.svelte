<!--
    A speed event's Enter results part: a grid of every result in pool, heat and lane order, with a time,
    a DQ and (when the event has them) penalties per row. With heats in more than one pool, a pool is
    picked first and only its rows and heats show, as heat numbers restart in each pool. One Save sends only the rows that changed to
    SpeedResultEntry, which saves none of them if any cell is wrong, and the failing cells show why.
-->
<script lang="ts">
    import { saveResults } from "@/actions/App/Http/Controllers/Manage/SpeedEventController";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import ChipGroup from "@/manage/components/ChipGroup.svelte";
    import type { SpeedGridRow } from "@/manage/types";
    import { router } from "@inertiajs/svelte";
    import type { MaskInputOptions } from "maska";
    import { untrack } from "svelte";
    import { maska } from "maska/svelte";
    import { upperCase } from "@/manage/ui";

    let {
        comp,
        event,
        rows,
        penalties,
        ropeThrow,
    }: {
        comp: number;
        event: number;
        rows: SpeedGridRow[];
        /** The event takes penalties, so they get a column */
        penalties: boolean;
        /** Rope Throw: ropes in (0-3) or a time */
        ropeThrow: boolean;
    } = $props();

    type Cells = Pick<SpeedGridRow, "result" | "disqualification" | "penalties">;

    function cellsOf(list: SpeedGridRow[]): Record<number, Cells> {
        return Object.fromEntries(list.map((row) => [row.id, { result: row.result, disqualification: row.disqualification, penalties: row.penalties }]));
    }

    // svelte-ignore state_referenced_locally
    let values = $state(cellsOf(rows));

    // Rows can change under the grid when a save comes back (another Organiser added an Entity): give new
    // ones their saved cells, keeping what's been typed in the rest
    $effect.pre(() => {
        const missing = rows.filter((row) => !untrack(() => row.id in values));
        if (missing.length > 0) Object.assign(values, cellsOf(missing));
    });

    let errors = $state<Record<string, string>>({});
    let processing = $state(false);

    const changed = $derived(
        rows.filter((row) => {
            const cells = values[row.id];
            if (!cells) return false;
            return cells.result !== row.result || cells.disqualification !== row.disqualification || cells.penalties !== row.penalties;
        }),
    );

    // Rows without a heat show in every pool, so none get lost
    const pools = $derived([...new Map(rows.flatMap((row) => (row.pool ? [[row.pool.id, row.pool] as const] : []))).values()]);
    let pool = $state<number | null>(null);
    const activePool = $derived(pools.length > 1 ? (pools.find(({ id }) => id === pool) ?? pools[0]).id : null);
    const inPool = (row: SpeedGridRow) => activePool === null || row.pool === null || row.pool.id === activePool;

    const hasHeats = $derived(rows.some((row) => row.heatKey !== null));
    // Rows come in pool and heat order. With a pool picked it's already named, so its chips say "Heat 3"
    const heats = $derived([
        ...new Map(
            rows
                .filter((row) => row.heatKey !== null && inPool(row))
                .map((row) => [row.heatKey!, row.pool && pools.length === 1 ? `${row.pool.name} · Heat ${row.heat}` : `Heat ${row.heat}`] as const),
        ),
    ]);
    let heat = $state<string | null>(null);
    let query = $state("");

    const shown = $derived(
        rows.filter((row) => inPool(row) && (heat === null || row.heatKey === heat) && row.name.toLowerCase().includes(query.trim().toLowerCase())),
    );

    function choosePool(id: number) {
        pool = id;
        heat = null;
    }

    function error(id: number, cell: keyof Cells) {
        return errors[`rows.${id}.${cell}`];
    }

    function save(submit: SubmitEvent) {
        submit.preventDefault();
        const data = Object.fromEntries(
            changed.map((row) => {
                const { result, disqualification, penalties: rowPenalties } = values[row.id];
                return [row.id, penalties ? { result, disqualification, penalties: rowPenalties } : { result, disqualification }];
            }),
        );

        router.post(saveResults({ comp, event }).url, { rows: data }, {
            preserveScroll: true,
            onStart: () => (processing = true),
            onFinish: () => (processing = false),
            onSuccess: () => {
                errors = {};
                values = cellsOf(rows);
            },
            onError: (failed) => {
                errors = failed;
                // Show every row, so none of the failing ones are hidden by a filter, in the first failing one's pool
                const failing = rows.find((row) => Object.keys(failed).some((key) => key.startsWith(`rows.${row.id}.`)));
                if (failing?.pool) pool = failing.pool.id;
                heat = null;
                query = "";
            },
        });
    }

    // Times as mm:ss.hh (or .hhh, as saved times show), DNS, DNF and OOT from their first letter, and DQ
    // codes, filled in as they're typed. Penalties are a free list, so only capitals
    const MASKS: Partial<Record<keyof Cells, MaskInputOptions>> = {
        result: {
            mask: (input: string) => (input.startsWith("D") ? "DNF" : input.startsWith("O") ? "OOT" : "99:59.99#"),
            tokens: {
                "5": { pattern: /[0-5]/ },
                "9": { pattern: /[0-9]/ },
                "#": { pattern: /[0-9]/, optional: true },
                // The last letter of DNS or DNF
                F: { pattern: /[SF]/ },
            },
            preProcess: (value: string) => value.toUpperCase(),
            eager: true,
        },
        disqualification: {
            mask: (input: string) => (input.startsWith("DQ100") ? "DQ####" : "DQ###"),
            preProcess: (value: string) => value.toUpperCase(),
            eager: true,
        },
    };

    const cell = "v2-input w-full min-w-[6.5rem] px-2 py-1.5 font-mono text-sm";
</script>

<form onsubmit={save}>
    <UnsavedChanges dirty={changed.length > 0} />

    <div class="flex flex-col gap-3">
        <input type="search" class="v2-input w-full" placeholder="Search {rows.length === 1 ? 'the entry' : 'entries'}" aria-label="Search entries" bind:value={query} />
        {#if pools.length > 1}
            <ChipGroup label="Pool" options={pools} value={activePool} onpick={(id) => id !== null && choosePool(id)} />
        {/if}
        {#if heats.length > 0}
            <ChipGroup label="Heat" options={heats.map(([id, name]) => ({ id, name }))} value={heat} all="All heats" onpick={(id) => (heat = id)} />
        {/if}
        <p class="text-sm text-v2-muted">
            Enter DNS, DNF or OOT in place of a time.{#if ropeThrow}
                For Rope Throw, enter a time if every rope was in, otherwise 0-3 for how many were.{/if}
        </p>
    </div>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full border-t border-v2-border text-left">
            <thead>
                <tr class="text-sm text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold">
                    <th scope="col">Entry</th>
                    {#if hasHeats}<th scope="col" class="w-px whitespace-nowrap">Heat</th>{/if}
                    <th scope="col">{ropeThrow ? "Ropes or time" : "Time"}</th>
                    <th scope="col">DQ</th>
                    {#if penalties}<th scope="col">Penalties</th>{/if}
                </tr>
            </thead>
            <tbody>
                {#each shown.filter((row) => values[row.id]) as row (row.id)}
                    {@const cells = values[row.id]}
                    <tr class="*:border-b *:border-v2-border *:px-2 *:py-2 *:align-top">
                        <th scope="row" class="pt-3.5 font-semibold wrap-anywhere">{row.name}</th>
                        {#if hasHeats}
                            <td class="pt-3.5 text-sm whitespace-nowrap text-v2-muted">{row.heatLane ?? "–"}</td>
                        {/if}
                        {#each [["result", ropeThrow ? "0-3 or 00:00.00" : "00:00.00"], ["disqualification", "DQ000"], ...(penalties ? [["penalties", "P000, P000"]] : [])] as [name, placeholder] (name)}
                            {@const key = name as keyof Cells}
                            {@const message = error(row.id, key)}
                            <td>
                                <input
                                    class={[cell, message && "is-invalid"]}
                                    {placeholder}
                                    autocomplete="off"
                                    spellcheck="false"
                                    aria-label="{row.name}: {key === 'result' ? 'time' : key === 'disqualification' ? 'DQ' : 'penalties'}"
                                    aria-invalid={message ? "true" : undefined}
                                    aria-describedby={message ? `g-${row.id}-${key}` : undefined}
                                    bind:value={cells[key]}
                                    use:maska={MASKS[key] ?? {}}
                                    oninput={MASKS[key] ? undefined : upperCase}
                                />
                                {#if message}<span id="g-{row.id}-{key}" class="v2-error">{message}</span>{/if}
                            </td>
                        {/each}
                    </tr>
                {/each}
            </tbody>
        </table>
        {#if rows.length === 0}
            <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries yet. Add them on Entries & Leagues, and each gets a row here.</p>
        {:else if shown.length === 0}
            <p class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted">No entries match.</p>
        {/if}
    </div>

    <div class="sticky bottom-0 mt-4 flex items-center justify-end gap-4 border-t border-v2-border bg-v2-bg py-3">
        <span class="mr-auto text-sm text-v2-muted">{shown.length} of {rows.length} shown</span>
        {#if changed.length > 0}<span class="text-sm font-semibold text-v2-muted">{changed.length} unsaved</span>{/if}
        <button type="submit" class="v2-btn v2-btn-primary" disabled={processing || changed.length === 0}>Save results</button>
    </div>
</form>
