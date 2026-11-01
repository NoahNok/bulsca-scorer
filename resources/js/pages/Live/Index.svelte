<!--
    The live page: the SERC order, with a search that outlines your team's tile and is remembered for
    next time. Inside LiveLayout.
-->
<script lang="ts">
    import SercOrder from "@/manage/live/SercOrder.svelte";
    import type { LiveHeader, SercOrder as Order } from "@/manage/types";

    let { competition, sercOrder }: { competition: LiveHeader; sercOrder: Order } = $props();

    const KEY = "live-team-search";
    let search = $state(localStorage.getItem(KEY) ?? "");

    $effect(() => {
        localStorage.setItem(KEY, search);
    });
</script>

<svelte:head><title>{competition.name} - Live - Scoring.Events</title></svelte:head>

<section>
    <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
        <h2 class="text-[1.75rem] font-extrabold uppercase">SERC order</h2>
        {#if sercOrder.tanks.length}
            <input type="search" class="v2-input w-full sm:w-72" placeholder="Find your team" aria-label="Find your team" bind:value={search} />
        {/if}
    </div>

    <SercOrder order={sercOrder} url={competition.dataUrl} {search} />
</section>
