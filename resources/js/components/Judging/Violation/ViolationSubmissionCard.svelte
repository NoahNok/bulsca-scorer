<script lang="ts">
    import { view } from "@/routes/judge/competition/violation/submission";
    import type { Competition } from "@/types/base";
    import { stateColor, type ViolationSubmission } from "@/types/violation";
    import { Link } from "@inertiajs/svelte";

    import { ArrowRight, MoveRight } from "@lucide/svelte";

    let {
        submission,
        competition,
    }: {
        submission: ViolationSubmission;
        competition: Competition;
    } = $props();

    let code = $derived.by(() => {
        return `${submission.violation.vtype === "DQ" ? "DQ" : "P"}${submission.violation.code}`;
    });

    let stateClass = $derived.by(() => {
        return stateColor(submission.status);
    });

    function formatOrder(submission: ViolationSubmission) {
        if ("heat" in submission.order) {
            return `Heat ${submission.order.heat} · Lane ${submission.order.lane}`;
        }

        const tank = submission.order.tank
            ? `Tank ${submission.order.tank}`
            : "";
        const draw = `Draw ${submission.order.draw}`;

        return [tank, draw].filter(Boolean).join(" · ");
    }
</script>

<div
    class="border rounded-xl shadow-sm px-4 py-2 transition-all w-full relative overflow-hidden hover:border-se"
>
    <Link
        href={view({ competition: competition, submission: submission.id })}
        class="w-full text-left focus:outline-none"
    >
        <div class="flex flex-col space-y-2">
            <div class="flex items-center">
                <span class=" text-sm text-gray-600">
                    {submission.event.name}
                </span>
                <span class="ml-auto text-sm text-gray-600"
                    >{formatOrder(submission)}</span
                >
            </div>

            <div class="flex items-center">
                <h2 class=" text-red-500 mr-auto">
                    {code}
                </h2>
                <MoveRight size={16} />
                <span class="font-medium text-gray-900 ml-auto">
                    <span class="ml-1">{submission.entity.name}</span>
                </span>
            </div>

            <hr class="spacer mb-2!" />

            <div class="flex items-center justify-between">
                <p class="text-sm font-archivo {stateClass}">
                    {submission.status}
                </p>
                <span
                    class="text-sm text-gray-600 inline-flex items-center gap-1"
                    >More <ArrowRight size={16} /></span
                >
            </div>
        </div>
    </Link>
</div>
