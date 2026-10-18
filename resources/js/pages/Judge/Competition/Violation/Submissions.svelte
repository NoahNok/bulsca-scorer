<script module lang="ts">
    export const layout = {
        title: "DQ/Penalty Submissions",
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import FilterChip from "@/components/FilterChip.svelte";
    import LiveIndicator from "@/components/LiveIndicator.svelte";
    import SegmentedControl from "@/components/SegmentedControl.svelte";

    import ViolationSubmissionCard from "@/components/Judging/Violation/ViolationSubmissionCard.svelte";
    import { issue } from "@/routes/judge/competition/violation";

    import type { Competition } from "@/types/base";
    import {
        statusLabels,
        violationStatuses,
        type ViolationStatus,
        type ViolationSubmission,
    } from "@/types/violation";
    import { page, Link, usePoll } from "@inertiajs/svelte";
    import { Inbox, Plus } from "@lucide/svelte";
    import { flip } from "svelte/animate";

    let {
        competition,
        submissions,
    }: {
        competition: Competition;
        submissions: ViolationSubmission[];
    } = $props();

    usePoll(
        5000,
        () => ({
            only: ["submissions"],
            onSuccess: () => {
                secondsSincePoll = 0;
            },
        }),
        {
            mode: "rest",
        },
    );

    // The head ref gets every judge's submissions and can narrow to their own
    let isHeadRef = $derived(page.props.judge.isHeadRef);
    let scope = $state<"everyone" | "mine">("everyone");

    let scoped = $derived(
        isHeadRef && scope === "mine"
            ? submissions.filter(
                  (s) => s.submitter.user?.id === page.props.auth.user?.id,
              )
            : submissions,
    );

    let filter = $state<ViolationStatus | "ALL">("ALL");

    let counts = $derived.by(() => {
        const acc = {} as Record<ViolationStatus, number>;
        for (const sub of scoped) {
            acc[sub.status] = (acc[sub.status] ?? 0) + 1;
        }
        return acc;
    });

    // Pending first, then accepted/appealed, with voided ones at the bottom
    let visible = $derived.by(() => {
        const filtered =
            filter === "ALL"
                ? scoped
                : scoped.filter((s) => s.status === filter);

        return [...filtered].sort(
            (a, b) =>
                violationStatuses.indexOf(a.status) -
                violationStatuses.indexOf(b.status),
        );
    });

    let secondsSincePoll = $state<number>(0);

    // page.url is "/path?query", so only the part after "?" holds the params
    // (e.g. ?event=se-1 from the competition home, passed on to Issue)
    let issueQuery = $derived(
        Object.fromEntries(new URLSearchParams(page.url.split("?")[1] ?? "")),
    );

    $effect(() => {
        let interval = setInterval(() => {
            secondsSincePoll++;
        }, 1000);

        return () => clearInterval(interval);
    });
</script>

<AppHead title="Submissions - DQ/Penalty - {competition.name}" />


<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <div class="flex items-end justify-between">
        <h2 class="">DQ/Penalty</h2>
        <LiveIndicator class="mb-1" label="Live · {secondsSincePoll}s" />
    </div>

    <BackLink href={home(competition)} label="All events" class="mt-2 mb-4" />

    <Link href={issue(competition, { query: issueQuery })} class="w-full">
        <Button label="New Submission" class="w-full" icon={Plus} />
    </Link>

    {#if isHeadRef}
        <SegmentedControl
            class="mt-5"
            options={[
                { value: "everyone", label: "All judges" },
                { value: "mine", label: "Mine" },
            ]}
            bind:value={scope}
        />
    {/if}

    <div class="-mx-1 mt-5 mb-3 flex gap-2 overflow-x-auto px-1 pb-1">
        <FilterChip
            label="All"
            count={scoped.length}
            active={filter === "ALL"}
            onclick={() => (filter = "ALL")}
        />
        {#each violationStatuses as status (status)}
            {#if counts[status] || filter === status}
                <FilterChip
                    label={statusLabels[status]}
                    count={counts[status] ?? 0}
                    active={filter === status}
                    onclick={() => (filter = status)}
                />
            {/if}
        {/each}
    </div>

    {#if visible.length === 0}
        <EmptyState
            icon={Inbox}
            title="No submissions yet"
            description="DQs and penalties will show up here."
        />
    {:else}
        <div class="flex flex-col gap-2">
            {#each visible as submission (submission.id)}
                <div animate:flip={{ duration: 200 }}>
                    <ViolationSubmissionCard
                        {submission}
                        {competition}
                        showSubmitter={isHeadRef && scope === "everyone"}
                    />
                </div>
            {/each}
        </div>
    {/if}
</section>

