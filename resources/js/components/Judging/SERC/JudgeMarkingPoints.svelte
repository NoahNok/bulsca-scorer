<script lang="ts">
    import Collapse from "@/components/Collapse.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import Input from "@/components/input.svelte";
    import MarkingPoint from "./MarkingPoint.svelte";
    import type { Judge } from "@/types/base";

    import { CircleSlash2, History } from "@lucide/svelte";

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

    // Quill saves an empty editor as "<p><br></p>", so check for real content
    const hasHint = $derived.by(() => {
        const html = judge.description ?? "";
        if (/<img\b/i.test(html)) return true;
        return (
            html
                .replace(/<[^>]*>/g, "")
                .replace(/&nbsp;/g, " ")
                .trim().length > 0
        );
    });

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

<div class="rounded-xl border bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between gap-3">
        <h3 class="min-w-0 truncate text-base!">{judge.name}</h3>
        <button
            type="button"
            class="inline-flex shrink-0 cursor-pointer items-center gap-1 text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
            onclick={() => loadPreviousMarks(judge)}
        >
            <History size={14} /> Previous marks
        </button>
    </div>

    {#if hasHint}
        <Collapse
            buttonText="Marking hints"
            class="mt-3 rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700"
        >
            <article
                class="prose prose-sm prose-neutral prose-p:mb-0 prose-ul:my-0 prose-ol:my-0 prose-li:my-0 mt-2 block leading-5! font-normal"
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
        triggerClass="mt-3 w-full py-1.5"
        triggerType="button"
        triggerIcon={CircleSlash2}
        onConfirm={zeroAllMarks}
    />

    <div class="mt-1 divide-y">
        {#each judge.marking_points as marking_point (marking_point.id)}
            <MarkingPoint
                {marking_point}
                bind:hasSubmitted
                bind:value={marks[marking_point.id]}
            />
        {/each}
    </div>

    <div class="mt-3 border-t pt-3">
        <Input
            type="textarea"
            variant="soft"
            rows={4}
            label="Notes for {judge.name}"
            name="team-notes-{judge.id}"
            placeholder="Type your notes for this team here..."
            bind:value={note}
        />
    </div>
</div>
