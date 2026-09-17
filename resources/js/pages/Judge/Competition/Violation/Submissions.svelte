<script module lang="ts">
    export const layout = {
        title: "Confirm Judge",
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";

    import GenericCollapse from "@/components/GenericCollapse.svelte";
    import ViolationSubmissionCard from "@/components/Judging/Violation/ViolationSubmissionCard.svelte";
    import { issue } from "@/routes/judge/competition/violation";

    import type { Competition } from "@/types/base";
    import {
        violationStatuses,
        type ViolationStatus,
        type ViolationSubmission,
    } from "@/types/violation";
    import { page, Link, usePoll } from "@inertiajs/svelte";
    import { ArrowRight, House } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    let {
        competition,
        submissions,
    }: {
        competition: Competition;
        submissions: ViolationSubmission[];
    } = $props();

    usePoll(
        2000,
        () => ({
            only: ["submissions"],
        }),
        {
            mode: "rest",
        },
    );

    let groups = $derived.by(() => {
        return submissions.reduce(
            (acc, sub) => {
                const key = sub.status;
                if (!acc[key]) acc[key] = [];
                acc[key].push(sub);
                return acc;
            },
            {} as Record<ViolationStatus, ViolationSubmission[]>,
        );
    });
</script>

<AppHead title="Submissions - DQ/Penalty - {competition.name}" />

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

<section class="flex flex-col h-full">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2 class="">DQ/Penalty</h2>
    <br />
    <Link
        href={issue(competition, {
            query: Object.fromEntries(new URLSearchParams(page.url)),
        })}
        class="w-full"
    >
        <Button
            label="New DQ/Penalty Submission"
            variant="success"
            class="w-full"
            icon={ArrowRight}
        />
    </Link>

    <hr class="spacer my-4!" />
    <div class="">
        {#each violationStatuses as status (status)}
            {#if groups[status]}
                <GenericCollapse open={true}>
                    {#snippet header()}
                        <h3 class="mb-1">{status}</h3>
                    {/snippet}

                    <div class="space-y-3 my-2 mb-4" transition:slide>
                        {#each groups[status] as submission (submission.id)}
                            <ViolationSubmissionCard
                                {submission}
                                {competition}
                            />
                        {/each}
                    </div>
                </GenericCollapse>
            {/if}
        {/each}
        <br />
        <br />
    </div>
</section>
