<script lang="ts">
    import type { Lane, Mark } from "@/types/base";
    import { useFormContext } from "@inertiajs/svelte";
    import { maska } from "maska/svelte";
    import { Exception } from "sass";

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

        const ok = isTime || (isDigit && allowSingleDigit) || isDNF || isDNS;

        el.setCustomValidity(ok ? "" : "Invalid result format");
    }
</script>

<tr class=" ">
    {#if typeof lane === "number"}
        <td class="p-2 sticky left-0 bg-white">
            {lane}
        </td>
        <td class="border-r p-2 pr-8 bg-white"></td>
    {:else}
        <td class="p-2 sticky left-0 bg-white">
            {lane.lane}
        </td>
        <td
            class="p-2 pr-6 border-r whitespace-nowrap hover:max-w-none bg-white max-w-[200px] overflow-hidden text-ellipsis"
        >
            {lane.entity.name}
        </td>
        <td>
            <input
                class="p-2 px-4"
                type="text"
                placeholder="Ropes In OR 00:00.00"
                bind:value={times[lane.entity.id]}
                name={`mark[${lane.entity.id}]`}
                use:maska={{
                    mask: (input: string) => {
                        const upper = input.toUpperCase();

                        if (upper.startsWith("D")) return "DNF";
                        if (upper.startsWith("O")) return "OOT";

                        return "99:59.99";
                    },
                    eager: true,
                }}
                data-maska-tokens="5:[0-5]|9:[0-9]|F:[S,F]"
                oninput={(e) => validate(e.target as HTMLInputElement)}
                required
            />
        </td>
    {/if}
</tr>
