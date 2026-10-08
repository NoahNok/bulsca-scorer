<script lang="ts">
    import type { Lane } from "@/types/base";
    import { maska } from "maska/svelte";

    let {
        lane,
        times = $bindable(),
        allowSingleDigit = false,
    }: {
        lane: number | Lane;
        times;
        allowSingleDigit?: boolean;
    } = $props();

    function validate(el: HTMLInputElement) {
        const v = el.value.toUpperCase();

        const isTime = /^\d{2}:\d{2}\.\d{2}$/.test(v);
        const isDigit = /^\d$/.test(v);
        const isDNF = v === "DNF";
        const isDNS = v === "DNS";
        const isOOT = v === "OOT";

        const ok =
            isTime || (isDigit && allowSingleDigit) || isDNF || isDNS || isOOT;

        el.setCustomValidity(ok ? "" : "Invalid result format");
    }
</script>

<div class="flex items-center gap-3 px-3 py-2">
    <span
        class="flex size-7 shrink-0 items-center justify-center rounded-md bg-gray-100 text-xs font-semibold text-gray-600"
    >
        {typeof lane === "number" ? lane : lane.lane}
    </span>

    {#if typeof lane === "number"}
        <span class="flex-1 text-sm text-gray-400 italic">Empty lane</span>
    {:else}
        <span class="min-w-0 flex-1 truncate text-sm font-medium text-gray-900">
            {lane.entity.name}
        </span>
        <input
            class="w-32 shrink-0 rounded-lg border border-gray-300 bg-white px-3 py-2 text-right font-mono text-sm text-gray-900 shadow-xs transition-all placeholder:text-gray-400 hover:border-gray-400 focus:border-se focus:ring-2 focus:ring-se/20 focus:outline-none user-invalid:border-red-400"
            type="text"
            aria-label="Result for lane {lane.lane}"
            placeholder={allowSingleDigit ? "0–3 / time" : "00:00.00"}
            bind:value={times[lane.entity.id]}
            name={`mark[${lane.entity.id}]`}
            use:maska={{
                mask: (input: string) => {
                    const upper = input.toUpperCase();

                    if (upper.startsWith("D")) return "DNF";
                    if (upper.startsWith("O")) return "OOT";

                    return "99:59.99";
                },
                tokens: {
                    "5": { pattern: /[0-5]/ },
                    "9": { pattern: /[0-9]/ },
                    // last letter of DNF/DNS, upper-cased so it can be typed in lower case
                    F: { pattern: /[SF]/i, transform: (c) => c.toUpperCase() },
                },
                eager: true,
            }}
            oninput={(e) => validate(e.target as HTMLInputElement)}
            required
        />
    {/if}
</div>
