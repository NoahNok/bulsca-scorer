<!--
    The icon at the end of a sidebar row (sidebarRow in SidebarPage): a chevron for a page in the app, or
    an arrow out of a box for one that opens in a new tab, like a print.
-->
<script lang="ts">
    let { icon = "chevron" }: { icon?: "chevron" | "external" } = $props();
</script>

<svg class="ml-auto size-4 shrink-0 text-v2-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
    {#if icon === "external"}
        <path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" />
    {:else}
        <path d="m9 6 6 6-6 6" />
    {/if}
</svg>
