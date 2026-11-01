<!--
    Whether the live page is getting updates (a pulsing teal dot and "Live", amber while connecting, red
    while it retries) and the time, in Anton. In LiveLayout's header, and bigger on the full-screen display.
-->
<script lang="ts">
    import { clock, follow } from "@/manage/live/liveData.svelte";

    let { url, display = false }: { url: string; /** The full-screen display's size, for across a pool */ display?: boolean } = $props();

    const live = $derived(follow(url));
</script>

<div class="flex items-center gap-4">
    <p class={["flex items-center gap-2 font-semibold", display ? "text-xl" : "text-sm"]} role="status">
        <span
            class={["v2-dot", live.connected === null ? "bg-v2-warning" : live.connected ? "is-live" : "bg-v2-danger", display && "size-3.5!"]}
            aria-hidden="true"
        ></span>
        {live.connected === null ? "Connecting…" : live.connected ? "Live" : "Lost connection, retrying…"}
    </p>
    <time class={["font-anton tabular-nums", display ? "text-6xl" : "text-2xl"]} datetime={live.now.toISOString()}>{clock(live.now)}</time>
</div>
