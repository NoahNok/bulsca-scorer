<!--
    A v2 switch (.v2-switch in a .v2-check label) that a form sends as `name`: 1 when on, 0 when off, so
    turning one off is saved too. A hint goes under the label.
-->
<script lang="ts">
    let {
        name,
        label,
        checked = $bindable(false),
        hint,
    }: { name: string; label: string; checked?: boolean; hint?: string } = $props();

    const id = $props.id();
    let input: HTMLInputElement;

    function toggle() {
        checked = !checked;
        // Tell the form it changed: a hidden input fires nothing itself
        queueMicrotask(() => input.dispatchEvent(new Event("input", { bubbles: true })));
    }
</script>

<!-- The label passes clicks on its text to the switch. A Switch under another sits further off than a checkbox -->
<label class="v2-check items-start gap-3 [&+&]:mt-4">
    <button
        type="button"
        class="v2-switch"
        role="switch"
        aria-checked={checked}
        aria-labelledby="{id}-label"
        aria-describedby={hint ? `${id}-hint` : undefined}
        onclick={toggle}
    ></button>
    <span>
        <span id="{id}-label" class="block leading-6 font-semibold">{label}</span>
        {#if hint}<span id="{id}-hint" class="v2-hint block">{hint}</span>{/if}
    </span>
    <input bind:this={input} type="hidden" {name} value={checked ? 1 : 0} />
</label>
