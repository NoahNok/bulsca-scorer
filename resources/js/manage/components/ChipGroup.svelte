<!--
    A row of .v2-chip buttons that picks one option: a League, Pool, event, Heat or Objective. The picked
    one is pressed. `all` adds a first chip, like "Overall", that picks none of them (null).
-->
<script lang="ts" generics="T extends string | number">
    import type { ClassValue } from "svelte/elements";

    let {
        label,
        options,
        value,
        onpick,
        all,
        class: className,
    }: {
        /** What the chips pick, for screen readers */
        label: string;
        options: { id: T; name: string }[];
        value: T | null;
        onpick: (id: T | null) => void;
        all?: string;
        class?: ClassValue;
    } = $props();
</script>

<div class={["flex flex-wrap gap-2", className]} role="group" aria-label={label}>
    {#if all}
        <button type="button" class="v2-chip" aria-pressed={value === null} onclick={() => onpick(null)}>{all}</button>
    {/if}
    {#each options as option (option.id)}
        <button type="button" class="v2-chip" aria-pressed={value === option.id} onclick={() => onpick(option.id)}>{option.name}</button>
    {/each}
</div>
