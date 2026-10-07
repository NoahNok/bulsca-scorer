<script lang="ts">
    import { view } from "@/routes/judge/competition/violation/submission";
    import type { Competition } from "@/types/base";
    import {
        formatOrder,
        isVoided,
        statusTileClass,
        submissionCode,
        type ViolationSubmission,
    } from "@/types/violation";
    import { Link } from "@inertiajs/svelte";
    import { ChevronRight } from "@lucide/svelte";
    import ViolationStatusBadge from "./ViolationStatusBadge.svelte";

    let {
        submission,
        competition,
    }: {
        submission: ViolationSubmission;
        competition: Competition;
    } = $props();

    let code = $derived(submissionCode(submission));
    let voided = $derived(isVoided(submission.status));
</script>

<Link
    href={view({ competition: competition, submission: submission.id })}
    class="group flex items-center gap-3 w-full rounded-xl border bg-white p-3 shadow-sm transition-all hover:border-se hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-se {voided
        ? 'opacity-60'
        : ''}"
>
    <div
        class="flex size-14 shrink-0 items-center justify-center rounded-lg font-archivo text-lg font-bold transition-colors {statusTileClass(submission)}"
        class:line-through={voided}
    >
        {code}
    </div>

    <div class="min-w-0 flex-1">
        <p class="truncate font-semibold text-gray-900">
            {submission.entity.name}
        </p>
        <p class="truncate text-sm text-gray-500">
            {submission.event.name}
        </p>
        <p class="truncate text-xs text-gray-400">
            {formatOrder(submission)}
        </p>
    </div>

    <div class="flex shrink-0 flex-col items-end gap-2">
        <ViolationStatusBadge status={submission.status} />
        <ChevronRight
            size={18}
            class="text-gray-300 transition-all group-hover:translate-x-0.5 group-hover:text-se"
        />
    </div>
</Link>
