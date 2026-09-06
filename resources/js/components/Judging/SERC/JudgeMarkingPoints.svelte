<script lang="ts">
    import Collapse from "@/components/Collapse.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import MarkingPoint from "./MarkingPoint.svelte";
    import type { Judge } from "@/types/base";

    import { CircleSlash2 } from "@lucide/svelte";

    let {
        judge,
        hasSubmitted = $bindable<boolean>(),
        loadPreviousMarks,
        marks = $bindable<Record<number, number | null>>(),
        note = $bindable<string | null>(),
    }: {
        judge: Judge;
        hasSubmitted: boolean;
        loadPreviousMarks: (judge: Judge) => void;
        marks: Record<number, number | null>;
        note: string | null;
    } = $props();

    const zeroAllMarks = () => {
        for (const marking_point of judge.marking_points) {
            // Set to 0, unless tempalte is choice, the nuse the lowest choice value
            if (marking_point.template?.mode === "choice") {
                const lowestChoice = Math.min(
                    ...marking_point.template.choice.map((c) => c.value),
                );
                marks[marking_point.id] = lowestChoice;
            } else {
                marks[marking_point.id] = 0;
            }
        }
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

{#each judge.marking_points as marking_point (marking_point.id)}
    <MarkingPoint
        {marking_point}
        bind:hasSubmitted
        bind:value={marks[marking_point.id]}
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
        bind:value={note}
    ></textarea>
</div>
