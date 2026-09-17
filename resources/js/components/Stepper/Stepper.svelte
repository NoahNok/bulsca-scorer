<script lang="ts">
    import { Accordion } from "bits-ui";
    import { setContext, type Snippet } from "svelte";

    import {
        STEPPER_CONTEXT,
        type RegisteredStep,
        type StepControls,
        type StepperContext,
        type StepperApi,
    } from "./context";
    import Button from "../Button.svelte";
    import { Check, ChevronDown, Shuffle } from "@lucide/svelte";

    let {
        children,
        ...restProps
    }: {
        children: Snippet;
    } = $props();

    let activeStep = $state("0");
    let registeredSteps = $state<RegisteredStep[]>([]);
    let nextStepId = 0;

    export function setActiveStep(index: number): void {
        if (registeredSteps.length === 0) {
            return;
        }

        const safeIndex = Math.max(
            0,
            Math.min(index, registeredSteps.length - 1),
        );
        activeStep = `${safeIndex}`;
    }

    export function next(): void {
        const current = Number(activeStep);

        if (!Number.isNaN(current) && current < registeredSteps.length - 1) {
            activeStep = `${current + 1}`;
        }
    }

    export function previous(): void {
        const current = Number(activeStep);

        if (!Number.isNaN(current) && current > 0) {
            activeStep = `${current - 1}`;
        }
    }

    export function completeStep(index: number): void {
        if (index >= 0 && index < registeredSteps.length) {
            registeredSteps[index].completed = true;
        }
    }

    export function getStepControls(index: number): StepControls | null {
        if (index >= 0 && index < registeredSteps.length) {
            return registeredSteps[index].controls;
        }

        return null;
    }

    const context: StepperContext = {
        register(step) {
            const registeredStepId = nextStepId++;
            const registeredStep = {
                ...step,
                id: registeredStepId,
                completed: false,
            } as RegisteredStep;

            const findStep = () =>
                registeredSteps.findIndex(
                    (step) => step.id === registeredStepId,
                );

            const controls: StepControls = {
                setTitle(title) {
                    const index = findStep();

                    if (index !== -1) {
                        registeredSteps[index].title = title;
                    }
                },
                complete() {
                    const index = findStep();

                    if (index !== -1) {
                        registeredSteps[index].completed = true;

                        if (index < registeredSteps.length - 1) {
                            activeStep = `${index + 1}`;
                        }
                    }
                },
                next() {
                    const index = findStep();

                    if (index !== -1) {
                        activeStep = `${index + 1}`;
                    }
                },
            };

            registeredStep.controls = controls;
            registeredSteps.push(registeredStep);

            return {
                controls,
                destroy() {
                    const index = findStep();

                    if (index !== -1) {
                        registeredSteps.splice(index, 1);
                    }
                },
            };
        },
    };

    setContext(STEPPER_CONTEXT, context);
</script>

<Accordion.Root
    bind:value={activeStep}
    {...restProps as any}
    type="single"
    collapsible
>
    {@render children()}

    {#each registeredSteps as step, index}
        <Accordion.Item value={`${index}`} class="group-data mb-3">
            <Accordion.Header>
                <Accordion.Trigger class="flex w-full transition-all">
                    <Button
                        label={step.title}
                        variant={step.completed ? "success" : "white"}
                        class="w-full text-left"
                        icon={step.completed ? Shuffle : ChevronDown}
                        iconClass="ml-auto transition-transform duration-300 {activeStep ===
                        `${index}`
                            ? 'rotate-180'
                            : ''}"
                        id={`step-${step.id}`}
                    >
                        {#snippet before()}
                            <p class="pr-3 border-r mr-1">{index + 1}</p>
                        {/snippet}
                    </Button>

                    {#if step.completed}
                        <span class="sr-only">Completed</span>
                    {/if}
                </Accordion.Trigger>
            </Accordion.Header>
            <Accordion.Content
                class="  data-[state=closed]:animate-accordion-up data-[state=open]:animate-accordion-down overflow-hidden text-sm tracking-[-0.01em]"
            >
                <div class="px-4 py-2" bind:this={step.tab}>
                    {@render step.content(step.controls)}
                </div>
            </Accordion.Content>
        </Accordion.Item>
    {/each}
</Accordion.Root>
