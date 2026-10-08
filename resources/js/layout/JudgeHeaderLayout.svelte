<script module lang="ts">
    import type { LinkComponentBaseProps } from "@inertiajs/core";
    import type { Component } from "svelte";

    /**
     * Per-page header config, set from a page's layout export, e.g.
     *
     *     export const layout = {
     *         title: "...",
     *         header: { icon: LifeBuoy, href: null },
     *     };
     *
     * Use the callback form when it depends on page props:
     *
     *     export const layout = (props) => ({
     *         header: { href: sercHome(props) },
     *     });
     */
    export type JudgeHeaderConfig = {
        // defaults to House
        icon?: Component<any>;
        // defaults to the competition home when there is a competition,
        // null to render the header without a link
        href?: LinkComponentBaseProps["href"] | null;
        // don't reserve space under the header, for pages that centre
        // themselves in the viewport (e.g. login)
        overlay?: boolean;
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import type { Competition } from "@/types/base";
    import { Link } from "@inertiajs/svelte";
    import { House } from "@lucide/svelte";
    import type { Snippet } from "svelte";

    let {
        competition,
        header = {},
        children,
    }: {
        competition?: Competition;
        header?: JudgeHeaderConfig;
        children?: Snippet;
    } = $props();

    let Icon = $derived(header.icon ?? House);
    let href = $derived(
        header.href === undefined
            ? competition
                ? home(competition)
                : null
            : header.href,
    );
</script>

{#snippet content()}
    <div class="">
        <h1 class="  -mb-3 normal-case! text-black! text-base!">Digital</h1>
        <h1 class=" indent-6 normal-case! text-se text-xl!">Judge</h1>
    </div>

    <Icon class="bg-se/20 rounded-full text-se p-2 shadow-md " size={40} />
{/snippet}

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    {#if href}
        <Link {href} class="flex w-full justify-between items-center">
            {@render content()}
        </Link>
    {:else}
        <div class="flex w-full justify-between items-center">
            {@render content()}
        </div>
    {/if}
</section>

{#if !header.overlay}
    <div class="h-16"></div>
{/if}

{@render children?.()}
