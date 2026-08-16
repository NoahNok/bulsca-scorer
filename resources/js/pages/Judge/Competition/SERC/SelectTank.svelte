<script module lang="ts">
    export const layout = {
        title: "Select Tank",
    };
</script>

<script lang="ts">
    import {
        index,
        home,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { setTank } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";

    import { appState } from "@/lib/stores/appState";

    import type { Competition, Judge, SERC } from "@/types/base";
    import { Form, page, Link } from "@inertiajs/svelte";
    import {
        ArrowRight,
        Check,
        Clipboard,
        House,
        LifeBuoy,
    } from "@lucide/svelte";

    const user = $derived(page.props.auth.user);

    let {
        competition,
        serc,
        tanks,
    }: {
        competition: Competition;
        serc: SERC;
        tanks: number[];
    } = $props();
</script>

<AppHead title="Dashboard" />

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
    <h2 class="">{serc.name}</h2>
    <br />

    <div class="flex flex-col space-y-3">
        <h2 class="font-bold text-center w-full break-words">Select a Tank</h2>

        <p class="">Please select which tank you are marking</p>

        <div class="flex flex-col space-y-3 w-full">
            {#each tanks as tank}
                <Link
                    href={setTank({
                        competition: competition,
                        serc: serc,
                        tank: tank,
                    })}
                    ><Button
                        label="Tank {tank}"
                        variant="success"
                        icon={ArrowRight}
                        class="w-full"
                    /></Link
                >
            {/each}
        </div>

        <Link href={home(competition)}
            ><Button label="Back" variant="danger" class="w-full py-1!" /></Link
        >
    </div>
</section>
