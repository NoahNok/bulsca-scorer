<script module lang="ts">
    export const layout = {
        title: "Confirm Judge",
    };
</script>

<script lang="ts">
    import {
        index,
        home,
        sercHome,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";

    import { appState } from "@/lib/stores/appState";
    import { attachJudge } from "@/routes/judge/competition/serc";

    import type { Competition, Draw, Judge, SERC } from "@/types/base";
    import { Form, page, Link } from "@inertiajs/svelte";
    import {
        ArrowRight,
        Check,
        Clipboard,
        Info,
        LifeBuoy,
        Plus,
        Shuffle,
    } from "@lucide/svelte";
    import { Label } from "bits-ui";

    const user = $derived(page.props.auth.user);

    let {
        competition,
        serc,
        judges,
        swap,
    }: {
        competition: Competition;
        serc: SERC;
        judges: Judge[];
        swap: boolean;
    } = $props();

    const showDraw = false;
    const isHead = false;
</script>

<AppHead title="Dashboard" />

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    <Link
        href={sercHome(competition)}
        class="flex w-full justify-between items-center"
    >
        <div class="">
            <h1 class="  -mb-3 normal-case! text-black! text-base!">Digital</h1>
            <h1 class=" indent-6 normal-case! text-se text-xl!">Judge</h1>
        </div>

        <LifeBuoy
            class="bg-se/20 rounded-full text-se p-2 shadow-md "
            size={40}
        />
    </Link>
</section>

<div class="h-16"></div>

<section class="flex flex-col h-full">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2 class="">{serc.name}</h2>

    <br />

    <div class="flex flex-col space-y-3">
        <p class="font-archivo -mb-2">
            {swap ? "Swap" : "Add"} Casualty/Objective
        </p>

        <br />
        <div class="space-y-2 w-full">
            {#each judges as judge}
                <Link
                    href={attachJudge(
                        {
                            competition: competition,
                            judge: judge,
                        },
                        {
                            query: swap
                                ? {
                                      swap: true,
                                  }
                                : undefined,
                        },
                    )}
                >
                    <div
                        class="border rounded-lg shadow-md p-4 group-hover:border-se focus:ring-1 focus:outline-none transition-all w-full"
                    >
                        <div class="flex items-center justify-between">
                            <div class="text-left">
                                <h3>{judge.name}</h3>
                                <p>{judge.no_marking_points} marking points</p>
                            </div>

                            {#if swap}
                                <Shuffle
                                    size={40}
                                    class="bg-se/20 rounded-md text-se p-2 shadow-md"
                                />
                            {:else}
                                <Plus
                                    size={40}
                                    class="bg-se/20 rounded-md text-se p-2 shadow-md"
                                />
                            {/if}
                        </div>
                    </div>
                </Link>
            {:else}
                <p>No other casualties/objectives to add.</p>
            {/each}
        </div>

        <Link href={sercHome(competition)}
            ><Button label="Back" variant="danger" class="w-full py-1!" /></Link
        >
    </div>
</section>
