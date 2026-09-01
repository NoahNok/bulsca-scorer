<script module lang="ts">
    export const layout = {
        title: "Select Tank",
    };
</script>

<script lang="ts">
    import {
        markTime,
        selectTimeHeat,
        storeTime,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Event/EventJudgeController";

    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import ActionStatusModal from "@/components/ActionStatusModal.svelte";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Lane from "@/components/Judging/Speed/Lane.svelte";

    import type { Competition, Event, Heat, SpeedEvent } from "@/types/base";
    import { page, Link, useHttp, setLayoutProps } from "@inertiajs/svelte";
    import { ArrowRight, Check, House } from "@lucide/svelte";

    const user = $derived(page.props.auth.user);

    let {
        competition,
        event,
        heat,
        existingTimes = {},
    }: {
        competition: Competition;
        event: SpeedEvent;
        heat: Heat;
        existingTimes?: Record<number, any>;
    } = $props();

    let modalRef: ActionStatusModal | null = null;

    let times = $derived.by<Record<number, any>>(() => existingTimes);

    let hasNextHeat = $state<boolean>(true);

    const http = useHttp<{}, { hasNextHeat: boolean }>();

    async function submit(e) {
        e.preventDefault();

        let data = { mark: times };

        http.data = () => data;

        let req = http.post(
            storeTime({
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
</script>

<AppHead title="Heat {heat.heat} - Times - {event.name} - {competition.name}" />

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
    <h2 class="">Time - {event.name}</h2>
    <br />

    <div class="flex flex-col space-y-3">
        <p class="font-semibold text-bulsca_red md:hidden">Rotate your phone</p>

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

        <form onsubmit={submit}>
            <div class="relative overflow-x-auto w-full">
                <table class="w-full">
                    <tbody class="divide-y">
                        {#each Array.from({ length: event.max_lanes }, (_, i) => i + 1) as lane}
                            <Lane
                                lane={heat.lanes?.find(
                                    (l) => l.lane === lane,
                                ) ?? lane}
                                bind:times
                                allowSingleDigit={event.name === "Rope Throw"}
                            />
                        {/each}
                    </tbody>
                </table>
            </div>
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
        title="Submitting Marks"
        message="Submitting your marks..."
    >
        {#snippet success()}
            {#if hasNextHeat}
                <Link
                    href={markTime({
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
        href={selectTimeHeat({ competition: competition, event: event })}
        class=""
    >
        <Button
            label="Change Heat"
            variant="white"
            class="w-full py-1 border border-black/10"
        />
    </Link>
{/snippet}
