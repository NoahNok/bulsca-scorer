<script module lang="ts">
    export const layout = {
        title: "Select Tank",
    };
</script>

<script lang="ts">
    import {
    markOOF,
        markTime,
        selectOOFHeat,
        selectTimeHeat,
        storeOOF,
        storeTime,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Event/EventJudgeController";

    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import ActionStatusModal from "@/components/ActionStatusModal.svelte";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import { confirm } from "@/lib/confirm";
    import { toastInfo, toastSuccess } from "@/lib/toast.svelte";
    import heats from "@/routes/comps/heats_and_draws/heats";

    import type { Competition, OOFHeat, OOFLane, SpeedEvent } from "@/types/base";
    import { page, Link, useHttp, setLayoutProps } from "@inertiajs/svelte";
    import { ArrowRight, Check, House, RefreshCcw } from "@lucide/svelte";

    const user = $derived(page.props.auth.user);

    let {
        competition,
        event,
        heat: initialHeat,
    }: {
        competition: Competition;
        event: SpeedEvent;
        heat: OOFHeat;
    } = $props();

    let heat = $state(initialHeat);


    let modalRef: ActionStatusModal | null = null;

    let times = $state<Record<number, any>>({});

    let hasNextHeat = $state<boolean>(true);

    const http = useHttp<OOFLane[], { hasNextHeat: boolean }>();

    async function submit(e) {
        e.preventDefault();

        

        http.data = () => heat.lanes ?? [];

        let req = http.post(
            storeOOF({
                competition: competition,
                event: event,
                heat: heat.heat,
            }).url,
            {
                onSuccess(response, httpResponse) {
                    hasNextHeat = response.hasNextHeat;
                },
            },
        );

        modalRef?.showFor(req);

        const success = await modalRef?.showFor(req);

        if (success) {
            modalRef?.setTitleAndMessage(
                "Times Submitted",
                hasNextHeat
                    ? "Your times have been submitted successfully."
                    : "You've finished marking all times for this event.",
            );
        } else {
        }
    }

    $effect(() => {
        setLayoutProps({
            nav: nav,
        });
    });

    let currentPlace = $state<number>(1);

    function setPlace(lane: OOFLane) {
        if (lane.oof) {
            toastInfo(`Lane ${lane.lane} has already be assigned.`)
            return
            
        }

        lane.oof = currentPlace;
        currentPlace++;
    } 

    async function reassign() {

        let confirmed = await confirm({
                title: "Reset Order?",
                description: "Are you sure you want to reset the order of finish?",
                confirmLabel: "Yes"
             })

        if (!confirmed) {
            return
        }

        heat.lanes?.forEach(lane => {
            
            lane.oof = undefined
        })

        currentPlace = 1;
        toastSuccess("Order of finish reset.")
    }


</script>

<AppHead title="Heat {heat.heat} - OOF - {event.name} - {competition.name}" />

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
        <h2 class="font-bold w-full break-words">
            Heat {heat.heat}
        </h2>
        <p>
            Times must match the format <strong>XX:XX.XX</strong> exactly!
            (include leading/trailing 0).<br /> Enter
            <strong>DNF or DNS</strong> as required!
            <br />

            {#if event.name == "Rope Throw"}
                <strong>OR</strong> enter the total amount of people pulled in from
                0-3
            {/if}
        </p>

        <div class="flex space-x-2 -mb-1 text-sm font-archivo font-semibold" >
                    <p class="w-10 text-center!" >OOF</p>

                    <p class="indent-1" >Lane</p>
                        
              


                 
                </div>

        {#each Array.from({ length: event.max_lanes }, (_, i) => i + 1) as lane_no}
            {const lane = heat.lanes?.find((l) => l.lane === lane_no)}     
                <div class="flex space-x-2" >
                    <Button label={ lane?.oof ? `${lane?.oof}` : '-' } variant="white" class="w-10 text-center! {!lane && "pointer-events-none!"}" onclick={() => lane && setPlace(lane)} />
                        
              

                    <Button label={lane ? `${lane?.lane}: ${lane?.entity.name}` : '-'} variant={lane?.oof ? "success" : "white"} class="w-full {!lane && "pointer-events-none!"}" onclick={() => lane && setPlace(lane)} />

                 
                </div>
        {/each}

         <Button label="Reset" variant="danger" class="w-full py-1 mt-1" icon={RefreshCcw} onclick={reassign} />


            <form  onsubmit={submit}>
                <br />

                <div class="flex flex-row space-x-2 md:space-x-4 items-center">
                    <label for="check-conf"
                        >I acknowledge that the above results are correct and cannot
                        be changed, and submission of this form acts as signing it
                        digitally.
                        <br />
                        <small class="text-gray-500"
                            >(Clicking the text will also check the box!)</small
                        >
                    </label>
                    <input
                        type="checkbox"
                        id="check-conf"
                        name="check_conf"
                        class="min-w-[20px] min-h-[20px]"
                        required
                    />
                </div>
                <br />

                <Button
                    variant="success"
                    label="Submit "
                    class="w-full py-2 "
                    icon={Check}
                
                />
            </form>
        
    </div>
    <br />
    <br />
    <br />
    <br />
    <ActionStatusModal
        bind:this={modalRef}
        title="Submitting Order of Finish"
        message="Submitting your order of finish..."
    >
        {#snippet success()}
            {#if hasNextHeat}
                <Link
                    href={markOOF({
                        competition: competition,
                        event: event,
                        heat: heat.heat + 1,
                    })}
                    viewTransition
                    class="w-full"
                >
                    <Button
                        variant="success"
                        class="w-full mb-0! "
                        label="Next heat"
                        type="button"
                        icon={ArrowRight}
                    />
                </Link>
            {/if}

            <Link
                href={home({
                    competition: competition.id,
                })}
                class="w-full"
            >
                <Button
                    variant="secondary"
                    class="w-full mb-0! py-1 "
                    label="Home"
                    type="button"
                    icon={House}
                />
            </Link>
        {/snippet}
    </ActionStatusModal>
</section>

{#snippet nav()}
    <Link href={home({ competition: competition })} class="mr-auto">
        <Button
            label="Home"
            variant="white"
            class="w-full py-1 border border-black/10"
        />
    </Link>
    <Link
        href={selectOOFHeat({ competition: competition, event: event })}
        class=""
    >
        <Button
            label="Change Heat"
            variant="white"
            class="w-full py-1 border border-black/10"
        />
    </Link>
{/snippet}
