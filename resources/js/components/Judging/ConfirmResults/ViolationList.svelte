<script lang="ts">
    import type { EntityViolations } from "@/types/results";
    import {
        statusBadgeClass,
        statusLabels,
        vtypeTileClass,
    } from "@/types/violation";
    import { cn } from "@/utils/utils";

    let {
        violations,
        class: className = "",
    }: {
        violations: EntityViolations;
        class?: string;
    } = $props();

    let rows = $derived([
        ...violations.dqs.map((v) => ({
            key: `dq-${v.label}`,
            label: v.label,
            message: v.message,
            pillClass: vtypeTileClass("DQ"),
            status: null,
        })),
        ...violations.penalties.map((v, i) => ({
            key: `p-${v.label}-${i}`,
            label: v.label,
            message: v.message,
            pillClass: vtypeTileClass("PEN"),
            status: null,
        })),
        ...violations.pending.map((v) => ({
            key: v.id,
            label: v.label,
            message: v.message,
            pillClass: cn("ring-1 ring-inset", statusBadgeClass(v.status)),
            status: statusLabels[v.status],
        })),
    ]);
</script>

{#if rows.length > 0}
    <ul class={cn("flex flex-col gap-1", className)}>
        {#each rows as row (row.key)}
            <li class="flex items-start gap-2 text-xs">
                <span
                    class="font-archivo shrink-0 rounded-full px-2 py-0.5 font-semibold {row.pillClass}"
                >
                    {row.label}
                </span>
                <span class="min-w-0 pt-0.5 text-gray-600">
                    {#if row.status}
                        <span class="font-semibold">{row.status}:</span>
                    {/if}
                    {row.message || "No description"}
                </span>
            </li>
        {/each}
    </ul>
{/if}
