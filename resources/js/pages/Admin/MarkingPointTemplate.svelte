<!--
    New and Edit marking point template (manage.admin.marking-point-templates.create, ….edit), in
    AdminLayout: its name, then Tabs between the two kinds, so only the chosen kind's fields are sent. A
    Range is marked from min to max in steps, optionally with a toggle for the half mark; Choices are
    labelled values. Deleting is refused while marking points use it, and says how many.
-->
<script lang="ts">
    import { confirm } from "@/lib/confirm";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import Tabs from "@/manage/components/Tabs.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import { destroy, index, store, update } from "@/routes/manage/admin/marking-point-templates";
    import { useForm } from "@inertiajs/svelte";

    type Choice = { label: string; value: number | null };
    type Settings =
        | { mode: "default"; min: number; max: number; step: number; use_toggle_for_half: boolean }
        | { mode: "choice"; choice: Choice[] };

    let {
        template,
    }: {
        /** Null for a new one */
        template: { id: string; name: string; settings: Settings; uses: number } | null;
    } = $props();

    // A new template starts as 0 to 10 in halves, with five choices ready if you switch to Choices
    // svelte-ignore state_referenced_locally
    const settings = template?.settings;
    // svelte-ignore state_referenced_locally
    const form = useForm({
        name: template?.name ?? "",
        mode: settings?.mode ?? "default",
        min: settings?.mode === "default" ? settings.min : 0,
        max: settings?.mode === "default" ? settings.max : 10,
        step: settings?.mode === "default" ? settings.step : 0.5,
        use_toggle_for_half: settings?.mode === "default" ? settings.use_toggle_for_half : true,
        choice: settings?.mode === "choice"
            ? settings.choice.map((choice) => ({ ...choice }))
            : [
                  { label: "Poor", value: 1 },
                  { label: "Bad", value: 3 },
                  { label: "Good", value: 5 },
                  { label: "Very good", value: 7 },
                  { label: "Excellent", value: 10 },
              ] as Choice[],
    });

    const deleting = useForm({});

    const KINDS = [
        { id: "default", label: "Range" },
        { id: "choice", label: "Choices" },
    ];

    function save(submit: SubmitEvent) {
        submit.preventDefault();
        form.transform((data) => ({
            name: data.name,
            settings:
                data.mode === "default"
                    ? { mode: "default", min: data.min, max: data.max, step: data.step, use_toggle_for_half: data.use_toggle_for_half }
                    : { mode: "choice", choice: data.choice },
        }));
        if (template) form.put(update(template.id).url);
        else form.post(store().url);
    }

    async function remove() {
        if (!template) return;
        const ok = await confirm({
            title: `Delete ${template.name}?`,
            description: "Organisers won't be able to pick it any more. It can't be undone.",
            confirmLabel: "Delete template",
            confirmVariant: "danger",
        });
        if (ok) deleting.delete(destroy(template.id).url);
    }

    const errors = $derived(form.errors as Record<string, string>);
    const error = (key: string) => errors[`settings.${key}`];
    const choicesError = $derived(errors["settings.choice"] ?? Object.entries(errors).find(([key]) => key.startsWith("settings.choice."))?.[1]);
    const heading = "text-lg font-extrabold uppercase";
</script>

<svelte:head>
    <title>{template ? template.name : "New template"} - Marking point templates - Admin - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: index().url, label: "Marking point templates" }} title={template ? template.name : "New template"} />

