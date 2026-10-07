<script module lang="ts">
    export const layout = {
        title: "DQ/Penalty Submissions",
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";

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
    import { House, Inbox, Plus } from "@lucide/svelte";
    import { flip } from "svelte/animate";
    import { fade } from "svelte/transition";

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

    let filter = $state<ViolationStatus | "ALL">("ALL");

    let counts = $derived.by(() => {
        const acc = {} as Record<ViolationStatus, number>;
        for (const sub of submissions) {
            acc[sub.status] = (acc[sub.status] ?? 0) + 1;
        }
        return acc;
    });

    // Pending first, then the rest in workflow order
    let visible = $derived.by(() => {
        const filtered =
            filter === "ALL"
                ? submissions
                : submissions.filter((s) => s.status === filter);

        return [...filtered].sort(
            (a, b) =>
                violationStatuses.indexOf(a.status) -
                violationStatuses.indexOf(b.status),
        );
    });

    let secondsSincePoll = $state<number>(0);

    $effect(() => {
        let interval = setInterval(() => {
            secondsSincePoll++;
        }, 1000);

        return () => clearInterval(interval);
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
    <div class="flex items-end justify-between">
        <h2 class="">DQ/Penalty</h2>
        <span
            class="mb-1 inline-flex items-center gap-1.5 text-xs text-gray-500"
            title="Updates automatically"
        >
            <span class="relative flex size-2">
                <span
                    class="absolute inline-flex size-full animate-ping rounded-full bg-se opacity-60"
                ></span>
                <span class="relative inline-flex size-2 rounded-full bg-se"
                ></span>
            </span>
            Live · {secondsSincePoll}s
        </span>
    </div>

    <Link
        href={issue(competition, {
            query: Object.fromEntries(new URLSearchParams(page.url)),
        })}
        class="w-full mt-4"
    >
        <Button label="New Submission" class="w-full" icon={Plus} />
    </Link>

    <div class="-mx-1 mt-5 mb-3 flex gap-2 overflow-x-auto px-1 pb-1">
        {@render chip("ALL", "All", submissions.length)}
        {#each violationStatuses as status (status)}
            {#if counts[status] || filter === status}
                {@render chip(status, statusLabels[status], counts[status] ?? 0)}
            {/if}
        {/each}
    </div>

    {#if visible.length === 0}
        <div
            class="flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed py-12 text-center text-gray-500"
            in:fade
        >
            <Inbox size={32} class="text-gray-300" />
            <p class="font-medium">No submissions yet</p>
            <p class="text-sm">DQs and penalties will show up here.</p>
        </div>
    {:else}
        <div class="flex flex-col gap-2">
            {#each visible as submission (submission.id)}
                <div animate:flip={{ duration: 200 }}>
                    <ViolationSubmissionCard {submission} {competition} />
                </div>
            {/each}
        </div>
    {/if}
</section>

{#snippet chip(value: ViolationStatus | "ALL", label: string, count: number)}
    <button
        type="button"
        onclick={() => (filter = value)}
        class="inline-flex shrink-0 cursor-pointer items-center gap-1.5 rounded-full border px-3 py-1 text-sm font-medium transition-colors {filter ===
        value
            ? 'border-black bg-black text-white'
            : 'bg-white text-gray-700 hover:border-gray-400'}"
    >
        {label}
        <span
            class="rounded-full px-1.5 text-xs {filter === value
                ? 'bg-white/20'
                : 'bg-gray-100 text-gray-500'}">{count}</span
        >
    </button>
{/snippet}
