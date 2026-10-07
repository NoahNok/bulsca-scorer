<script module lang="ts">
    export const layout = {
        title: "Confirm Judge",
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import {
        getDrawFor,
        getEventRelatedCodes,
        getHeatsFor,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Violation/ViolationController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
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
        type Entity,
        EventType,
        type Competition,
        type Event,
        type Heat,
        type Tank,
    } from "@/types/base";
    import {
        emptySubmission,
        type ViolationSubmissionPost,
        type Violation,
        type ViolationSubmission,
    } from "@/types/violation";
    import {
        page,
        Link,
        useForm,
        useHttp,
        Form,
        router,
    } from "@inertiajs/svelte";
    import { ArrowLeft, Check, House, Search } from "@lucide/svelte";
    import { onMount, tick } from "svelte";
    import { slide } from "svelte/transition";

    type ViolationCollection = {
        dqs: Violation[];
        pens: Violation[];
    };

    const user = $derived(page.props.auth.user);

    let stepperRef: StepperApi | undefined;

    let {
        competition,
        sercs,
        speeds,
    }: {
        competition: Competition;
        sercs: Event[];
        speeds: Event[];
    } = $props();

    const form = useForm<ViolationSubmissionPost>(emptySubmission());

    let selectedEvent = $state<Event>();
    let selectedViolation = $state<Violation>();

    function selectEvent(event: Event, step_controls: StepControls) {
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
        let prefix = violation.vtype === "DQ" ? "DQ" : "P";
        step_controls.setTitle(`${prefix}${violation.code}`);
        step_controls.complete();
    }

    function submit(e) {
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

        form.post("");
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

                    // for testing only
                    setTimeout(() => {
                        let stepControls = stepperRef?.getStepControls(1);
                        let entity =
                            heats[0].lanes?.at(0)?.entity ||
                            tanks[0].draw[0].entity;
                        if (entity && stepControls) {
                            selectEntity(entity, stepControls);
                        }
                    }, 100);
                }
            }
        }
    });

    let expanded = $state<boolean>(false);

    type EntityRow = { position: number; entity: Entity };

    function codeTileClass(vtype: Violation["vtype"] | undefined) {
        return vtype === "DQ"
            ? "bg-red-100 text-red-700"
            : "bg-orange-100 text-orange-700";
    }
</script>

<AppHead title="Issue - DQ/Penalty - {competition.name}" />

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
    <h2 class="">New DQ/Penalty</h2>

    <Link
        href={violation.submissions({ competition: competition })}
        class="mt-2 mb-4 inline-flex w-fit items-center gap-1 text-sm text-gray-600 hover:text-se"
        ><ArrowLeft size={14} /> All submissions</Link
    >

    <Stepper bind:this={stepperRef}>
        <Step title="Event">
            {#snippet children(step_controls)}
                {@render eventGroup("SERCs", sercs, step_controls)}
                {@render eventGroup("Speeds", speeds, step_controls)}
            {/snippet}
        </Step>

        <Step title="Competitor">
            {#snippet children(step_controls)}
                {@render searchInput(
                    "Search teams...",
                    () => entitySearchTerm,
                    (v) => (entitySearchTerm = v),
                )}

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
                <div class="mb-3 grid grid-cols-2 gap-1 rounded-lg bg-gray-100 p-1">
                    {#each [{ value: "dq", label: "DQs" }, { value: "pen", label: "Penalties" }] as const as option}
                        <button
                            type="button"
                            class="cursor-pointer rounded-md py-1.5 text-sm font-semibold transition-all {violationFilter ===
                            option.value
                                ? 'bg-white shadow-sm'
                                : 'text-gray-500 hover:text-gray-800'}"
                            onclick={() => (violationFilter = option.value)}
                        >
                            {option.label}
                        </button>
                    {/each}
                </div>

                {@render searchInput(
                    "Search code or description...",
                    () => violationSearchTerm,
                    (v) => (violationSearchTerm = v),
                )}

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
                                <span
                                    class="flex size-12 shrink-0 items-center justify-center rounded-lg font-archivo font-bold {codeTileClass(
                                        v.vtype,
                                    )}"
                                >
                                    {v.vtype === "DQ" ? "DQ" : "P"}{v.code}
                                </span>
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
                    <!-- svelte-ignore a11y_click_events_have_key_events -->
                    <!-- svelte-ignore a11y_no_static_element_interactions -->
                    <div
                        class="col-span-2 flex cursor-pointer items-start gap-3 rounded-xl bg-gray-50 p-3"
                        onclick={() => (expanded = !expanded)}
                    >
                        <span
                            class="flex size-12 shrink-0 items-center justify-center rounded-lg font-archivo font-bold {codeTileClass(
                                selectedViolation.vtype,
                            )}"
                        >
                            {selectedViolation.vtype === "DQ"
                                ? "DQ"
                                : "P"}{selectedViolation.code}
                        </span>
                        <p
                            class="text-sm text-gray-700"
                            class:line-clamp-3={!expanded}
                        >
                            {selectedViolation.description}
                        </p>
                    </div>
                {/if}

                {#if selectedEvent?.type === EventType.SPEED}
                    {@render formHeading("Where")}
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

                {@render formHeading("What happened")}
                <div class="col-span-2">
                    <Input
                        variant="soft"
                        type="textarea"
                        name="violation-details"
                        placeholder="Describe what you saw..."
                        bind:value={form.details.details}
                    />
                </div>

                {@render formHeading("Submitted by")}
                <div class="col-span-2">
                    <Input
                        variant="soft"
                        label="Your role"
                        placeholder="Turn/Lane/SERC/etc..."
                        bind:value={form.submitter.position}
                        required
                    />
                </div>

                {@render formHeading("Seconded by")}
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
                    label="Submit"
                    variant="success"
                    class="col-span-2 mt-2 w-full"
                    icon={Check}
                    type="submit"
                    loading={form.processing}
                />
            </form>
        </Step>
    </Stepper>
</section>

{#snippet formHeading(text: string)}
    <p
        class="col-span-2 -mb-1 mt-2 text-xs font-semibold uppercase tracking-wide text-gray-500"
    >
        {text}
    </p>
{/snippet}

{#snippet eventGroup(
    heading: string,
    events: Event[],
    step_controls: StepControls,
)}
    {#if events.length}
        <p
            class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500"
        >
            {heading}
        </p>
        <div class="mb-4 grid grid-cols-2 gap-2 last:mb-0">
            {#each events as event}
                {@const selected =
                    selectedEvent?.id === event.id &&
                    selectedEvent?.type === event.type}
                <button
                    type="button"
                    class="flex cursor-pointer items-center justify-between gap-2 rounded-lg border bg-white px-3 py-2.5 text-left text-sm font-semibold transition-all hover:border-se {selected
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
        <p
            class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500"
        >
            {heading}
        </p>
        <div class="mb-4 divide-y overflow-hidden rounded-lg border">
            {#each matching as row (row.entity.id)}
                {@const selected = form.entity_id === row.entity.id}
                <button
                    type="button"
                    class="flex w-full cursor-pointer items-center gap-3 px-3 py-2 text-left transition-colors hover:bg-gray-50 {selected
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

{#snippet searchInput(
    placeholder: string,
    get: () => string,
    set: (v: string) => void,
)}
    <div class="relative mb-3">
        <Search
            size={16}
            class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-gray-400"
        />
        <input
            type="search"
            {placeholder}
            class="w-full rounded-lg border py-2 pr-3 pl-9 text-sm transition-all focus:border-se focus:ring-1 focus:ring-se/10 focus:outline-none"
            bind:value={get, set}
        />
    </div>
{/snippet}
