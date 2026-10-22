<!--
    A form's submit button (.v2-btn). While `processing` it shows a spinner and is disabled, so the form
    can't be sent twice; it still reads as busy, not unavailable.
-->
<script lang="ts">
    import type { Snippet } from "svelte";

    let {
        processing = false,
        variant = "primary",
        children,
    }: { processing?: boolean; variant?: "primary" | "accent"; children: Snippet } = $props();
</script>

<button type="submit" class="v2-btn v2-btn-{variant} h-11 w-full justify-center disabled:cursor-progress disabled:opacity-80"
    disabled={processing} aria-busy={processing}>
    {#if processing}
        <span
            class="size-4 animate-spinner rounded-full border-2 border-current border-r-transparent motion-reduce:animate-none"
            aria-hidden="true"
        ></span>
    {/if}
    {@render children()}
</button>
