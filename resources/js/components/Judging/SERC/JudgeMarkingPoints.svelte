<script lang="ts">
    import Button from "@/components/Button.svelte";
    import Collapse from "@/components/Collapse.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import MarkingPoint from "./MarkingPoint.svelte";
    import type { Judge } from "@/types/base";
    import { judgeMarks } from "@/lib/stores/judgeMarks";
    import { CircleSlash2 } from "@lucide/svelte";

    let {
        judge,
        judge_index,
        hasSubmitted = $bindable<boolean>(),
        loadPreviousMarks,
    }: {
        judge: Judge;
        judge_index: number;
        hasSubmitted: boolean;
        loadPreviousMarks: (judge: Judge) => void;
    } = $props();

    const zeroAllMarks = () => {
        $judgeMarks = $judgeMarks.map((judgeMark, index) => {
            if (index !== judge_index) {
                return judgeMark;
            }

            return {
                ...judgeMark,
                marks: judgeMark.marks.map((mark) => {
                    const template = mark.marking_point.template;
                    const fallbackValue =
                        template?.mode === "choice"
                            ? Math.min(
                                  ...(template.choice?.map(
                                      (choice) => choice.value,
                                  ) ?? [0]),
                              )
                            : 0;

                    return {
                        ...mark,
                        mark: fallbackValue,
                    };
                }),
            };
        });
    };
</script>

<div>
    <h3>{judge.name}</h3>
    <button
        type="button"
        class="-mt-2 -mb-4 text-sm text-blue-700 hover:underline cursor-pointer"
        onclick={() => loadPreviousMarks(judge)}
    >
        Previous Marks
    </button>
</div>

{#if judge.description}
    <Collapse buttonText="Marking Hints/Specification" class="mb-3">
        <article
            class="block prose prose-neutral prose-p:mb-0 prose-ul:my-0 prose-ol:my-0 prose-li:my-0 leading-5!"
        >
            {@html judge.description}
        </article>
    </Collapse>
{/if}

<ConfirmDialog
    title="ZERO all marks?"
    description="This will set every mark for this judge to zero."
    triggerLabel="ZERO all"
    triggerVariant="danger"
    triggerClass="w-full py-1  mb-3"
    triggerType="button"
    triggerIcon={CircleSlash2}
    onConfirm={zeroAllMarks}
/>

{#each $judgeMarks[judge_index].marks as mark, i (mark.marking_point.id)}
    <MarkingPoint
        {mark}
        bind:hasSubmitted
        bind:value={$judgeMarks[judge_index].marks[i].mark}
    />
{/each}

<div>
    <h5>Notes for {judge.name}</h5>

    <textarea
        name="team-notes-{judge.id}"
        rows="5"
        placeholder="Type your notes for this team here..."
        class="w-full border hover:border-gray-400 p-3 h-max focus:border-gray-400 outline-hidden rounded-md"
        id=""
        bind:value={$judgeMarks[judge_index].notes}
    ></textarea>
</div>
