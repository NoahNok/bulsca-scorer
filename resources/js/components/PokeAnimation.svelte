<script lang="ts">
    import { presence } from "@/lib/presence.svelte";
    import { LifeBuoy } from "@lucide/svelte";
    import { fade } from "svelte/transition";

    // Easter egg: someone tapped you in the Online sheet. A lifebuoy spins in,
    // bounces, then pops in a ring of bubbles.

    const DURATION = 1800;
    const BUBBLES = 12;

    let poke = $state<{ fromName: string; key: number } | null>(null);
    let timer: ReturnType<typeof setTimeout> | undefined;

    $effect(() => {
        const incoming = presence.incomingPoke;
        if (!incoming) return;

        poke = incoming;
        navigator.vibrate?.(200);
        clearTimeout(timer);
        timer = setTimeout(() => (poke = null), DURATION);
    });
</script>

{#if poke}
    {#key poke.key}
        <div
            class="poke pointer-events-none fixed inset-0 z-70 flex items-center justify-center"
            role="status"
            aria-live="polite"
            out:fade={{ duration: 200 }}
        >
            <div class="relative flex flex-col items-center">
                <div class="relative">
                    <LifeBuoy
                        size={112}
                        strokeWidth={1.5}
                        class="buoy rounded-full bg-white/90 text-se shadow-lg"
                    />
                    <!-- after the buoy so they're drawn over it -->
                    {#each { length: BUBBLES } as _, i}
                        <span
                            class="bubble absolute top-1/2 left-1/2 z-10 size-3 rounded-full bg-se"
                            style="--angle: {(360 / BUBBLES) * i}deg"
                        ></span>
                    {/each}
                </div>
                <p
                    class="caption font-archivo mt-6 rounded-full bg-white px-4 py-2 text-lg font-semibold text-gray-900 shadow-md"
                >
                    {poke.fromName} says hi! <span class="wave">👋</span>
                </p>
            </div>
        </div>
    {/key}
{/if}

<style>
    .poke :global(.buoy) {
        animation: buoy-in 1.8s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    /* hidden until the buoy pops (50% of 1.8s), then burst from its rim.
       "forwards", not "both", so they don't show during the delay */
    .bubble {
        opacity: 0;
        animation: bubble 0.65s ease-out 0.9s forwards;
    }

    .caption {
        animation: caption 1.8s ease both;
    }

    /* a little wave once the caption is in, rocking from the wrist */
    .wave {
        display: inline-block;
        transform-origin: 70% 70%;
        animation: wave 0.9s ease-in-out 0.65s both;
    }

    @keyframes wave {
        0%,
        100% {
            transform: rotate(0deg);
        }
        15% {
            transform: rotate(14deg);
        }
        30% {
            transform: rotate(-8deg);
        }
        45% {
            transform: rotate(14deg);
        }
        60% {
            transform: rotate(-4deg);
        }
        75% {
            transform: rotate(10deg);
        }
    }

    @keyframes buoy-in {
        0% {
            transform: translateX(-120vw) rotate(-720deg) scale(0.6);
        }
        30% {
            transform: translateX(0) rotate(0deg) scale(1.1);
        }
        38% {
            transform: scale(0.95);
        }
        46%,
        50% {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }
        /* pop: swell and fade in place as the bubbles burst out */
        61%,
        100% {
            transform: scale(1.35) rotate(0deg);
            opacity: 0;
        }
    }

    @keyframes bubble {
        0% {
            opacity: 1;
            transform: translate(-50%, -50%) rotate(var(--angle))
                translateY(-48px) scale(1);
        }
        100% {
            opacity: 0;
            transform: translate(-50%, -50%) rotate(var(--angle))
                translateY(-140px) scale(0.3);
        }
    }

    @keyframes caption {
        0%,
        25% {
            opacity: 0;
            transform: translateY(12px);
        }
        35%,
        85% {
            opacity: 1;
            transform: translateY(0);
        }
        100% {
            opacity: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .poke :global(.buoy),
        .bubble {
            display: none;
        }

        .caption,
        .wave {
            animation: none;
        }
    }
</style>
