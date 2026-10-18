<script lang="ts">
    import { getContext, onMount } from "svelte";
    import type { Snippet } from "svelte";

    import {
        STEPPER_CONTEXT,
        type StepControls,
        type StepperContext,
    } from "./context";

    let {
        title,
        children,
    }: {
        title: string;
        children: Snippet<[StepControls]>;
    } = $props();

    const stepper = getContext<StepperContext>(STEPPER_CONTEXT);

    onMount(() => stepper.register({ title, content: children }).destroy);
</script>
