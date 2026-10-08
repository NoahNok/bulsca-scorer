<script module lang="ts">
    import { LifeBuoy } from "@lucide/svelte";

    export const layout = {
        title: "SERC",
        header: { icon: LifeBuoy, href: null },
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import {
        addJudge,
        detachJudge,
        markEntity,
        nextEntityToMark,
        selectTank,
        storeOverallNotes,
    } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import GenericDialog from "@/components/GenericDialog.svelte";
    import Input from "@/components/input.svelte";
    import NumberedList from "@/components/NumberedList/NumberedList.svelte";
    import NumberedListItem from "@/components/NumberedList/NumberedListItem.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import type { Competition, Draw, Judge, SERC } from "@/types/base";
    import { FlashActionType } from "@/types/flash";
    import { page, Link, Form } from "@inertiajs/svelte";
    import {
        ArrowRight,
        ClipboardList,
        EyeOff,
        Info,
        Plus,
        Save,
        Shuffle,
        X,
    } from "@lucide/svelte";

    let {
        competition,
        serc,
        judges,
        tank,
        draws,
    }: {
        competition: Competition;
        serc: SERC;
        judges: Judge[];
        tank?: number;
        draws?: Draw[];
    } = $props();

    const isHead = $derived(page.props.judge.isHeadRef);
    const showTeamNames = $derived(
        competition.show_teams_to_judges || isHead,
    );

    let isOverallNotesModalOpen = $state<boolean>(
        page.flash.action?.type == FlashActionType.OVERALL_NOTES,
    );
    let overallNote = $state<string>(page.flash.action?.data ?? "");
</script>

<AppHead title="{serc.name} - {competition.name}" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{serc.name}</h2>

    {#if tank}
        <BackLink
            href={selectTank({ competition, serc })}
            label="Change tank"
            class="mt-2 mb-4"
        />
    {:else}
        <BackLink href={home(competition)} label="All events" class="mt-2 mb-4" />
    {/if}

    <SectionLabel class="mb-2">Judging · {judges.length}</SectionLabel>
    <div class="divide-y overflow-hidden rounded-xl border bg-white shadow-sm">
        {#each judges as judge (judge.id)}
            <div class="flex items-center gap-3 px-3 py-2.5">
                <span
                    class="flex size-8 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-600"
                >
                    <ClipboardList size={16} />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="font-archivo truncate text-sm">{judge.name}</p>
                    <p class="text-xs text-gray-500">
                        {judge.marking_points?.length} marking points
                    </p>
                </div>

                {#if judges.length == 1}
                    <Link
                        href={addJudge(
                            { competition, serc },
                            { query: { swap: true } },
                        )}
                        aria-label="Swap {judge.name}"
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-se/10 text-teal-700 transition-colors hover:bg-se/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                    >
                        <Shuffle size={16} />
                    </Link>
                {:else}
                    <Link
                        only={["judges"]}
                        href={detachJudge({ competition, serc, judge })}
                        aria-label="Remove {judge.name}"
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-700 transition-colors hover:bg-red-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400"
                    >
                        <X size={16} />
                    </Link>
                {/if}
            </div>
        {/each}

        <Link
            href={addJudge({ competition, serc })}
            class="group flex items-center gap-3 px-3 py-2.5 text-teal-700 transition-colors hover:bg-se/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset"
        >
            <span
                class="flex size-8 shrink-0 items-center justify-center rounded-md border border-dashed border-se/60"
            >
                <Plus size={16} />
            </span>
            <span class="font-archivo flex-1 text-sm">Add casualty/objective</span>
        </Link>
    </div>

    <Link href={nextEntityToMark({ competition, serc })} class="mt-6">
        <Button
            icon={ArrowRight}
            label="Start Judging{tank ? ` Tank ${tank}` : ''}"
            class="w-full"
        />
    </Link>

    <Button
        label="Tutorial"
        icon={Info}
        variant="secondary"
        class="mt-2 w-full py-1.5"
    />

    <SectionLabel class="mt-6 mb-2">
        {tank ? `Tank ${tank} order` : "Order"} · {draws?.length ?? 0}
    </SectionLabel>

    {#if showTeamNames}
        <NumberedList>
            {#each draws ?? [] as draw (draw.entity.id)}
                <NumberedListItem
                    number={draw.draw}
                    href={isHead
                        ? markEntity({
                              competition,
                              serc,
                              entity_id: draw.entity.id,
                          })
                        : undefined}
                >
                    <span class="block truncate font-medium text-gray-900">
                        {draw.entity.name}
                    </span>
                    {#snippet trailing()}
                        {#if isHead}
                            <span class="text-xs text-gray-400">Edit</span>
                        {/if}
                    {/snippet}
                </NumberedListItem>
            {/each}
        </NumberedList>
    {:else}
        <div
            class="flex items-start gap-3 rounded-xl bg-gray-50 p-4 text-sm text-gray-700"
        >
            <EyeOff size={16} class="mt-0.5 shrink-0 text-gray-400" />
            <p>
                There are <strong>{draws?.length ?? 0}</strong> SERCs to mark.
                Team names are hidden from judges at this competition.
            </p>
        </div>
    {/if}
</section>

<GenericDialog title="Overall Notes" bind:open={isOverallNotesModalOpen}>
    <Form
        action={storeOverallNotes({ competition, serc })}
        disableWhileProcessing={true}
        id="overall-notes"
        onFinish={() => {
            isOverallNotesModalOpen = false;
        }}
        options={{
            only: [],
        }}
    >
        <Input
            type="textarea"
            variant="soft"
            rows={5}
            name="note"
            placeholder="Type overall feedback here, or leave it blank..."
            bind:value={overallNote}
        />
    </Form>
    {#snippet footer()}
        <Button label="Save" icon={Save} type="submit" form="overall-notes" />
    {/snippet}
</GenericDialog>
