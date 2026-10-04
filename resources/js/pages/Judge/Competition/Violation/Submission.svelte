<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { updateState } from "@/actions/App/Http/Controllers/DigitalJudge/Violation/ViolationStateController";
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import Spinner from "@/components/Spinner.svelte";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import event from "@/routes/judge/competition/event";
    import { submissions } from "@/routes/judge/competition/violation";
    import type { Competition } from "@/types/base";
    import {
        stateColor,
        submissionCode,
        type ViolationStatus,
        type ViolationSubmission,
    } from "@/types/violation";
    import { Link, page, router, useHttp } from "@inertiajs/svelte";
    import {
        ArrowLeft,
        Check,
        CircleCheck,
        CircleDashed,
        CircleMinus,
        CircleOff,
        CircleX,
        Gavel,
        House,
        X,
    } from "@lucide/svelte";

    let {
        submission: rawSubmission,
        competition,
    }: {
        submission: ViolationSubmission;
        competition: Competition;
    } = $props();

    let submission = $derived<ViolationSubmission>(rawSubmission);

    let http = useHttp<{ state: string }>({ state: "" });

    function updateStatus(status: ViolationStatus) {
        if (http.processing) {
            toastError("This submission is updating, please wait.");
            return;
        }

        http.data = () => {
            return {
                state: status,
            };
        };

        http.post(
            updateState({
                competition: competition.id,
                submission: `${submission.id}`,
            }).url,
            {
                onSuccess(response, httpResponse) {
                    submission.status = status;
                    toastSuccess("Submission updated");
                },
                onError() {
                    toastError(
                        "Failed to update submission. Please try again.",
                    );
                },
            },
        );
    }

    let code = $derived(submissionCode(submission));

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

<AppHead
    title="{code} for {submission.entity.name} in {submission.event
        .name} - DQ/Penalty - {competition.name}"
/>

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    <Link
        href={home(competition)}
        class="flex w-full justify-between items-center"
    >
        <div class="">
            <h1 class="  -mb-3 normal-case! text-black! text-base!">Digital</h1>
            <h1 class=" indent-6 normal-case! text-se text-xl!">Judge</h1>
        </div>

        <House class="bg-se/20 rounded-full text-se p-2 shadow-md " size={40} />
    </Link>
</section>

<div class="h-16"></div>

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2 class="">DQ/Penalty Submission</h2>
    <br />
    <Link
        href={submissions({ competition: competition })}
        class="text-sm text-gray-600 inline-flex items-center gap-1 group-hover:font-bold mb-1"
        ><ArrowLeft size={14} /> Back</Link
    >
</section>

<section class="flex flex-col relative">
    <p class="font-archivo {stateColor(submission.status)}">
        {submission.status}
    </p>

    <div class="flex items-center justify-between">
        <p class="font-medium">{submission.entity.name}</p>
        <p class="font-medium">{submission.event.name}</p>
    </div>

    <div class="flex items-center justify-between mb-1">
        <h1 class="text-red-500">{code}</h1>

        {#if submission.status === "SUBMITTED"}
            <CircleDashed class="animate-pulse" />
        {:else if submission.status === "ACCEPTED"}
            <CircleCheck class="text-green-500" />
        {:else if submission.status === "APPEALED"}
            <CircleMinus class="text-orange-500" />
        {:else}
            <CircleX class="text-red-500" />
        {/if}
    </div>
    <p class="text-sm text-gray-600">{submission.violation.description}</p>

    <div class="flex items-center justify-between my-2">
        <p class="text-sm font-medium">
            {formatOrder(submission)}
        </p>
        <p class=" text-sm font-medium">
            Length {submission.details.length ?? "-"} · Turn {submission.details
                .turn ?? "-"}
        </p>
    </div>

    <div>
        <p>
            <span class=" text-sm font-medium">Submitter:</span>
            <span
                >{submission.submitter.user?.name ??
                    submission.submitter.name ??
                    "-"} ({submission.submitter.position ?? "-"})</span
            >
        </p>
        <p>
            <span class=" text-sm font-medium">Seconder:</span>
            <span
                >{submission.seconder.name ?? "-"} ({submission.seconder
                    .position ?? "-"})</span
            >
        </p>

        <div class="my-2">
            <p class="text-sm font-medium">Details:</p>
            <p>
                {submission.details.details !== ""
                    ? submission.details.details
                    : "No details given."}
            </p>
        </div>
    </div>

    {#if page.props.judge.isHeadRef}
        {#if submission.status === "SUBMITTED"}
            <hr class="spacer mb-4!" />
            <div class="flex space-x-2 mb-1">
                <ConfirmDialog
                    title="Approve {code}"
                    description="Are you sure you want to approve this submission for {submission
                        .entity.name} in {submission.event.name}?"
                    triggerLabel="Approve"
                    triggerClass="w-full py-1"
                    triggerVariant="success"
                    triggerIcon={Check}
                    confirmVariant="success"
                    confirmLabel="Approve"
                    onConfirm={() => updateStatus("ACCEPTED")}
                    loading={http.processing}
                />

                <ConfirmDialog
                    title="Reject {code}"
                    description="Are you sure you want to reject this submission for {submission
                        .entity.name} in {submission.event.name}?"
                    triggerLabel="Reject"
                    triggerClass="w-full py-1"
                    triggerVariant="danger"
                    triggerIcon={X}
                    confirmLabel="Reject"
                    onConfirm={() => updateStatus("REJECTED")}
                    loading={http.processing}
                />
            </div>
        {:else if submission.status === "ACCEPTED"}
            <hr class="spacer mb-4!" />
            <div class="flex space-x-2 mb-1">
                <ConfirmDialog
                    title="Appeal {code}"
                    description="Are you sure you want to appeal this submission for {submission
                        .entity.name} in {submission.event
                        .name}? (You should only do this if the appeal was successful)"
                    triggerLabel="Appealed"
                    triggerClass="w-full py-1"
                    triggerVariant="success"
                    triggerIcon={Check}
                    confirmVariant="success"
                    confirmLabel="Appeal"
                    onConfirm={() => updateStatus("APPEALED")}
                    loading={http.processing}
                />

                <ConfirmDialog
                    title="Remove {code}"
                    description="Are you sure you want to remove this submission for {submission
                        .entity.name} in {submission.event
                        .name}? (It will appear as if it never existed)"
                    triggerLabel="Remove"
                    triggerClass="w-full py-1"
                    triggerVariant="danger"
                    triggerIcon={X}
                    confirmLabel="Remove"
                    onConfirm={() => updateStatus("REMOVED")}
                    loading={http.processing}
                />
            </div>
        {/if}
    {/if}
</section>
