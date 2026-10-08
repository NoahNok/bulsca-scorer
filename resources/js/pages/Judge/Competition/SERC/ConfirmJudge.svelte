<script module lang="ts">
    export const layout = {
        title: "Confirm Judge",
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { confirmJudgePost } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import NumberedList from "@/components/NumberedList/NumberedList.svelte";
    import NumberedListItem from "@/components/NumberedList/NumberedListItem.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import type { Competition, Judge, SERC } from "@/types/base";
    import { Form } from "@inertiajs/svelte";
    import { ArrowRight, ClipboardList } from "@lucide/svelte";

    let {
        competition,
        serc,
        judge,
    }: {
        competition: Competition;
        serc: SERC;
        judge: Judge;
    } = $props();
</script>

<AppHead title="Confirm Judge - {serc.name} - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{serc.name}</h2>

    <BackLink href={home(competition)} label="All events" class="mt-2 mb-4" />

    <div class="flex items-center gap-3 rounded-xl border bg-white p-4 shadow-sm">
        <span
            class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-se/20 text-teal-700"
        >
            <ClipboardList size={20} />
        </span>
        <div class="min-w-0">
            <p class="text-xs text-gray-500">Judging as</p>
            <p class="font-archivo truncate text-lg font-semibold">
                {judge.name}
            </p>
        </div>
    </div>

    <p class="mt-3 text-sm text-gray-500">
        Check the criteria below match your brief, then continue.
    </p>

    <SectionLabel class="mt-4 mb-2">
        Criteria · {judge.marking_points.length}
    </SectionLabel>

    {#if judge.marking_points.length === 0}
        <p class="text-sm text-gray-400 italic">No marking points set.</p>
    {:else}
        <NumberedList>
            {#each judge.marking_points as marking_point, i (marking_point.id)}
                <NumberedListItem number={i + 1}>
                    {marking_point.description}
                </NumberedListItem>
            {/each}
        </NumberedList>
    {/if}

    <Form action={confirmJudgePost({ competition, serc })} class="mt-6">
        {#snippet children({ processing })}
            <input type="hidden" name="judge" value={judge.id} />
            <Button
                type="submit"
                label="Continue"
                icon={ArrowRight}
                loading={processing}
                class="w-full"
            />
        {/snippet}
    </Form>
</section>
