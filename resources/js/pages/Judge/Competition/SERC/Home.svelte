<script module lang="ts">
    export const layout = {
        title: "Confirm Judge",
    };
</script>

<script lang="ts">
    import {
        index,
        home,
        addJudge,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";

    import { appState } from "@/lib/stores/appState";
    import { detachJudge } from "@/routes/judge/competition/serc";

    import type { Competition, Draw, Judge, SERC } from "@/types/base";
    import { Form, page, Link, router } from "@inertiajs/svelte";
    import {
        ArrowRight,
        Check,
        Clipboard,
        Cross,
        Home,
        Info,
        LifeBuoy,
        Plus,
        Shuffle,
        X,
    } from "@lucide/svelte";
    import { Label } from "bits-ui";

    const user = $derived(page.props.auth.user);

    let {
        competition,
        serc,
        judges,
        tank,
        draws,
    }: {
        competition: Competition;
        serc: SERC;
        judges: Judge[];
        tank?: number;
        draws?: Draw[];
    } = $props();

    const showDraw = false;
    const isHead = false;
</script>

<AppHead title="Dashboard" />

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    <span class="flex w-full justify-between items-center">
        <div class="">
            <h1 class="  -mb-3 normal-case! text-black! text-base!">Digital</h1>
            <h1 class=" indent-6 normal-case! text-se text-xl!">Judge</h1>
        </div>

        <LifeBuoy
            class="bg-se/20 rounded-full text-se p-2 shadow-md "
            size={40}
        />
    </span>
</section>

<div class="h-16"></div>

<section class="flex flex-col h-full">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2 class="">{serc.name}</h2>

    <br />

    <div class="flex flex-col space-y-3">
        <div class="space-y-2 w-full">
            {#each judges as judge, index}
                <div
                    class="border rounded-lg shadow-md p-4 group-hover:border-se focus:ring-1 focus:outline-none transition-all w-full"
                >
                    <div class="flex items-center justify-between">
                        <div class="text-left">
                            <h3>{judge.name}</h3>
                            <p>{judge.marking_points?.length} marking points</p>
                        </div>

                        {#if judges.length == 1}
                            <Link
                                href={addJudge(competition, {
                                    query: {
                                        swap: true,
                                    },
                                })}
                            >
                                <Shuffle
                                    size={40}
                                    class="bg-se/20 rounded-md text-se p-2 shadow-md"
                                />
                            </Link>
                        {:else}
                            <Link
                                only={["judges"]}
                                href={detachJudge({
                                    competition: competition,
                                    judge: judge,
                                })}
                            >
                                <X
                                    size={40}
                                    class="bg-red-500/20 rounded-md text-red-500 p-2 shadow-md"
                                />
                            </Link>
                        {/if}
                    </div>
                </div>
            {/each}
        </div>

        <Link href={addJudge(competition)}>
            <Button
                label="Add Casualty/Objective"
                variant="secondary"
                icon={Plus}
                class="w-full"
            />
        </Link>

        <hr class="spacer mb-4!" />

        <Button
            variant="success"
            icon={ArrowRight}
            label={`Start Judging ${tank ? "Tank " + tank : ""}`}
            class="w-full"
        />

        <Button
            label="Tutorial"
            icon={Info}
            variant="secondary"
            class="w-full py-1"
        />

        <hr class="spacer mb-3!" />

        <h4>
            {#if tank}
                Tank {tank}
            {/if} Order
        </h4>

        {#if showDraw || isHead}
            <ul class=" list-none -mt-2 w-full">
                {#each draws as draw}
                    {#if isHead}
                        <li class=" ">
                            <div class="flex justify-between">
                                <p>{draw.draw}. {draw.entity.name}</p>
                                <a
                                    href="#judge-entity-id"
                                    class="link col-start-5">Edit</a
                                >
                            </div>
                        </li>
                    {:else}
                        <li>{draw.draw}. {draw.entity.name}</li>
                    {/if}
                {/each}
            </ul>
        {:else}
            <div>
                <p class="mb-0">
                    There are <strong>{draws?.length}</strong> SERCs to mark.
                    You
                    <strong>are not</strong> permitted to view team names.
                </p>
            </div>
        {/if}
    </div>
</section>
