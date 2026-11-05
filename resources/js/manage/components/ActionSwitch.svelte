<!--
    A v2 switch that acts as soon as it's flipped, for a sidebar of actions: "Shown in results", "Show
    heats publicly". The page saves it in `onchange`; Switch is the one a form sends with the rest. One
    under another sits further off.
-->
<script lang="ts">
    import type { ClassValue } from "svelte/elements";

    let {
        label,
        checked,
        onchange,
        class: className,
    }: { label: string; checked: boolean; onchange: (checked: boolean) => void; class?: ClassValue } = $props();

    const id = $props.id();
</script>

<label class={["v2-check [&+&]:mt-4", className]}>
    <button type="button" class="v2-switch" role="switch" aria-checked={checked} aria-labelledby={id} onclick={() => onchange(!checked)}></button>
    <span {id} class="font-semibold">{label}</span>
</label>
