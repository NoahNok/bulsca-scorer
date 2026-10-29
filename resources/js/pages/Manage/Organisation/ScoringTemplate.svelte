<!--
    New Scoring template (manage.organisations.scoring.templates.create) and editing one (….edit), in
    OrganisationLayout under a link back to Scoring: its name and the scoring editor events use. Delete
    (confirmed) is at the top right when editing. Events that started from it keep their own copy.
-->
<script lang="ts">
    import { destroyTemplate, index, storeTemplate, updateTemplate } from "@/actions/App/Http/Controllers/Manage/OrganisationScoringController";
    import { confirm } from "@/lib/confirm";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import ScoringSchemaEditor from "@/manage/components/ScoringSchemaEditor.svelte";
    import type { OrganisationHeader, ScoringSchema } from "@/manage/types";
    import { router } from "@inertiajs/svelte";

    let {
        organisation,
        template,
    }: {
        organisation: OrganisationHeader;
        /** Null for a new one */
        template: { id: number; name: string; schema: ScoringSchema } | null;
    } = $props();

    const ids = $derived(template ? { organisation: organisation.id, template: template.id } : null);

    async function remove() {
        if (!template || !ids) return;
        const ok = await confirm({
            title: `Delete ${template.name}?`,
            description: "Events that started from it keep their scoring. It can't be undone.",
            confirmLabel: "Delete template",
            confirmVariant: "danger",
        });
        if (ok) router.delete(destroyTemplate(ids).url);
    }
</script>

<svelte:head>
    <title>{template ? template.name : "New Scoring template"} - Scoring - {organisation.name} - Scoring.Events</title>
</svelte:head>

<PageHeading
    back={{ href: index(organisation.id).url, label: "Scoring" }}
    title={template ? template.name : "New Scoring template"}
    lead="An event's scoring can start from it. Changing it later doesn't change events that already did."
>
    {#snippet actions()}
        {#if template}
            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm text-v2-danger-text" onclick={remove}>Delete</button>
        {/if}
    {/snippet}
</PageHeading>

<div class="max-w-4xl">
    {#if template && ids}
        {#key template.id}
            <ScoringSchemaEditor scoring={{ schema: template.schema }} name={template.name} url={updateTemplate(ids).url} saveLabel="Save template" />
        {/key}
    {:else}
        <ScoringSchemaEditor scoring={{ schema: null }} name="" url={storeTemplate(organisation.id).url} saveLabel="Create template" />
    {/if}
</div>
