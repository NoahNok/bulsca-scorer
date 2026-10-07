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
    import { Check, ChevronDown } from "@lucide/svelte";

    let {
        children,
        ...restProps
    }: {
        children: Snippet;
    } = $props();

    let activeStep = $state("0");
    let registeredSteps = $state<RegisteredStep[]>([]);
    let nextStepId = 0;

    // A step can only be opened once every step before it is complete
    function isReachable(index: number): boolean {
        return registeredSteps.slice(0, index).every((step) => step.completed);
    }

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
                label: step.title,
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
    class="flex flex-col gap-3"
>
    {@render children()}

    {#each registeredSteps as step, index}
        {@const open = activeStep === `${index}`}
        {@const locked = !isReachable(index)}
        <Accordion.Item
            value={`${index}`}
            disabled={locked}
            class="overflow-hidden rounded-xl border bg-white shadow-sm transition-all {open
                ? 'border-se'
                : ''} {locked ? 'opacity-50' : ''}"
        >
            <Accordion.Header>
                <Accordion.Trigger
                    class="flex w-full cursor-pointer items-center gap-3 p-3 text-left disabled:cursor-not-allowed"
                    id={`step-${step.id}`}
                >
                    <span
                        class="flex size-7 shrink-0 items-center justify-center rounded-full text-sm font-semibold transition-colors {step.completed
                            ? 'bg-green-500 text-white'
                            : open
                              ? 'bg-se text-white'
                              : 'bg-gray-100 text-gray-500'}"
                    >
                        {#if step.completed}
                            <Check size={16} strokeWidth={3} />
                            <span class="sr-only">Completed</span>
                        {:else}
                            {index + 1}
                        {/if}
                    </span>

                    <span class="min-w-0 flex-1">
                        {#if step.completed && step.title !== step.label}
                            <span class="block text-xs text-gray-500"
                                >{step.label}</span
                            >
                        {/if}
                        <span class="block truncate font-semibold"
                            >{step.title}</span
                        >
                    </span>

                    <ChevronDown
                        size={18}
                        class="shrink-0 text-gray-400 transition-transform duration-300 {open
                            ? 'rotate-180'
                            : ''}"
                    />
                </Accordion.Trigger>
            </Accordion.Header>
            <Accordion.Content
                class="data-[state=closed]:animate-accordion-up data-[state=open]:animate-accordion-down overflow-hidden"
            >
                <div class="border-t p-3" bind:this={step.tab}>
                    {@render step.content(step.controls)}
                </div>
            </Accordion.Content>
        </Accordion.Item>
    {/each}
</Accordion.Root>
