<!--
    The Management app's two-column page (Overview, Entries & Leagues, an event's page): the page's main
    content in the first column and a sidebar in the second, split 2:1 from 56rem, with nothing above
    them, so the sidebar starts at the top beside the page's heading. On phones the sidebar follows the
    content. A sidebar of actions for the content beside it is `sticky`, so it stays in view.
-->
<script lang="ts" module>
    /** A row in a sidebar list of actions: the whole row is the link or button, the current one on --v2-sunk */
    export const sidebarRow =
        "flex w-full cursor-pointer items-center gap-3 px-2 py-[0.8rem] text-left font-semibold transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover aria-[current=true]:bg-v2-sunk motion-reduce:transition-none";
</script>

<script lang="ts">
    import type { Snippet } from "svelte";

    let {
        children,
        sidebar,
        sticky = false,
    }: {
        /** The first column */
        children: Snippet;
        /** The second column; the page decides its element (a section, or a nav of actions) */
        sidebar?: Snippet;
        /** Keep the sidebar in view as the content scrolls, from 56rem */
        sticky?: boolean;
    } = $props();
</script>

<div class="grid gap-10 min-[56rem]:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] min-[56rem]:items-start">
    <div class="min-w-0">
        {@render children()}
    </div>
    {#if sidebar}
        <div class={["min-w-0", sticky && "min-[56rem]:sticky min-[56rem]:top-6"]}>
            {@render sidebar()}
        </div>
    {/if}
</div>
