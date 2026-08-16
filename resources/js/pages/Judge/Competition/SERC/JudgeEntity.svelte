<script module lang="ts">
    export const layout = {
        title: "Select Tank",
    };
</script>

<script lang="ts">
    import { index } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import {
        nextEntityToMark,
        setTank,
        storeEntityMarks,
        home,
        getJudgeNotes,
        getPreviousMarks,
    } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";
    import ActionStatusModal from "@/components/ActionStatusModal.svelte";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Collapse from "@/components/Collapse.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import JudgeMarkingPoints from "@/components/Judging/SERC/JudgeMarkingPoints.svelte";
    import MarkingPoint from "@/components/Judging/SERC/MarkingPoint.svelte";
    import Spinner from "@/components/Spinner.svelte";
    import { confirm } from "@/lib/confirm";

    import { appState } from "@/lib/stores/appState";
    import { judgeMarks } from "@/lib/stores/judgeMarks";
    import { toastError } from "@/lib/toast.svelte";
    import markSplits from "@/routes/comps/events/sercs/mark-splits";
    import judge from "@/routes/judge";

    import type {
        Mark,
        Competition,
        Entity,
        Judge,
        SERC,
        JudgeMarks,
        JudgeNotes,
        PreviousMarks,
    } from "@/types/base";
    import {
        Form,
        page,
        Link,
        router,
        useHttp,
        setLayoutProps,
    } from "@inertiajs/svelte";
    import {
        ArrowRight,
        Check,
        Clipboard,
        House,
        LifeBuoy,
    } from "@lucide/svelte";
    import { onMount } from "svelte";
    import { writable } from "svelte/store";

    const http = useHttp<
        {
            judge_id: number;
            marks: {
                marking_point_id: number;
                mark: number;
            }[];
            notes?: string;
        }[]
    >();

    const user = $derived(page.props.auth.user);

    let canLeaveWithoutConfirming = $state<boolean>(false);

    let modalRef: ActionStatusModal | null = null;

    let {
        competition,
        serc,
        judges,
        entity,
    }: {
        competition: Competition;
        serc: SERC;
        judges: Judge[];
        entity: Entity;
    } = $props();

    $effect(() => {
        $judgeMarks = judges.map((judge) => ({
            judge: judge,
            marks: judge.marking_points.map((mp) => ({
                marking_point: mp,
                mark: null,
            })),
        }));
    });

    let hasSubmitted = $state(false);

    async function submit(e: SubmitEvent) {
        e.preventDefault();

        hasSubmitted = true;
        // work out if any marks are null
        let hasNullMarks = $judgeMarks.some((jm) =>
            jm.marks.some((m) => m.mark === null),
        );

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

        console.log("Submitting marks:", $judgeMarks);

        // lets condense marks down to a simpler structure for submission
        const marksToSubmit: {
            judge_id: number;
            marks: {
                marking_point_id: number;
                mark: number;
            }[];
            notes?: string;
        }[] = $judgeMarks.map((jm) => ({
            judge_id: jm.judge.id,
            marks: jm.marks.map((m) => ({
                marking_point_id: m.marking_point.id,
                mark: m.mark!,
            })),
            notes: jm.notes,
        }));

        http.data = () => marksToSubmit;

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

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    <Link
        href={home({ competition: competition, serc: serc })}
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
    <h2 class="">{serc.name}</h2>
    <br />

    <div class="flex item-center justify-between">
        <p class="text-bulsca font-bold text-xl">{entity.name}</p>
    </div>

    <div class="flex flex-col space-y-3">
        <form onsubmit={submit} novalidate>
            <div class="flex flex-col space-y-6">
                {#each $judgeMarks as jm, j (jm.judge.id)}
                    <JudgeMarkingPoints
                        judge={jm.judge}
                        bind:hasSubmitted
                        judge_index={j}
                        {loadPreviousMarks}
                    />
                    <br />
                {/each}
            </div>

            <br />

            <div class="flex flex-row space-x-2 md:space-x-4 items-center">
                <label for="confirm"
                    >I acknowledge that the above results are correct and cannot
                    be changed, and submission of this form acts as signing it
                    digitally.
                    <br />
                    <small class="text-gray-500"
                        >(Clicking the text will also check the box!)</small
                    >
                </label>
                <input
                    type="checkbox"
                    required
                    name=""
                    class="min-w-5 min-h-5"
                    id="confirm"
                />
            </div>
            <br />

            <Button
                variant="success"
                label="Submit Marks"
                class="w-full py-2 "
                icon={Check}
            />
        </form>
    </div>
    <br />
    <br />
    <br />
    <br />
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
                    variant="success"
                    class="w-full mb-0! "
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
                    class="w-full mb-0! py-1 "
                    label="SERC Home"
                    type="button"
                    icon={House}
                />
            </Link>
        {/snippet}
    </ActionStatusModal>

    <GenericDialog title="Notes" bind:open={notesOpen} withX={true}>
        {#if nHttp.processing}
            <Spinner />
        {:else if nHttp.response}
            {#each nHttp.response as judgeNotes}
                <h3 class="text-black! mb-1">Notes for {judgeNotes.name}</h3>
                <div class="space-y-2">
                    {#each judgeNotes.notes as note}
                        <div>
                            <h4>{note.entity.name}</h4>
                            <p class="indent-4">{note.note}</p>
                        </div>
                        {#each judgeNotes.notes as note}
                            <div>
                                <h4>{note.entity.name}</h4>
                                <p class="indent-4">{note.note}</p>
                            </div>
                            {#each judgeNotes.notes as note}
                                <div>
                                    <h4>{note.entity.name}</h4>
                                    <p class="indent-4">{note.note}</p>
                                </div>
                            {/each}
                        {/each}
                    {/each}
                </div>
            {/each}
        {/if}
    </GenericDialog>

    <GenericDialog
        title="Previous Marks"
        bind:open={previousMarksOpen}
        withX={true}
    >
        {#if pmHttp.processing}
            <Spinner />
        {:else}
            <div class="se-table">
                <table>
                    <thead>
                        <tr>
                            <th scope="col" class="sticky top-0 left-0 bg-black"
                                >Entry</th
                            >
                            {#each pmHttp.response as previousMarks}
                                <th scope="col"
                                    >{previousMarks.marking_point
                                        .description}</th
                                >
                            {/each}
                        </tr>
                    </thead>
                    <tbody>
                        {#each pmHttp.response?.at(0)?.marks as entityMark}
                            <tr>
                                <th
                                    scope="row"
                                    class="sticky top-0 left-0 bg-white"
                                    >{entityMark.entity.name}</th
                                >

                                {#each pmHttp.response as previousMarks}
                                    <td
                                        >{previousMarks.marks.find(
                                            (mark) =>
                                                mark.entity.id ===
                                                entityMark.entity.id,
                                        )?.mark ?? "-"}</td
                                    >
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
