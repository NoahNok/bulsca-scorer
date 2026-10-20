<!--
    A v2 alert (.v2-alert) with its icon, sliding in when it appears. Danger alerts are announced at once.
-->
<script lang="ts">
    import type { Snippet } from "svelte";
    import { prefersReducedMotion } from "svelte/motion";
    import { slide } from "svelte/transition";

    let { tone, title, children }: { tone: "info" | "success" | "danger"; title?: string; children?: Snippet } = $props();
</script>

<div
    class="v2-alert is-{tone}"
    role={tone === "danger" ? "alert" : "status"}
    in:slide={{ duration: prefersReducedMotion.current ? 0 : 200 }}
>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        {#if tone === "danger"}
            <path d="M12 9v4M12 17h.01" />
            <path d="M10.3 3.9 2.4 18a2 2 0 0 0 1.7 3h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
        {:else if tone === "success"}
            <path d="m5 12 5 5 9-10" />
        {:else}
            <circle cx="12" cy="12" r="9" />
            <path d="M12 11v5M12 8h.01" />
        {/if}
    </svg>
    <div>
        {#if title}<strong>{title}</strong>{/if}{@render children?.()}
    </div>
</div>
