<script module lang="ts">
    export const layout = {
        title: "Order of Finish",
    };
</script>

<script lang="ts">
    import {
        markOOF,
        selectOOFHeat,
        storeOOF,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Event/EventJudgeController";

    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import ActionStatusModal from "@/components/ActionStatusModal.svelte";

    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import SignOffCheckbox from "@/components/Judging/SignOffCheckbox.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import { confirm } from "@/lib/confirm";
    import { toastInfo, toastSuccess } from "@/lib/toast.svelte";

    import type { Competition, OOFHeat, OOFLane, SpeedEvent } from "@/types/base";
    import { Link, useHttp, setLayoutProps } from "@inertiajs/svelte";
    import { ArrowRight, Check, House, Info, RefreshCcw } from "@lucide/svelte";

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

    let hasNextHeat = $state<boolean>(true);

    // the controller expects the lanes array as the request body
    const http = useHttp<{}, { hasNextHeat: boolean }>().transform(
        () => heat.lanes ?? [],
    );

    async function submit(e: SubmitEvent) {
        e.preventDefault();

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

        const success = await modalRef?.showFor(req);

        if (success) {
            modalRef?.setTitleAndMessage(
                "Order of Finish Submitted",
                hasNextHeat
                    ? "Your order of finish has been submitted successfully."
                    : "You've finished the order of finish for this event.",
            );
        }
    }

    $effect(() => {
        setLayoutProps({
            nav: nav,
        });
    });

    // carry on after any places already saved for this heat
    let currentPlace = $state<number>(
        Math.max(0, ...(heat.lanes ?? []).map((l) => l.oof ?? 0)) + 1,
    );

    function setPlace(lane: OOFLane) {
        if (lane.oof) {
            toastInfo(`Lane ${lane.lane} has already been assigned.`);
            return;
        }

        lane.oof = currentPlace;
        currentPlace++;
    }

    async function reassign() {
        let confirmed = await confirm({
            title: "Reset Order?",
            description: "Are you sure you want to reset the order of finish?",
            confirmLabel: "Yes",
        });

        if (!confirmed) {
            return;
        }

        // null rather than undefined so the cleared value is still sent
        // (JSON drops undefined) and the server removes the saved place
        heat.lanes?.forEach((lane) => {
            lane.oof = null;
        });

        currentPlace = 1;
        toastSuccess("Order of finish reset.");
    }

    const hasPlaces = $derived(heat.lanes?.some((l) => l.oof) ?? false);
</script>

<AppHead title="Heat {heat.heat} - OOF - {event.name} - {competition.name}" />


<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{event.name}</h2>

    <BackLink
        href={selectOOFHeat({ competition, event })}
        label="Change heat"
        class="mt-2 mb-4"
    />

    <SectionLabel>Order of finish</SectionLabel>
    <p class="font-archivo text-xl font-semibold">Heat {heat.heat}</p>

    <div
        class="mt-3 flex items-start gap-3 rounded-xl bg-gray-50 p-3 text-sm text-gray-700"
    >
        <Info size={16} class="mt-0.5 shrink-0 text-gray-400" />
        <p>
            Tap each lane in the order it finished. Lanes that didn't finish can
            be left blank.
        </p>
    </div>

    <div class="mt-4 mb-2 flex items-center justify-between gap-3">
        <SectionLabel>Lanes</SectionLabel>
        <button
            type="button"
            onclick={reassign}
            disabled={!hasPlaces}
            class="inline-flex cursor-pointer items-center gap-1 text-sm text-red-600 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-300 disabled:cursor-default disabled:text-gray-300"
        >
            <RefreshCcw size={14} /> Reset
        </button>
    </div>

    <div class="divide-y overflow-hidden rounded-xl border bg-white shadow-sm">
        {#each Array.from({ length: event.max_lanes }, (_, i) => i + 1) as lane_no}
            {@const lane = heat.lanes?.find((l) => l.lane === lane_no)}
            {#if lane}
                <button
                    type="button"
                    onclick={() => setPlace(lane)}
                    aria-pressed={!!lane.oof}
                    class="flex w-full cursor-pointer items-center gap-3 px-3 py-2.5 text-left transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset {lane.oof
                        ? 'bg-se/5'
                        : 'bg-white hover:bg-gray-50'}"
                >
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg font-archivo text-base font-semibold transition-colors {lane.oof
                            ? 'bg-se text-white'
                            : 'border border-dashed border-gray-300 text-gray-300'}"
                    >
                        {lane.oof ?? "–"}
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-xs text-gray-500">
                            Lane {lane.lane}
                        </span>
                        <span
                            class="block truncate text-sm font-medium text-gray-900"
                        >
                            {lane.entity.name}
                        </span>
                    </span>
                </button>
            {:else}
                <div class="flex items-center gap-3 px-3 py-2.5">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-gray-50 text-gray-300"
                    >
                        –
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-xs text-gray-400">
                            Lane {lane_no}
                        </span>
                        <span class="block text-sm text-gray-400 italic">
                            Empty lane
                        </span>
                    </span>
                </div>
            {/if}
        {/each}
    </div>

    <form onsubmit={submit} class="mt-4 flex flex-col gap-4">
        <SignOffCheckbox id="check-conf" name="check_conf" class="mt-2" />

        <Button
            type="submit"
            label="Submit Order of Finish"
            class="w-full"
            icon={Check}
        />
    </form>

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
                        class="mb-0! w-full"
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
                    class="mb-0! w-full py-1.5"
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
