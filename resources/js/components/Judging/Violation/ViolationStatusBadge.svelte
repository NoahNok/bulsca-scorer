<script lang="ts">
    import {
        statusBadgeClass,
        statusLabels,
        type ViolationStatus,
    } from "@/types/violation";
    import { cn } from "@/utils/utils";

    let {
        status,
        class: className = "",
    }: {
        status: ViolationStatus;
        class?: string;
    } = $props();
</script>

<span
    class={cn(
        "inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset whitespace-nowrap",
        statusBadgeClass(status),
        className,
    )}
>
    {#if status === "SUBMITTED"}
        <span class="size-1.5 rounded-full bg-se animate-pulse"></span>
    {/if}
    {statusLabels[status]}
</span>
