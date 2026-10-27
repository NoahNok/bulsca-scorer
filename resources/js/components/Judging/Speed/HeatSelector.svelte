<script
    lang="ts"
    generics="THref extends (args: any, options?: any) => { url: string; method: 'get' }"
>
    import NumberedList from "@/components/NumberedList/NumberedList.svelte";
    import NumberedListItem from "@/components/NumberedList/NumberedListItem.svelte";
    import SegmentedControl from "@/components/SegmentedControl.svelte";
    import { heatKey, heatLabel, heatQuery } from "@/lib/heats";
    import { toastInfo } from "@/lib/toast.svelte";

    import type { Heat, Pool } from "@/types/base";

    import { page } from "@inertiajs/svelte";
    import { Check } from "@lucide/svelte";

    type HeatRouteParams = Extract<
        Parameters<THref>[0],
        { heat: string | number }
    >;

    let {
        heats,
        href,
        params,
    }: {
        heats: Heat[];
        href: THref;
        params: Omit<HeatRouteParams, "heat">;
    } = $props();

    // Remembered per device: a judge usually stays at one pool all day
    const POOL_STORAGE_KEY = "dj:heat-selector-pool";

    const pools = $derived(
        heats
            .map((heat) => heat.pool)
            .filter((pool): pool is Pool => !!pool)
            .filter((pool, i, all) => all.findIndex((p) => p.id === pool.id) === i),
    );

    function initialPool(): string {
        let stored: string | null = null;
        try {
            stored = localStorage.getItem(POOL_STORAGE_KEY);
        } catch {}
        if (stored && pools.some((pool) => String(pool.id) === stored)) {
            return stored;
        }
        // Otherwise the first pool that still has heats to judge
        const next = heats.find((heat) => heat.pool && !heat.complete)?.pool ?? pools[0];
        return next ? String(next.id) : "";
    }

    let selectedPool = $state(initialPool());

    $effect(() => {
        if (!selectedPool) return;
        try {
            localStorage.setItem(POOL_STORAGE_KEY, selectedPool);
        } catch {}
    });

    // With more than one pool, show one pool at a time behind a toggle
    const showPoolToggle = $derived(pools.length > 1);
    const visibleHeats = $derived(
        showPoolToggle
            ? heats.filter((heat) => String(heat.pool?.id) === selectedPool)
            : heats,
    );

    function canHeatBeSelected(heat: Heat): boolean {
        return !heat.complete || page.props.judge.isHeadRef;
    }
</script>

{#if showPoolToggle}
    <SegmentedControl
        options={pools.map((pool) => ({ value: String(pool.id), label: pool.name }))}
        bind:value={selectedPool}
        class="mb-3"
    />
{/if}

<NumberedList>
    {#each visibleHeats as heat (heatKey(heat))}
        <NumberedListItem
            number={heat.heat}
            href={canHeatBeSelected(heat)
                ? href({ ...params, heat: heat.heat }, heatQuery(heat))
                : undefined}
            onclick={() => toastInfo("This heat is already complete")}
            class={canHeatBeSelected(heat) ? "" : "opacity-60"}
        >
            <!-- The toggle already names the pool -->
            <span class="font-medium text-gray-900"
                >{showPoolToggle ? `Heat ${heat.heat}` : heatLabel(heat)}</span
            >
            {#snippet trailing()}
                {#if heat.complete}
                    <span
                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700 ring-1 ring-green-300 ring-inset"
                    >
                        <Check size={12} strokeWidth={3} /> Complete
                    </span>
                {/if}
            {/snippet}
        </NumberedListItem>
    {/each}
</NumberedList>
