<!--
    An Entity's marks on a SERC (manage.competitions.events.sercs.marks), in CompetitionLayout, laid out like
    a score sheet: its DQ and penalties, then each Objective with a 0-10 mark per marking point. "Save" saves
    and goes back to the SERC; "Save & next" saves and opens the next Entity in draw order (not on the last);
    "Skip to next" opens it unsaved.
-->
<script lang="ts">
    import { leagueObjectives, saveMarks, show } from "@/actions/App/Http/Controllers/Manage/SercEventController";
    import Alert from "@/manage/components/Alert.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { CompetitionHeader, SercEventHeader, SercMarkingObjective } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";
    import { tick } from "svelte";
    import { upperCase } from "@/manage/ui";

    let {
        competition,
        event,
        entity,
        disqualification,
        penalties,
        objectives,
        objectiveProblem,
        nextUrl,
    }: {
        competition: CompetitionHeader;
        event: SercEventHeader;
        entity: { id: number; name: string; draw: string | null; index: number; of: number };
        /** DQ101, or empty */
        disqualification: string;
        /** P100, P200, or empty */
        penalties: string;
        objectives: SercMarkingObjective[];
        /** With League Objectives on, its Leagues give it no Objective or several, so every one shows */
        objectiveProblem: boolean;
        /** Null for the last Entity */
        nextUrl: string | null;
    } = $props();

    const comp = $derived(competition.id);

    type Values = { disqualification: string; penalties: string; marks: Record<number, number | null> };

    function saved(): Values {
        return {
            disqualification,
            penalties,
            marks: Object.fromEntries(objectives.flatMap((objective) => objective.markingPoints.map((point) => [point.id, point.mark]))),
        };
    }

    // svelte-ignore state_referenced_locally
    let values = $state<Values>(saved());
    let errors = $state<Record<string, string>>({});
    let processing = $state(false);

    // Each Entity is its own visit to this page; start again when it changes
    // svelte-ignore state_referenced_locally
    let shownFor = $state(entity.id);
    $effect.pre(() => {
        if (entity.id !== shownFor) {
            shownFor = entity.id;
            values = saved();
            errors = {};
        }
    });

    const dirty = $derived(JSON.stringify(values) !== JSON.stringify(saved()));

    // Enter saves and moves on, as "Save & next" does; "Save" goes back to the SERC
    function save(submit: SubmitEvent) {
        submit.preventDefault();
        const next = (submit.submitter as HTMLButtonElement | null)?.value !== "back";
        router.post(saveMarks({ comp, serc: event.id, entity: entity.id }).url, { ...$state.snapshot(values), next }, {
            onStart: () => (processing = true),
            onFinish: () => (processing = false),
            onError: async (failed) => {
                errors = failed;
                await tick();
                document.querySelector<HTMLElement>("[data-serc-marks] [aria-invalid='true']")?.focus();
            },
        });
    }

    const heading = "text-lg font-extrabold uppercase";
</script>

<svelte:head>
    <title>{entity.name} - {event.name} - {competition.name} - Scoring.Events</title>
</svelte:head>

