<script lang="ts">
    import type { LinkComponentBaseProps } from "@inertiajs/core";
    import { Link } from "@inertiajs/svelte";

    import type { Snippet } from "svelte";

    let {
        condition,
        href,
        children,
    }: {
        condition: boolean;
        href: LinkComponentBaseProps["href"];
        children: Snippet;
    } = $props();
</script>

{#if condition}
    <Link {href}>
        {@render children()}
    </Link>
{:else}
    {@render children()}
{/if}
