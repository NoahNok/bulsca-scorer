<!--
    A SERC's setup, for New SERC and Edit setup: its name, type, what it scores and whether League
    Objectives are on, then its Objectives as cards, each with its Leagues (with League Objectives on), a
    marking hint and its marking points. One save sends the lot with the ids of the Objectives and marking
    points taken out. Saving one that moves Leagues onto other Objectives after marking has started asks
    first, with how many marks it affects.
-->
<script lang="ts">
    import { confirm } from "@/lib/confirm";
    import MultiSelect from "@/manage/components/MultiSelect.svelte";
    import RichText from "@/manage/components/RichText.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { EventTarget, SercMarkingPoint, SercObjective, SercSetup, SercType } from "@/manage/types";
    import { Link, router } from "@inertiajs/svelte";
    import { tick } from "svelte";
    import { closeOnBackdrop, TARGET_LABELS } from "@/manage/ui";

    let {
        setup,
        leagues,
        templates,
        defaultTemplate,
        url,
        cancelUrl,
        isNew,
    }: {
        setup: SercSetup;
        leagues: { id: number; name: string }[];
        templates: { id: string; name: string }[];
        defaultTemplate: string | null;
        /** Where the save posts */
        url: string;
        cancelUrl: string;
        /** Adding a SERC: no unsaved changes guard, and the button says Add */
        isNew: boolean;
    } = $props();

    // The editor's copy, with each Objective's Leagues as strings for MultiSelect
    type Objective = Omit<SercObjective, "restricted_leagues"> & { restricted_leagues: string[] };

    let name = $state("");
    let type = $state<SercType>("DRY");
    let target = $state<EventTarget>("team");
    let leagueObjectives = $state(false);
    let objectives = $state<Objective[]>([]);
    let deleted = $state<{ judges: number[]; marking_points: number[] }>({ judges: [], marking_points: [] });
    // Remounts the Objectives (their RichText starts from its first value) each time they're reset
    let version = $state(0);

    let errors = $state<Record<string, string>>({});
    let processing = $state(false);
    // Incomplete fields only show once a save has been tried
    let tried = $state(false);

    let saved = "";

    function reset() {
        name = setup.name;
        type = setup.type;
        target = setup.target;
        leagueObjectives = setup.leagueObjectives;
        objectives = setup.objectives.map((objective) => ({
            ...objective,
            hint: objective.hint ?? "",
            restricted_leagues: objective.restricted_leagues.map(String),
            marking_points: objective.marking_points.map((point) => ({ ...point })),
        }));
        deleted = { judges: [], marking_points: [] };
        errors = {};
        tried = false;
        version++;
        saved = snapshot();
    }

    // What's compared to tell whether anything changed
    function snapshot() {
        return JSON.stringify({ name, type, target, leagueObjectives, objectives, deleted });
    }

    reset();

    const dirty = $derived(snapshot() !== saved);

    let nextKey = Date.now();

    function addObjective() {
        objectives.push({
            id: null,
            name: "",
            hint: "",
            restricted_leagues: [],
            marking_points: [newMarkingPoint()],
            sort_id: nextKey++,
        });
        tick().then(() => document.getElementById(`ss-obj-${objectives.length - 1}-name`)?.focus());
    }

    function newMarkingPoint(): SercMarkingPoint {
        return { id: null, description: "", weight: 1, template_id: defaultTemplate, sort_id: nextKey++ };
    }

    function addMarkingPoint(index: number) {
        const points = objectives[index].marking_points;
        points.push(newMarkingPoint());
        tick().then(() => document.getElementById(`ss-mp-${index}-${points.length - 1}`)?.focus());
    }

    // Taking out a saved one deletes its marks when saved, so that asks first
    async function removeObjective(index: number) {
        const objective = objectives[index];
        if (objective.id !== null) {
            const ok = await confirm({
                title: `Remove ${objective.name || "this Objective"}?`,
                description: "Saving deletes it with its marking points and every mark entered against them.",
                confirmLabel: "Remove",
                confirmVariant: "danger",
            });
            if (!ok) return;
            deleted.judges.push(objective.id);
        }
        objectives.splice(index, 1);
    }

    async function removeMarkingPoint(index: number, pointIndex: number) {
        const point = objectives[index].marking_points[pointIndex];
        if (point.id !== null) {
            const ok = await confirm({
                title: `Remove ${point.description || "this marking point"}?`,
                description: "Saving deletes it with every mark entered against it.",
                confirmLabel: "Remove",
                confirmVariant: "danger",
            });
            if (!ok) return;
            deleted.marking_points.push(point.id);
        }
        objectives[index].marking_points.splice(pointIndex, 1);
    }

    const pointIncomplete = (point: SercMarkingPoint) => !point.description.trim() || point.weight === null || !point.template_id;

    const incomplete = $derived(
        !name.trim() || objectives.length === 0 || objectives.some((objective) => !objective.name.trim() || objective.marking_points.length === 0 || objective.marking_points.some(pointIncomplete)),
    );

    // A field's server error, or that it's empty once a save was tried
    function problem(key: string, empty: boolean, message: string): string | undefined {
        return errors[key] ?? (tried && empty ? message : undefined);
    }

    const nameError = $derived(problem("name", !name.trim(), "Give it a name."));

    // Server errors about an Objective as a whole (its marking points, or a League on two Objectives)
    const generalErrors = $derived(
        Object.entries(errors)
            .filter(([key]) => key === "judges" || /^judges\.\d+\.(marking_points|restricted_leagues(\.\d+)?)$/.test(key))
            .map(([, message]) => message),
    );

    async function save(submit?: SubmitEvent, confirmAffectedMarks = false) {
        submit?.preventDefault();
        tried = true;
        if (incomplete) {
            await tick();
            document.querySelector<HTMLElement>("[data-serc-setup] [aria-invalid='true']")?.focus();
            return;
        }

        const payload = {
            name,
            type,
            target_entity: target,
            use_restricted_judges: leagueObjectives,
            judges: objectives.map(({ sort_id, ...objective }) => ({
                ...objective,
                restricted_leagues: objective.restricted_leagues.map(Number),
                marking_points: objective.marking_points.map(({ sort_id, ...point }) => point),
            })),
            deleted,
            confirm_affected_marks: confirmAffectedMarks,
        };

        router.post(url, payload, {
            preserveScroll: true,
            onStart: () => (processing = true),
            onFinish: () => (processing = false),
            // The page's setup now has the saved ids, so start again from it
            onSuccess: () => reset(),
            onError: async (failed) => {
                if (failed.confirm_affected_marks) {
                    errors = {};
                    const ok = await confirm({
                        title: "Mark against other Objectives?",
                        description: `${failed.confirm_affected_marks} Save anyway?`,
                        confirmLabel: "Save anyway",
                        confirmVariant: "danger",
                    });
                    if (ok) save(undefined, true);
                    return;
                }
                errors = failed;
                await tick();
                document.querySelector<HTMLElement>("[data-serc-setup] [aria-invalid='true']")?.focus();
            },
        });
    }

    // A marking point's settings (its template), in a dialog: it's rarely changed from the default
    let settingsDialog: HTMLDialogElement;
    let editing = $state<{ index: number; pointIndex: number } | null>(null);
    const editingPoint = $derived(editing ? objectives[editing.index]?.marking_points[editing.pointIndex] : undefined);

    async function openSettings(index: number, pointIndex: number) {
        editing = { index, pointIndex };
        await tick();
        settingsDialog.showModal();
    }

    const templateName = (id: string | null) => templates.find((template) => template.id === id)?.name ?? "none";

    const leagueOptions = $derived(leagues.map((league) => ({ value: String(league.id), label: league.name })));
    const heading = "text-lg font-extrabold uppercase";
    const iconButton =
        "flex size-8 cursor-pointer items-center justify-center self-center rounded-md transition-colors hover:bg-v2-row-hover motion-reduce:transition-none";
    const addCard =
        "flex h-full min-h-[10rem] w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-v2-border px-4 py-3.5 font-semibold text-v2-muted transition-colors hover:border-v2-teal hover:text-v2-teal-text focus-visible:border-v2-teal focus-visible:text-v2-teal-text motion-reduce:transition-none";
