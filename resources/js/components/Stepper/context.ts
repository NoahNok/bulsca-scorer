import type { Snippet, SvelteComponent } from "svelte";

export const STEPPER_CONTEXT = Symbol("stepper");

export type StepControls = {
    setTitle: (title: string) => void;
    complete: () => void;
    next: () => void;
};

export type StepperApi = {
    setActiveStep: (index: number) => void;
    next: () => void;
    previous: () => void;
    completeStep: (index: number) => void;
    getStepControls: (index: number) => StepControls | null
};

export type RegisteredStep = {
    title: string;
    content: Snippet<[StepControls]>;
    controls: StepControls;
    id: number;
    completed: boolean;
    tab: HTMLElement
};

export type StepperContext = {
    register: (
        step: Omit<RegisteredStep, "controls" | "id" | "completed" | "tab">,
    ) => {
        controls: StepControls;
        destroy: () => void;
    };
};