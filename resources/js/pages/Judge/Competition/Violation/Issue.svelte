<script module lang="ts">
    export const layout = {
        title: "New DQ/Penalty",
    };
</script>

<script lang="ts">
    import {
        getDrawFor,
        getEventRelatedCodes,
        getHeatsFor,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Violation/ViolationController";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import ViolationCodeTile from "@/components/Judging/Violation/ViolationCodeTile.svelte";
    import SearchInput from "@/components/SearchInput.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import SegmentedControl from "@/components/SegmentedControl.svelte";
    import Spinner from "@/components/Spinner.svelte";
    import type {
        StepControls,
        StepperApi,
    } from "@/components/Stepper/context";
    import Step from "@/components/Stepper/Step.svelte";
    import Stepper from "@/components/Stepper/Stepper.svelte";
    import { toastError } from "@/lib/toast.svelte";
    import violation from "@/routes/judge/competition/violation";
    import {
        resubmit,
        view as viewSubmission,
    } from "@/routes/judge/competition/violation/submission";

    import {
        type Entity,
        EventType,
        type Competition,
        type Event,
        type Heat,
        type Tank,
    } from "@/types/base";
    import {
        emptySubmission,
        submissionToPost,
        violationCode,
        vtypeTileClass,
        type ViolationSubmission,
        type ViolationSubmissionPost,
        type Violation,
    } from "@/types/violation";
    import { useForm, useHttp } from "@inertiajs/svelte";
    import { Check, ChevronDown } from "@lucide/svelte";
    import { onMount, tick, untrack } from "svelte";
    import { slide } from "svelte/transition";

    type ViolationCollection = {
        dqs: Violation[];
        pens: Violation[];
    };

    let stepperRef: StepperApi | undefined;

    let {
        competition,
        sercs,
        speeds,
        submission,
    }: {
        competition: Competition;
        sercs: Event[];
        speeds: Event[];
        // set when editing a rejected submission to resubmit it
        submission?: ViolationSubmission;
    } = $props();

    // The form only takes the submission it opened with
    const initial = untrack(() => submission);
    const editing = !!initial;

    const form = useForm<ViolationSubmissionPost>(
        initial ? submissionToPost(initial) : emptySubmission(),
    );

    let selectedEvent = $state<Event>();
    let selectedViolation = $state<Violation>();

    function selectEvent(event: Event, step_controls: StepControls) {
        // a competitor or code picked for another event no longer applies
        if (
            selectedEvent &&
            (selectedEvent.id !== event.id || selectedEvent.type !== event.type)
        ) {
            form.entity_id = -1;
            form.violation = undefined;
            selectedViolation = undefined;
        }

        form.event = {
            id: event.id,
            type: event.type,
        };
        selectedEvent = event;
        step_controls.setTitle(event.name);
        step_controls.complete();

        if (event.type === EventType.SPEED) {
            loadHeatsFor(event);
        } else {
            loadDrawFor(event);
        }
    }

    function selectEntity(entity: Entity, step_controls: StepControls) {
        form.entity_id = entity.id;
        step_controls.setTitle(entity.name);
        step_controls.complete();

        loadViolations();
    }

    function selectViolation(
        violation: Violation,
        step_controls: StepControls,
    ) {
        form.violation = violation;
        selectedViolation = violation;
        step_controls.setTitle(violationCode(violation));
        step_controls.complete();
    }

    function submit(e: SubmitEvent) {
        e.preventDefault();

        if (!form.entity_id) {
            toastError("Please select an entity");
            return;
        } else if (!form.event) {
            toastError("Please select an event");
            return;
        } else if (!form.violation) {
            toastError("Please select a violation");
            return;
        }

        if (submission) {
            form.post(
                resubmit({ competition: competition, submission: submission })
                    .url,
            );
        } else {
            form.post("");
        }
    }

    let heats = $state<Heat[]>([]);
    let tanks = $state<Tank[]>([]);
    let violations = $state<ViolationCollection>({ dqs: [], pens: [] });

    let violationFilter = $state<"dq" | "pen">("dq");

    let entitySearchTerm = $state<string>("");
    let violationSearchTerm = $state<string>("");

    let entitySearchableTerm = $derived.by<string>(() => {
        return entitySearchTerm.toLowerCase().trim();
    });

    let violationSearchableTerm = $derived.by<string>(() => {
        return violationSearchTerm.toLowerCase().trim();
    });

    const heatsHttp = useHttp<{}, Heat[]>();
    const drawHttp = useHttp<{}, Tank[]>();
    const violationsHttp = useHttp<{}, ViolationCollection>();

    function loadHeatsFor(event: Event) {
        heatsHttp.get(
            getHeatsFor({ competition: competition, event: event }).url,
            {
                onSuccess(response, httpResponse) {
                    heats = response;
                },
            },
        );
    }

    function loadDrawFor(serc: Event) {
        drawHttp.get(getDrawFor({ competition: competition, serc: serc }).url, {
            onSuccess(response, httpResponse) {
                tanks = response;
            },
        });
    }

    function loadViolations() {
        let eventSlugPrefix =
            selectedEvent!.type == EventType.SPEED ? "sp" : "se";

        violationsHttp.get(
            getEventRelatedCodes({
                competition: competition,
                eventName: `${eventSlugPrefix}:${selectedEvent!.id}`,
            }).url,
            {
                onSuccess(response, httpResponse) {
                    violations = response;
                },
            },
        );
    }

    function searchMatchesEntity(entity: Entity) {
        if (entitySearchableTerm === "") {
            return true;
        }

        return entity.name.toLowerCase().trim().includes(entitySearchableTerm);
    }

    function searchMatchesViolation(violation: Violation) {
        if (violationSearchableTerm === "") {
            return true;
        }

        return (
            violation.code.toString().includes(violationSearchableTerm) ||
            violation.description
                .toLowerCase()
                .includes(violationSearchableTerm)
        );
    }

    onMount(async () => {
        await tick();

        if (submission) {
            prefillFrom(submission);
            return;
        }

        const searchParams = new URLSearchParams(window.location.search);
        let specifiedEvent = searchParams.get("event");

        if (specifiedEvent) {
            let event: Event | undefined;

            if (specifiedEvent.startsWith("sp-")) {
                let id = +specifiedEvent.slice(3);
                event = speeds.find((e) => e.id === id);
            } else if (specifiedEvent.startsWith("se-")) {
                let id = +specifiedEvent.slice(3);
                event = sercs.find((e) => e.id === id);
            }

            if (event) {
                let stepControls = stepperRef?.getStepControls(0);
                if (stepControls) {
                    selectEvent(event, stepControls);
                }
            }
        }
    });

    // Walk the steps with the submission's choices, which lands on Details
    // and loads the heats/draw and codes so any of them can still be changed
    function prefillFrom(existing: ViolationSubmission) {
        const steps = [0, 1, 2].map((i) => stepperRef?.getStepControls(i));
        if (steps.some((s) => !s)) {
            return;
        }

        selectEvent(existing.event, steps[0]!);
        selectEntity(existing.entity, steps[1]!);
        violationFilter = existing.violation.vtype === "DQ" ? "dq" : "pen";
        selectViolation(existing.violation, steps[2]!);
    }

    let expanded = $state<boolean>(false);

    type EntityRow = { position: number; entity: Entity };

</script>

<AppHead
    title="{editing ? 'Edit' : 'Issue'} - DQ/Penalty - {competition.name}"
/>


<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2 class="">{editing ? "Edit DQ/Penalty" : "New DQ/Penalty"}</h2>

    {#if submission}
        <BackLink
            href={viewSubmission({
                competition: competition,
                submission: submission,
            })}
            label="Back to submission"
            class="mt-2 mb-4"
        />
    {:else}
        <BackLink
            href={violation.submissions({ competition: competition })}
            label="All submissions"
            class="mt-2 mb-4"
        />
    {/if}

    <Stepper bind:this={stepperRef}>
        <Step title="Event">
            {#snippet children(step_controls)}
                {@render eventGroup("SERCs", sercs, step_controls)}
                {@render eventGroup("Speeds", speeds, step_controls)}
            {/snippet}
        </Step>

        <Step title="Competitor">
            {#snippet children(step_controls)}
                <SearchInput
                    class="mb-3"
                    placeholder="Search teams..."
                    bind:value={entitySearchTerm}
                />

                {#if heatsHttp.processing || drawHttp.processing}
                    <div class="flex justify-center py-6"><Spinner /></div>
                {/if}

                {#if heats && form.event?.type === EventType.SPEED && heatsHttp.wasSuccessful}
                    <div transition:slide>
                        {#each heats as heat}
                            {@render entityGroup(
                                `Heat ${heat.heat}`,
                                "Lane",
                                (heat.lanes ?? []).map((l) => ({
                                    position: l.lane,
                                    entity: l.entity,
                                })),
                                step_controls,
                            )}
                        {/each}
                    </div>
                {/if}

                {#if tanks && form.event?.type === EventType.SERC && drawHttp.wasSuccessful}
                    <div transition:slide>
                        {#each tanks as tank}
                            {@render entityGroup(
                                `Tank ${tank.tank}`,
                                "Draw",
                                tank.draw.map((d) => ({
                                    position: d.draw,
                                    entity: d.entity,
                                })),
                                step_controls,
                            )}
                        {/each}
                    </div>
                {/if}
            {/snippet}
        </Step>

        <Step title="Code">
            {#snippet children(step_controls)}
                <SegmentedControl
                    class="mb-3"
                    options={[
                        { value: "dq", label: "DQs" },
                        { value: "pen", label: "Penalties" },
                    ]}
                    bind:value={violationFilter}
                />

                <SearchInput
                    class="mb-3"
                    placeholder="Search code or description..."
                    bind:value={violationSearchTerm}
                />

                {#if violationsHttp.processing}
                    <div class="flex justify-center py-6"><Spinner /></div>
                {/if}

                {#if violations && violationsHttp.wasSuccessful}
                    <div class="flex max-h-100 flex-col gap-2 overflow-y-auto p-0.5">
                        {#each violationFilter === "dq" ? violations.dqs : violations.pens as v (v.id)}
                            {@const selected =
                                selectedViolation?.id === v.id &&
                                selectedViolation?.vtype === v.vtype}
                            <button
                                type="button"
                                class="group flex w-full cursor-pointer items-center gap-3 rounded-xl border bg-white p-3 text-left transition-all hover:border-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se {selected
                                    ? 'border-se bg-se/5'
                                    : ''}"
                                onclick={() => selectViolation(v, step_controls)}
                                hidden={!searchMatchesViolation(v)}
                            >
                                <ViolationCodeTile
                                    code={violationCode(v)}
                                    class={vtypeTileClass(v.vtype)}
                                />
                                <span class="flex-1 text-sm text-gray-700">
                                    {v.description}
                                </span>
                                {#if selected}
                                    <Check size={18} class="shrink-0 text-se" />
                                {/if}
                            </button>
                        {/each}
                    </div>
                {/if}
            {/snippet}
        </Step>

        <Step title="Details">
            <form onsubmit={submit} class="grid grid-cols-2 gap-x-3 gap-y-3">
                {#if selectedViolation}
                    <button
                        type="button"
                        aria-expanded={expanded}
                        class="col-span-2 flex cursor-pointer items-start gap-3 rounded-xl bg-gray-50 p-3 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                        onclick={() => (expanded = !expanded)}
                    >
                        <ViolationCodeTile
                            code={violationCode(selectedViolation)}
                            class={vtypeTileClass(selectedViolation.vtype)}
                        />
                        <span
                            class="flex-1 text-sm text-gray-700"
                            class:line-clamp-3={!expanded}
                        >
                            {selectedViolation.description}
                        </span>
                        <ChevronDown
                            size={16}
                            class="mt-0.5 shrink-0 text-gray-400 transition-transform {expanded
                                ? 'rotate-180'
                                : ''}"
                        />
                    </button>
                {/if}

                {#if selectedEvent?.type === EventType.SPEED}
                    <SectionLabel class="col-span-2 -mb-1 mt-2">Where</SectionLabel>
                    <Input
                        variant="soft"
                        label="Length"
                        type="number"
                        inputmode="numeric"
                        placeholder="–"
                        bind:value={form.details.length}
                    />
                    <Input
                        variant="soft"
                        label="Turn"
                        type="number"
                        inputmode="numeric"
                        placeholder="–"
                        bind:value={form.details.turn}
                    />
                {/if}

                <SectionLabel class="col-span-2 -mb-1 mt-2">What happened</SectionLabel>
                <div class="col-span-2">
                    <Input
                        variant="soft"
                        type="textarea"
                        name="violation-details"
                        placeholder="Describe what you saw..."
                        bind:value={form.details.details}
                    />
                </div>

                <SectionLabel class="col-span-2 -mb-1 mt-2">Submitted by</SectionLabel>
                <div class="col-span-2">
                    <Input
                        variant="soft"
                        label="Your role"
                        placeholder="Turn/Lane/SERC/etc..."
                        bind:value={form.submitter.position}
                        required
                    />
                </div>

                <SectionLabel class="col-span-2 -mb-1 mt-2">Seconded by</SectionLabel>
                <Input
                    variant="soft"
                    label="Name"
                    placeholder="Their name"
                    bind:value={form.seconder.name}
                />
                <Input
                    variant="soft"
                    label="Role"
                    placeholder="Their role"
                    bind:value={form.seconder.position}
                />

                <Button
                    label={editing ? "Resubmit" : "Submit"}
                    class="col-span-2 mt-2 w-full"
                    icon={Check}
                    type="submit"
                    loading={form.processing}
                />
            </form>
        </Step>
    </Stepper>
</section>

{#snippet eventGroup(
    heading: string,
    events: Event[],
    step_controls: StepControls,
)}
    {#if events.length}
        <SectionLabel class="mb-2">{heading}</SectionLabel>
        <div class="mb-4 grid grid-cols-2 gap-2 last:mb-0">
            {#each events as event}
                {@const selected =
                    selectedEvent?.id === event.id &&
                    selectedEvent?.type === event.type}
                <button
                    type="button"
                    class="flex cursor-pointer items-center justify-between gap-2 rounded-lg border bg-white px-3 py-2.5 text-left text-sm font-semibold transition-all hover:border-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se {selected
                        ? 'border-se bg-se/5'
                        : ''}"
                    onclick={() => selectEvent(event, step_controls)}
                >
                    <span class="truncate">{event.name}</span>
                    {#if selected}
                        <Check size={16} class="shrink-0 text-se" />
                    {/if}
                </button>
            {/each}
        </div>
    {/if}
{/snippet}

{#snippet entityGroup(
    heading: string,
    positionLabel: string,
    rows: EntityRow[],
    step_controls: StepControls,
)}
    {@const matching = rows.filter((r) => searchMatchesEntity(r.entity))}
    {#if matching.length}
        <SectionLabel class="mb-2">{heading}</SectionLabel>
        <div class="mb-4 divide-y overflow-hidden rounded-xl border bg-white">
            {#each matching as row (row.entity.id)}
                {@const selected = form.entity_id === row.entity.id}
                <button
                    type="button"
                    class="flex w-full cursor-pointer items-center gap-3 px-3 py-2 text-left transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset {selected
                        ? 'bg-se/5'
                        : 'bg-white'}"
                    onclick={() => selectEntity(row.entity, step_controls)}
                >
                    <span
                        class="flex h-8 min-w-8 shrink-0 items-center justify-center rounded-md px-1 bg-gray-100 text-sm font-semibold text-gray-600"
                        title="{positionLabel} {row.position}"
                    >
                        {positionLabel === "Lane" ? "L" : ""}{row.position}
                    </span>
                    <span class="flex-1 truncate text-sm font-medium"
                        >{row.entity.name}</span
                    >
                    {#if selected}
                        <Check size={16} class="shrink-0 text-se" />
                    {/if}
                </button>
            {/each}
        </div>
    {/if}
{/snippet}

