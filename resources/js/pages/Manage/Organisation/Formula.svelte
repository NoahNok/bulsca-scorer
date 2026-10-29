<!--
    New Formula (manage.organisations.scoring.formulas.create) and editing one (….edit), in
    OrganisationLayout under a link back to Scoring: its name, global variables and result equation, the
    same editor as a Results sheet's Custom Formula. Delete (confirmed) is at the top right when editing.
    Results sheets copy a Formula in, so changing or deleting it doesn't change them.
-->
<script lang="ts">
    import { destroyFormula, index, storeFormula, updateFormula } from "@/actions/App/Http/Controllers/Manage/OrganisationScoringController";
    import { confirm } from "@/lib/confirm";
    import FormulaEditor from "@/manage/components/FormulaEditor.svelte";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { FormulaVariable, OrganisationHeader } from "@/manage/types";
    import { router, useForm } from "@inertiajs/svelte";

    let {
        organisation,
        formula,
    }: {
        organisation: OrganisationHeader;
        /** Null for a new one */
        formula: { id: number; name: string; equation: string; global_variables: FormulaVariable[] } | null;
    } = $props();

    const ids = $derived(formula ? { organisation: organisation.id, formula: formula.id } : null);

    // svelte-ignore state_referenced_locally
    const form = useForm({
        name: formula?.name ?? "",
        equation: formula?.equation ?? "",
        global_variables: structuredClone($state.snapshot(formula?.global_variables ?? [])) as FormulaVariable[],
    });

    const filled = (value: string | null | undefined) => (value ?? "").trim() !== "";

    function save(submit: SubmitEvent) {
        submit.preventDefault();
        form.transform((data) => ({ ...data, global_variables: data.global_variables.filter((v) => filled(v.name) && filled(v.expression)) }));
        if (ids) form.post(updateFormula(ids).url, { preserveScroll: true, onSuccess: () => form.defaults() });
        else form.post(storeFormula(organisation.id).url);
    }

    async function remove() {
        if (!formula || !ids) return;
        const ok = await confirm({
            title: `Delete ${formula.name}?`,
            description: "Results sheets that copied it in keep their Formula. It can't be undone.",
            confirmLabel: "Delete Formula",
            confirmVariant: "danger",
        });
        if (ok) router.delete(destroyFormula(ids).url);
    }
</script>

<svelte:head>
    <title>{formula ? formula.name : "New Formula"} - Scoring - {organisation.name} - Scoring.Events</title>
</svelte:head>

<PageHeading
    back={{ href: index(organisation.id).url, label: "Scoring" }}
    title={formula ? formula.name : "New Formula"}
    lead="A Results sheet copies it in to edit. Changing it later doesn't change sheets that already did."
>
    {#snippet actions()}
        {#if formula}
            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm text-v2-danger-text" onclick={remove}>Delete</button>
        {/if}
    {/snippet}
</PageHeading>

<form onsubmit={save} class="flex max-w-4xl flex-col gap-6">
    <UnsavedChanges dirty={form.isDirty} />

    <div class={["v2-field mt-0 max-w-md", form.errors.name && "is-invalid"]}>
        <label for="f-name">Name</label>
        <input
            id="f-name"
            class="v2-input w-full"
            bind:value={form.name}
            maxlength="255"
            autocomplete="off"
            aria-invalid={form.errors.name ? "true" : undefined}
            aria-describedby={form.errors.name ? "f-name-error" : undefined}
        />
        {#if form.errors.name}<span id="f-name-error" class="v2-error">{form.errors.name}</span>{/if}
    </div>

    <div class="flex flex-col gap-5 border-t border-v2-border pt-5">
        <FormulaEditor bind:variables={form.global_variables} bind:equation={form.equation} error={form.errors.equation} id="f" />
    </div>

    <div class="flex items-center justify-end gap-4 border-t border-v2-border pt-5">
        {#if form.hasErrors}<span class="mr-auto text-sm font-semibold text-v2-danger-text">Check the fields above.</span>{/if}
        {#if formula && form.isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
        <button type="submit" class="v2-btn v2-btn-primary" disabled={form.processing}>{formula ? "Save Formula" : "Create Formula"}</button>
    </div>
</form>
