<script module lang="ts">
    export const layout = {
        title: "Select Tank",
    };
</script>

<script lang="ts">
    import {
        markOOF,
        markTime,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Event/EventJudgeController";

    import {
        index,
        home,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { setTank } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import ConditionalLink from "@/components/ConditionalLink.svelte";
    import HeatSelector from "@/components/Judging/Speed/HeatSelector.svelte";

    import { appState } from "@/lib/stores/appState";
    import { toastInfo } from "@/lib/toast.svelte";

    import type { Competition, Event, Heat } from "@/types/base";
    import { page, Link } from "@inertiajs/svelte";
    import { ArrowRight, Check, House } from "@lucide/svelte";

    let {
        competition,
        event,
        heats,
    }: {
        competition: Competition;
        event: Event;
        heats: Heat[];
    } = $props();
</script>

<AppHead title="Select Heat - OOF - {event.name} - {competition.name}" />

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    <Link
        href={home({ competition: competition })}
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
    <h2 class="">Order of Finish - {event.name}</h2>
    <br />

    <div class="flex flex-col space-y-3">
        <h2 class="font-bold text-center w-full break-words">Select a Heat</h2>

        <p class="">
            Please select a heat to submit, it'll turn green once complete
            (unless no-one finished a heat).
        </p>

        <HeatSelector
            {heats}
            href={markOOF}
            params={{ competition: competition, event: event }}
        />

        <Link href={home(competition)}
            ><Button label="Back" variant="danger" class="w-full py-1!" /></Link
        >
    </div>
</section>
