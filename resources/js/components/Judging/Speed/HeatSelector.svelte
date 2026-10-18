<script
    lang="ts"
    generics="THref extends (...args: any[]) => { url: string; method: 'get' }"
>
    import NumberedList from "@/components/NumberedList/NumberedList.svelte";
    import NumberedListItem from "@/components/NumberedList/NumberedListItem.svelte";
    import { toastInfo } from "@/lib/toast.svelte";

    import type { Heat } from "@/types/base";

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

    function canHeatBeSelected(heat: Heat): boolean {
        return !heat.complete || page.props.judge.isHeadRef;
    }
</script>

<NumberedList>
    {#each heats as heat (heat.heat)}
        <NumberedListItem
            number={heat.heat}
            href={canHeatBeSelected(heat)
                ? href({ ...params, heat: heat.heat })
                : undefined}
            onclick={() => toastInfo("This heat is already complete")}
            class={canHeatBeSelected(heat) ? "" : "opacity-60"}
        >
            <span class="font-medium text-gray-900">Heat {heat.heat}</span>
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
