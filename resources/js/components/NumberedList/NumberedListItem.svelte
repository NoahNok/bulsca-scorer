<script lang="ts">
    import { cn } from "@/utils/utils";
    import type { LinkComponentBaseProps } from "@inertiajs/core";
    import { Link } from "@inertiajs/svelte";
    import { ChevronRight } from "@lucide/svelte";
    import type { Snippet } from "svelte";

    let {
        number,
        href,
        children,
        trailing,
        onclick,
        class: className = "",
    }: {
        number: string | number;
        // makes the whole row a link, with a chevron on the right
        href?: LinkComponentBaseProps["href"];
        children: Snippet;
        // extra content before the chevron, e.g. a hint or badge
        trailing?: Snippet;
        // without href: makes the whole row a button
        onclick?: () => void;
        class?: string;
    } = $props();

    const rowClass = "flex items-start gap-3 px-3 py-2.5";
</script>

{#snippet content()}
    <span
        class="flex size-7 shrink-0 items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-600"
    >
        {number}
    </span>
    <span class="min-w-0 flex-1 text-sm leading-7 text-gray-700">
        {@render children()}
    </span>
    {#if trailing}
        <span class="flex h-7 shrink-0 items-center">
            {@render trailing()}
        </span>
    {/if}
{/snippet}

<li>
    {#if href}
        <Link
            {href}
            class={cn(
                rowClass,
                "group w-full transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset",
                className,
            )}
        >
            {@render content()}
            <span class="flex h-7 shrink-0 items-center">
                <ChevronRight
                    size={18}
                    class="text-gray-300 transition-all group-hover:translate-x-0.5 group-hover:text-se"
                />
            </span>
        </Link>
    {:else if onclick}
        <button
            type="button"
            {onclick}
            class={cn(
                rowClass,
                "w-full cursor-pointer text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset",
                className,
            )}
        >
            {@render content()}
        </button>
    {:else}
        <div class={cn(rowClass, className)}>
            {@render content()}
        </div>
    {/if}
</li>
