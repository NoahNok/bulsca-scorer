<script module lang="ts">
    import { home as sercHome } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";
    import { LifeBuoy } from "@lucide/svelte";

    export const layout = (props: Record<string, any>) => ({
        title: "Mark",
        header: {
            icon: LifeBuoy,
            href: sercHome({ competition: props.competition, serc: props.serc }),
        },
    });
</script>

<script lang="ts">
    import {
        nextEntityToMark,
        storeEntityMarks,
        home,
        getJudgeNotes,
        getPreviousMarks,
    } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";
    import ActionStatusModal from "@/components/ActionStatusModal.svelte";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import JudgeMarkingPoints from "@/components/Judging/SERC/JudgeMarkingPoints.svelte";
    import SignOffCheckbox from "@/components/Judging/SignOffCheckbox.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import Spinner from "@/components/Spinner.svelte";
    import { confirm } from "@/lib/confirm";

    import { toastError } from "@/lib/toast.svelte";

    import type {
        Competition,
        Entity,
        Judge,
        SERC,
        JudgeNotes,
        PreviousMarks,
        CurrentDraw,
        ExisitingMarks,
        ExistingNotes,
    } from "@/types/base";
    import {
        page,
        Link,
        router,
        useHttp,
        setLayoutProps,
    } from "@inertiajs/svelte";
    import { ArrowRight, Check, House, NotebookText } from "@lucide/svelte";

    // marks/notes stay as page state (derived from props), sent via transform
    const http = useHttp().transform(() => ({ marks, notes }));

    let canLeaveWithoutConfirming = $state<boolean>(false);

    let modalRef: ActionStatusModal | null = null;

    let {
        competition,
        serc,
        judges,
        entity,
        draw,
        existingMarks,
        existingNotes,
    }: {
        competition: Competition;
        serc: SERC;
        judges: Judge[];
        entity: Entity;
        draw: CurrentDraw;
        existingMarks: ExisitingMarks;
        existingNotes: ExistingNotes;
    } = $props();

    // this is a store that holds the marks for each judge and marking point
    let marks = $derived<ExisitingMarks>(existingMarks);
    let notes = $derived<ExistingNotes>(existingNotes);

    let hasSubmitted = $state(false);

    const showTeamNames = $derived(
        competition.show_teams_to_judges || page.props.judge.isHeadRef,
    );

    async function submit(e: SubmitEvent) {
        e.preventDefault();

        hasSubmitted = true;
        // work out if any marks are null
        let hasNullMarks = false;
        for (const judge of judges) {
            for (const marking_point of judge.marking_points) {
                if (marks[judge.id][marking_point.id] === null) {
                    hasNullMarks = true;
                    break;
                }
            }
        }

        if (hasNullMarks) {
            console.info("Cannot submit marks, some marks are null");
            toastError("Please fill in all marks before submitting.");
            return;
        }

        const form = e.currentTarget as HTMLFormElement;

        if (!form.checkValidity()) {
            form.reportValidity();
            console.info("Cannot submit marks, form is invalid");
            toastError(
                "Please check the confirmation checkbox before submitting.",
            );
            return;
        }

        const req = http.post(
            storeEntityMarks({
                competition: competition.id,
                serc: serc.id,
                entity_id: entity.id,
            }).url,
        );

        const success = await modalRef?.showFor(req);

        if (success) {
            canLeaveWithoutConfirming = true;
            modalRef?.setTitleAndMessage(
                "Marks Submitted",
                "Your marks have been submitted successfully.",
            );
        } else {
            hasSubmitted = false;
        }
    }

    const nHttp = useHttp<{}, JudgeNotes[]>();
    const pmHttp = useHttp<{}, PreviousMarks[]>();

    let notesOpen = $state<boolean>(false);
    let previousMarksOpen = $state<boolean>(false);

    function loadNotes() {
        notesOpen = true;

        if (nHttp.wasSuccessful) return;

        nHttp.get(
            getJudgeNotes({ competition: competition.id, serc: serc.id }).url,
            {
                onSuccess: (response) => {},
            },
        );
    }

    function loadPreviousMarks(judge: Judge) {
        previousMarksOpen = true;

        pmHttp.get(
            getPreviousMarks({
                competition: competition.id,
                serc: serc.id,
                judge_id: judge.id,
            }).url,
            {},
        );
    }

    $effect(() => {
        setLayoutProps({
            nav: nav,
        });

        const originalVisit = router.visit.bind(router);

        const wrappedVisit = async function (
            href: Parameters<typeof router.visit>[0],
            options?: Parameters<typeof router.visit>[1],
        ) {
            if (canLeaveWithoutConfirming) {
                return originalVisit(href, options);
            }

            const ok = await confirm({
                title: "Leave page?",
                description:
                    "Are you sure you want to leave this page? Any unsubmitted marks will be lost!",
                confirmLabel: "Yes",
            });

            if (!ok) return; // cancel navigation

            return originalVisit(href, options);
        } as typeof router.visit;

        router.visit = wrappedVisit;

        return () => {
            router.visit = originalVisit as typeof router.visit;
        };
    });
