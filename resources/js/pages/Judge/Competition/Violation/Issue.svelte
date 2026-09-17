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
    import { Check, House, Plus } from "@lucide/svelte";
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
        step_controls.setTitle(`Event: ${event.name}`);
        step_controls.complete();

        if (event.type === EventType.SPEED) {
            loadHeatsFor(event);
        } else {
            loadDrawFor(event);
        }
    }

    function selectEntity(entity: Entity, step_controls: StepControls) {
        form.entity_id = entity.id;
        step_controls.setTitle(`For: ${entity.name}`);
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
            violation.description.includes(violationSearchableTerm)
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
    <h2 class="">Issue DQ/Penalty</h2>
    <br />

    <Stepper bind:this={stepperRef}>
        <Step title="Event">
            {#snippet children(step_controls)}
                <p>Please select an event.</p>
                <h4>SERCs</h4>
                <div class="grid grid-cols-2 gap-4">
                    {#each sercs as serc}
                        <Button
                            label={serc.name}
                            variant={form.event?.id === serc.id
                                ? "success"
                                : "white"}
                            class="w-full mb-2"
                            onclick={() => selectEvent(serc, step_controls)}
                            icon={form.event?.id === serc.id ? Check : null}
                        />
                    {/each}
                </div>

                <hr class="spacer" />

                <h4>Speeds</h4>
                <div class="grid grid-cols-2 gap-4">
                    {#each speeds as speed}
                        <Button
                            label={speed.name}
                            variant={form.event?.id === speed.id
                                ? "success"
                                : "white"}
                            class="w-full mb-2"
                            onclick={() => selectEvent(speed, step_controls)}
                            icon={form.event?.id === speed.id ? Check : null}
                        />
                    {/each}
                </div>
            {/snippet}
        </Step>

        <Step title="For">
            {#snippet children(step_controls)}
                {#if heatsHttp.processing || drawHttp.processing}
                    <Spinner />
                {/if}

                <Input
                    placeholder="Search..."
                    class="mb-2"
                    bind:value={entitySearchTerm}
                />

                {#if heats && form.event?.type === EventType.SPEED && heatsHttp.wasSuccessful}
                    <div transition:slide>
                        {#each heats as heat}
                            <h3>Heat {heat.heat}</h3>
                            <div class="grid grid-cols-1 gap-y-2">
                                {#each heat.lanes as lane}
                                    <Button
                                        label={`${lane.lane}: ${lane.entity.name}`}
                                        variant={form.entity_id ===
                                        lane.entity.id
                                            ? "success"
                                            : "white"}
                                        class="w-full "
                                        icon={form.entity_id === lane.entity.id
                                            ? Check
                                            : null}
                                        onclick={() =>
                                            selectEntity(
                                                lane.entity,
                                                step_controls,
                                            )}
                                        hidden={!searchMatchesEntity(
                                            lane.entity,
                                        )}
                                    />
                                {/each}
                            </div>
                            <br />
                        {/each}
                    </div>
                {/if}

                {#if tanks && form.event?.type === EventType.SERC && drawHttp.wasSuccessful}
                    <div transition:slide>
                        {#each tanks as tank}
                            <h3>Tank {tank.tank}</h3>
                            <div class="grid grid-cols-1 gap-y-2">
                                {#each tank.draw as draw}
                                    <Button
                                        label={`${draw.draw}. ${draw.entity.name}`}
                                        variant={form.entity_id ===
                                        draw.entity.id
                                            ? "success"
                                            : "white"}
                                        class="w-full "
                                        icon={form.entity_id === draw.entity.id
                                            ? Check
                                            : null}
                                        onclick={() =>
                                            selectEntity(
                                                draw.entity,
                                                step_controls,
                                            )}
                                        hidden={!searchMatchesEntity(
                                            draw.entity,
                                        )}
                                    />
                                {/each}
                            </div>
                            <br />
                        {/each}
                    </div>
                {/if}
            {/snippet}
        </Step>
        <Step title="Code">
            {#snippet children(step_controls)}
                <Input
                    placeholder="Search... (code or description)"
                    class="mb-2"
                    bind:value={violationSearchTerm}
                />
                <div class="flex space-x-2 text-center mb-4">
                    <Button
                        label="DQs"
                        variant={violationFilter === "dq" ? "primary" : "white"}
                        class="py-1 w-full text-center"
                        onclick={() => (violationFilter = "dq")}
                    />
                    <Button
                        label="Penalties"
                        variant={violationFilter === "pen"
                            ? "primary"
                            : "white"}
                        class="py-1 w-full  "
                        onclick={() => (violationFilter = "pen")}
                    />
                </div>

                {#if violationsHttp.processing}
                    <Spinner />
                {/if}

                {#if violations && violationsHttp.wasSuccessful}
                    <div
                        class="grid grid-cols-1 gap-y-2 max-h-100 overflow-y-auto"
                    >
                        {#if violationFilter === "dq"}
                            {#each violations.dqs as dq}
                                <button
                                    class="border rounded-lg shadow-md p-4 group hover:border-se cursor-pointer focus:ring-1 focus:outline-none transition-all w-full"
                                    onclick={() =>
                                        selectViolation(dq, step_controls)}
                                    hidden={!searchMatchesViolation(dq)}
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="text-left max-w-[80%]">
                                            <h3>DQ{dq.code}</h3>
                                            <p class="">
                                                {dq.description} marking points
                                            </p>
                                        </div>

                                        <Plus
                                            size={40}
                                            class="bg-se/20 rounded-md text-se p-2 shadow-md"
                                        />
                                    </div>
                                </button>
                            {/each}
                        {/if}

                        {#if violationFilter === "pen"}
                            {#each violations.pens as pen}
                                <button
                                    class="border rounded-lg shadow-md p-4 group hover:border-se cursor-pointer focus:ring-1 focus:outline-none transition-all w-full"
                                    onclick={() =>
                                        selectViolation(pen, step_controls)}
                                    hidden={!searchMatchesViolation(pen)}
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="text-left max-w-[80%]">
                                            <h3>P{pen.code}</h3>
                                            <p class="">
                                                {pen.description} marking points
                                            </p>
                                        </div>

                                        <Plus
                                            size={40}
                                            class="bg-se/20 rounded-md text-se p-2 shadow-md"
                                        />
                                    </div>
                                </button>
                            {/each}
                        {/if}
                    </div>
                {/if}
            {/snippet}
        </Step>
        <Step title="Details">
            <form onsubmit={submit} class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <h2 class="text-red-500 font-semibold font-archivo">
                        {form.violation?.vtype === "DQ"
                            ? "DQ"
                            : "P"}{selectedViolation?.code}
                    </h2>

                    <!-- svelte-ignore a11y_click_events_have_key_events -->
                    <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
                    <p
                        class:line-clamp-3={!expanded}
                        onclick={() => (expanded = !expanded)}
                    >
                        {selectedViolation?.description}
                    </p>
                </div>

                {#if selectedEvent?.type === EventType.SPEED}
                    <Input
                        label="Turn"
                        type="number"
                        bind:value={form.details.turn}
                    />
                    <Input
                        label="Length"
                        type="number"
                        bind:value={form.details.length}
                    />
                {/if}

                <div class="col-span-2">
                    <label for="violation-details">Details</label>
                    <textarea
                        name="violation-details"
                        rows="5"
                        placeholder="Type details here..."
                        class="w-full border hover:border-gray-400 p-3 focus:border-gray-400 outline-hidden rounded-lg shadow-md"
                        id=""
                        bind:value={form.details.details}
                    ></textarea>
                </div>

                <hr class="spacer col-span-2" />

                <div class="col-span-2">
                    <Input
                        label="You're Role"
                        placeholder="Turn/Lane/SERC/etc..."
                        bind:value={form.submitter.position}
                        required
                    />
                </div>

                <Input label="Seconder" bind:value={form.seconder.name} />
                <Input label="Position" bind:value={form.seconder.position} />

                <hr class="spacer col-span-2" />

                <Button
                    label="Submit"
                    variant="success"
                    class="col-span-2 w-full"
                    icon={Check}
                    type="submit"
                />
            </form>
        </Step>
    </Stepper>
    <br />
    <br />
</section>
