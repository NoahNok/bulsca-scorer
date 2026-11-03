<!--
    The Activity filter bar: "Add …" pickers, the History shortcut and the active filters as removable
    chips grouped by kind ("and" between kinds, "or" within one), with Clear. It doesn't visit anything
    itself: `onchange` gets the filters to show, so the page decides how they reach the url.
-->
<script lang="ts">
    import type {
        ActivityChipGroup,
        ActivityFilters,
        ActivityParam,
        ActivityPicker,
    } from "@/manage/types";
    import { History, X } from "@lucide/svelte";

    let {
        filters,
        chipGroups,
        pickers,
        history,
        onchange,
    }: {
        filters: ActivityFilters;
        chipGroups: ActivityChipGroup[];
        pickers: ActivityPicker[];
        /** The History shortcut: the Mark, Time and Place types */
        history: { value: string[]; active: boolean };
        onchange: (next: ActivityFilters) => void;
    } = $props();

    function add(param: ActivityParam, value: string) {
        if (value && !filters[param].includes(value))
            onchange({ ...filters, [param]: [...filters[param], value] });
    }

    function remove(param: ActivityParam, value: string) {
        onchange({
            ...filters,
            [param]: filters[param].filter((v) => v !== value),
        });
    }

    const muted = "text-xs font-semibold text-v2-muted";
</script>

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
                                <option value={option.value}
                                    >{option.label}</option
                                >
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
            onclick={() =>
                onchange({
                    ...filters,
                    activity: history.active ? [] : history.value,
                })}
        >
            <History size={14} aria-hidden="true" />Event History
        </button>
        {#each chipGroups as group, g (group.label)}
            {#if g > 0}<span class={muted}>and</span>{/if}
            <span
                class="flex flex-wrap items-center gap-1.5"
                role="group"
                aria-label={group.label}
            >
                <span class={muted}>{group.label}</span>
                {#each group.chips as chip, c (chip.value)}
                    {#if c > 0}<span class={muted}>or</span>{/if}
                    <span
                        class="inline-flex items-center gap-1 rounded-md bg-v2-sunk py-0.5 pr-1 pl-2 text-sm font-semibold"
                    >
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
        {#if chipGroups.length > 0}
            <button
                type="button"
                class="v2-link-muted cursor-pointer text-sm"
                onclick={() => onchange({ related: [], by: [], activity: [] })}
            >
                Clear
            </button>
        {/if}
    </div>
</section>
