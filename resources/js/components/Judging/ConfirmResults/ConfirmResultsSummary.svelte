<script lang="ts">
    import type { ConfirmSummary } from "@/types/results";
    import { cn } from "@/utils/utils";
    import type { LinkComponentBaseProps } from "@inertiajs/core";
    import { Link } from "@inertiajs/svelte";
    import { TriangleAlert } from "@lucide/svelte";

    let {
        summary,
        entityLabel,
        missingLabel,
        submissionsHref,
        class: className = "",
    }: {
        summary: ConfirmSummary;
        entityLabel: string;
        missingLabel: string;
        submissionsHref: LinkComponentBaseProps["href"];
        class?: string;
    } = $props();

    let warnings = $derived(
        [
            summary.missing > 0 &&
                `${summary.missing} ${summary.missing === 1 ? "entry is" : "entries are"} missing results.`,
            (summary.missingOof ?? 0) > 0 &&
                `${summary.missingOof} heat${summary.missingOof === 1 ? " has" : "s have"} no order of finish.`,
        ].filter(Boolean) as string[],
    );
</script>

<div class={cn("flex flex-col gap-3", className)}>
    <dl class="grid grid-cols-3 divide-x rounded-xl border bg-white">
        <div class="p-3">
            <dt class="text-xs text-gray-500">{entityLabel}</dt>
            <dd class="text-sm font-semibold">{summary.entities}</dd>
        </div>
        <div class="p-3">
            <dt class="text-xs text-gray-500">{missingLabel}</dt>
            <dd class="text-sm font-semibold {summary.missing > 0 ? 'text-amber-700' : ''}">
                {summary.missing}
            </dd>
        </div>
        <div class="p-3">
            <dt class="text-xs text-gray-500">Pending DQ/Pen</dt>
            <dd class="text-sm font-semibold {summary.pending > 0 ? 'text-amber-700' : ''}">
                {summary.pending}
            </dd>
        </div>
    </dl>

    {#if warnings.length > 0 || summary.pending > 0}
        <div
            class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800"
        >
            <TriangleAlert size={16} class="mt-0.5 shrink-0" />
            <div class="flex flex-col gap-1">
                {#each warnings as warning}
                    <p>{warning}</p>
                {/each}
                {#if summary.pending > 0}
                    <p>
                        {summary.pending} DQ/penalty submission{summary.pending === 1
                            ? " is"
                            : "s are"} still pending, so not applied to the results yet.
                        <Link href={submissionsHref} class="font-semibold underline">
                            Review submissions
                        </Link>
                    </p>
                {/if}
            </div>
        </div>
    {/if}
</div>
