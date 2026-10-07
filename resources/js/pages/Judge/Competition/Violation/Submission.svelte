<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { updateState } from "@/actions/App/Http/Controllers/DigitalJudge/Violation/ViolationStateController";
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import ViolationStatusBadge from "@/components/Judging/Violation/ViolationStatusBadge.svelte";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import { submissions } from "@/routes/judge/competition/violation";
    import type { Competition } from "@/types/base";
    import {
        formatOrder,
        isVoided,
        stateColor,
        statusTileClass,
        submissionCode,
        type ViolationStatus,
        type ViolationSubmission,
    } from "@/types/violation";
    import { Link, page, useHttp } from "@inertiajs/svelte";
    import { ArrowLeft, Check, Gavel, House, Trash2, X } from "@lucide/svelte";

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

    let isDQ = $derived(submission.violation.vtype === "DQ");
    let voided = $derived(isVoided(submission.status));
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
    <Link
        href={submissions({ competition: competition })}
        class="mb-3 inline-flex w-fit items-center gap-1 text-sm text-gray-600 hover:text-se"
        ><ArrowLeft size={14} /> All submissions</Link
    >

    <!-- Summary -->
    <div class="rounded-xl border bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between">
            <span
                class="text-xs font-semibold uppercase tracking-wide {stateColor(submission)}"
            >
                {isDQ ? "Disqualification" : "Penalty"}
            </span>
            <ViolationStatusBadge status={submission.status} />
        </div>

        <div class="mt-3 flex items-center gap-3">
            <div
                class="flex size-16 shrink-0 items-center justify-center rounded-lg font-archivo text-xl font-bold transition-colors {statusTileClass(submission)}"
                class:line-through={voided}
            >
                {code}
            </div>
            <div class="min-w-0">
                <p class="text-lg font-semibold leading-tight text-gray-900">
                    {submission.entity.name}
                </p>
                <p class="text-sm text-gray-500">{submission.event.name}</p>
            </div>
        </div>

        <p
            class="mt-4 border-l-2 border-gray-200 pl-3 text-sm text-gray-700"
        >
            {submission.violation.description}
        </p>
    </div>

    <!-- Where -->
    <dl class="mt-3 grid grid-cols-3 divide-x rounded-xl border bg-white">
        <div class="p-3">
            <dt class="text-xs text-gray-500">Position</dt>
            <dd class="text-sm font-semibold">{formatOrder(submission)}</dd>
        </div>
        <div class="p-3">
            <dt class="text-xs text-gray-500">Length</dt>
            <dd class="text-sm font-semibold">
                {submission.details.length ?? "–"}
            </dd>
        </div>
        <div class="p-3">
            <dt class="text-xs text-gray-500">Turn</dt>
            <dd class="text-sm font-semibold">
                {submission.details.turn ?? "–"}
            </dd>
        </div>
    </dl>

    <!-- Who -->
    <dl class="mt-3 divide-y rounded-xl border bg-white">
        {@render person(
            "Submitted by",
            submission.submitter.user?.name ?? submission.submitter.name,
            submission.submitter.position,
        )}
        {@render person(
            "Seconded by",
            submission.seconder.name,
            submission.seconder.position,
        )}
    </dl>

    <!-- Notes -->
    <div class="mt-3 rounded-xl border bg-white p-3">
        <p class="text-xs text-gray-500">Details</p>
        {#if submission.details.details}
            <p class="mt-1 whitespace-pre-line text-sm">
                {submission.details.details}
            </p>
        {:else}
            <p class="mt-1 text-sm italic text-gray-400">No details given.</p>
        {/if}
    </div>

    {#if page.props.judge.isHeadRef && (submission.status === "SUBMITTED" || submission.status === "ACCEPTED")}
        <div class="mt-6 rounded-xl border border-se/40 bg-se/5 p-4">
            <p
                class="mb-3 inline-flex items-center gap-2 font-archivo text-sm font-semibold uppercase"
            >
                <Gavel size={16} class="text-se" /> Head Referee
            </p>

            {#if submission.status === "SUBMITTED"}
                <div class="flex gap-2">
                    <ConfirmDialog
                        title="Approve {code}"
                        description="Are you sure you want to approve this submission for {submission
                            .entity.name} in {submission.event.name}?"
                        triggerLabel="Approve"
                        triggerClass="w-full"
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
                        triggerClass="w-full"
                        triggerVariant="danger"
                        triggerIcon={X}
                        confirmLabel="Reject"
                        onConfirm={() => updateStatus("REJECTED")}
                        loading={http.processing}
                    />
                </div>
            {:else}
                <p class="mb-3 text-xs text-gray-600">
                    Only mark as appealed if the appeal was successful.
                    Removing makes it as if the submission never existed.
                </p>
                <div class="flex gap-2">
                    <ConfirmDialog
                        title="Appeal {code}"
                        description="Are you sure you want to appeal this submission for {submission
                            .entity.name} in {submission.event
                            .name}? (You should only do this if the appeal was successful)"
                        triggerLabel="Appealed"
                        triggerClass="w-full"
                        triggerVariant="secondary"
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
                        triggerClass="w-full"
                        triggerVariant="danger"
                        triggerIcon={Trash2}
                        confirmLabel="Remove"
                        onConfirm={() => updateStatus("REMOVED")}
                        loading={http.processing}
                    />
                </div>
            {/if}
        </div>
    {/if}
</section>

{#snippet person(label: string, name?: string, position?: string)}
    <div class="flex items-center justify-between gap-3 p-3">
        <dt class="text-xs text-gray-500">{label}</dt>
        <dd class="text-right text-sm">
            <span class="font-semibold">{name || "–"}</span>
            {#if position}
                <span class="text-gray-500"> · {position}</span>
            {/if}
        </dd>
    </div>
{/snippet}
