<script lang="ts">
    import { cn } from "@/utils/utils";
    import type { Snippet } from "svelte";

    let {
        children,
        class: className = "",
    }: {
        children: Snippet;
        class?: string;
    } = $props();
</script>

<p
    class={cn(
        "text-xs font-semibold uppercase tracking-wide text-gray-500",
        className,
    )}
>
    {@render children()}
</p>
