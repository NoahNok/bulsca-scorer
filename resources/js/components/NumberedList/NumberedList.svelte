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

<ol class={cn("divide-y overflow-hidden rounded-xl border bg-white", className)}>
    {@render children()}
</ol>
