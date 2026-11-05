<!--
    The Organisers' status message (Settings → Status) as an info alert on the live pages and the full-screen
    display. It starts from the page's `competition.statusMessage`, then follows the 5s poll, so it fades in
    when an Organiser sets it and fades out when they clear it, easing its height so what's below glides.
    A page that loads with one already set shows it without animating.
-->
<script lang="ts">
    import { follow } from "@/manage/live/liveData.svelte";
    import { prefersReducedMotion } from "svelte/motion";
    import { fade, slide } from "svelte/transition";

    let {
        url,
        initial,
        display = false,
        class: className = "",
    }: {
        url: string;
        /** The message the page was drawn with (LiveHeader.statusMessage) */
        initial: string | null;
        /** The full-screen display's size, for across a pool */
        display?: boolean;
        /** Spacing on the wrapper, which collapses with it */
        class?: string;
    } = $props();

    const live = $derived(follow(url));

    // svelte-ignore state_referenced_locally
    let message = $state(initial);

    // A failed poll keeps the last message up
    $effect(() => {
        if (live.statusMessage !== undefined) message = live.statusMessage;
    });

    function fadeSlide(node: Element) {
        const duration = prefersReducedMotion.current ? 0 : 250;
        const height = slide(node, { duration });
        const opacity = fade(node, { duration });
        return { duration, css: (t: number, u: number) => height.css!(t, u) + opacity.css!(t, u) };
    }
</script>

<div class="shrink-0" role="status">
    {#if message}
        <div class={className} transition:fadeSlide>
            <div class={["v2-alert is-info", display && "text-2xl [&>svg]:size-8"]}>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 11v5M12 8h.01" />
                </svg>
                <div class="[&_a]:text-v2-teal-text [&_a]:underline">{@html message}</div>
            </div>
        </div>
    {/if}
</div>
