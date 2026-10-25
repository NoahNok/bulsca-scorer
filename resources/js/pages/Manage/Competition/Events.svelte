<!--
    A competition's Events (manage.competitions.events), in CompetitionLayout: its speed events and SERCs
    as cards, each type only with the access for it. "Add speed event" opens a dialog that adds one at
    once; "Add SERC" goes to the SERC setup page.
-->
<script lang="ts">
    import { storeSpeed } from "@/actions/App/Http/Controllers/Manage/CompetitionEventsController";
    import type { CompetitionHeader, EventTarget, SercCard, SpeedEventCard } from "@/manage/types";
    import { Form } from "@inertiajs/svelte";
    import { tick } from "svelte";

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

    const TARGETS: Record<EventTarget, string> = { club: "Clubs", team: "Teams", competitor: "Competitors" };

    let speedDialog: HTMLDialogElement;
    // Remounts the form each time the dialog opens, so it starts empty
    let opened = $state(0);

    async function openSpeed() {
        opened++;
        await tick();
        speedDialog.showModal();
    }

    // Close a dialog on a click on its backdrop (the dialog itself, outside its content)
    function onBackdrop(event: MouseEvent) {
        if (event.target === event.currentTarget) (event.currentTarget as HTMLDialogElement).close();
    }

    const heading = "text-lg font-extrabold uppercase";
    const grid = "grid grid-cols-[repeat(auto-fill,minmax(15rem,1fr))] gap-3";
    const card =
        "group flex h-full items-center justify-between gap-3 rounded-xl border border-v2-border bg-v2-card px-4 py-3.5 transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover motion-reduce:transition-none";
    // The add card: dashed, with the label in the middle
    const addCard =
        "flex h-full min-h-[5.5rem] w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-v2-border px-4 py-3.5 font-semibold text-v2-muted transition-colors hover:border-v2-teal hover:text-v2-teal-text focus-visible:border-v2-teal focus-visible:text-v2-teal-text motion-reduce:transition-none";
</script>

<svelte:head>
    <title>Events - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet eventCard(event: SpeedEventCard | SercCard)}
    <li>
        <a href={event.url} class={card}>
            <span class="font-bold wrap-anywhere group-hover:text-v2-teal-text">{event.name}</span>
            <span class="flex shrink-0 items-center gap-1">
                {#if event.hidden}<span class="v2-badge mr-1" title="Left out of the results">Hidden</span>{/if}
                {#if event.confirmed}
                    <span title="Confirmed by Referee">
                        <svg class="size-6 text-v2-success" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375Zm9.586 4.594a.75.75 0 0 0-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 0 0-1.06 1.06l1.5 1.5a.75.75 0 0 0 1.116-.062l3-3.75Z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Confirmed by Referee</span>
                    </span>
                {/if}
                {#if event.complete}
                    <span title="Complete">
                        <svg class="size-6 text-v2-success" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Complete</span>
                    </span>
                {:else}
                    <span title="Incomplete">
                        <svg class="size-6 text-v2-danger" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Incomplete</span>
                    </span>
                {/if}
            </span>
        </a>
    </li>
{/snippet}

{#snippet plus()}
    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M12 5v14M5 12h14" />
    </svg>
{/snippet}

<div class="flex flex-col gap-10">
    {#if speeds}
        <section aria-labelledby="ev-speeds">
            <h2 id="ev-speeds" class="mb-3 {heading}">Speed events</h2>
            <ul class={grid}>
                {#each speeds as event (event.id)}
                    {@render eventCard(event)}
                {/each}
                <li>
                    <button type="button" class={addCard} onclick={openSpeed}>{@render plus()}Add speed event</button>
                </li>
            </ul>
            {#if speeds.length === 0}
                <p class="mt-3 text-[0.9375rem]/[1.6] text-v2-muted">
                    No speed events yet. Each one you add gets a result for every Entity it scores.
                </p>
            {/if}
        </section>
    {/if}

    {#if sercs}
        <section aria-labelledby="ev-sercs">
            <h2 id="ev-sercs" class="mb-3 {heading}">SERCs</h2>
            <ul class={grid}>
                {#each sercs as event (event.id)}
                    {@render eventCard(event)}
                {/each}
                <li>
                    <a href={newSercUrl} class={addCard}>{@render plus()}Add SERC</a>
                </li>
            </ul>
            {#if sercs.length === 0}
                <p class="mt-3 text-[0.9375rem]/[1.6] text-v2-muted">
                    No SERCs yet. Set one up with its Objectives and marking points, and Officials can mark it.
                </p>
            {/if}
        </section>
    {/if}
</div>

<dialog bind:this={speedDialog} class="v2-modal" aria-labelledby="ev-speed-title" onclick={onBackdrop}>
    <h2 id="ev-speed-title">Add speed event</h2>

    {#key opened}
        <Form action={storeSpeed(competition.id)} options={{ preserveScroll: true }} onSuccess={() => speedDialog.close()}>
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
                            {#each Object.entries(TARGETS) as [value, label] (value)}
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
