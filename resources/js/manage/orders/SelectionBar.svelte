<!--
    The heat and draw editors' bar of what's selected and what to do next, kept in view while the
    Heats or Draw scroll. Buttons for the selection, like Unassign, go before Clear selection.
-->
<script lang="ts">
    import type { Snippet } from "svelte";

    let { hint, selected, onclear, children }: { hint: string; selected: boolean; onclear: () => void; children?: Snippet } = $props();
</script>

<div class="sticky top-0 z-20 -mx-1 mb-3 flex flex-wrap items-center gap-3 bg-v2-bg px-1 py-2" aria-live="polite">
    <p class="min-w-0 flex-1 text-[0.9375rem] text-v2-muted">{hint}</p>
    {@render children?.()}
    {#if selected}
        <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm" onclick={onclear}>Clear selection</button>
    {/if}
</div>
