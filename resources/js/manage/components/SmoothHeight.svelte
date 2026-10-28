<!--
    Eases its height to fit what's inside as that changes, as Tabs does for its panels, so what's below
    glides rather than jumps: the heats table when another Pool, with a different lane count, is picked.
    Off under reduced motion.
-->
<script lang="ts">
    import type { Snippet } from "svelte";

    let { children }: { children: Snippet } = $props();

    let height = $state(0);
</script>

<!-- Padding out and back in, so overflow doesn't clip focus rings -->
<div
    class="-mx-1 -mb-1 overflow-hidden transition-[height] duration-250 ease-[cubic-bezier(0.3,0.7,0.4,1)] motion-reduce:transition-none"
    style:height={height ? `${height}px` : undefined}
>
    <div class="px-1 pb-1" bind:offsetHeight={height}>
        {@render children()}
    </div>
</div>
