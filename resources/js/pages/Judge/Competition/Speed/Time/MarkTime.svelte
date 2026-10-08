<script module lang="ts">
    export const layout = {
        title: "Times",
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
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import Lane from "@/components/Judging/Speed/Lane.svelte";
    import SignOffCheckbox from "@/components/Judging/SignOffCheckbox.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";

    import type { Competition, Heat, SpeedEvent } from "@/types/base";
    import { Link, useHttp, setLayoutProps } from "@inertiajs/svelte";
    import { ArrowRight, Check, House, Info } from "@lucide/svelte";

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

    // keyed by entity id. PHP sends an empty array as [] rather than {}, and
    // setting times[entityId] on an array pads it with nulls when serialised
    let times = $derived.by<Record<number, any>>(() => ({ ...existingTimes }));

    let hasNextHeat = $state<boolean>(true);

    const http = useHttp<{}, { hasNextHeat: boolean }>().transform(() => ({
        mark: times,
    }));

    async function submit(e: SubmitEvent) {
        e.preventDefault();

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

        const success = await modalRef?.showFor(req);

        if (success) {
            modalRef?.setTitleAndMessage(
                "Times Submitted",
                hasNextHeat
                    ? "Your times have been submitted successfully."
                    : "You've finished marking all times for this event.",
            );
        }
    }

    $effect(() => {
        setLayoutProps({
            nav: nav,
        });
    });
</script>

<AppHead title="Heat {heat.heat} - Times - {event.name} - {competition.name}" />


<section class="flex flex-col">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2>{event.name}</h2>

    <BackLink
        href={selectTimeHeat({ competition, event })}
        label="Change heat"
        class="mt-2 mb-4"
    />

    <SectionLabel>Times</SectionLabel>
    <p class="font-archivo text-xl font-semibold">Heat {heat.heat}</p>

    <div
        class="mt-3 flex items-start gap-3 rounded-xl bg-gray-50 p-3 text-sm text-gray-700"
    >
        <Info size={16} class="mt-0.5 shrink-0 text-gray-400" />
        <p>
            Enter times as <strong class="font-mono">00:00.00</strong>, including
            leading zeros, or <strong>DNF</strong> / <strong>DNS</strong>.
            {#if event.name == "Rope Throw"}
                For Rope Throw you can instead enter how many people were pulled
                in (0–3).
            {/if}
        </p>
    </div>

    <form onsubmit={submit} class="mt-4 flex flex-col gap-4">
        <div class="divide-y overflow-hidden rounded-xl border bg-white shadow-sm">
            {#each Array.from({ length: event.max_lanes }, (_, i) => i + 1) as lane}
                <Lane
                    lane={heat.lanes?.find((l) => l.lane === lane) ?? lane}
                    bind:times
                    allowSingleDigit={event.name === "Rope Throw"}
                />
            {/each}
        </div>

        <SignOffCheckbox id="check-conf" name="check_conf" class="mt-2" />

        <Button type="submit" label="Submit Times" class="w-full" icon={Check} />
    </form>

    <ActionStatusModal
        bind:this={modalRef}
        title="Submitting Times"
        message="Submitting your times..."
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