<form onsubmit={save} class="flex max-w-2xl flex-col gap-6">
    {#if template}<UnsavedChanges dirty={form.isDirty} />{/if}

    <div class={["v2-field", errors.name && "is-invalid"]}>
        <label for="mpt-name">Name</label>
        <input
            id="mpt-name"
            class="v2-input w-full"
            bind:value={form.name}
            required
            maxlength={255}
            autocomplete="off"
            aria-invalid={errors.name ? "true" : undefined}
            aria-describedby={errors.name ? "mpt-name-error" : undefined}
        />
        {#if errors.name}<span id="mpt-name-error" class="v2-error">{errors.name}</span>{/if}
    </div>

    <div>
        <h2 id="mpt-kind" class={heading}>How it's marked</h2>
        <div class="mt-3">
            <Tabs tabs={KINDS} bind:value={form.mode} id="mpt" labelledby="mpt-kind">
                {#snippet panel(kind)}
                    {#if kind === "default"}
                        <p class="mt-3 mb-4 text-[0.9375rem]/[1.6] text-v2-muted">A mark from the lowest to the highest, in steps.</p>
                        <div class="grid gap-4 *:mt-0 sm:grid-cols-3">
                            {#each [{ key: "min", label: "Lowest mark" }, { key: "max", label: "Highest mark" }, { key: "step", label: "Step" }] as field (field.key)}
                                {@const fieldError = error(field.key)}
                                <div class={["v2-field", fieldError && "is-invalid"]}>
                                    <label for="mpt-{field.key}">{field.label}</label>
                                    <input
                                        id="mpt-{field.key}"
                                        type="number"
                                        step={field.key === "step" ? 0.5 : "any"}
                                        min={field.key === "step" ? 0.5 : undefined}
                                        required
                                        class="v2-input w-full"
                                        bind:value={form[field.key as "min" | "max" | "step"]}
                                        aria-invalid={fieldError ? "true" : undefined}
                                        aria-describedby={fieldError ? `mpt-${field.key}-error` : undefined}
                                    />
                                    {#if fieldError}<span id="mpt-{field.key}-error" class="v2-error">{fieldError}</span>{/if}
                                </div>
                            {/each}
                        </div>
                        <div class="mt-5">
                            <Switch
                                name="use_toggle_for_half"
                                label="Toggle for half marks"
                                hint="Officials mark whole numbers, with a toggle to add a half."
                                bind:checked={form.use_toggle_for_half}
                            />
                        </div>
                    {:else}
                        <p class="mt-3 mb-4 text-[0.9375rem]/[1.6] text-v2-muted">Officials pick one of these labels, which scores its value.</p>
                        <ul class="flex flex-col gap-2">
                            {#each form.choice as choice, i (i)}
                                {@const labelError = errors[`settings.choice.${i}.label`]}
                                {@const valueError = errors[`settings.choice.${i}.value`]}
                                <li class="flex flex-wrap items-start gap-2">
                                    <input
                                        class={["v2-input min-w-0 flex-1", labelError && "is-invalid"]}
                                        placeholder="Label"
                                        aria-label="Choice {i + 1} label"
                                        required
                                        maxlength={255}
                                        bind:value={choice.label}
                                    />
                                    <input
                                        type="number"
                                        step="0.5"
                                        class={["v2-input w-28", valueError && "is-invalid"]}
                                        placeholder="Value"
                                        aria-label="Choice {i + 1} value"
                                        required
                                        bind:value={choice.value}
                                    />
                                    <button
                                        type="button"
                                        class="v2-btn v2-btn-ghost v2-btn-sm"
                                        disabled={form.choice.length === 1}
                                        onclick={() => (form.choice = form.choice.filter((_, j) => j !== i))}
                                    >
                                        Remove<span class="sr-only"> choice {i + 1}</span>
                                    </button>
                                    {#if labelError || valueError}<span class="v2-error w-full">{labelError ?? valueError}</span>{/if}
                                </li>
                            {/each}
                        </ul>
                        <button type="button" class="v2-btn v2-btn-sm mt-3" onclick={() => (form.choice = [...form.choice, { label: "", value: null }])}>
                            Add choice
                        </button>
                        {#if choicesError && !Object.keys(errors).some((key) => /^settings\.choice\.\d/.test(key))}
                            <span class="v2-error mt-2 block">{choicesError}</span>
                        {/if}
                    {/if}
                {/snippet}
            </Tabs>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-v2-border pt-6">
        {#if template}
            <div>
                {#if template.uses > 0}
                    <p class="text-sm text-v2-muted">
                        {template.uses} marking {template.uses === 1 ? "point uses" : "points use"} it, so it can't be deleted.
                    </p>
                {:else}
                    <button type="button" class="v2-btn v2-btn-danger v2-btn-sm" disabled={deleting.processing} onclick={remove}>Delete template</button>
                {/if}
                {#if (deleting.errors as Record<string, string>).template}
                    <span class="v2-error mt-2 block">{(deleting.errors as Record<string, string>).template}</span>
                {/if}
            </div>
        {:else}
            <span></span>
        {/if}
        <div class="flex items-center gap-4">
            {#if template && form.isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
            <button type="submit" class="v2-btn v2-btn-primary" disabled={form.processing}>{template ? "Save" : "Add template"}</button>
        </div>
    </div>
</form>
