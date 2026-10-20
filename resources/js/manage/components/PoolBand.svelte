<!--
    The signed-out pages' brand band (AuthLayout): a pool seen from above on the favicon's tile, with the
    lane ropes drawing in across it and a big line over the top. Dark in both themes. Beside the form on
    desktop, a short strip above it on phones.
-->
<script lang="ts">
    import BrandMark from "@/manage/components/BrandMark.svelte";
    import { page } from "@inertiajs/svelte";
    import { prefersReducedMotion } from "svelte/motion";
    import { fade } from "svelte/transition";

    let { headline }: { headline: string } = $props();

    const lanes = [1, 2, 3, 4, 5, 6, 7, 8];
</script>

<!-- A div, not an aside: app.css collapses every aside for the old sidebar -->
<div class="p-band">
    <div class="p-pool" aria-hidden="true">
        {#each lanes as lane (lane)}
            <div class="p-lane">
                <span class="p-number">{lane}</span>
                <span class="p-floor"></span>
                {#if lane < lanes.length}
                    <span class="p-rope" style="--i: {lane}"></span>
                {/if}
            </div>
        {/each}
    </div>

    <a href={page.props.manage?.site.home} class="p-brand">
        <BrandMark size={36} live />
        Scoring.Events
    </a>

    <!-- Old and new lines share one grid cell, so they crossfade in place -->
    <div class="p-headline">
        {#key headline}
            <p transition:fade={{ duration: prefersReducedMotion.current ? 0 : 250 }}>{headline}</p>
        {/key}
    </div>
</div>

<style>
    .p-band {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.5rem;
        height: 11rem;
        padding: 1rem;
        overflow: hidden;
        background: var(--v2-tile);
        color: #fff;
    }

    /* Lanes run across the band; a phone's strip shows two or three of them */
    .p-pool {
        position: absolute;
        inset: 0;
        display: grid;
        grid-auto-rows: minmax(4.5rem, 1fr);
    }

    .p-lane {
        position: relative;
    }

    /* at the far wall, clear of the brand and the line */
    .p-number {
        position: absolute;
        top: 50%;
        right: 1rem;
        transform: translateY(-50%);
        font-family: "Anton", sans-serif;
        font-size: clamp(1.5rem, 3vw, 2.5rem);
        line-height: 1;
        color: rgb(255 255 255 / 0.12);
    }

    /* The black line on the pool floor, ending in a T before each wall */
    .p-floor {
        position: absolute;
        top: 50%;
        left: 8%;
        right: 12%;
        height: 0.5rem;
        transform: translateY(-50%);
        background: rgb(255 255 255 / 0.04);
    }

    .p-floor::before,
    .p-floor::after {
        content: "";
        position: absolute;
        top: 50%;
        width: 0.5rem;
        height: 2.25rem;
        transform: translateY(-50%);
        background: inherit;
    }

    .p-floor::before {
        left: 0;
    }

    .p-floor::after {
        right: 0;
    }

    /* Lane ropes: floats, teal for the 5m by each wall */
    .p-rope {
        position: absolute;
        left: 0;
        right: 0;
        bottom: -0.1875rem;
        height: 0.375rem;
        background: linear-gradient(90deg, var(--v2-teal) 0 10%, rgb(255 255 255 / 0.22) 10% 90%, var(--v2-teal) 90%);
        mask: repeating-linear-gradient(90deg, #000 0 0.875rem, transparent 0.875rem 1.25rem);
        animation: p-rope-in 600ms cubic-bezier(0.2, 0.7, 0.2, 1) both;
        animation-delay: calc(var(--i) * 70ms);
    }

    @keyframes p-rope-in {
        from {
            clip-path: inset(0 100% 0 0);
        }

        to {
            clip-path: inset(0);
        }
    }

    .p-brand {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        align-self: flex-start;
        font-weight: 800;
        font-size: 1.125rem;
    }

    .p-headline {
        position: relative;
        display: grid;
        /* fades the ropes out behind the line so it stays readable */
        margin: -1rem;
        padding: 1rem;
        background: linear-gradient(to top, var(--v2-tile) 40%, transparent);
    }

    .p-headline p {
        grid-area: 1 / 1;
        max-width: 14ch;
        font-weight: 800;
        font-size: 1.5rem;
        line-height: 1.05;
        text-transform: uppercase;
        text-wrap: balance;
    }

    @media (min-width: 64rem) {
        .p-band {
            position: sticky;
            top: 0;
            height: 100dvh;
            padding: 2.5rem;
        }

        .p-pool {
            grid-auto-rows: 1fr;
        }

        .p-headline {
            margin: -2.5rem;
            padding: 6rem 2.5rem 2.5rem;
        }

        .p-headline p {
            font-size: clamp(2rem, 4.5vw, 3.25rem);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .p-rope {
            animation: none;
        }
    }
</style>
