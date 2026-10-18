<script module lang="ts">
    import { home as sercHome } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";
    import { LifeBuoy } from "@lucide/svelte";

    export const layout = (props: Record<string, any>) => ({
        title: "Add Judge",
        header: {
            icon: LifeBuoy,
            href: sercHome({ competition: props.competition, serc: props.serc }),
        },
    });
</script>

<script lang="ts">
    import { attachJudge } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import type { Competition, Judge, SERC } from "@/types/base";
    import { Link } from "@inertiajs/svelte";
    import { ClipboardList, Plus, Shuffle } from "@lucide/svelte";

    let {
        competition,
        serc,
        judges,
        swap,
    }: {
        competition: Competition;
        serc: SERC;
        judges: Judge[];
        swap: boolean;
    } = $props();

    const ActionIcon = $derived(swap ? Shuffle : Plus);
</script>

<AppHead
    title="{swap ? 'Swap' : 'Add'} Judge - {serc.name} - {competition.name}"
/>

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{serc.name}</h2>

    <BackLink
        href={sercHome({ competition, serc })}
        label="Back to {serc.name}"
        class="mt-2 mb-4"
    />

    <SectionLabel class="mb-1">
        {swap ? "Swap" : "Add"} casualty/objective
    </SectionLabel>
    <p class="mb-3 text-sm text-gray-500">
        {swap
            ? "Pick the casualty or objective to judge instead."
            : "Pick another casualty or objective to judge alongside yours."}
    </p>

    {#if judges.length === 0}
        <EmptyState
            icon={ClipboardList}
            title="Nothing else to add"
            description="You're already judging every casualty and objective."
        />
    {:else}
        <div class="flex flex-col gap-2">
            {#each judges as judge (judge.id)}
                <Link
                    href={attachJudge(
                        { competition, serc, judge },
                        { query: swap ? { swap: true } : undefined },
                    )}
                    class="group flex items-center gap-3 rounded-xl border bg-white p-3 shadow-sm transition-all hover:border-se hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                >
                    <span
                        class="flex size-8 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-600"
                    >
                        <ClipboardList size={16} />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="font-archivo truncate text-sm">{judge.name}</p>
                        <p class="text-xs text-gray-500">
                            {judge.no_marking_points} marking points
                        </p>
                    </div>
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-se/10 text-teal-700 transition-colors group-hover:bg-se group-hover:text-white"
                    >
                        <ActionIcon size={16} />
                    </span>
                </Link>
            {/each}
        </div>
    {/if}
</section>
