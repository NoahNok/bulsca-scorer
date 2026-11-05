<!--
    A Formula: its global variables (add, remove) and the result equation, with the equation's error. A
    Results sheet's Custom Formula and an organisation's Formula page both edit one. Bind `variables` and
    `equation` to the form; the form drops rows left half filled in when it saves.
-->
<script lang="ts">
    import type { FormulaVariable } from "@/manage/types";

    let {
        variables = $bindable(),
        equation = $bindable(),
        error,
        id = "formula",
        equationLead = "Gives each event result its points on a Results sheet. It can use the variables above.",
    }: {
        variables: FormulaVariable[];
        equation: string;
        /** The equation's error */
        error?: string;
        /** Prefixes the equation's error id */
        id?: string;
        /** What the result equation's for, under its heading */
        equationLead?: string;
    } = $props();

    const heading = "text-sm font-extrabold uppercase";
    const lead = "mt-1 mb-3 text-sm/[1.6] text-v2-muted";
    const code = "v2-input min-w-0 px-2 py-1.5 font-mono text-sm";

    function add() {
        variables.push({ name: "", expression: "", order: Math.max(0, ...variables.map((v) => v.order ?? 0)) + 1 });
    }
</script>

<div>
    <h3 class={heading}>Global variables</h3>
    <p class={lead}>Worked out once per event, for the result equation.</p>
    <ul class="flex flex-col gap-2">
        {#each variables as variable, i (i)}
            <li class="flex items-center gap-2">
                <input class="{code} w-32" placeholder="name" aria-label="Global variable {i + 1} name" bind:value={variable.name} />
                <span class="text-sm text-v2-muted">=</span>
                <input class="{code} flex-1" placeholder="MINIMUM(results, 'points')" aria-label="Global variable {i + 1} expression" bind:value={variable.expression} />
                <button type="button" class="v2-btn v2-btn-sm v2-btn-ghost" aria-label="Remove global variable {i + 1}" onclick={() => variables.splice(i, 1)}>Remove</button>
            </li>
        {/each}
    </ul>
    <button type="button" class="v2-btn v2-btn-sm mt-3" onclick={add}>Add global variable</button>
</div>
<div>
    <h3 class={heading}>Result equation</h3>
    <p class={lead}>{equationLead}</p>
    <input
        class={[code, "w-full", error && "is-invalid"]}
        placeholder="item.points"
        aria-label="Result equation"
        aria-invalid={error ? "true" : undefined}
        aria-describedby={error ? `${id}-equation-error` : undefined}
        bind:value={equation}
    />
    {#if error}<span id="{id}-equation-error" class="v2-error">{error}</span>{/if}
</div>