</script>

{#snippet plus()}
    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M12 5v14M5 12h14" />
    </svg>
{/snippet}

{#snippet sliders()}
    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M20 7h-9M14 17H5" /><circle cx="17" cy="17" r="3" /><circle cx="7" cy="7" r="3" />
    </svg>
{/snippet}

{#snippet cross()}
    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M18 6 6 18M6 6l12 12" />
    </svg>
{/snippet}

<form onsubmit={save} data-serc-setup novalidate>
    {#if !isNew}<UnsavedChanges {dirty} />{/if}

    <div class="grid gap-x-4 sm:grid-cols-2 min-[56rem]:grid-cols-[minmax(0,2fr)_minmax(0,1fr)_minmax(0,1fr)]">
        <div class="v2-field mt-0 sm:col-span-2 min-[56rem]:col-span-1">
            <label for="ss-name">Name</label>
            <input
                id="ss-name"
                class={["v2-input w-full", nameError && "is-invalid"]}
                autocomplete="off"
                bind:value={name}
                aria-invalid={nameError ? "true" : undefined}
                aria-describedby={nameError ? "ss-name-error" : undefined}
            />
            {#if nameError}<span id="ss-name-error" class="v2-error">{nameError}</span>{/if}
        </div>
        <div class="v2-field mt-0">
            <label for="ss-type">Type</label>
            <select id="ss-type" class="v2-input w-full" bind:value={type}>
                <option value="DRY">Dry</option>
                <option value="WET">Wet</option>
            </select>
        </div>
        <div class="v2-field mt-0">
            <label for="ss-target">Scores</label>
            <select id="ss-target" class="v2-input w-full" bind:value={target}>
                {#each Object.entries(TARGET_LABELS) as [value, label] (value)}
                    <option {value}>{label}</option>
                {/each}
            </select>
        </div>
    </div>

    <div class="mt-5 max-w-xl">
        <Switch
            name="use_restricted_judges"
            label="League Objectives"
            bind:checked={leagueObjectives}
            hint="Mark each Entity against the one Objective its League is assigned, rather than the Objective a judge picks."
        />
    </div>

    <section aria-labelledby="ss-objectives" class="mt-10">
        <h3 id="ss-objectives" class={heading}>Objectives</h3>
        {#if generalErrors.length > 0}
            <ul class="mt-3 flex flex-col gap-1">
                {#each generalErrors as message, index (index)}<li class="v2-error">{message}</li>{/each}
            </ul>
        {/if}

        {#key version}
            <ul class="mt-4 grid gap-4 min-[56rem]:grid-cols-2">
                {#each objectives as objective, index (objective.id ?? objective.sort_id)}
                    {@const nameError = problem(`judges.${index}.name`, !objective.name.trim(), "Give it a name.")}
                    <li class="flex flex-col gap-4 rounded-xl border border-v2-border bg-v2-card p-4 sm:p-5">
                        <div class="flex items-end gap-3">
                            <div class="v2-field my-0 min-w-0 flex-1">
                                <label for="ss-obj-{index}-name">Objective</label>
                                <input
                                    id="ss-obj-{index}-name"
                                    class={["v2-input w-full font-semibold", nameError && "is-invalid"]}
                                    placeholder="Casualty {index + 1}"
                                    autocomplete="off"
                                    bind:value={objective.name}
                                    aria-invalid={nameError ? "true" : undefined}
                                    aria-describedby={nameError ? `ss-obj-${index}-name-error` : undefined}
                                />
                            </div>
                            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm mb-[0.2rem]" onclick={() => removeObjective(index)}>
                                Remove<span class="sr-only"> {objective.name || `Objective ${index + 1}`}</span>
                            </button>
                        </div>
                        {#if nameError}<span id="ss-obj-{index}-name-error" class="v2-error -mt-3">{nameError}</span>{/if}

                        {#if leagueObjectives}
                            <div class="[&_.v2-field]:my-0">
                                <MultiSelect
                                    id="ss-obj-{index}-leagues"
                                    label="Leagues"
                                    name="judges[{index}][restricted_leagues][]"
                                    options={leagueOptions}
                                    bind:value={objective.restricted_leagues}
                                    placeholder="Choose Leagues…"
                                    hint={objective.restricted_leagues.length === 0 ? "No Leagues, so no Entities are marked against it." : undefined}
                                    error={errors[`judges.${index}.restricted_leagues.0`]}
                                />
                            </div>
                        {/if}

                        <div class="v2-field my-0">
                            <span id="ss-obj-{index}-hint-label" class="mb-[0.35rem] block font-semibold">Marking hint</span>
                            <RichText
                                id="ss-obj-{index}-hint"
                                labelledby="ss-obj-{index}-hint-label"
                                placeholder="What judges look for when marking it"
                                bind:value={objective.hint}
                                minHeight="4.5rem"
                            />
                        </div>

                        <div>
                            <div class="grid grid-cols-[minmax(0,1fr)_4.5rem_2rem_2rem] gap-x-2 border-b border-v2-border pb-1 text-sm font-semibold text-v2-muted">
                                <span>Marking point</span>
                                <span>Weight</span>
                                <span class="sr-only">Template</span>
                                <span class="sr-only">Remove</span>
                            </div>
                            {#if objective.marking_points.length === 0}
                                <p class={["py-3 text-sm", tried ? "text-v2-danger-text" : "text-v2-muted"]}>Add at least one marking point.</p>
                            {/if}
                            <ul>
                                {#each objective.marking_points as point, pointIndex (point.id ?? point.sort_id)}
                                    {@const prefix = `judges.${index}.marking_points.${pointIndex}`}
                                    {@const descriptionError = problem(`${prefix}.description`, !point.description.trim(), "Describe it.")}
                                    {@const weightError = problem(`${prefix}.weight`, point.weight === null, "Give it a weight.")}
                                    {@const templateError = problem(`${prefix}.template_id`, !point.template_id, "Pick a template in its settings.")}
                                    <li class="grid grid-cols-[minmax(0,1fr)_4.5rem_2rem_2rem] gap-x-2 gap-y-1.5 border-b border-v2-border py-2.5">
                                        <input
                                            id="ss-mp-{index}-{pointIndex}"
                                            class={["v2-input w-full", descriptionError && "is-invalid"]}
                                            placeholder="Marking point {pointIndex + 1}"
                                            aria-label="Marking point {pointIndex + 1}"
                                            autocomplete="off"
                                            bind:value={point.description}
                                            aria-invalid={descriptionError ? "true" : undefined}
                                        />
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.5"
                                            class={["v2-input w-full tabular-nums", weightError && "is-invalid"]}
                                            aria-label="Weight of marking point {pointIndex + 1}"
                                            bind:value={point.weight}
                                            aria-invalid={weightError ? "true" : undefined}
                                        />
                                        <button
                                            type="button"
                                            class={[iconButton, templateError ? "text-v2-danger-text" : "text-v2-muted hover:text-v2-ink"]}
                                            title="Template: {templateName(point.template_id)}"
                                            onclick={() => openSettings(index, pointIndex)}
                                        >
                                            {@render sliders()}<span class="sr-only">Settings for {point.description || `marking point ${pointIndex + 1}`}</span>
                                        </button>
                                        <button
                                            type="button"
                                            class={[iconButton, "text-v2-muted hover:text-v2-danger-text"]}
                                            onclick={() => removeMarkingPoint(index, pointIndex)}
                                        >
                                            {@render cross()}<span class="sr-only">Remove {point.description || `marking point ${pointIndex + 1}`}</span>
                                        </button>
                                        {#each [descriptionError, weightError, templateError].filter(Boolean) as message, messageIndex (messageIndex)}
                                            <span class="v2-error col-span-4">{message}</span>
                                        {/each}
                                    </li>
                                {/each}
                            </ul>
                            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm mt-2" onclick={() => addMarkingPoint(index)}>
                                {@render plus()}Add marking point
                            </button>
                        </div>
                    </li>
                {/each}
                <li>
                    <button type="button" class={addCard} onclick={addObjective}>{@render plus()}Add Objective</button>
                </li>
            </ul>
        {/key}
        {#if objectives.length === 0}
            <p class={["mt-3 text-[0.9375rem]/[1.6]", tried ? "text-v2-danger-text" : "text-v2-muted"]}>
                Add an Objective for each casualty or task judges mark, with the marking points they mark it on.
            </p>
        {/if}
    </section>

    <dialog bind:this={settingsDialog} class="v2-modal" aria-labelledby="ss-mp-settings-title" onclick={closeOnBackdrop} onclose={() => (editing = null)}>
        {#if editing && editingPoint}
            <h2 id="ss-mp-settings-title" class="wrap-anywhere">{editingPoint.description || `Marking point ${editing.pointIndex + 1}`}</h2>
            <p>How the Digital Judge asks for its mark.</p>
            <div class="v2-field mt-4 mb-0">
                <label for="ss-mp-template">Template</label>
                <select id="ss-mp-template" class="v2-input w-full" bind:value={editingPoint.template_id}>
                    <option value={null} disabled>Pick a template…</option>
                    {#each templates as template (template.id)}
                        <option value={template.id}>{template.name}</option>
                    {/each}
                </select>
            </div>
            <div class="v2-modal-actions mt-6">
                <button type="button" class="v2-btn v2-btn-primary" onclick={() => settingsDialog.close()}>Done</button>
            </div>
        {/if}
    </dialog>

    <div class="sticky bottom-0 mt-6 flex items-center justify-end gap-4 border-t border-v2-border bg-v2-bg py-3">
        {#if !isNew && dirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
        <Link href={cancelUrl} class="v2-link-muted text-sm">Cancel</Link>
        <button type="submit" class="v2-btn v2-btn-primary" disabled={processing || (!isNew && !dirty)}>{isNew ? "Add SERC" : "Save setup"}</button>
    </div>
</form>
