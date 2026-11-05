<!--
    The heading of a page under another (an event's, or one of its tools): a link back to the page above,
    the page's name with any badges beside it, and a lead under it. Actions for the whole page, like a
    Save, sit at its right end.
-->
<script lang="ts">
    import { Link } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let {
        back,
        title,
        lead,
        badges,
        actions,
    }: {
        /** The page above, linked as "← label" */
        back: { href: string; label: string };
        title: string;
        lead?: string | Snippet;
        badges?: Snippet;
        actions?: Snippet;
    } = $props();
</script>

<div class="mb-6 flex flex-wrap items-end gap-4">
    <div class="min-w-0 flex-1">
        <Link href={back.href} class="v2-link-muted text-sm wrap-anywhere">← {back.label}</Link>
        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-2">
            <h2 class="text-2xl font-extrabold uppercase wrap-anywhere">{title}</h2>
            {@render badges?.()}
        </div>
        {#if lead}
            <p class="mt-[0.35rem] text-[0.9375rem]/[1.6] text-v2-muted">
                {#if typeof lead === "string"}{lead}{:else}{@render lead()}{/if}
            </p>
        {/if}
    </div>
    {@render actions?.()}
</div>