</script>

<AppHead title="{entity.name} ({serc.name})" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{serc.name}</h2>

    <BackLink
        href={home({ competition, serc })}
        label="Back to {serc.name}"
        class="mt-2 mb-4"
    />

    <div class="flex items-end justify-between gap-3">
        <div class="min-w-0">
            <SectionLabel>Marking</SectionLabel>
            <p class="font-archivo truncate text-xl font-semibold">
                {showTeamNames ? entity.name : draw.text}
            </p>
        </div>
        {#if showTeamNames}
            <span
                class="mb-1 shrink-0 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold whitespace-nowrap text-gray-600"
            >
                {draw.text}
            </span>
        {/if}
    </div>
    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-gray-100">
        <div
            class="h-full rounded-full bg-se transition-all"
            style:width="{draw.percent}%"
        ></div>
    </div>

    <form onsubmit={submit} novalidate class="mt-4 flex flex-col gap-4">
        {#each judges as judge (judge.id)}
            <JudgeMarkingPoints
                {judge}
                bind:hasSubmitted
                {loadPreviousMarks}
                bind:marks={marks[judge.id]}
                bind:note={notes[judge.id]}
            />
        {/each}

        <SignOffCheckbox id="confirm" class="mt-2" />

        <Button type="submit" label="Submit Marks" class="w-full" icon={Check} />
    </form>

    <ActionStatusModal
        bind:this={modalRef}
        title="Submitting Marks"
        message="Submitting your marks..."
    >
        {#snippet success()}
            <Link
                href={nextEntityToMark({
                    competition: competition.id,
                    serc: serc.id,
                })}
                viewTransition
                class="w-full"
            >
                <Button
                    class="mb-0! w-full"
                    label="Continue"
                    type="button"
                    icon={ArrowRight}
                />
            </Link>

            <Link
                href={home({
                    competition: competition.id,
                    serc: serc.id,
                })}
                class="w-full"
            >
                <Button
                    variant="secondary"
                    class="mb-0! w-full py-1.5"
                    label="SERC Home"
                    type="button"
                    icon={House}
                />
            </Link>
        {/snippet}
    </ActionStatusModal>

    <GenericDialog title="Notes" bind:open={notesOpen} withX={true}>
        {#if nHttp.processing}
            <div class="flex justify-center py-6"><Spinner /></div>
        {:else if nHttp.response}
            {#each nHttp.response as judgeNotes}
                <SectionLabel class="mt-2 mb-2">
                    Notes for {judgeNotes.name}
                </SectionLabel>
                {#if judgeNotes.notes.length === 0}
                    <p class="text-sm text-gray-400 italic">No notes yet.</p>
                {:else}
                    <dl class="divide-y rounded-xl border bg-white">
                        {#each judgeNotes.notes as note}
                            <div class="p-3">
                                <dt class="font-archivo text-sm">
                                    {note.entity.name}
                                </dt>
                                <dd class="mt-0.5 text-sm text-gray-700">
                                    {note.note}
                                </dd>
                            </div>
                        {/each}
                    </dl>
                {/if}
            {:else}
                <EmptyState icon={NotebookText} title="No notes yet" />
            {/each}
        {/if}
    </GenericDialog>

    <GenericDialog
        title="Previous Marks"
        bind:open={previousMarksOpen}
        withX={true}
    >
        {#if pmHttp.processing}
            <div class="flex justify-center py-6"><Spinner /></div>
        {:else}
            <div class="max-h-[60vh] overflow-auto rounded-xl border">
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th
                                scope="col"
                                class="sticky top-0 left-0 z-20 border-b bg-gray-50 px-3 py-2 text-left text-xs font-semibold text-gray-500"
                            >
                                Entry
                            </th>
                            {#each pmHttp.response as previousMarks}
                                <th
                                    scope="col"
                                    class="sticky top-0 z-10 border-b bg-gray-50 px-3 py-2 text-left text-xs font-semibold text-gray-500"
                                >
                                    {previousMarks.marking_point.description}
                                </th>
                            {/each}
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        {#each pmHttp.response?.at(0)?.marks as entityMark}
                            <tr>
                                <th
                                    scope="row"
                                    class="sticky left-0 bg-white px-3 py-2 text-left font-medium whitespace-nowrap"
                                >
                                    {entityMark.entity.name}
                                </th>

                                {#each pmHttp.response as previousMarks}
                                    <td class="px-3 py-2 font-mono">
                                        {previousMarks.marks.find(
                                            (mark) =>
                                                mark.entity.id ===
                                                entityMark.entity.id,
                                        )?.mark ?? "–"}
                                    </td>
                                {/each}
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        {/if}
    </GenericDialog>
</section>

{#snippet nav()}
    <Link href={home({ competition: competition, serc: serc })} class="mr-auto">
        <Button
            label="SERC Home"
            variant="white"
            class="w-full py-1 border border-black/10"
        />
    </Link>
    <Button label="Notes" onclick={() => loadNotes()} class=" py-1"></Button>
{/snippet}