<div class="mb-6">
    <Link href={show({ comp, serc: event.id }).url} class="v2-link-muted text-sm wrap-anywhere">← {event.name}</Link>
    <h2 class="mt-1 text-2xl font-extrabold uppercase wrap-anywhere">{entity.name}</h2>
    <p class="mt-[0.35rem] text-[0.9375rem]/[1.6] text-v2-muted">
        {#if entity.draw}Draw {entity.draw}, {/if}entry {entity.index} of {entity.of}
    </p>
</div>

<form onsubmit={save} class="max-w-3xl" data-serc-marks novalidate>
    <UnsavedChanges {dirty} />

    {#if objectiveProblem}
        <div class="mb-6">
            <Alert tone="danger" title="No single Objective">
                <p class="mt-1">
                    Its Leagues give it no Objective, or more than one, so every Objective shows here. Fix it on
                    <Link href={leagueObjectives({ comp, serc: event.id }).url} class="v2-link-inline">League Objectives</Link>.
                </p>
            </Alert>
        </div>
    {/if}

    <div class="grid gap-x-4 sm:grid-cols-2">
        {#each [{ key: "disqualification", label: "DQ", placeholder: "DQ101" }, { key: "penalties", label: "Penalties", placeholder: "P100, P200" }] as const as field (field.key)}
            {@const error = errors[field.key]}
            <div class="v2-field mt-0">
                <label for="sm-{field.key}">{field.label}</label>
                <input
                    id="sm-{field.key}"
                    class={["v2-input w-full uppercase", error && "is-invalid"]}
                    placeholder={field.placeholder}
                    autocomplete="off"
                    bind:value={values[field.key]}
                    oninput={upperCase}
                    aria-invalid={error ? "true" : undefined}
                    aria-describedby={error ? `sm-${field.key}-error` : undefined}
                />
                {#if error}<span id="sm-{field.key}-error" class="v2-error">{error}</span>{/if}
            </div>
        {/each}
    </div>

    {#each objectives as objective (objective.id)}
        <section aria-labelledby="sm-obj-{objective.id}" class="mt-8">
            <h3 id="sm-obj-{objective.id}" class={heading}>{objective.name}</h3>
            {#if objective.hint}
                <details class="mt-1 text-[0.9375rem]">
                    <summary class="cursor-pointer font-semibold text-v2-muted">Marking hint</summary>
                    <div class="mt-2 text-v2-muted [&_ol]:list-decimal [&_ol]:pl-5 [&_ul]:list-disc [&_ul]:pl-5">{@html objective.hint}</div>
                </details>
            {/if}

            <ul class="mt-3 border-t border-v2-border">
                {#each objective.markingPoints as point (point.id)}
                    {@const error = errors[`marks.${point.id}`]}
                    <li class="border-b border-v2-border py-2">
                        <div class="flex items-center gap-4">
                            <label for="sm-mp-{point.id}" class="min-w-0 flex-1 font-semibold wrap-anywhere">
                                {point.name}
                                {#if point.weight != 1}<span class="ml-1 text-sm font-normal text-v2-muted tabular-nums">×{point.weight}</span>{/if}
                            </label>
                            <input
                                id="sm-mp-{point.id}"
                                type="number"
                                min="0"
                                max="10"
                                step="1"
                                inputmode="numeric"
                                placeholder="0–10"
                                class={["v2-input w-24 text-right tabular-nums", error && "is-invalid"]}
                                bind:value={values.marks[point.id]}
                                aria-invalid={error ? "true" : undefined}
                                aria-describedby={error ? `sm-mp-${point.id}-error` : undefined}
                            />
                        </div>
                        {#if error}<span id="sm-mp-{point.id}-error" class="v2-error mt-1 block text-right">{error}</span>{/if}
                    </li>
                {/each}
            </ul>
        </section>
    {:else}
        <p class="mt-8 text-[0.9375rem]/[1.6] text-v2-muted">
            It has no Objectives to mark yet. Add them in its setup.
        </p>
    {/each}

    <div class="sticky bottom-0 mt-6 flex items-center justify-end gap-4 border-t border-v2-border bg-v2-bg py-3">
        {#if dirty}<span class="mr-auto text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
        {#if nextUrl}<Link href={nextUrl} class="v2-link-muted text-sm">Skip to next</Link>{/if}
        {#if nextUrl}
            <!-- First in the form so Enter is "Save & next", shown last -->
            <button type="submit" class="v2-btn v2-btn-primary order-last" disabled={processing}>Save & next</button>
            <button type="submit" value="back" class="v2-btn" disabled={processing}>Save</button>
        {:else}
            <button type="submit" value="back" class="v2-btn v2-btn-primary" disabled={processing}>Save</button>
        {/if}
    </div>
</form>
