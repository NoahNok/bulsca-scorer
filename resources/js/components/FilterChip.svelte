<script lang="ts">
    import { cn } from "@/utils/utils";
    import type { HTMLButtonAttributes } from "svelte/elements";

    let {
        label,
        count,
        active = false,
        class: className = "",
        ...restProps
    }: {
        label: string;
        count?: number;
        active?: boolean;
        class?: string;
    } & HTMLButtonAttributes = $props();
</script>

<button
    type="button"
    aria-pressed={active}
    {...restProps}
    class={cn(
        "inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-full border px-3 py-1 text-sm font-medium transition-colors",
        active
            ? "border-black bg-black text-white"
            : "bg-white text-gray-700 hover:border-gray-400",
        className,
    )}
>
    {label}
    {#if count !== undefined}
        <span
            class="rounded-full px-1.5 text-xs {active
                ? 'bg-white/20'
                : 'bg-gray-100 text-gray-500'}">{count}</span
        >
    {/if}
</button>
