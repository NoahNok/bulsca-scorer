<!--
    Previous and Next under a long list split into pages (App\Helpers\Pagination::props, e.g. Admin's
    Competitions and Users), with which page this is between them. Each is an Inertia visit, and the
    page's urls keep its search. Shows nothing when everything fits on one page.
-->
<script lang="ts">
    import type { Pagination } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";
    import { ArrowLeft, ArrowRight } from "@lucide/svelte";

    let { pagination, label }: { pagination: Pagination; /** Names the nav, e.g. "Competitions pages" */ label: string } = $props();
</script>

{#if pagination.last > 1}
    <nav class="mt-4 flex items-center justify-between gap-4" aria-label={label}>
        {#if pagination.prev}
            <Link href={pagination.prev} class="v2-btn v2-btn-ghost v2-btn-sm" rel="prev"><ArrowLeft size={16} aria-hidden="true" /> Previous</Link>
        {:else}
            <span></span>
        {/if}
        <p class="text-sm text-v2-muted tabular-nums">Page {pagination.current} of {pagination.last}</p>
        {#if pagination.next}
            <Link href={pagination.next} class="v2-btn v2-btn-ghost v2-btn-sm" rel="next">Next <ArrowRight size={16} aria-hidden="true" /></Link>
        {:else}
            <span></span>
        {/if}
    </nav>
{/if}
