<!--
    An organisation's Scoring (manage.organisations.scoring, Organisers only), in OrganisationLayout: its
    Scoring templates, which an event's scoring starts from, and its Formulas, which a Results sheet
    copies in. Each is a list of whole-row links to its edit page, with a New button.
-->
<script lang="ts">
    import { createFormula, createTemplate } from "@/actions/App/Http/Controllers/Manage/OrganisationScoringController";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import RowTable, { rowLink } from "@/manage/components/RowTable.svelte";
    import type { OrganisationHeader } from "@/manage/types";
    import { Link } from "@inertiajs/svelte";

    type Item = { id: number; name: string; url: string };

    let {
        organisation,
        templates,
        formulas,
    }: {
        organisation: OrganisationHeader;
        /** By name */
        templates: Item[];
        /** By name */
        formulas: Item[];
    } = $props();

    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
</script>

<svelte:head>
    <title>Scoring - {organisation.name} - Scoring.Events</title>
</svelte:head>

{#snippet list(items: Item[], label: string)}
    <RowTable rows={items} columns={[label]}>
        {#snippet row(item)}
            <td><Link href={item.url} class={rowLink}>{item.name}</Link></td>
        {/snippet}
    </RowTable>
{/snippet}

<div class="grid gap-10 lg:grid-cols-2">
    <section aria-labelledby="s-templates">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 id="s-templates" class={heading}>Scoring templates</h2>
            {#if templates.length > 0}
                <Link href={createTemplate(organisation.id).url} class="v2-btn v2-btn-sm">New template</Link>
            {/if}
        </div>
        <p class={lead}>An event's scoring can start from one. Changing a template later doesn't change events that started from it.</p>

        {#if templates.length > 0}
            {@render list(templates, "Scoring template")}
        {:else}
            <EmptyState>
                <p>No Scoring templates yet. Make one to start events' scoring from.</p>
                <Link href={createTemplate(organisation.id).url} class="v2-btn mt-4">New template</Link>
            </EmptyState>
        {/if}
    </section>

    <section aria-labelledby="s-formulas">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 id="s-formulas" class={heading}>Formulas</h2>
            {#if formulas.length > 0}
                <Link href={createFormula(organisation.id).url} class="v2-btn v2-btn-sm">New Formula</Link>
            {/if}
        </div>
        <p class={lead}>How a Results sheet turns event results into points. A sheet copies one in to edit.</p>

        {#if formulas.length > 0}
            {@render list(formulas, "Formula")}
        {:else}
            <EmptyState>
                <p>No Formulas yet. Make one to copy into {organisation.name}'s Results sheets.</p>
                <Link href={createFormula(organisation.id).url} class="v2-btn mt-4">New Formula</Link>
            </EmptyState>
        {/if}
    </section>
</div>
