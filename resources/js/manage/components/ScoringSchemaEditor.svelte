<!--
    How an event's results become points and a ranking: an event page's Scoring part, and an organisation's
    Scoring template page. Given `options` (an event's), before it has any scoring pick an organisation's
    Scoring template to start from or start blank, and after that start again from one at any time. Without
    them (a Scoring template) it opens straight on the editor. Given a `name`, it edits that too (a Scoring
    template's). Nothing is saved until Save, and rows left half filled in are dropped then.
-->
<script lang="ts">
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { EventScoring, ScoringSchema, ScoringVariable, ScoringViolation } from "@/manage/types";
    import { useForm } from "@inertiajs/svelte";

    let {
        scoring,
        url,
        name,
        saveLabel = "Save scoring",
    }: {
        /** The schema (null for none yet), and an event's Scoring templates to start from */
        scoring: { schema: ScoringSchema | null; options?: EventScoring["options"] };
        /** Where it saves */
        url: string;
        /** A Scoring template's name, to edit with it. Left out, there's no Name field */
        name?: string;
        saveLabel?: string;
    } = $props();

    const BLANK: ScoringSchema = {
        equation: "",
        penalty_func: "",
        global_variables: [],
        local_variables: [],
        auto_penalties: [],
        auto_disqualifications: [],
        rank_higher: true,
        rank_equation: "",
        allow_disqualified_to_rank: false,
        group_by: [],
        force_penalty: false,
    };

    const GROUPS = [
        ["league", "League"],
        ["club", "Club"],
        ["team", "Team"],
    ];

    // svelte-ignore state_referenced_locally
    const form = useForm<ScoringSchema & { name?: string }>({
        ...structuredClone($state.snapshot(scoring.schema) ?? BLANK),
        ...(name !== undefined ? { name } : {}),
    });
    // svelte-ignore state_referenced_locally
    const options = scoring.options ?? [];
    // svelte-ignore state_referenced_locally
    const canPick = scoring.options !== undefined;
    // svelte-ignore state_referenced_locally
    let started = $state(scoring.schema !== null || !canPick);
    let template = $state("");

    // The schemas to start from, a group per organisation
    const groups = $derived.by(() => {
        const byOrganisation = new Map<string, EventScoring["options"]>();
        for (const option of options) {
            byOrganisation.set(option.organisation, [...(byOrganisation.get(option.organisation) ?? []), option]);
        }
        return [...byOrganisation];
    });

    function startFrom(id: string) {
        const option = options.find((option) => String(option.id) === id);
        const schema = structuredClone($state.snapshot(option?.schema ?? BLANK));
        for (const key of Object.keys(BLANK) as (keyof ScoringSchema)[]) {
            (form as unknown as Record<string, unknown>)[key] = schema[key];
        }
        started = true;
        template = "";
    }

    const filled = (value: string | null | undefined) => (value ?? "").trim() !== "";

    function save(submit: SubmitEvent) {
        submit.preventDefault();
        form.transform((data) => ({
            ...data,
            global_variables: data.global_variables.filter((v) => filled(v.name) && filled(v.expression)),
            local_variables: data.local_variables.filter((v) => filled(v.name) && filled(v.expression)),
            auto_penalties: data.auto_penalties.filter((v) => filled(v.code) && filled(v.condition) && filled(v.amount)),
            auto_disqualifications: data.auto_disqualifications.filter((v) => filled(v.code) && filled(v.condition) && filled(v.amount)),
        })).post(url, { preserveScroll: true, onSuccess: () => form.defaults() });
    }

    function nextOrder(list: ScoringVariable[]) {
        return Math.max(0, ...list.map((v) => v.order ?? 0)) + 1;
    }

    function toggleGroup(group: string) {
        form.group_by = form.group_by.includes(group) ? form.group_by.filter((g) => g !== group) : [...form.group_by, group];
    }

    const heading = "text-base font-extrabold uppercase";
    const lead = "mt-1 mb-3 text-sm/[1.6] text-v2-muted";
    const code = "v2-input min-w-0 px-2 py-1.5 font-mono text-sm";
    const remove =
        "inline-flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-md text-v2-muted transition-colors hover:bg-v2-sunk hover:text-v2-danger-text motion-reduce:transition-none";
    const section = "border-t border-v2-border pt-5";
</script>

{#snippet cross(label: string, onclick: () => void)}
    <button type="button" class={remove} aria-label={label} title={label} {onclick}>
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
    </button>
{/snippet}

{#snippet violations(list: ScoringViolation[], kind: string, placeholder: string)}
    <ul class="flex flex-col gap-2">
        {#each list as row, index (index)}
            <li class="flex flex-wrap items-center gap-2">
                <input class="{code} w-24" placeholder={placeholder} aria-label="{kind} {index + 1} code" bind:value={row.code} />
                <span class="text-sm text-v2-muted">×</span>
                <input class="{code} w-24" placeholder="amount" aria-label="{kind} {index + 1} amount" bind:value={row.amount} />
                <span class="text-sm text-v2-muted">if</span>
                <input class="{code} flex-1 basis-48" placeholder="condition" aria-label="{kind} {index + 1} condition" bind:value={row.condition} />
                {@render cross(`Remove ${kind.toLowerCase()} ${index + 1}`, () => list.splice(index, 1))}
            </li>
        {/each}
    </ul>
    <button type="button" class="v2-btn v2-btn-sm mt-3" onclick={() => list.push({ code: "", amount: "", condition: "" })}>Add {kind.toLowerCase()}</button>
{/snippet}

{#snippet variables(list: ScoringVariable[], kind: string)}
    <ul class="flex flex-col gap-2">
        {#each list as row, index (index)}
            <li class="flex items-center gap-2">
                <input class="{code} w-32" placeholder="name" aria-label="{kind} {index + 1} name" bind:value={row.name} />
                <span class="text-sm text-v2-muted">=</span>
                <input class="{code} flex-1" placeholder="score * 2" aria-label="{kind} {index + 1} expression" bind:value={row.expression} />
                {@render cross(`Remove ${kind.toLowerCase()} ${index + 1}`, () => list.splice(index, 1))}
            </li>
        {/each}
    </ul>
    <button type="button" class="v2-btn v2-btn-sm mt-3" onclick={() => list.push({ order: nextOrder(list), name: "", expression: "" })}>
        Add {kind.toLowerCase()}
    </button>
{/snippet}

{#snippet templatePicker(label: string)}
    <div class="flex flex-wrap items-end gap-2">
        <div class="v2-field mt-0 min-w-0 flex-1 basis-56">
            <label for="sc-template">{label}</label>
            <select id="sc-template" class="v2-input w-full" bind:value={template}>
                <option value="" disabled>Pick a schema</option>
                <option value="blank">Blank</option>
                {#each groups as [organisation, options] (organisation)}
                    <optgroup label={organisation}>
                        {#each options as option (option.id)}
                            <option value={String(option.id)}>{option.name}</option>
                        {/each}
                    </optgroup>
                {/each}
            </select>
        </div>
        <button type="button" class="v2-btn" disabled={template === ""} onclick={() => startFrom(template)}>{started ? "Start again" : "Start"}</button>
    </div>
{/snippet}

{#if !started}
    <p class="mb-4 text-[0.9375rem]/[1.6] text-v2-muted">
        This event has no scoring yet, so it has no points or positions. Start from one of your organisations' schemas, or a blank one.
    </p>
    {@render templatePicker("Start from")}
{:else}
    <form onsubmit={save} class="flex flex-col gap-6">
        <UnsavedChanges dirty={form.isDirty} />

        {#if name !== undefined}
            <div class={["v2-field mt-0 max-w-md", form.errors.name && "is-invalid"]}>
                <label for="sc-name">Name</label>
                <input
                    id="sc-name"
                    class="v2-input w-full"
                    bind:value={form.name}
                    maxlength="255"
                    autocomplete="off"
                    aria-invalid={form.errors.name ? "true" : undefined}
                    aria-describedby={form.errors.name ? "sc-name-error" : undefined}
                />
                {#if form.errors.name}<span id="sc-name-error" class="v2-error">{form.errors.name}</span>{/if}
            </div>
        {/if}

        {#if canPick}
        <details class="rounded-xl border border-v2-border bg-v2-card px-4 py-3">
            <summary class="cursor-pointer text-sm font-semibold">Start again from another schema</summary>
            <p class="mt-2 mb-3 text-sm text-v2-muted">Replaces everything below. Nothing changes until you save.</p>
            {@render templatePicker("Schema")}
        </details>
        {/if}

        <section>
            <h3 class={heading}>Result equation</h3>
            <p class={lead}>Gives each row its points, which are then ranked. It can use the variables below.</p>
            <input class={[code, "w-full", form.errors.equation && "is-invalid"]} placeholder="equation" aria-label="Result equation" bind:value={form.equation} />
            {#if form.errors.equation}<span class="v2-error">{form.errors.equation}</span>{/if}
        </section>

        <section class={section}>
            <h3 class={heading}>Penalty function</h3>
            <p class={lead}>Runs on each row before the result equation. Include the result in it, like <code class="font-mono">result - (penalties * 5)</code>.</p>
            <input class="{code} w-full" placeholder="function" aria-label="Penalty function" bind:value={form.penalty_func} />
            <label class="v2-check mt-3">
                <input type="checkbox" bind:checked={form.force_penalty} />
                Always run, even on rows without a penalty
            </label>
        </section>

        <section class={section}>
            <h3 class={heading}>Global variables</h3>
            <p class={lead}>Worked out once for the event, for local variables and the result equation.</p>
            {@render variables(form.global_variables, "Global variable")}
        </section>

        <section class={section}>
            <h3 class={heading}>Local variables</h3>
            <p class={lead}>Worked out once per row, for the result equation.</p>
            {@render variables(form.local_variables, "Local variable")}
        </section>

        <section class={section}>
            <h3 class={heading}>Automatic penalties</h3>
            <p class={lead}>Gives a row the penalty code when its condition is true.</p>
            {@render violations(form.auto_penalties, "Penalty", "P000")}
        </section>

        <section class={section}>
            <h3 class={heading}>Automatic DQs</h3>
            <p class={lead}>Disqualifies a row with the code when its condition is true.</p>
            {@render violations(form.auto_disqualifications, "DQ", "DQ000")}
        </section>

        <section class={section}>
            <h3 class={heading}>Grouping</h3>
            <p class={lead}>Ranks results within each group, or all together with none ticked.</p>
            <div class="flex flex-wrap gap-x-6 gap-y-2 *:mt-0">
                {#each GROUPS as [value, label] (value)}
                    <label class="v2-check">
                        <input type="checkbox" checked={form.group_by.includes(value)} onchange={() => toggleGroup(value)} />
                        {label}
                    </label>
                {/each}
            </div>
        </section>

        <section class={section}>
            <h3 class={heading}>Ranking</h3>
            <p class={lead}>An optional equation to rank by instead of points.</p>
            <input class="{code} w-full" placeholder="rank equation" aria-label="Rank equation" bind:value={form.rank_equation} />
            <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2 *:mt-0">
                <label class="v2-check"><input type="checkbox" bind:checked={form.rank_higher} /> Highest wins</label>
                <label class="v2-check"><input type="checkbox" bind:checked={form.allow_disqualified_to_rank} /> Rank disqualified rows</label>
            </div>
        </section>

        <div class="flex items-center justify-end gap-4 border-t border-v2-border pt-5">
            {#if form.hasErrors}<span class="mr-auto text-sm font-semibold text-v2-danger-text">Check the fields above.</span>{/if}
            {#if form.isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
            <button type="submit" class="v2-btn v2-btn-primary" disabled={form.processing}>{saveLabel}</button>
        </div>
    </form>
{/if}
