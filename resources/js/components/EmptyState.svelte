<script lang="ts">
    import { cn } from "@/utils/utils";
    import type { Snippet } from "svelte";
    import { fade } from "svelte/transition";

    let {
        icon: Icon = null,
        title,
        description,
        children,
        class: className = "",
    }: {
        icon?: any;
        title: string;
        description?: string;
        children?: Snippet;
        class?: string;
    } = $props();
</script>

<div
    class={cn(
        "flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed py-12 text-center text-gray-500",
        className,
    )}
    in:fade
>
    {#if Icon}
        <Icon size={32} class="text-gray-300" />
    {/if}
    <p class="font-medium">{title}</p>
    {#if description}
        <p class="text-sm">{description}</p>
    {/if}
    {#if children}
        {@render children()}
    {/if}
</div>
