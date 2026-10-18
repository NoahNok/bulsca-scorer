<script lang="ts">
    import {
        statusLabels,
        timelineDotClass,
        type ViolationTimelineEntry,
    } from "@/types/violation";
    import { cn } from "@/utils/utils";

    let {
        entries,
        class: className = "",
    }: {
        entries: ViolationTimelineEntry[];
        class?: string;
    } = $props();

    // e.g. "Sat 12 Oct, 14:32"
    const formatter = new Intl.DateTimeFormat(undefined, {
        weekday: "short",
        day: "numeric",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
    });

    function label(entry: ViolationTimelineEntry) {
        if (entry.state === "SUBMITTED") {
            return entry.from === "REJECTED" ? "Resubmitted" : "Submitted";
        }

        return statusLabels[entry.state];
    }
</script>

<ol class={cn("rounded-xl border bg-white p-4", className)}>
    {#each entries as entry, i (entry.id)}
        {@const last = i === entries.length - 1}
        <li class="relative flex gap-3 {last ? '' : 'pb-4'}">
            {#if !last}
                <span
                    class="absolute top-4 bottom-0 left-[5px] w-px bg-gray-200"
                    aria-hidden="true"
                ></span>
            {/if}
            <span
                class="relative mt-1.5 size-[11px] shrink-0 rounded-full ring-4 ring-white {timelineDotClass(
                    entry.state,
                )}"
                aria-hidden="true"
            ></span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900">
                    {label(entry)}
                </p>
                <p class="text-xs text-gray-500">
                    {#if entry.user}by {entry.user.name}{/if}
                    {#if entry.user && entry.at}·{/if}
                    {#if entry.at}
                        <time datetime={entry.at}>
                            {formatter.format(new Date(entry.at))}
                        </time>
                    {/if}
                </p>
            </div>
        </li>
    {/each}
</ol>
