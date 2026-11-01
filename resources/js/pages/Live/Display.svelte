<!--
    The full-screen SERC order for a poolside TV or projector: no header or footer, and it never scrolls.
    A QR code takes phones to the live page, and the Organisers' status message shows under the header. No
    layout (manage.ts).
-->
<script lang="ts">
    import LiveStatus from "@/manage/live/LiveStatus.svelte";
    import LiveStatusMessage from "@/manage/live/LiveStatusMessage.svelte";
    import SercOrder from "@/manage/live/SercOrder.svelte";
    import type { LiveHeader, SercOrder as Order } from "@/manage/types";

    let { competition, sercOrder, qr }: { competition: LiveHeader; sercOrder: Order; /** An SVG of the live page's url */ qr: string } = $props();
</script>

<svelte:head><title>SERC order - {competition.name} - Scoring.Events</title></svelte:head>

<!-- Spaced by padding rather than gap, so the space for the status message goes with it -->
<div class="flex h-dvh flex-col overflow-hidden p-8">
    <header class="flex shrink-0 items-center justify-between gap-6 pb-6">
        <h1 class="min-w-0 truncate text-5xl font-extrabold uppercase">{competition.name}</h1>

        <div class="flex shrink-0 items-center gap-6">
            <LiveStatus url={competition.dataUrl} display />
            <div class="flex items-center gap-3">
                <!-- Always on white, so phones can read it in dark mode -->
                <div class="rounded-md bg-[#fff] p-1.5" aria-hidden="true">{@html qr}</div>
                <p class="w-32 text-base font-semibold text-v2-muted">Scan to see when your team starts</p>
            </div>
        </div>
    </header>

    <LiveStatusMessage url={competition.dataUrl} initial={competition.statusMessage} display class="pb-6" />

    <SercOrder order={sercOrder} url={competition.dataUrl} display />
</div>
