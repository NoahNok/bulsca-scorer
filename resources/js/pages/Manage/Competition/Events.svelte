<!--
    A competition's Events (manage.competitions.events), in CompetitionLayout: its speed events and SERCs
    side by side (stacked on phones) as whole-row links with their badges beside the name, each type only
    with the access for it. "Add speed event" opens a dialog that adds one and opens it; "Add SERC" goes
    to the SERC setup page.
-->
<script lang="ts">
    import { storeSpeed } from "@/actions/App/Http/Controllers/Manage/CompetitionEventsController";
    import type { CompetitionHeader, EventTarget, SercCard, SpeedEventCard } from "@/manage/types";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import { Form, Link } from "@inertiajs/svelte";
    import { tick } from "svelte";
    import { closeOnBackdrop, TARGET_LABELS } from "@/manage/ui";

    let {
        competition,
        speeds,
        sercs,
        speedOptions,
        newSercUrl,
    }: {
        competition: CompetitionHeader;
        /** Null without speed event access */
        speeds: SpeedEventCard[] | null;
        /** Null without SERC access */
        sercs: SercCard[] | null;
        speedOptions: { id: number; name: string }[];
        newSercUrl: string;
    } = $props();

    let speedDialog: HTMLDialogElement;
    // Remounts the form each time the dialog opens, so it starts empty
    let opened = $state(0);

    async function openSpeed() {
        opened++;
        await tick();
        speedDialog.showModal();
    }

    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
</script>

<svelte:head>
    <title>Events - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet list(events: (SpeedEventCard | SercCard)[], label: string)}
    <RowTable rows={events} columns={[label]}>
        {#snippet row(event)}
            <td>
                <span class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
                    <Link href={event.url} class={rowLink}>{event.name}</Link>
                    <span class="flex flex-wrap items-center gap-1.5">
                        {#if event.complete}
                            <span class="v2-badge is-success">Complete</span>
                        {:else}
                            <span class="v2-badge is-danger">Incomplete</span>
                        {/if}
                        {#if event.confirmed}<span class="v2-badge is-live">Confirmed by Referee</span>{/if}
                        {#if event.hidden}<span class="v2-badge" title="Left out of the results">Hidden</span>{/if}
                    </span>
                </span>
            </td>
        {/snippet}
    </RowTable>
{/snippet}

<div class={["grid gap-10", speeds && sercs && "lg:grid-cols-2"]}>
    {#if speeds}
        <section aria-labelledby="ev-speeds">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 id="ev-speeds" class={heading}>Speed events</h2>
                {#if speeds.length > 0}
                    <button type="button" class="v2-btn v2-btn-sm" onclick={openSpeed}>Add speed event</button>
                {/if}
            </div>
            <p class={lead}>Timed events. Each one gets a result for every Entity it scores.</p>

            {#if speeds.length > 0}
                {@render list(speeds, "Speed event")}
            {:else}
                <EmptyState>
                    <p>No speed events yet.</p>
                    <button type="button" class="v2-btn mt-4" onclick={openSpeed}>Add speed event</button>
                </EmptyState>
            {/if}
        </section>
    {/if}

    {#if sercs}
        <section aria-labelledby="ev-sercs">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h2 id="ev-sercs" class={heading}>SERCs</h2>
                {#if sercs.length > 0}
                    <Link href={newSercUrl} class="v2-btn v2-btn-sm">Add SERC</Link>
                {/if}
            </div>
            <p class={lead}>Set one up with its Objectives and marking points, and Officials can mark it.</p>

            {#if sercs.length > 0}
                {@render list(sercs, "SERC")}
            {:else}
                <EmptyState>
                    <p>No SERCs yet.</p>
                    <Link href={newSercUrl} class="v2-btn mt-4">Add SERC</Link>
                </EmptyState>
            {/if}
        </section>
    {/if}
</div>

<dialog bind:this={speedDialog} class="v2-modal" aria-labelledby="ev-speed-title" onclick={closeOnBackdrop}>
    <h2 id="ev-speed-title">Add speed event</h2>

    {#key opened}
        <Form action={storeSpeed(competition.id)}>
            {#snippet children({ errors, processing })}
                <div class="mt-5 flex flex-col gap-[0.6rem] *:mt-0">
                    <div class={["v2-field", errors.event && "is-invalid"]}>
                        <label for="ev-event">Event</label>
                        <select
                            id="ev-event"
                            name="event"
                            class="v2-input w-full"
                            required
                            aria-invalid={errors.event ? "true" : undefined}
                            aria-describedby={errors.event ? "ev-event-error" : undefined}
                        >
                            <option value="" selected disabled>Pick an event</option>
                            {#each speedOptions as option (option.id)}
                                <option value={String(option.id)}>{option.name}</option>
                            {/each}
                        </select>
                        {#if errors.event}<span id="ev-event-error" class="v2-error">{errors.event}</span>{/if}
                    </div>
                    <div class="v2-field">
                        <label for="ev-target">Scores</label>
                        <select id="ev-target" name="target_entity" class="v2-input w-full" value="team">
                            {#each Object.entries(TARGET_LABELS) as [value, label] (value)}
                                <option {value}>{label}</option>
                            {/each}
                        </select>
                        {#if errors.target_entity}<span class="v2-error">{errors.target_entity}</span>{/if}
                    </div>
                </div>
                <div class="v2-modal-actions mt-6">
                    <button type="button" class="v2-btn" onclick={() => speedDialog.close()}>Cancel</button>
                    <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Add event</button>
                </div>
            {/snippet}
        </Form>
    {/key}
</dialog>
