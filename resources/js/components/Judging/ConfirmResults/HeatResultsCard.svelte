<script lang="ts">
    import SectionLabel from "@/components/SectionLabel.svelte";
    import type { ConfirmHeat } from "@/types/results";
    import ViolationList from "./ViolationList.svelte";

    let {
        heat,
        isRopeThrow = false,
    }: {
        heat: ConfirmHeat;
        isRopeThrow?: boolean;
    } = $props();
</script>

<div>
    <div class="mb-2 flex items-end justify-between">
        <SectionLabel>Heat {heat.heat}</SectionLabel>
        <span class="text-xs text-gray-400">
            OOF · {isRopeThrow ? "Result" : "Time"}
        </span>
    </div>

    <ol class="divide-y overflow-hidden rounded-xl border bg-white shadow-sm">
        {#each heat.lanes as lane (lane.lane)}
            <li class="flex flex-col gap-2 px-3 py-2 {lane.entity ? '' : 'opacity-60'}">
                <div class="flex items-center gap-3">
                    <span
                        class="flex size-7 shrink-0 items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-600"
                        title="Lane {lane.lane}"
                    >
                        {lane.lane}
                    </span>

                    {#if lane.entity === null}
                        <span class="flex-1 text-sm text-gray-400 italic">Empty lane</span>
                    {:else}
                        <span class="min-w-0 flex-1 truncate text-sm font-medium text-gray-900">
                            {lane.entity.name}
                        </span>

                        <span
                            class="flex size-7 shrink-0 items-center justify-center rounded-md text-xs font-semibold {lane.oof
                                ? 'bg-se/20 text-teal-700'
                                : 'bg-amber-100 text-amber-700'}"
                            title={lane.oof ? `Finished ${lane.oof}` : "No order of finish"}
                        >
                            {lane.oof ?? "–"}
                        </span>

                        <span
                            class="w-20 shrink-0 text-right font-mono text-sm font-semibold {lane.resultIsDq
                                ? 'text-red-700'
                                : lane.result === null
                                  ? 'text-amber-700'
                                  : 'text-gray-900'}"
                            title={lane.result === null ? "No result entered" : undefined}
                        >
                            {lane.result ?? "–"}
                        </span>
                    {/if}
                </div>

                {#if lane.entity !== null}
                    <ViolationList violations={lane} class="pl-10" />
                {/if}
            </li>
        {/each}
    </ol>
</div>
